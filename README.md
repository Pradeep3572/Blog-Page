# PHP Role-Based Blog System

A lightweight, secure Content Management System (CMS) built with PHP and MySQL. This system features a robust admin approval workflow, role-based access control, and a clean, responsive UI built with Bootstrap 5.

## 🚀 Features

* **Role-Based Access Control (RBAC):**
  * **Admin:** Full control over users and content. Can approve/reject new account registrations, review blog submissions, provide rejection feedback, and manage live/archived posts.
  * **Employee (Writer):** Can draft new blogs with HTML content, upload media, and edit rejected blogs based on admin feedback.
  * **User (Reader):** Basic account access for viewing live content.
* **Content Approval Workflow:** Blogs don't go live immediately. They are placed in an `under_review` queue for admins to approve or reject with specific feedback.
* **Security:** 
  * Strict server-side password enforcement (minimum 8 chars, uppercase, number, special char).
  * Secure password hashing using PHP's native `password_hash()`.
  * Server-side XSS protection (blocks `<script>` tag injections).
  * Protected routes and session hijacking prevention.

## 🛠️ Tech Stack

* **Backend:** PHP 8+
* **Database:** MySQL (PDO for secure database interactions)
* **Frontend:** HTML5, CSS3, JavaScript, Bootstrap 5

## ⚙️ Local Setup & Installation

Follow these steps to run this project on your local machine.

### 1. Prerequisites
You will need a local web server environment like **XAMPP**, **WAMP**, **MAMP**, or a standard LAMP stack installed.

### 2. Database Setup
1. Open your local MySQL manager (e.g., phpMyAdmin at `http://localhost/phpmyadmin`).
2. Create a new, empty database (e.g., `blog_system`).
3. Import the provided **`schema.sql`** file into this database. This will generate the required `users` and `blogs` tables.

### 3. Configure Database Credentials
For security, the actual database credentials are not tracked in this repository.
1. Locate the **`db.example.php`** file in the root directory.
2. Duplicate or rename this file to **`db.php`**.
3. Open `db.php` and update the `$user`, `$pass`, and `$db` variables to match your local MySQL configuration (for default XAMPP, user is usually `root` and password is left blank).

### 4. Media Uploads Folder
User-uploaded images and videos are stored in the `uploads/` directory. 
* Ensure your local server has **write permissions** for the `uploads/` folder so PHP can save files there.

### 5. Run the Application
1. Place the project folder inside your server's web root (e.g., the `htdocs` folder for XAMPP).
2. Open your web browser and navigate to: `http://localhost/blog_system/` (adjust the folder name if you renamed it).
3. Register a new account to begin testing!

## 🛡️ License
This project is open-source and available under the [MIT License](LICENSE).