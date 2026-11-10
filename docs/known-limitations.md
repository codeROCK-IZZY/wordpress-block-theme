# Known limitations

- **The contrast list is maintained by hand.** `eval/run.php` checks the text/background pairs the theme uses
  today. A new pattern that introduces a new pairing (for example muted text on the primary colour) needs a new line
  in that list, or the gate will not see it.
- **Editors can still create poor contrast in content.** Turning off custom colours limits choices to the palette,
  but an editor can still put `muted` text on a `contrast` background inside a post. axe-core on the demo pages
  does not cover every combination an editor can make.
- **The Lighthouse performance score is not gated.** On shared CI runners and laptops, total blocking time (from the
  core Navigation script both themes load) varies by seconds between identical runs, so the score moves by 10+
  points. Bytes, fonts, accessibility and layout shift are deterministic and gated instead.
- **No custom block styles or JavaScript.** The theme relies on core blocks only. Anything interactive (sliders,
  tabs) needs a plugin.
- **English demo content only.** Strings in patterns are translatable, but no translations are shipped.
- **Tested with the latest WordPress and 6.6 only.** Versions in between are expected to work (the theme uses
  theme.json v3, introduced in 6.6) but are not in the CI matrix.
