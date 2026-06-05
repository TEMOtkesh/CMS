# Experimental — Restaurant Portfolio CMS

> Branch: `experimental` | Status: planning

## Concept

A CMS for managing a network of restaurants that use 3D AR menus.
Public visitors browse participating restaurants and preview their 3D models directly in the browser.
Restaurant owners (users) manage their own listings. Admins manage the network.

## User flows

### Public visitor (no account)
- Lands on homepage — sees a grid of all active restaurants in the network
- Clicks a restaurant card — opens a detail page with info + a 3D model viewer modal
- The modal loads a `.glb` / `.gltf` model file and renders it with model-viewer or Three.js
- Can browse all 3D menu items for that restaurant in a list

### Restaurant owner (user role)
- Registers and logs in
- Uploads their restaurant profile: name, description, logo, cover photo
- Uploads 3D model files (.glb) for each menu item: name, price, category, thumbnail
- Can update and delete their own listings

### Admin
- Approves or rejects restaurants before they go public
- Can feature restaurants on the homepage
- Full CRUD on all users, restaurants, and models
- Activity log for all admin actions

## Database design (planned)

```
restaurants   — id, user_id(FK), name, description, logo, cover, is_approved, is_featured
menu_items    — id, restaurant_id(FK), name, price, model_file, thumbnail, is_active
categories    — id, name  (e.g. starters, mains, desserts)
item_categories — item_id, category_id  (N:N pivot)
```

Combined with the existing `users` and `roles` tables this gives 6 tables
with multiple 1:N and N:N relationships.

## 3D viewer

Use `<model-viewer>` web component (Google):
```html
<script type="module" src="https://unpkg.com/@google/model-viewer/dist/model-viewer.min.js"></script>
<model-viewer src="uploads/burger.glb" auto-rotate camera-controls></model-viewer>
```
No Three.js setup needed — just a `<model-viewer>` tag.
GLB files are uploaded via the existing FileManager class (extend allowed MIME types).

## Pages (planned, 5+ required)

1. `index.php` — restaurant network grid (public)
2. `restaurant.php?id=X` — single restaurant + 3D model list (public)
3. `register.php` / `login.php` — auth
4. `dashboard.php` — owner manages their restaurant + menu items
5. `admin.php` — admin panel (approve restaurants, manage network)
6. `contact.php` — contact form

## Status

Nothing built yet. Main branch (`main`) runs the Photo Portfolio Gallery CMS.
This branch is a sandbox for the restaurant concept.
