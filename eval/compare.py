"""Keystone against Twenty Twenty-Five on the same content: bytes, requests and axe-core violations per page,
and Lighthouse scores when a Lighthouse binary is given.

Usage: python eval/compare.py <axe.min.js> [--lighthouse <path to lighthouse/cli/index.js>]
Lighthouse drives the Chromium that Playwright installed, so both tools measure the same browser.
Needs the integration stack running with demo content (bash tests/integration/run.sh).
Writes eval/reports/compare.json.
"""

import json
import os
import statistics
import subprocess
import sys
import tempfile
from pathlib import Path

from playwright.sync_api import sync_playwright

ROOT = Path(__file__).resolve().parent.parent
SITE = "http://127.0.0.1:8080"
PAGES = {"home": "/", "page": "/about/", "post": "/brushing-guide/", "blog": "/news/"}
THEMES = ["keystone", "twentytwentyfive"]
AXE = Path(sys.argv[1]).read_text(encoding="utf-8")
args = sys.argv[2:]
LH = args[args.index("--lighthouse") + 1] if "--lighthouse" in args else None


def wp(*cmd):
    env = dict(os.environ, MSYS_NO_PATHCONV="1")
    return subprocess.run(["docker", "compose", "exec", "-T", "cli", "wp", *cmd], cwd=ROOT, capture_output=True, text=True, env=env, check=True).stdout


def kind(url, ctype):
    u = url.split("?")[0]
    if u.endswith(".css") or "text/css" in ctype:
        return "css"
    if u.endswith(".js") or "javascript" in ctype:
        return "js"
    if u.endswith((".woff", ".woff2", ".ttf")) or "font" in ctype:
        return "font"
    if "image" in ctype:
        return "image"
    if "html" in ctype:
        return "html"
    return "other"


def measure(browser, path):
    ctx = browser.new_context(viewport={"width": 1280, "height": 900})
    page = ctx.new_page()
    sizes = {}
    count = 0

    def on_response(resp):
        nonlocal count
        try:
            body = resp.body()
        except Exception:  # noqa: BLE001 - redirects and aborted requests have no body
            return
        count += 1
        k = kind(resp.url, resp.headers.get("content-type", ""))
        sizes[k] = sizes.get(k, 0) + len(body)

    page.on("response", on_response)
    page.goto(SITE + path, wait_until="networkidle")
    page.add_script_tag(content=AXE)
    axe = page.evaluate(
        """async () => {
            const r = await axe.run(document, { runOnly: { type: 'tag', values: ['wcag2a', 'wcag2aa', 'wcag21a', 'wcag21aa'] } });
            return r.violations.map((v) => ({ id: v.id, impact: v.impact, nodes: v.nodes.length }));
        }"""
    )
    ctx.close()
    return {"requests": count, "bytes": sizes, "total": sum(sizes.values()), "axe": axe}


def lighthouse_once(path, chrome):
    out = Path(tempfile.gettempdir()) / "keystone-lh.json"
    cmd = ["node", LH, SITE + path, "--quiet", "--output=json", f"--output-path={out}",
           "--only-categories=performance,accessibility,best-practices,seo", "--form-factor=mobile",
           "--throttling-method=simulate", "--chrome-flags=--headless=new --no-sandbox"]
    subprocess.run(cmd, check=True, env=dict(os.environ, CHROME_PATH=chrome))
    data = json.loads(out.read_text(encoding="utf-8"))
    cats = {k: round(v["score"] * 100) for k, v in data["categories"].items()}
    audits = data["audits"]
    cats["lcp_ms"] = round(audits["largest-contentful-paint"]["numericValue"])
    cats["cls"] = round(audits["cumulative-layout-shift"]["numericValue"], 3)
    cats["tbt_ms"] = round(audits["total-blocking-time"]["numericValue"])
    return cats


def lighthouse(path, chrome, runs=3):
    """Median of several runs per metric: single Lighthouse runs move by 10+ points on a busy machine."""
    samples = [lighthouse_once(path, chrome) for _ in range(runs)]
    return {k: statistics.median(s[k] for s in samples) for k in samples[0]} | {"runs": runs}

results = {}
with sync_playwright() as p:
    browser = p.chromium.launch(headless=True)
    for theme in THEMES:
        wp("theme", "activate", theme)
        results[theme] = {}
        for name, path in PAGES.items():
            results[theme][name] = measure(browser, path)
            if LH:
                results[theme][name]["lighthouse"] = lighthouse(path, p.chromium.executable_path)
            print(theme, name, results[theme][name]["total"], "bytes", len(results[theme][name]["axe"]), "axe violations", flush=True)
    browser.close()
wp("theme", "activate", "keystone")

out = ROOT / "eval" / "reports"
out.mkdir(parents=True, exist_ok=True)
(out / "compare.json").write_text(json.dumps(results, indent=2) + "\n", encoding="utf-8")
