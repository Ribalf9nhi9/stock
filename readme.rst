# Inventory Management System (IMS)

## Project Description
The **Inventory Management System (IMS)** is a comprehensive solution designed to help businesses efficiently track and manage their stock, orders, products, and related operational data. Built using the **CodeIgniter** PHP framework, the system follows the **Model-View-Controller (MVC)** architectural pattern. The system provides a user-friendly interface for managing various aspects of inventory, including product creation, stock updates, order processing, and user administration.

## Features

### Dashboard
- Provides an at-a-glance overview of key inventory metrics, such as total products, orders, and items needing reorder.

### User Authentication
- Secure login system to protect application data and functionalities.

### Product Management
- **CRUD** (Create, Read, Update, Delete) operations for products.
- Manage product details including name, SKU, description, category, brand, supplier, cost, selling price, and images.
- Support for product attributes (e.g., size, color).
- Track stock quantities and set reorder points.

### Order Management
- Create and manage customer orders.
- Track order status (e.g., pending, paid, shipped, completed).
- Automatically update stock levels upon order fulfillment.
- Calculate order totals, including taxes and service charges.

### Category and Brand Management
- Organize products by categories and brands.

### Store Management
- Manage details for multiple stores or outlets (if applicable).

### User and Group Management
- Administer user accounts.
- Manage user groups and assign permissions for role-based access control.

### Company Settings
- Configure system-wide settings like company details, currency, and tax rates.

### Reporting
- Generate reports on sales, inventory levels, and other key performance indicators.

### Alerts
- System alerts for low stock levels and potentially other critical events.

### Internal Stock Requests
- Manage internal requests for stock between departments or locations.

## Technologies Used

### Backend:
- **PHP** (Hypertext Preprocessor)
- **CodeIgniter 3.x** (PHP Framework)
- **MySQL** (Relational Database)
- **Database Driver**: mysqli

### Frontend:
- **HTML5**
- **CSS3** (including **Bootstrap** framework for styling)
- **JavaScript** (including **jQuery** library for interactivity)

### Web Server:
- Apache or Nginx (typical PHP hosting environment)

## Project Structure

The main application code resides in the `/application` directory, following CodeIgniter conventions:

- `/application/config/`: Configuration files (database, routes, autoload, etc.).
- `/application/controllers/`: Handles incoming requests and orchestrates responses.
- `/application/models/`: Manages data interaction with the database and business logic.
- `/application/views/`: Contains the presentation layer (HTML templates).
- `/application/core/`: Custom base controllers (e.g., `MY_Controller.php`).
- `/application/helpers/`: Custom helper functions.
- `/application/libraries/`: Custom or third-party libraries.
- `/assets/`: Frontend assets like CSS, JavaScript, images, and fonts.
- `/DATABASE_FILE/`: Contains the SQL dump for the database schema and initial data (e.g., `stock_v2.sql`).

## Setup Instructions

### Prerequisites:
- A web server (e.g., Apache, Nginx) with **PHP** support.
- **MySQL** database server.
- **PHP version** compatible with CodeIgniter 3.x (typically PHP 5.6+ to 7.x).
- PHP extensions: **mysqli**.

### 1. Clone or Download the Project:
- Place the project files in your web server's document root (e.g., `htdocs` for XAMPP/Apache, `www` for Nginx).

### 2. Database Setup:
- Create a new **MySQL** database (e.g., `stock`).
- Import the `stock_v2.sql` file (found in the `DATABASE_FILE` directory) into your newly created database. This will create the necessary tables and populate some initial data.
  ```bash
  mysql -u your_username -p your_database_name < /path/to/DATABASE_FILE/stock_v2.sql




Setup Instructions
Prerequisites:
A web server (e.g., Apache, Nginx) with PHP support.
MySQL database server.
PHP version compatible with CodeIgniter 3.x (typically PHP 5.6+ to 7.x).
PHP extensions: mysqli.
Clone or Download the Project:
Place the project files in your web server's document root (e.g., htdocs for XAMPP/Apache, www for Nginx).
Database Setup:
Create a new MySQL database (e.g., stock).
Import the stock_v2.sql file (found in the DATABASE_FILE directory) into your newly created database. This will create the necessary tables and populate some initial data.
bash
mysql -u your_username -p your_database_name < /path/to/DATABASE_FILE/stock_v2.sql
Configure the Application:
Navigate to /application/config/.
Base URL: Open config.php and set your project's base URL:
php
$config["base_url"] = "http://localhost/your_project_directory/"; // Adjust as per your setup
Database Configuration: Open database.php and update the database connection details:
php
$db["default"] = array(
    // ... other settings
    "hostname" => "localhost",
    "username" => "your_mysql_username",
    "password" => "your_mysql_password",
    "database" => "stock", // Or your chosen database name
    "dbdriver" => "mysqli",
    // ... other settings
) ;
File Permissions (if on Linux/macOS):
Ensure that the /application/cache/ and /application/logs/ directories are writable by the web server.
bash
chmod -R 777 application/cache
chmod -R 777 application/logs
(Note: 777 is permissive; adjust according to your server's security best practices).
Access the Application:
Open your web browser and navigate to the base URL you configured (e.g., http://localhost/your_project_directory/) .
You should be redirected to the login page (auth/login).
Default Login Credentials (from database dump)
Username: admin (or check the users table in the database for admin credentials)
Password: password (or as per the hashed password in the users table for the admin user - the provided SQL likely has a bcrypt hash for 'password')
Basic Usage
Login: Access the application using your credentials.
Dashboard: View an overview of your inventory status.
Manage Products: Navigate to the products section to add new products, update existing ones, or manage stock levels.
Manage Orders: Process new orders, track existing orders, and update their statuses.
Manage Users/Groups: (If admin) Manage user accounts and their permissions.
Settings: Configure company and application settings.
Reports: Generate reports to analyze inventory and sales data.
Contributing
(If this were an open-source project, this section would detail how others can contribute. For a personal or academic project, this might be omitted or adapted.)
License
(Specify the license under which the project is released, e.g., MIT, GPL, or state if it's proprietary.)