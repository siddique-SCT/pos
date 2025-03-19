# POS System

A modern Point of Sale (POS) system built with PHP 8.2, featuring a clean and efficient architecture.

## Requirements

- PHP 8.2 or higher
- MySQL 5.7 or higher
- Composer
- Web server (Apache/Nginx)
- PDO PHP Extension
- GD PHP Extension
- mbstring PHP Extension

## Installation

1. Clone the repository:
```bash
git clone [repository-url]
cd pos
```

2. Install dependencies:
```bash
composer install
```

3. Create a MySQL database and import the schema:
```bash
mysql -u your_username -p your_database < sql/pos.sql
```

4. Configure your database connection:
   - Open `db_connect.php`
   - Update the database credentials:
     ```php
     const DB_HOST = 'localhost';
     const DB_NAME = 'your_database';
     const DB_USER = 'your_username';
     const DB_PASS = 'your_password';
     ```

5. Set up your web server to point to the project directory

## Features

- Modern PHP 8.2 implementation with strict typing
- Secure database operations using PDO with prepared statements
- User authentication and authorization
- Product management
- Order processing
- Category management
- Sales reporting
- PDF invoice generation
- Responsive design
- Keyboard shortcuts for faster operation

## PHP 8.2 Features Implemented

- Strict type declarations
- Return type declarations
- Parameter type hints
- Named arguments
- Null coalescing operators
- Modern PDO configuration
- Improved error handling
- Readonly properties where applicable

## Directory Structure

```
pos/
├── asset/           # Static assets (CSS, JS, images)
├── sql/            # Database schema and migrations
├── uploads/        # User uploaded files
├── vendor/         # Composer dependencies
├── add_order.php   # Order creation
├── add_product.php # Product management
├── auth_function.php # Authentication functions
├── config.php      # Application configuration
├── db_connect.php  # Database connection
└── ...            # Other PHP files
```

## Security Features

- PDO prepared statements for all database queries
- Input validation and sanitization
- Session-based authentication
- Role-based access control
- Secure password handling
- XSS protection

## Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## License

This project is licensed under the MIT License - see the LICENSE file for details.

## Support

For support, please open an issue in the repository or contact the maintainers.

## Acknowledgments

- Built with PHP 8.2
- Uses Composer for dependency management
- PDF generation powered by dompdf 
