# Platform2027 — Explore build (Inertia/React)

Standalone build of the **Explore** feature (public community/location explorer). It
will eventually be **extracted and merged into Mobilize v2** — a Laravel + Inertia.js +
React app. This repo is currently self-contained and does **not** answer to Mobilize v2's
style guide, but we deliberately adopt conventions that make the future merge cheap.

> Merge target reference: `/Users/rudston/PhpstormProjects/Mobilze Projects/Mobilize_Version2/CLAUDE.md`

## Stack

- **Backend:** Laravel 12, PHP 8.2, MySQL
- **Admin:** Filament v5 (Livewire-based) at `/admin` — its own login on the `web` guard
- **Public app:** Inertia.js 2 + React 18 (plain JS/JSX, no TypeScript), Vite 7 — Explore
  (`/explore`) and community detail (`/communities/{circle}`) are Inertia/React.
- **Styling:** Tailwind CSS 4 (CSS-first: `@import`, `@theme`, `@variant`), `@tailwindcss/vite`
- **Livewire 4:** now only the `/counter` demo + Filament (`/admin`). The Explore feature was
  ported to Inertia/React; the Livewire Explore components/views were removed.

### Coexistence
Filament/Livewire (`/admin`) and the Inertia public app run side by side. Filament has its
own asset pipeline; the Inertia app uses `resources/js/app.jsx`. Do not entangle the two.

> `wire-elements/modal` is no longer used (replaced by `Components/Modal.jsx`) — safe to
> remove from composer if desired.

## Auth
There is **no in-app authentication** — the manual login/register/password-reset layer was
removed. The eventual host app (Mobilize v2) provides auth. Filament's admin login is
separate and untouched. Explore browsing is fully public; the "add community" write-flow is
gated/disabled pending the host app's auth.

## Namespacing (already refactored)
Everything domain-specific lives under an `Explorer` sub-namespace so it extracts as a unit:
- Models → `App\Models\Explorer\…` (except `App\Models\User`)
- Enums → `App\Enums\Explorer\…`
- Contracts → `App\Contracts\Explorer\…`

Polymorphic `*_type` columns store these FQCNs; a migration remapped existing rows
(`remap_morph_types_to_explorer_namespace`).

## Frontend conventions (mirror the merge target)
```
resources/js/
  app.jsx          # Inertia entry (createInertiaApp)
  Pages/           # Inertia pages, by module (Explore/, Communities/)
  Components/       # shared React components (Explore/…)
  Layouts/         # persistent layouts
  hooks/           # useExploreNavigation, useGeolocation, useDebouncedSearch, useTrans
```
- URL query string is the **single source of truth** for Explore state (`?circle&type&community&view`);
  read server-side, echoed back as props. Navigation = Inertia visits with `preserveState`/
  `preserveScroll` + partial reloads (replaces Livewire `#[Url]`).
- Enum labels/icons/singular/plural and translated strings come **from the server** (props +
  a shared translation bag via `HandleInertiaRequests` → `useTrans()`), so the client never
  re-implements the enum `match()` logic or duplicates i18n. Existing `lang/{en,pt}` stay.
- Thin controllers; query logic lives in `App\Services\Explore\ExploreQueryService`; JSON/prop
  shapes go through API Resources.

## ⚠️ Merging into Mobilize v2 — required changes

### Tailwind (this build is v4 CSS-first; target uses Tailwind 3 + `tailwind.config.js`)
Our components reference custom utilities that only generate if the config knows about them.
If the target is still on **Tailwind 3** at merge, add the following to **their**
`tailwind.config.js` (a ~15-line edit — **not** a component rewrite; the `className` strings
themselves are identical across v3/v4):

1. **Semantic color tokens** — so `bg-surface`, `text-main`, `text-muted`,
   `border-border-muted` generate:
   ```js
   theme: { extend: { colors: {
     surface: 'var(--color-surface)',
     main: 'var(--color-main)',
     muted: 'var(--color-muted)',
     'border-muted': 'var(--color-border-muted)',
   } } }
   ```
   The light/dark hex values stay as `:root` / `.dark` CSS variables in a stylesheet
   (identical in v3 and v4 — copies over untouched). See `resources/css/app.css`.
2. **`darkMode: 'class'`** — our `@variant dark (&:where(.dark, .dark *))` becomes this.
3. **Content paths** — their `content: [...]` must scan wherever the Explore components land
   (e.g. `resources/js/Pages/Explore/**`, `resources/js/Components/**`), matching our
   `@source` directives, or the classes get purged.

If the target upgrades to Tailwind 4 first, the port is instead a near copy of our
`@theme`/`:root`/`.dark` blocks — even less work.

### Portability discipline (enforced while building, to keep the above small)
- Components use **only** stable core utilities + the 4 semantic tokens — no raw hex / one-off
  colors in markup.
- **Avoid v4-renamed utilities** in component markup: `outline-hidden` (v3 `outline-none`),
  `shadow-xs` (v3 `shadow-sm`), and non-default ring widths — so there's nothing to
  find/replace for a v3 target.
- Dark mode via the CSS-var + `.dark` class mechanism only (portable across v3/v4).

### Backend
- **No Laravel-12-only APIs** in mergeable code (controllers/services/models/routes) — 11/12
  compatible so it drops into the target's L11 skeleton unchanged.
- Shared-prop wiring is the main merge task: `HandleInertiaRequests` here stubs `locale`,
  `appName`, and a translation bag; the target additionally shares `auth`, `companyName`
  (white-label), `enabledFeatures`, `permissions`. Leave clear TODO slots.
- **White-label:** never hardcode "Mobilize"/"Platform2027" in UI — use an app-name prop.
- Explore may need **multi-tenant DB context** in the target (central vs tenant DB) — TBD.

## Dev commands
```bash
php artisan serve
npm run dev            # Vite dev server (HMR)
npm run build          # production build
php artisan test       # test suite
php artisan migrate
```
