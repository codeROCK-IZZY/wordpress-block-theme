# 2. Colour slots by role, with contrast checked in every variation

## Context

Style variations are easy to add and easy to get wrong: a new palette that looks fine on a designer's screen can
put light grey captions on white. Block themes also let editors pick any colour for any block, which breaks a
brand quickly.

## Decision

Every palette, including each style variation, fills the same eight slots, named for their job rather than their
hue: `base`, `surface`, `contrast`, `muted`, `primary`, `on-primary`, `accent` and `line`. Templates and patterns
only use these slots. The default WordPress palette and the custom colour picker are turned off.

`eval/run.php` lists every text/background pairing that appears in the templates, parts, patterns and `theme.json`
element styles (body text on both backgrounds, muted text on both backgrounds, links on both backgrounds, button
text, button hover, the contrast band) and computes the WCAG 2.x contrast ratio for each pair in each palette. CI
fails if any pair is below 4.5:1. `accent` and `line` are decorative (a quote border, separators) and are reported
but not gated.

## Consequences

- A new variation is a JSON file of eight colours; the gate says immediately whether it is readable.
- The dark variation works without any CSS because slots describe roles: `on-primary` is dark there, light
  elsewhere.
- Editors cannot pick arbitrary colours. A site that needs more freedom can re-enable `color.custom` in a child
  theme.
- The list of pairs is maintained by hand. A new pattern that puts `muted` text on `primary` would need a new line
  in the list (see known limitations).
