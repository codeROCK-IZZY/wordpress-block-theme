# 3. Compare against Twenty Twenty-Five on identical content

## Context

"Lightweight" claims are easy to make by comparing a theme's demo page with another theme's much richer demo. The
comparison only means something when both themes render the same blocks.

## Decision

`tests/integration/setup.php` expands Keystone's patterns into plain core-block markup and saves it as the home
page content, so the home page contains the same blocks whichever theme is active. The about page, a blog post and
the posts page use the same content in both themes too. `eval/compare.py` activates each theme in turn and measures
the four pages in the same Chromium build:

- every response body the page downloads, grouped by type;
- axe-core WCAG 2.1 A/AA violations;
- Lighthouse (mobile, simulated throttling), the median of three runs per page.

## Consequences

- The byte difference comes from what the themes add (fonts, styles, template markup), not from different content.
  Both themes load the same core JavaScript for the Navigation block's mobile menu, so JavaScript is equal.
- The gate checks deterministic numbers: bytes, web fonts, axe-core, Lighthouse accessibility and layout shift.
  The Lighthouse performance score is reported but not gated, because the same page scores 10+ points apart
  between runs on a shared machine.
- Twenty Twenty-Five ships with WordPress 6.7 and later, so the comparison runs on the latest WordPress only. The
  6.6 job runs the theme and HTTP checks.
