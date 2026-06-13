# LUMA Dashboard Redesign — White + Rose Pro Theme

**Date:** 2026-06-13
**Context:** Redesign all 4 dashboards (Admin, Mama, Expert, CTT) from dark theme to white+rose professional theme with full CRUD.

---

## 1. Design System

### Color Palette

| Token | Value | Usage |
|-------|-------|-------|
| `--bg-body` | `#f8f9fc` | Page background |
| `--bg-card` | `#ffffff` | Cards, tables, modals |
| `--sidebar-bg` | `#ffffff` | Sidebar |
| `--sidebar-active` | `#fdf2f6` | Active nav bg |
| `--sidebar-border` | `#f0f0f5` | Sidebar divider |
| `--primary` | `#c94b72` | Primary actions, active states |
| `--primary-hover` | `#b33d62` | Primary hover |
| `--primary-light` | `#f0a0bb` | Soft accents |
| `--primary-subtle` | `#fdf2f6` | Very light pink bg |
| `--text-dark` | `#1a1a2e` | Primary text |
| `--text-muted` | `#6b7280` | Secondary text |
| `--border` | `#e5e7eb` | Borders |
| `--success` | `#10b981` | Active/approved |
| `--warning` | `#f59e0b` | Pending |
| `--danger` | `#ef4444` | Suspended/deleted |
| `--shadow-sm` | `0 1px 3px rgba(0,0,0,0.06)` | Card shadow |
| `--shadow-md` | `0 4px 12px rgba(0,0,0,0.08)` | Elevated |

### Typography

All text uses `Inter` font family (already loaded via Google Fonts):

| Level | Size | Weight | Color |
|-------|------|--------|-------|
| Page title | 1.5rem | 700 | `--text-dark` |
| Card title | 1rem | 600 | `--text-dark` |
| Stat number | 1.75rem | 700 | `--text-dark` |
| Stat label | 0.8rem | 500 | `--text-muted` |
| Table header | 0.75rem | 600 | `--text-muted` |
| Table cell | 0.875rem | 400 | `--text-dark` |
| Sidebar nav | 0.875rem | 500 | `--text-muted` |
| Sidebar active | 0.875rem | 600 | `--primary` |

### Spacing

Cards: 24px padding, 16px gap
Tables: 12px cell padding
Sections: 24px gap between sections
Sidebar: 16px horizontal padding, 8px between items

---

## 2. Layout Architecture

### Single Dashboard Layout (`views/layouts/dashboard.php`)

```
┌────────────────────────────────────────────────────┐
│ TOP BAR                                            │
│ [☰] [LUMA] [breadcrumb]              [avatar ▾]   │
├──────────┬─────────────────────────────────────────┤
│ SIDEBAR  │  MAIN CONTENT                           │
│ (fixed,  │  (scrollable)                           │
│  240px)  │                                         │
│          │  ┌─ Page Title ────────────────────┐    │
│ Nav Item │  │ [search] [filter] [+ add]       │    │
│ Nav Item │  ├─────────────────────────────────┤    │
│ Nav Item │  │ Stat | Stat | Stat | Stat       │    │
│ ──────── │  ├─────────────────────────────────┤    │
│ Nav Item │  │ Table / Form / Content          │    │
│          │  │                                  │    │
│          │  └──────────────────────────────────┘    │
│          │                                         │
│          │  ── Footer ──                           │
└──────────┴─────────────────────────────────────────┘
```

### Top Bar
- Height: 64px
- White background, `border-bottom: 1px solid var(--border)`
- Left: hamburger toggle (collapses sidebar on mobile or to icon-only)
- Center: breadcrumb based on current page
- Right: user avatar + name dropdown (profile, settings, logout)

### Sidebar
- Width: 240px (desktop), full-screen overlay (mobile)
- White background, `border-right: 1px solid var(--border)`
- Logo at top with brand mark
- Navigation items grouped by section with labels
- Active item: `var(--sidebar-active)` bg + 3px `var(--primary)` left border
- Hover: slight bg tint
- Icons (Bootstrap Icons) before each label
- Bottom: fixed "Voir le site" and "Déconnexion"
- Collapsible to 64px icon-only mode

### Main Content
- Left margin accounts for sidebar width
- Max-width content with responsive padding
- White stat cards row at top
- Data tables with toolbar
- Paginated lists

---

## 3. Components

### 3.1 Stat Cards

4-column row (responsive: 2-col tablet, 1-col mobile):

```html
<div class="stat-card">
  <div class="stat-card-icon"><i class="bi bi-people"></i></div>
  <div class="stat-card-info">
    <span class="stat-card-number">1,234</span>
    <span class="stat-card-label">Utilisateurs</span>
  </div>
  <span class="stat-card-trend up">+5.2%</span>
</div>
```

- White card with subtle shadow
- Icon in rose circle container
- Trend indicator (green up / red down)

### 3.2 Data Tables

```html
<div class="table-toolbar">
  <input type="search" placeholder="Rechercher...">
  <div class="table-filters">...</div>
  <button class="btn btn-primary">+ Ajouter</button>
</div>
<div class="table-wrapper">
  <table class="table-dashboard">
    <thead><tr><th>Nom</th><th>Email</th><th>Statut</th><th>Actions</th></tr></thead>
    <tbody>
      <tr>
        <td>...</td>
        <td>...</td>
        <td><span class="badge-dashboard success">Actif</span></td>
        <td class="actions-cell">
          <button class="btn-icon" title="Modifier"><i class="bi bi-pencil"></i></button>
          <button class="btn-icon" title="Supprimer"><i class="bi bi-trash"></i></button>
        </td>
      </tr>
    </tbody>
  </table>
</div>
<div class="table-pagination">...</div>
```

