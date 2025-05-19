# CMS Website Project

A simple Content Management System (CMS) for managing website content with a MySQL database backend.

## Features

- Admin panel for content management
- Article management (create, edit, delete)
- Company information customization
- Social media links management
- Responsive frontend design
- Image upload support
- MySQL database integration
- Environment variable configuration

## Installation

1. Clone this repository to your web server
2. Create a MySQL database
3. Import the `database.sql` file to create the necessary tables
4. Copy `.env.example` to `.env` and update the database configuration
5. Make sure the `uploads` directory is writable (chmod 755)
6. Access the admin panel at `/admin`

## Login Information

- Username: admin
- Password: admin123

## Directory Structure

- `/` - Main website files
- `/admin` - Admin panel
- `/uploads` - Uploaded images
- `/data` - Data storage

## Requirements

- PHP 7.4+ with PDO MySQL extension
- MySQL 5.7+ or MariaDB 10.2+
- Web server (Apache/Nginx)

## Usage

1. Login to the admin panel
2. Add or edit articles
3. Update company information
4. Add/edit social media links
5. View the changes on the frontend

## Security Notes

For production use, consider enhancing security:

- Change the default admin credentials
- Implement CSRF protection
- Add input sanitization
- Consider using prepared statements throughout
- Implement appropriate file upload validation

## Credits

Created for educational purposes.