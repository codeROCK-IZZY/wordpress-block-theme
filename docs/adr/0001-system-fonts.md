# 1. System fonts instead of bundled web fonts

## Context

Twenty Twenty-Five downloads about 54 KB of WOFF2 files on every page. On a mobile connection the text either waits
for those files or is drawn twice (a fallback font, then the web font, which moves the text). For a dentist's or a
plumber's site, the typeface is rarely what wins the customer; the phone number appearing quickly is.

## Decision

`theme.json` declares two system font stacks: a sans-serif for body text (San Francisco, Segoe UI, Roboto…) and a
serif for headings (Charter, Sitka, Cambria, Georgia). The theme ships no font files and registers no `fontFace`.

## Consequences

- No font requests, no flash of unstyled or invisible text, and no layout shift from font swapping.
- Headings look slightly different on macOS, Windows and Android. The stacks were chosen so each platform gets a
  well-hinted face with similar proportions.
- A site owner who wants a brand typeface can install one from the Font Library in the Site Editor; the theme does
  not prevent it. The integration test checks that the theme itself registers none.