- Sticky header with `#f9fafb` background
- Row hover: `var(--primary-subtle)` tint
- Status badges: success (green), warning (yellow), danger (red), info (blue)
- Action buttons as icon-only circles
- Delete with JS confirmation + AJAX

### 3.3 Forms

```html
<div class="form-floating mb-3">
  <input type="text" class="form-control-dashboard" id="name" placeholder="Nom">
  <label for="name">Nom</label>
</div>
```

- Floating labels (Bootstrap 5 pattern)
- `border: 1px solid var(--border)`, `border-radius: 8px`
- Focus: `box-shadow: 0 0 0 3px rgba(201,75,114,0.15); border-color: var(--primary)`
- Validation: rose border + message
- Buttons: `.btn-primary` (rose bg), `.btn-outline` (rose border)

### 3.4 Modal Dialogs

```html
<div class="modal-dashboard fade" id="modal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5>Title</h5>
        <button class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">...</div>
      <div class="modal-footer">
        <button class="btn btn-outline">Annuler</button>
        <button class="btn btn-primary">Enregistrer</button>
      </div>
    </div>
  </div>
</div>
```

- Max-width centered, backdrop blur
- Clean white card, no shadows

### 3.5 Notifications / Flash Messages

- Toast notifications (top-right) for AJAX actions
- Bootstrap alerts for flash messages

---

## 4. Sidebar Navigation per Role

### Admin (16 links)
Dashboard, Articles, Catégories, Utilisateurs, Mamans, Experts, Ressources, Communauté, Tickets, Commentaires, Témoignages, FAQ, Messages, Newsletter, Paramètres, [separator] Voir le site, Déconnexion

### Mama (15 links)
Dashboard, Mon Profil, Ma Grossesse, Mon Bébé, Croissance, Vaccination, Rendez-vous, Messagerie, Mon Agenda, Blog, Ressources, Communauté, Support, Notifications, Paramètres, [separator] Voir le site, Déconnexion

### Expert (10 links)
Dashboard, Profil Professionnel, Questions Mamans, Articles, Ressources, Communauté, Notifications, Paramètres, [separator] Voir le site, Déconnexion

### CTT (9 links)
Dashboard, Gestion Tickets, Support Mamans, Support Experts, FAQ, Historique, Rapports, Notifications, [separator] Voir le site, Déconnexion

---

## 5. AJAX Action System

### Pattern

```js
// All action buttons use data attributes
<button class="btn-icon btn-action" 
        data-action="approve" 
        data-id="123" 
        data-url="/admin/comments/approve/123"
        title="Approuver">
  <i class="bi bi-check-lg"></i>
</button>

// dashboard.js handles click:
// 1. Show confirmation for destructive actions
// 2. POST to data-url via fetch()
// 3. Show toast on success/error
// 4. Update row state (badge/toggle) without page reload
// 5. Auto-hide toast after 3s
```

### Actions
- **Toggle status:** active/inactive, publish/draft — instant toggle
- **Delete:** confirmation modal → POST → remove row with animation
- **Approve/Reject:** confirmation → POST → update badge color
- **Mark as read:** POST → fade row

### Toast Template
```html
<div class="toast-dashboard success">
  <i class="bi bi-check-circle-fill"></i>
  <span>Message</span>
</div>
```

---

## 6. Responsive Behavior

| Breakpoint | Sidebar | Table | Cards |
|-----------|---------|-------|-------|
| >1200px | Fixed 240px | Full | 4-col |
| 992-1199px | Fixed 240px | Scroll X | 3-col |
| 768-991px | Collapsed 64px | Scroll X | 2-col |
| <768px | Full overlay | Scroll X + stack | 1-col |

Mobile: sidebar becomes full-screen overlay with backdrop, toggled by hamburger.

---

## 7. Files to Create / Modify

### New files:
1. `views/layouts/dashboard.php` — unified dashboard layout
2. `public/assets/css/dashboard.css` — all dashboard styles
3. `public/assets/js/dashboard.js` — sidebar, AJAX, toasts, modals

### Modify layout files:
4. `views/layouts/admin.php` → redirect to dashboard.php
5. `views/layouts/maman.php` → redirect to dashboard.php
6. `views/layouts/expert.php` → redirect to dashboard.php
7. `views/layouts/ctt.php` → redirect to dashboard.php

### Redesign view files (42 files):
8. `views/admin/*` (17 files)
9. `views/dashboard/*` (12 files)
10. `views/expert/*` (7 files)
11. `views/ctt/*` (6 files)

---

## 8. Implementation Order

1. Create `dashboard.css` — full design system
2. Create `dashboard.js` — sidebar, AJAX, toasts, modals
3. Create `views/layouts/dashboard.php` — unified layout
4. Redirect all 4 layouts → dashboard.php
5. Redesign Admin dashboard views (template)
6. Redesign Mama dashboard views
7. Redesign Expert dashboard views
8. Redesign CTT dashboard views
9. Verify all CRUD flows work end-to-end

---

## 9. YAGNI Exclusions

- No real-time notifications (no WebSocket/Socket.io)
- No drag-and-drop table reordering
- No dark mode toggle
- No export to CSV/PDF
- No bulk select actions (MVP scope)
- No advanced filtering (basic search only)
