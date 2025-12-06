# 🚀 EMS -- Employee Management System

[![PHP](https://img.shields.io/badge/PHP-8.x-blue?logo=php)]()\
[![License: MIT](https://img.shields.io/badge/License-MIT-green.svg)]()\
[![Build](https://img.shields.io/badge/Build-Passing-brightgreen)]()

A complete Human Resource Management System designed to help
organizations efficiently manage employees, attendance, payroll,
projects, and analytics.\
Built with **PHP**, **MVC Architecture**, **MySQL**, and runs on
**XAMPP**.

------------------------------------------------------------------------

## 📚 **Table of Contents**

-   [✨ Features](#-features)\
-   [⚙️ Installation Guide](#️-installation-guide)\
-   [📁 Project Structure](#-project-structure)\
-   [🌐 Access Information](#-access-information)\
-   [🖼 Screenshots](#-screenshots)\
-   [🤝 Contributing](#-contributing)\
-   [📄 License](#-license)

------------------------------------------------------------------------

# ✨ **Features**

## 👤 **1. Employee Management**

-   Add, edit, and delete employees
-   Store personal details, salary, position, and start date
-   Show basic details of employees

## 🕒 **2. Attendance Tracking**

-   Check-in / Check-out
-   Track working hours & absences
-   Show attendance status on the browser
## 🏢 **3. Department Management**

-   Create and manage departments
-   Assign employees to departments
-   Track department heads & total members

## 💰 **4. Payroll System**

-   Monthly salary calculation
-   Base salary + overtime + bonus - deduction
-   Payroll generation & history tracking
-   Indicate payroll status: Paid - Unpaid - Pending

## 📁 **5. Project Management**

-   Create/update/manage projects
-   Assign employees to projects
-   Track project budget and departments

## 📊 **6. Reports**

-   Coming soon!

------------------------------------------------------------------------

# ⚙️ **Installation Guide**

## 🔧 **Requirements**

-   PHP 8.x
-   MySQL
-   XAMPP (Apache + MySQL)
-   Browser
-   Composer (optional)

------------------------------------------------------------------------

## 🛠 **Step 1 --- Clone the Repository**

``` bash
git clone https://github.com/MinhQuangQu/ems-database-project.git
```

Or download the ZIP from GitHub.

------------------------------------------------------------------------

## 📂 **Step 2 --- Move Project to XAMPP**

-   Rename folder to **CSDL**
-   Place the project folder inside:

    xampp/htdocs/

------------------------------------------------------------------------

## 🗄 **Step 3 --- Import the Database**

1.  Open **MySQL 9.1**

2.  Install database folder: `SQLr`

3.  Import file:

        schema.sql
        seed.sql
    

------------------------------------------------------------------------

## 🧰 **Step 4 --- Configure Database Connection**

Open:

    http://localhost/phpmyadmin

Check if database `db_employee_infomation_manager` is available on **phpMyAdmin**

------------------------------------------------------------------------

## 🔌 **Step 5 --- Start XAMPP**

-   Start **Apache**
-   Start **MySQL**

------------------------------------------------------------------------

## 🌐 **Step 6 --- Run the Application**

Visit:

👉 **http://localhost/CSDL/public**

You will be redirected to the Login or Dashboard, depending on your
routing setup.

------------------------------------------------------------------------

# 🌐 **Access Information**

### 👥 **Register/Login**

    Register by typing your username and password

------------------------------------------------------------------------

# 📁 **Project Structure**

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

------------------------------------------------------------------------

# 🖼 **Screenshots**

### 🔐 Login Page

![Login Screenshot](https://github.com/MinhQuangQu/ems-database-project/blob/05a5a1372527d3b81524dafdc9fb8683d5e6e275/public/assets/images/Screenshot%202025-12-04%20223457.png)

### 🕒 Attendance Management

![Attendance Screenshot](https://github.com/MinhQuangQu/ems-database-project/blob/c8dc1921c722f7fb5d418231c66f3a668a59d0e8/public/assets/images/Attendance.png)

### 🏠 Dashboard

![Dashboard Screenshot](https://github.com/MinhQuangQu/ems-database-project/blob/c8dc1921c722f7fb5d418231c66f3a668a59d0e8/public/assets/images/Dashboard.png)

### 👤 Employee Management

![Employee Screenshot](https://github.com/MinhQuangQu/ems-database-project/blob/c8dc1921c722f7fb5d418231c66f3a668a59d0e8/public/assets/images/Employee.png)

### 🏢 Department Management

![Department Screenshot](https://github.com/MinhQuangQu/ems-database-project/blob/c8dc1921c722f7fb5d418231c66f3a668a59d0e8/public/assets/images/Department.png)

### 📁 Project Management

![Project Screenshot](https://github.com/MinhQuangQu/ems-database-project/blob/c8dc1921c722f7fb5d418231c66f3a668a59d0e8/public/assets/images/Project.png)

### 💰 Payroll Management

![Payroll Screenshot](https://github.com/MinhQuangQu/ems-database-project/blob/c8dc1921c722f7fb5d418231c66f3a668a59d0e8/public/assets/images/Payroll.png)

------------------------------------------------------------------------
### Youtube Link 

[![Watch the video](https://www.youtube.com/watch?v=pB4x5NXqU5A)](https://youtu.be/pB4x5NXqU5A)

-----------------------------------------------------------------------

# 🤝 **Contributing**

Contributions are welcome!\
Feel free to:

-   Submit pull requests\
-   Open issues\
-   Suggest new features

------------------------------------------------------------------------

# 📄 **License**

Licensed under the **MIT License**.\
Free for personal, educational, and commercial use.




