# DevBlog - Modern CMS & Blog Management System

DevBlog is a premium, lightweight blog management system built with PHP and MySQL. It features a modern, responsive design and a powerful admin panel for managing content efficiently.

## Features
- **Modern Admin Panel**: Manage posts, pages, categories, and media library with a sleek UI.
- **Dynamic Pagination**: Robust pagination system across all admin modules and the front-end homepage.
- **Rich Text Editor**: Integrated TinyMCE for advanced post and page creation.
- **Responsive Design**: Mobile-first architecture using Inter and Outfit font systems.
- **SEO Ready**: Dynamic meta tags and clean URL structure.

## Prerequisites
- **Web Server**: Apache or Nginx (XAMPP/WAMP recommended for local development).
- **PHP**: Version 7.4 or higher.
- **Database**: MySQL or MariaDB.

## Installation Steps

### 1. Clone or Download
Place the project files in your web server's root directory (e.g., `htdocs` for XAMPP).

### 2. Database Configuration
Open `includes/db.php` and update the database credentials if they differ from the defaults:
- **Host**: `localhost`
- **Database Name**: `blog_db`
- **Username**: `root`
- **Password**: ` ` (Empty)

### 3. Database Initialization (Option A: Automated Scripts)
To initialize the system feature-by-feature, run these scripts in your browser:

1.  **Core Setup**: `setup.php`
2.  **Comments**: `update_db.php`
3.  **Settings**: `update_db_settings.php`
4.  **Menus**: `update_db_menus.php`
5.  **Pages**: `update_db_pages.php`
6.  **Advanced Pages**: `update_db_pages_advanced.php`
7.  **Sample Data**: `seed.php`

### 3b. Database Initialization (Option B: Direct SQL Import)
Alternatively, you can import the complete database in one go:
1.  Open **phpMyAdmin**.
2.  Create a new database named `blog_db`.
3.  Select the database and click the **Import** tab.
4.  Choose the `blog_db.sql` file from the project root and click **Go/Import**.

*Note: Replace `demo` with your actual folder name if different.*

### 4. Login to Admin Panel
You can access the admin dashboard at:
- **URL**: `http://localhost/demo/admin/login.php`
- **Username**: `admin`
- **Password**: `password`

## Project Structure
- `/admin`: All administrative backend files (Post, Page, Category, and Comment management).
- `/assets`: CSS, JS, and image assets.
- `/includes`: Core PHP logic, database connection, and helper functions.
- `/uploads`: Directory for uploaded media files.
- `index.php`: Front-end blog listing page with dynamic pagination.
- `post.php` / `page.php`: Single post and page view templates.
- `update_db*.php`: Incremental database migration scripts.

## Technologies Used
- **Backend**: PHP (PDO)
- **Frontend**: Vanilla CSS, FontAwesome, Google Fonts
- **Features**: Custom Pagination, TinyMCE Editor, Mega Menu support
