# fctCare - E-Channeling System

## Book. Channel. Care.

fctCare is a web-based healthcare e-channeling system designed to make the process of finding doctors and booking medical appointments easier.

The system provides separate interfaces for patients, doctors, and administrators. Patients can explore doctor and hospital information and use the appointment-booking process. Doctors can access their dashboards, while administrators can manage system information.

The project is developed using PHP, MySQL/MariaDB, HTML, CSS, and JavaScript.

**GitHub Repository:** [fctCare_eChannelling_System](https://github.com/sanushi2003/fctCare_eChannelling_System)

## Project Objectives

* Provide a convenient online healthcare channeling platform.
* Help users find doctor and hospital information.
* Support the medical appointment-booking process.
* Provide separate login access for patients, doctors, and administrators.
* Support the management of doctors, hospitals, and schedules.

## Main Features

### Patient Portal

* Patient registration and login.
* Patient dashboard.
* View doctor and hospital information.
* Access the appointment-booking process.
* Access patient-specific pages after login.

### Doctor Portal

* Doctor login and dashboard.
* Doctor account linked to doctor information in the database.
* Access appointment-related information where implemented.

### Admin Portal

* Administrator login and dashboard.
* Manage doctor information.
* Manage hospital information.
* Add and edit doctor schedules.
* Access system management pages.

### Authentication and User Interface

* Role-based login for patients, doctors, and administrators.
* Password verification using PHP's `password_verify()` function.
* Session-based authentication.
* Role-specific redirects after login.
* Show and hide password functionality.
* Responsive layouts for desktop and mobile screens.
* Healthcare-themed design and navigation.

Note: The availability of individual features depends on the source code and database structure included in the current version of the project.

## Technologies Used

| Technology      | Purpose                                 |
| --------------- | --------------------------------------- |
| PHP             | Server-side programming                 |
| MySQL / MariaDB | Database management                     |
| HTML5           | Web page structure                      |
| CSS3            | Styling and responsive layouts          |
| JavaScript      | Interactive functionality               |
| Apache          | Web server                              |
| XAMPP           | Local development environment           |
| Git and GitHub  | Version control and source-code hosting |

## Project Structure

The following structure represents the main areas of the project. Actual file and folder names may differ depending on the current version.

```text
fctCare_eChannelling_System/

├── admin/
│   └── Admin dashboard and management pages
├── patient/
│   └── Patient pages and dashboard
|   |__register page
|
├── doctor/
│   └── Doctor pages and dashboard
├── backend/
│   └── config/
│       ├── db.php
│       └── session.php
├── images/
│   └── Website images and visual assets
├── index.php
|___about.php
├── login.php
├── doctors.php
├── hospitals.php
├── appointment.php
├── contact.php
└── README.md
```

## Installation and Setup

Follow these steps to run the project on a Windows computer using XAMPP.

### Requirements

* XAMPP with Apache, PHP, MySQL or MariaDB, and phpMyAdmin.
* Git, if you want to clone the repository.
* A modern web browser.

### 1: Clone the Repository

Open Git Bash or Command Prompt and run:

```bash
git clone https://github.com/sanushi2003/fctCare_eChannelling_System.git
```

### 2: Move the Project into the XAMPP Folder

Place the project folder inside:

```text
C:\xampp\htdocs\
```

The project path should look like this:

```text
C:\xampp\htdocs\fctCare_eChannelling_System
```

### 3: Start XAMPP

Open the XAMPP Control Panel and start these services:

* Apache
* MySQL

Make sure both services are running.

### 4: Create the Database

1. Open `http://localhost/phpmyadmin` in your browser.
2. Create a database named `fctcare_db`.
3. If the repository contains an SQL database file, import it into the new database.
4. If no SQL file is provided, create the tables required by the application.

The website may not work correctly until the required database tables and columns are available.

### 5: Configure the Database Connection

Open the database configuration file:

```text
backend/config/db.php
```

Check that the database connection settings match your local environment.

Typical XAMPP settings are:

| Setting  | Value                            |
| -------- | -------------------------------- |
| Host     | `localhost`                      |
| Username | `root`                           |
| Password | Empty by default, unless changed |
| Database | `fctcare_db`                     |

Keep the existing database connection code and update the settings only when necessary.

Do not publish private credentials or production passwords on GitHub.

### 6: Open the Website

Open your browser and visit:

```text
http://localhost/fctCare_eChannelling_System/
```

If you use a different project folder name, update the URL accordingly.

## Database Overview

Depending on the current version, the application may use database records for:

* Patients
* Doctors
* Doctor login accounts
* Administrators
* Hospitals
* Appointments
* Schedules
* Contact messages
* Notifications
* Audit logs

The exact database tables and columns must match the SQL queries used by the application.

## Security Considerations

* Store passwords as secure hashes rather than plain text.
* Use prepared statements for database queries.
* Validate user input on the server side.
* Protect restricted pages using session and role checks.
* Keep sensitive credentials out of public repositories.
* Use HTTPS and appropriate server configuration when deploying the application online.

These points describe recommended security practices and do not represent a formal security audit of the project.

## Testing Checklist

Before demonstrating the application, check the following:

* [ ] Apache and MySQL/MariaDB start successfully.
* [ ] The database connection works.
* [ ] Required database tables are available.
* [ ] Patient registration and login work.
* [ ] Doctor login and dashboard access work.
* [ ] Administrator login and dashboard access work.
* [ ] Doctor information loads correctly.
* [ ] Hospital information loads correctly.
* [ ] Appointment forms validate user input.
* [ ] Schedule management works as expected.
* [ ] The website displays correctly on desktop and mobile screens.
* [ ] Invalid login attempts display appropriate messages.

## Future Improvements

Possible improvements for future versions include:

* Appointment status tracking and cancellation.
* More detailed doctor availability management.
* Email or SMS appointment notifications.
* Improved form validation and accessibility.
* Automated testing.
* Clearer deployment instructions.
* A database setup file for easier installation.
* Screenshots and a demonstration video.

## Contributing

Contributions and suggestions are welcome.

1. Fork the repository.
2. Create a new feature branch.
3. Make your changes.
4. Test your changes.
5. Commit your work with a descriptive message.
6. Open a pull request explaining your changes.

## Author

**Sanushi Nethmika**

GitHub: [@sanushi2003](https://github.com/sanushi2003)

## License

No license has been specified for this project. Add a `LICENSE` file if you intend to distribute the project under a particular open-source license.

---

**fctCare - Book. Channel. Care.**

A web-based project focused on making healthcare channeling more convenient.
