# Styles

Small layered CSS for the mini CRM (no UI framework).

| File | Role |
|------|------|
| `app.css` | Vite entry — imports the layers below |
| `tokens.css` | Design tokens (`:root` CSS variables) |
| `base.css` | Reset, typography, `body` |
| `utilities.css` | Cross-app helpers (e.g. `.visually-hidden`) |
| `auth.css` | Login / guest screens |

Dashboard and layout styles stay **scoped** in Vue components and consume `var(--crm-*)` from `tokens.css`.
