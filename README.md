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

-   Add, edit, delete employees\
-   Store personal details, salary, position, start date\
-   Manage active/inactive employee status

## 🕒 **2. Attendance Tracking**

-   Check-in / Check-out\
-   Track working hours & absences\
-   Monthly attendance summaries\
-   Exportable attendance reports

## 🏢 **3. Department Management**

-   Create and manage departments\
-   Assign employees to departments\
-   Track department heads & total members

## 💰 **4. Payroll System**

-   Monthly salary calculation\
-   Base salary + overtime + bonus\
-   Payroll generation & history tracking

## 📁 **5. Project Management**

-   Create / update / manage projects\
-   Assign employees to projects\
-   Track project deadlines and overall progress

## 📊 **6. Reports & Dashboard**

-   HR analytics & summary\
-   Staff distribution charts\
-   Attendance & payroll visualizations\
-   Filter by date range

------------------------------------------------------------------------

# ⚙️ **Installation Guide**

## 🔧 **Requirements**

-   PHP 8.x\
-   MySQL\
-   XAMPP (Apache + MySQL)\
-   Browser\
-   Composer (optional)

------------------------------------------------------------------------

## 🛠 **Step 1 --- Clone the Repository**

``` bash
git clone https://github.com/<your-username>/ems.git
```

Or download the ZIP from GitHub.

------------------------------------------------------------------------

## 📂 **Step 2 --- Move Project to XAMPP**

Place the project folder inside:

    xampp/htdocs/ems

------------------------------------------------------------------------

## 🗄 **Step 3 --- Import the Database**

1.  Open **phpMyAdmin** → http://localhost/phpmyadmin\

2.  Create a database named: `ems_db`\

3.  Import file:

        database/ems.sql

------------------------------------------------------------------------

## 🧰 **Step 4 --- Configure Database Connection**

Open:

    app/config/database.php

Replace the connection settings:

``` php
return new PDO(
    "mysql:host=localhost;dbname=ems_db;charset=utf8",
    "root",
    ""
);
```

*(Default XAMPP: user = `root`, password = empty.)*

------------------------------------------------------------------------

## 🔌 **Step 5 --- Start XAMPP**

-   Start **Apache**\
-   Start **MySQL**

------------------------------------------------------------------------

## 🌐 **Step 6 --- Run the Application**

Visit:

👉 **http://localhost/ems**

You will be redirected to the Login or Dashboard depending on your
routing setup.

------------------------------------------------------------------------

# 🌐 **Access Information**

### 👥 **Default Admin Login**

    Email: admin@ems.com
    Password: admin123

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

### 🏠 Dashboard

![Dashboard Screenshot](https://placehold.co/800x400?text=Dashboard)

### 👤 Employee Management

![Employee
Screenshot](https://placehold.co/800x400?text=Employee+Management)

------------------------------------------------------------------------

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

