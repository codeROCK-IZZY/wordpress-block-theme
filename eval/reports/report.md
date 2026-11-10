# Keystone evaluation

## Summary

- Contrast: 36 text/background pairs across 4 palettes; lowest ratio 5.36:1 (WCAG AA needs 4.5:1).
- Page weight: Keystone is 30.8% lighter than Twenty Twenty-Five on the same content (average of 4 pages).
- axe-core (WCAG 2.1 A/AA): Keystone 0 violations, Twenty Twenty-Five 0.
- Result: all checks passed

## Keystone against Twenty Twenty-Five (same blocks, same content)

| Page | Keystone bytes | Twenty Twenty-Five bytes | Saving | Web fonts (K / TT5) | axe violations (K / TT5) | Lighthouse perf (K / TT5) | Lighthouse a11y (K / TT5) |
|---|---:|---:|---:|---:|---:|---:|---:|
| home | 146,611 | 205,774 | 28.8% | 0 / 53,600 | 0 / 0 | 69 / 68 | 100 / 100 |
| page | 131,029 | 189,942 | 31% | 0 / 53,600 | 0 / 0 | 81 / 75 | 100 / 100 |
| post | 133,827 | 200,207 | 33.2% | 0 / 53,600 | 0 / 0 | 68 / 66 | 100 / 100 |
| blog | 136,945 | 195,766 | 30% | 0 / 53,600 | 0 / 0 | 79 / 83 | 100 / 100 |

Bytes are uncompressed response bodies for every request the page makes, measured in Chromium with an empty cache. Both themes load the same core JavaScript (the Navigation block's mobile menu).

## Contrast

| Palette | Text | Background | Ratio | Needed | Used for |
|---|---|---|---:|---:|---|
| Default | contrast | base | 15.97:1 | 4.5:1 | Body text and headings |
| Default | contrast | surface | 14.61:1 | 4.5:1 | Text in the hero, testimonials and footer bands |
| Default | muted | base | 7.56:1 | 4.5:1 | Dates, captions, small print |
| Default | muted | surface | 6.91:1 | 4.5:1 | Footer tagline and credits |
| Default | primary | base | 6.67:1 | 4.5:1 | Links, and the focus outline (3:1 needed) |
| Default | primary | surface | 6.10:1 | 4.5:1 | Links in the hero and footer bands |
| Default | on-primary | primary | 6.67:1 | 4.5:1 | Button text |
| Default | on-primary | contrast | 15.97:1 | 4.5:1 | Button text on hover |
| Default | base | contrast | 15.97:1 | 4.5:1 | Contact band text, links and button |
| Default | accent | base | 2.26:1 | - | Quote border (decorative, not required) |
| Default | line | base | 1.37:1 | - | Separators and FAQ dividers (decorative, not required) |
| Forest | contrast | base | 16.52:1 | 4.5:1 | Body text and headings |
| Forest | contrast | surface | 14.80:1 | 4.5:1 | Text in the hero, testimonials and footer bands |
| Forest | muted | base | 7.67:1 | 4.5:1 | Dates, captions, small print |
| Forest | muted | surface | 6.87:1 | 4.5:1 | Footer tagline and credits |
| Forest | primary | base | 6.52:1 | 4.5:1 | Links, and the focus outline (3:1 needed) |
| Forest | primary | surface | 5.84:1 | 4.5:1 | Links in the hero and footer bands |
| Forest | on-primary | primary | 6.52:1 | 4.5:1 | Button text |
| Forest | on-primary | contrast | 16.52:1 | 4.5:1 | Button text on hover |
| Forest | base | contrast | 16.52:1 | 4.5:1 | Contact band text, links and button |
| Forest | accent | base | 3.40:1 | - | Quote border (decorative, not required) |
| Forest | line | base | 1.38:1 | - | Separators and FAQ dividers (decorative, not required) |
| Midnight (dark) | contrast | base | 16.30:1 | 4.5:1 | Body text and headings |
| Midnight (dark) | contrast | surface | 14.45:1 | 4.5:1 | Text in the hero, testimonials and footer bands |
| Midnight (dark) | muted | base | 8.71:1 | 4.5:1 | Dates, captions, small print |
| Midnight (dark) | muted | surface | 7.73:1 | 4.5:1 | Footer tagline and credits |
| Midnight (dark) | primary | base | 9.85:1 | 4.5:1 | Links, and the focus outline (3:1 needed) |
| Midnight (dark) | primary | surface | 8.73:1 | 4.5:1 | Links in the hero and footer bands |
| Midnight (dark) | on-primary | primary | 9.38:1 | 4.5:1 | Button text |
| Midnight (dark) | on-primary | contrast | 15.52:1 | 4.5:1 | Button text on hover |
| Midnight (dark) | base | contrast | 16.30:1 | 4.5:1 | Contact band text, links and button |
| Midnight (dark) | accent | base | 11.00:1 | - | Quote border (decorative, not required) |
| Midnight (dark) | line | base | 1.45:1 | - | Separators and FAQ dividers (decorative, not required) |
| Terracotta | contrast | base | 15.69:1 | 4.5:1 | Body text and headings |
| Terracotta | contrast | surface | 13.99:1 | 4.5:1 | Text in the hero, testimonials and footer bands |
| Terracotta | muted | base | 8.07:1 | 4.5:1 | Dates, captions, small print |
| Terracotta | muted | surface | 7.20:1 | 4.5:1 | Footer tagline and credits |
| Terracotta | primary | base | 6.01:1 | 4.5:1 | Links, and the focus outline (3:1 needed) |
| Terracotta | primary | surface | 5.36:1 | 4.5:1 | Links in the hero and footer bands |
| Terracotta | on-primary | primary | 6.24:1 | 4.5:1 | Button text |
| Terracotta | on-primary | contrast | 16.28:1 | 4.5:1 | Button text on hover |
| Terracotta | base | contrast | 15.69:1 | 4.5:1 | Contact band text, links and button |
| Terracotta | accent | base | 2.35:1 | - | Quote border (decorative, not required) |
| Terracotta | line | base | 1.34:1 | - | Separators and FAQ dividers (decorative, not required) |
