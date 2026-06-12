# LUMA v2 — UI Enhancement Design

## Approach
**Pure CSS transitions + `@keyframes` animations** (Approach B)
No external animation libraries. Everything layered on top of existing Bootstrap 5 + `style.css`.

## CSS Architecture
Two new files inside `public/assets/css/`:
- **`animations.css`** — all `@keyframes`, transition defaults, animation utility classes
- **`enhancements.css`** — refined component styles (cards, forms, buttons, sidebar, tables, stat widgets)

Both loaded after Bootstrap and `style.css` in layout `<head>`.
All animations respect `prefers-reduced-motion: reduce` via single `@media` block.

## Animation System

| Animation | Trigger | Duration | Easing | Elements |
|-----------|---------|----------|--------|----------|
| `fade-up` | load/scroll | 0.6s | cubic-bezier(0.16,1,0.3,1) | Hero sections, page titles, stat numbers |
| `scale-in` | hover | 0.3s | cubic-bezier(0.16,1,0.3,1) | Cards, blog thumbnails, testimonial cards |
| `slide-in-right` | load | 0.5s | cubic-bezier(0.16,1,0.3,1) | Sidebar nav items (staggered `--delay`) |
| `pulse-glow` | hover | 1.5s cycle | ease-in-out | Primary CTAs, .btn-luma |
| `shimmer` | load | 2s | linear | Glass borders, stat card accent lines |

Default: `transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1)` on all interactive elements.

## Component Enhancements

### Cards (`.card-luma`, `.bg-luma-glass`)
- `box-shadow: 0 4px 24px rgba(0,0,0,0.3)`
- `border: 1px solid rgba(201, 75, 114, 0.15)`
- Hover: translateY(-4px), `box-shadow: 0 8px 40px rgba(201,75,114,0.15)`, border glow

### Buttons (`.btn-luma`)
- Gradient: `linear-gradient(135deg, #C94B72, #A63B5A)`
- Hover: scale(1.02), brighter gradient, subtle glow
- `.btn-outline-luma` hover: fill + text color swap

### Forms (`.form-control-luma`)
- Floating labels via CSS `:focus` + `:not(:placeholder-shown)` pattern
- Focus: `box-shadow: 0 0 0 3px rgba(201,75,114,0.25)`
- Validation: `.is-valid` / `.is-invalid` with rose/red borders

### Sidebar Nav
- Active indicator: 3px left border (rose), background tint
- Hover: slide-in effect on icon/text
- Staggered entrance animation via `--delay` custom property

### Tables (`.table-luma`)
- Row hover: `background: rgba(201,75,114,0.08)`, subtle lift
- Striped: alternating glass tint
- Left accent border on hover

### Stat Cards (dashboards)
- Large gradient numbers
- Icon in circular container with glass bg
- Accent underline (gradient line)
- CSS counter animation via `@property` where supported

### Badges
- Glass-like `backdrop-filter: blur(4px)`
- Subtle border matching badge color
- Pulse animation on status badges (open/in-progress)

## Dashboard-specific Enhancements

| Dashboard | Elements Enhanced |
|-----------|------------------|
| **Maman** | Welcome greeting (fade-up), pregnancy progress bar (gradient), stat cards (icon + number), quick action cards (hover lift), forms (floating labels), tickets table (priority row accent), notifications (unread dot) |
| **Expert** | Stats cards (tickets/questions/articles), questions table (inline reply animation), article form (floating labels), resource grid |
| **CTT** | Dashboard stats cards, ticket table (priority left border: red/amber/green), FAQ accordion (chevron animation), history search, report cards |
| **Admin** | Dashboard stat cards with icons, all CRUD tables (hover rows), user badges (role colors), modal animations, settings form |

## Page-specific Enhancements

| Page | Enhancements |
|------|-------------|
| **Home** | Hero with gradient overlay, staggered fade-up on heading/text/CTAs, stat counters animate-in on scroll, testimonial cards scale-in, featured blog cards with image zoom |
| **Blog** | Category filter pills with active glow, blog cards with image zoom on hover, pagination with hover scale |
| **Blog Single** | Wider content max-width, related posts as mini-cards, comment cards with avatar circle, comment form with floating label |
| **Auth** | Centered card on gradient bg, role selection cards (maman/expert) with icon + hover glow + selected state |
| **Contact** | Icon service cards with hover lift, form with floating labels, send button arrow animation |
| **FAQ** | Accordion with animated chevron, hover indicator, category dividers |
| **Community** | Post cards with like/comment pill badges, new post modal centered with backdrop blur, comment thread with avatars |
| **Resources** | Category filter pills, resource cards with color-coded category, download button with icon animation |

## Implementation Order
1. Create `animations.css` — all keyframes + animation utility classes
2. Create `enhancements.css` — all component refinements
3. Link both files in all 5 layout files
4. Add animation classes to dashboard views (stat cards, profile forms, etc.)
5. Add animation classes to admin views (tables, forms, stat cards)
6. Add animation classes to public page views (hero, blog, auth, contact, etc.)
7. Add JS enhancements (stagger delays, scroll-trigger observer, sidebar active)

## Files to Modify
- **New:** `public/assets/css/animations.css`, `public/assets/css/enhancements.css`
- **Layouts:** All 5 layout files (add CSS links)
- **Views:** ~60+ view files (add animation classes, refined markup)
- **JS:** `public/assets/js/app.js` (add IntersectionObserver for scroll animations, sidebar active state)
