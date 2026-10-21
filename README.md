
\# fctCare --- E-Channeling System 

\### Book. Channel. Care. A web-based

healthcare e-channeling platform designed to help patients find doctors,
explore hospital information, and manage appointment-related tasks
through a role-based interface.

\[\![PHP\]  (https://img.shields.io/badge/PHP-8%2B-777BB4?logo=php&logoColor=white)\](https://www.php.net/)
\[\![MySQL\]  (https://img.shields.io/badge/Database-MySQL%2FMariaDB-4479A1?logo=mysql&logoColor=white)\](https://www.mysql.com/)
\[\![Frontend\]  (https://img.shields.io/badge/Frontend-HTML%20%7C%20CSS%20%7C%20JavaScript-E34F26)\](https://developer.mozilla.org/)
\[\![Environment\]  (https://img.shields.io/badge/Local%20Server-XAMPP-FB7A24)\](https://www.apachefriends.org/)
\*\*Repository:\*\*

\[sanushi2003/fctCare_eChannelling_System\](https://github.com/sanushi2003/fctCare_eChannelling_System)


\-\-- \## 📖 About the Project

\*fctCare\*is a web-based e-channeling system developed to make healthcare appointment-related
tasks more accessible and organized. 
The project brings patients,doctors, and administrators into one application with separate
role-based access. The application uses \*\*PHP\*\* for server-side processing and \*\*MySQL/MariaDB\*\* for database operations.
Its interface is built with \*\*HTML, CSS, and JavaScript\*\*, with a healthcare-focused design and responsive layouts. This repository is
intended to contain the source code and project documentation for the
fctCare e-channeling system.


\## 🎯 Project Objectives - 

Provide aconvenient web interface for healthcare channeling. - Help users access doctor and hospital information. - Support appointment booking workflows. - Provide separate login experiences for patients doctors,and administrators. - Support administrative management of doctors,hospitals, and schedules. 

\## ✨ Features 
\### 👤 Patient Portal -
Patient registration and login. - Patient dashboard. - Browse doctor and
hospital information. - Appointment booking workflow. - Role-specific
access after login. 

\### 🩺 Doctor Portal - Doctor login and
dashboard. - Doctor account linked to doctor information in the
database. - Access to doctor-related appointment information, where
implemented in the current version. 

\### 🛠️ Admin Portal - Administrator
login and dashboard. - Manage doctor information. - Manage hospital
information. - Add and edit doctor schedules. - Access administration
pages for managing system records. 

\### 🔐 Authentication and User
Interface - Role-based login for patients, doctors, and
administrators. - Password verification using PHP\'s
\`password_verify()\` for stored password hashes. - Session-based
authentication and role-specific redirects. - Show/hide password control
on the login form. - Responsive interface for desktop and mobile
screens. - Healthcare-themed design, branding, and navigation. \>
\*\*Feature note:\*\* The exact availability of each feature depends on
the source files and database schema included in the current repository
version. 


\## 🧰 Technologies 
Used \| Technology \| Purpose \|\|\-\--\|\-\--\| \| PHP \| Server-side processing and application logic
\| \| MySQL / MariaDB \| Database storage \| \| HTML5 \| Page structure
\| \| CSS3 \| Styling, layout, and responsive design \| \| JavaScript \|
Client-side interactions \| \| Apache \| Local web server \| \| XAMPP \|
Local development environment \| \| Git and GitHub \| Version control
and source-code hosting \| 


\## 🗂️ Project Structure The structure below
is a guide based on the project organization. File names may differ
slightly in the current repository. \`\`\`text
fctCare_eChannelling_System/ ├── admin/ \# Admin dashboard and
management pages ├── patient/ \# Patient registration and dashboard
pages ├── doctor/ \# Doctor dashboard and related pages ├── backend/ │
└── config/ │ ├── db.php \# Database connection │ └── session.php \#
Session and access-control logic ├── images/ \# Website images and other
visual assets ├── index.php \# Home page ├── login.php \# Patient,
doctor, and admin login ├── doctors.php \# Doctor information ├──
hospitals.php \# Hospital information ├── appointment.php \# Appointment
booking page ├── contact.php \# Contact page └── README.md \# Project
documentation \`\`\` 



\## ⚙️ Installation and Setup Follow these steps to
run the project locally on Windows using XAMPP. \### Prerequisites
Install the following tools: - \[XAMPP\](https://www.apachefriends.org/)
with Apache, PHP, MySQL/MariaDB, and phpMyAdmin. -
\[Git\](https://git-scm.com/) (optional, if you clone the repository). -
A modern web browser. 

\### 1. Clone the Repository Open Git Bash,
Command Prompt, or a terminal and run: \`\`\`bash git clone
https://github.com/sanushi2003/fctCare_eChannelling_System.git \`\`\`
\### 2. Move the Project into \`htdocs\` Place the project folder inside
your XAMPP web directory. For a standard Windows installation, the path
will look like this: \`\`\`text
C:\\xampp\\htdocs\\fctCare_eChannelling_System \`\`\` If you downloaded
the repository as a ZIP file, extract it into the \`htdocs\` folder.
\### 3. Start XAMPP Open the XAMPP Control Panel and start: -
\*\*Apache\*\* - \*\*MySQL\*\* Make sure both services start
successfully. 
\### 4. Create the Database 
        \*1. Open
        \`http://localhost/phpmyadmin\` in your browser. 
        \*2. Create a database
        named: \`\`\`text fctcare_db \`\`\` 
        \*3. If the repository includes a
        database \`.sql\` export, select the new database and import that file
        through phpMyAdmin. 
        \*4. If no SQL export is included, create the required
        tables using the schema expected by the application. The application
        will not work fully until its required database tables and columns
        exist. 
        \*5. Configure the Database Connection Open the database
        configuration file: \`\`\`text backend/config/db.php \`\`\` Make sure
        the connection settings match your local MySQL/MariaDB configuration. A
        typical local XAMPP configuration looks like this: \`\`\`php
        connect_error) { die(\"Database connection failed.\"); }
        \$conn-\>set_charset(\"utf8mb4\"); ?\> \`\`\` \*\*Important:\*\* This is
        a sample configuration. Keep the existing structure expected by your
        project and change the settings only when necessary. Do not publish
        production passwords, private credentials, or secrets in the repository.
        \* 6. Open the Website Visit: \`\`\`text
        http://localhost/fctCare_eChannelling_System/ \`\`\` If the project
        folder has a different name inside \`htdocs\`, use that folder name in
        the URL. 



\## 🗄️ Database Overview Based on the application\'s intended
functionality, the database may contain records for areas such as: -
Patients - Doctors - Doctor login accounts - Administrators -
Hospitals - Appointments - Schedules - Contact messages -
Notifications - Audit logs The exact table names, column names, and
relationships must match the SQL queries used in the version of the
source code you are running. Use the supplied SQL export or project
schema if available. \## 🔒 Security Considerations - Store passwords as
secure hashes rather than plain text. - Use prepared statements for
database queries. - Validate user input on the server side. - Protect
patient, doctor, and admin pages with appropriate session and role
checks. - Keep credentials and environment-specific settings out of
public source control. - Use HTTPS and secure server configuration when
deploying to a live environment. This documentation does not represent a
formal security audit of the application. \## 🧪 Testing Checklist
Before demonstrating or deploying the project, check the following: - \[
\] Apache and MySQL/MariaDB start successfully. - \[ \] The database
connection works. - \[ \] Required database tables have been imported or
created. - \[ \] Patient registration and login work. - \[ \] Doctor
login and dashboard access work. - \[ \] Admin login and dashboard
access work. - \[ \] Doctor and hospital information loads correctly. -
\[ \] Appointment-related forms validate their inputs. - \[ \] Schedule
management pages work as expected. - \[ \] The interface displays
correctly on desktop and mobile screens. - \[ \] Invalid login attempts
display an appropriate message. \## 🚀 Possible Future Improvements
Potential enhancements for future versions include: - Appointment status
tracking and cancellation workflows. - More detailed doctor availability
and schedule management. - Email or SMS appointment notifications. -
Improved form validation and accessibility. - Automated tests and
deployment instructions. - A database seed file for easier local
setup. - Screenshots or a short demonstration video in the repository.
\## 🤝 Contributing Suggestions and improvements are welcome. If you are
working with this repository: 1. Fork the repository. 2. Create a
feature branch. 3. Make and test your changes. 4. Commit your work with
a descriptive message. 5. Open a pull request describing the changes.
\## 👩‍💻 Author \*\*Sanushi Nethmika\*\* GitHub:
\[@sanushi2003\](https://github.com/sanushi2003) \## 📄 License No
license has been specified in this README. If you intend to distribute
the project for reuse, add an appropriate \`LICENSE\` file and update
this section. \-\--

::: {align="center"}
\*\*fctCare --- Book. Channel. Care.\*\* Made to support a more
convenient digital healthcare experience.
:::
