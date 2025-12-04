🚀 EMS – Employee Management System

A complete HR management solution designed to help organizations efficiently manage employees, attendance, payroll, and more.
Built with PHP, MVC architecture, MySQL, and runs on XAMPP.

📌 1. Key Features
👤 Employee Management

Add / edit / delete employees

Personal info, position, salary, joining date

Track employee status (active / inactive)

🕒 Attendance Tracking

Daily check-in / check-out

Track worked hours and absence

Monthly attendance reports

Exportable data

🏢 Department Management

Manage departments

Assign employees to departments

Track department heads and employee count

💰 Payroll System

Monthly salary calculation

Flexible formula: base salary + overtime + bonus

Generate payroll reports

Store payment history

📁 Project Management

Create and manage projects

Assign employees to tasks/projects

Track deadlines and progress

📊 Reports & Dashboard

HR analytics overview

Department statistics

Employee performance reports

Time-based data filtering

⚙️ 2. How to Set Up (XAMPP)
🔧 Requirements

PHP 8.x

MySQL

XAMPP (Apache + MySQL)

Any modern browser

Composer (optional, depending on project setup)

🛠 Step 1 – Clone the Project
git clone https://github.com/<your-username>/ems.git


Or download and extract the ZIP file from GitHub.

📂 Step 2 – Move Project to XAMPP

Place the project folder inside:

xampp/htdocs/ems

🗄 Step 3 – Import the Database

Open phpMyAdmin:
👉 http://localhost/phpmyadmin

Create a new database (e.g., ems_db)

Import the SQL file located in:
database/ems.sql

🧰 Step 4 – Configure Database Connection

Edit the file:

app/config/database.php


Update the connection details:

return new PDO(
    "mysql:host=localhost;dbname=ems_db;charset=utf8",
    "root",
    ""
);


(Default XAMPP user is root with no password.)

🔌 Step 5 – Start XAMPP Services

Open XAMPP Control Panel and start:

Apache

MySQL

🌐 Step 6 – Run the Application

Open your browser:

👉 http://localhost/ems

If using an MVC router, this will direct you to the Login or Dashboard page.

👥 Default Admin Credentials
Email: admin@ems.com
Password: admin123


(Update if your project uses different credentials.)

📝 3. Suggested Folder Structure
ems/
│── app/
│   ├── controllers/
│   ├── models/
│   ├── views/
│   ├── core/
│   └── config/
│── public/
│── database/
│── vendor/
│── .htaccess
│── README.md

🤝 4. Contributing

Contributions are welcome!
Feel free to submit a pull request or open an issue for suggestions and bug reports.

📄 5. License

MIT License – open for educational and commercial use.
