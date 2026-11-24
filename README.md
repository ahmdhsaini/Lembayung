# Lembayung Restaurant Booking System

A PHP and MySQL web application for table reservations at Lembayung Restaurant. Customers sign up, book a table for a date and time slot, and track their reservations. Staff review and approve bookings, assign tables, manage time slots and tables, and export reports.

## Features

### Customers
- Register and log in, with an optional "Remember me" cookie
- Make a booking: date, time slot, number of guests and special notes
- See their bookings and status on the customer dashboard
- Edit or cancel a booking while it is still **Pending**
- Print a booking receipt

### Staff
- Dashboard with counts for pending bookings, approved bookings and today's guests
- Filter bookings by status (Pending / Approved / Cancelled)
- Approve or cancel bookings and assign a restaurant table
- Manage time slots (`manage_slots.php`) and restaurant tables (`manage_tables.php`)
- Export all bookings to CSV (`export_bookings.php`)

## Tech Stack

- PHP (procedural, `mysqli` with prepared statements)
- MySQL / MariaDB
- Bootstrap 3 (`css/bootstrap.css`)
- Vanilla JavaScript for client-side form validation (`js/validation.js`)

## Project Structure

```
├── index.php               # Landing page
├── register.php            # Customer sign-up
├── login.php / logout.php  # Authentication (redirects by role)
├── customer_dashboard.php  # Customer's bookings
├── make_booking.php        # Create a booking
├── edit_booking.php        # Edit a pending booking
├── delete_booking.php      # Cancel/delete a booking
├── print_receipt.php       # Printable booking receipt
├── staff_dashboard.php     # Staff overview and booking list
├── update_booking.php      # Staff: change status and assign a table
├── manage_slots.php        # Staff: time slot management
├── manage_tables.php       # Staff: table management
├── export_bookings.php     # Staff: CSV export
├── includes/
│   ├── config.php          # Database connection settings
│   ├── header.php, cust_header.php, staff_header.php
│   └── footer.php
├── css/bootstrap.css
├── js/validation.js
└── image/                  # Banner and menu images
```

## Getting Started

### Requirements
- PHP 7.4 or newer with the `mysqli` extension
- MySQL or MariaDB
- A local server stack such as [Laragon](https://laragon.org/) or [XAMPP](https://www.apachefriends.org/)

### Installation
1. Clone the repository into your web root (for example `C:\laragon\www` or `C:\xampp\htdocs`):
   ```bash
   git clone https://github.com/ahmdhsaini/Lembayung.git
   ```
2. Create a database named `lembayung_db` and the tables described below.
3. Check the connection settings in `includes/config.php`:
   ```php
   define('DB_SERVER', 'localhost');
   define('DB_USERNAME', 'root');
   define('DB_PASSWORD', '');
   define('DB_NAME', 'lembayung_db');
   ```
4. Start Apache and MySQL, then open `http://localhost/Lembayung/` in your browser.

### Database Tables

The application expects these tables (column names taken from the code):

| Table | Main columns |
|---|---|
| `users` | `id`, `username`, `password`, `full_name`, `phone_number`, `role` (`customer` or `staff`) |
| `bookings` | `id`, `customer_id` → `users.id`, `slot_id` → `timeslots.slot_id`, `table_id` → `restaurant_tables.table_id`, `booking_date`, `num_guests`, `special_notes`, `status` (`Pending` / `Approved` / `Cancelled`) |
| `timeslots` | `slot_id`, `time_range`, `status` (`Active` / inactive) |
| `restaurant_tables` | `table_id`, `table_number`, `capacity` |

New accounts made through `register.php` get the `customer` role. To create a staff account, insert a user with `role = 'staff'` directly in the database.

## Known Limitations

- Passwords are stored and compared as plain text. Before any real deployment, switch to `password_hash()` and `password_verify()`.
- No SQL schema or seed file is included in the repository yet.
- `manage_tables.php` inserts into a `seating_capacity` column, while `update_booking.php` reads `capacity`. Align these column names with your schema.

## License

No license has been specified for this project.
