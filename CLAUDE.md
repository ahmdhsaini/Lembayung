# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Running the App

Served by Laragon (Apache + MySQL). Start Laragon, then visit:
- `http://localhost/Lembayung` or the virtual host if configured

No build step. PHP files are served directly.

## Database

- Name: `lembayung_db`
- Connection: `includes/config.php` (Laragon defaults: `root`, no password)
- Tables:
  - `users` — `id, username, password, full_name, phone_number, role` (`role`: `'customer'` or `'staff'`)
  - `bookings` — `id, customer_id, slot_id, booking_date, num_guests, special_notes, status, table_id`
  - `timeslots` — `slot_id, time_range`
  - `restaurant_tables` — `table_id, table_number, seating_capacity` (note: column is `seating_capacity`, not `capacity`)

Passwords stored plain text — no hashing.

## Architecture

Every page follows the same pattern:
1. `session_start()` + role check → redirect to `login.php` if unauthorized
2. `require_once "includes/config.php"` for `$link`
3. Business logic (POST handling first, then SELECT for display)
4. Include a header partial, render HTML, include `includes/footer.php`

**Header partials** (each call `session_start()` themselves — do not call it again before including them):
- `includes/header.php` — public pages (index, login, register)
- `includes/staff_header.php` — staff pages
- `includes/cust_header.php` — customer pages

**Role routing** in `login.php`: compares `strtolower($row['role']) === 'staff'`; anything else goes to `customer_dashboard.php`.

## Page Map

| File | Role | Purpose |
|---|---|---|
| `index.php` | public | Landing page |
| `login.php` | public | Auth + Remember Me cookie |
| `register.php` | public | Customer self-registration |
| `customer_dashboard.php` | customer | List own bookings, filter by ID/status |
| `make_booking.php` | customer | Create booking (overbooking check: max 10 tables per slot, ceil(guests/4)) |
| `edit_booking.php` | customer | Edit Pending bookings |
| `delete_booking.php` | customer | Delete Pending bookings |
| `print_receipt.php` | customer | Print slip for Approved bookings |
| `staff_dashboard.php` | staff | View all bookings, search by name/status |
| `update_booking.php` | staff | Set status + assign table |
| `manage_tables.php` | staff | Add/delete `restaurant_tables` rows |
| `manage_slots.php` | staff | Manage `timeslots` rows |
| `export_bookings.php` | staff | Download CSV |
| `logout.php` | any | Destroy session |

## Key Conventions

- All DB writes/reads use `mysqli` prepared statements with `bind_param`.
- Dynamic filter queries build `$types` and `$params[]` then spread: `mysqli_stmt_bind_param($stmt, $types, ...$params)`.
- `htmlspecialchars()` on all user-sourced output.
- JS validation lives in `js/validation.js`, loaded only on `register.php`. The form must have `name="registerForm"` for `document.registerForm` to resolve.
- Bootstrap 3.4.1 (CDN) + local `css/bootstrap.css` overrides.
