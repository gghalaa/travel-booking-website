# Travel Booking Website

A PHP and MySQL travel booking website that allows users to browse travel destinations, view trip details, book trips, manage their bookings, and use a personal dashboard. The project also includes an admin dashboard for managing trips, users, and bookings.

## Features

- User registration and login
- Browse travel destinations
- View trip details
- Book trips with selected dates, class, and number of passengers
- User dashboard for viewing and managing bookings
- Edit user profile
- Cancel or edit bookings
- Admin dashboard
- Manage trips
- Manage users
- Manage bookings
- MySQL database integration

## User Roles

### User

Regular users can:

- Create an account and log in
- Browse available travel destinations
- View trip details, descriptions, and itineraries
- Book trips by selecting travel dates, class, and number of passengers
- View their bookings through the user dashboard
- Edit their profile information
- Edit pending bookings
- Cancel pending or confirmed bookings
- Log out of the system

### Admin

Admins can:

- Access the admin dashboard
- View and manage all trips
- Add new trips
- Edit existing trip information
- Delete trips
- View and manage user accounts
- Add new users
- View and manage customer bookings
- Update booking statuses, such as pending, confirmed, or cancelled

## Technologies Used

- PHP
- MySQL
- HTML
- CSS
- XAMPP
- phpMyAdmin

## Project Structure

    travel-booking-website/
    ├── assets/
    │   ├── css/
    │   │   └── styles.css
    │   └── images/
    ├── database/
    │   └── travel_website.sql
    ├── docs/
    │   └── web project summary.pdf
    ├── aboutus.php
    ├── account.php
    ├── admin.php
    ├── cancel-trip.php
    ├── confirmation.php
    ├── dashboard.php
    ├── db_config.php
    ├── edit-booking.php
    ├── edit-profile.php
    ├── home.php
    ├── logout.php
    ├── manage_bookings.php
    ├── manage_trips.php
    ├── manage_users.php
    ├── submit-booking.php
    ├── trip-details.php
    └── trips.php

## Main Pages

- `home.php` - Main homepage of the website
- `aboutus.php` - About page for the website
- `trips.php` - Displays available travel destinations
- `trip-details.php` - Shows detailed information about each trip
- `account.php` - Handles user login and registration
- `dashboard.php` - User dashboard for profile and booking management
- `edit-profile.php` - Allows users to update their profile information
- `edit-booking.php` - Allows users to edit their booking details
- `cancel-trip.php` - Allows users to cancel a booking
- `confirmation.php` - Displays booking confirmation
- `admin.php` - Admin dashboard landing page
- `manage_trips.php` - Admin page for adding, editing, deleting, and searching trips
- `manage_bookings.php` - Admin page for managing booking statuses
- `manage_users.php` - Admin page for managing users
- `submit-booking.php` - Handles booking form submission
- `logout.php` - Logs the user out of the system
- `db_config.php` - Stores the database connection settings

## Database Setup

The database file is located in:

    database/travel_website.sql

To set up the database:

1. Open XAMPP.
2. Start Apache and MySQL.
3. Open phpMyAdmin.
4. Create a new database.
5. Import `database/travel_website.sql`.

## How to Run the Project

1. Clone or download this repository.
2. Move the project folder into the XAMPP `htdocs` folder.
3. Start Apache and MySQL using XAMPP.
4. Import the database file in phpMyAdmin.
5. Open the project in the browser:

    http://localhost/webproject/home.php

## Notes

- This project was developed as a local PHP and MySQL web application using XAMPP.
- The database connection settings are stored in `db_config.php`.
- The project must be run through a local server such as XAMPP because it uses PHP and MySQL.
- The `assets` folder contains the CSS file and website images.
- The `database` folder contains the SQL file needed to create/import the database.
- The `docs` folder contains project documentation.
