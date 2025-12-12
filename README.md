# RxHub - Pharmaceutical Supply Chain Platform

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
[![PHP Version](https://img.shields.io/badge/PHP-8.0%2B-blue.svg)](https://php.net)
[![MySQL Version](https://img.shields.io/badge/MySQL-5.7%2B-orange.svg)](https://mysql.com)

RxHub is Africa's leading B2B pharmaceutical supply chain platform connecting pharmacies and hospitals directly to verified manufacturers with anti-counterfeit tracking and reliable last-mile delivery.

## 🌟 Features

### For Healthcare Providers (Pharmacies, Hospitals, Clinics)
- **Product Catalog**: Browse available pharmaceutical products with pricing
- **Order Management**: Place, track, and manage orders
- **Payment Processing**: Multiple payment methods (Bank Transfer, POS, Cash)
- **Invoice Management**: View and download invoices
- **Sales Reports**: Track spending and order history

### For Investors
- **Investment Dashboard**: View portfolio and returns
- **Investment Opportunities**: Browse and invest in available options
- **ROI Tracking**: Monitor investment performance
- **Statement Generation**: View transaction history

### For Administrators
- **User Management**: Create and manage healthcare users, investors, and admins
- **Product Management**: Add products with purchase and selling prices
- **Stock Management**: Track inventory with batch numbers and expiry dates
- **Order Processing**: Manage and fulfill customer orders
- **Investment Management**: Create investment options, set ROI
- **Bulk Operations**: Upload/download CSV for users and products
- **Reports & Analytics**: Sales reports, profit analysis
- **System Settings**: Configure currency, tax rates, etc.

## 📁 Project Structure

```
rxhub/
├── admin/                    # Admin dashboard and management
│   ├── dashboard.php         # Main admin dashboard
│   ├── login.php             # Admin login page
│   └── sections/             # Dashboard sections
│       ├── overview.php      # Dashboard overview
│       ├── settings.php      # System settings
│       ├── users.php         # User management
│       ├── investors.php     # Investor management
│       ├── products.php      # Product management
│       ├── orders.php        # Order management
│       ├── investment_options.php  # Investment options
│       └── stock.php         # Stock management
│
├── investor/                 # Investor portal
│   ├── dashboard.php         # Investor dashboard
│   ├── login.php             # Investor login
│   └── signup.php            # Investor registration
│
├── user/                     # Healthcare provider portal
│   ├── dashboard.php         # User dashboard
│   ├── login.php             # User login
│   └── signup.php            # User registration
│
├── includes/                 # Core PHP files
│   ├── config.php            # Application configuration
│   ├── database.php          # Database connection class
│   ├── session.php           # Session management
│   ├── auth.php              # Authentication functions
│   ├── helpers.php           # Utility functions
│   └── init.php              # Bootstrap file
│
├── database/                 # Database files
│   └── schema.sql            # Database schema
│
├── api/                      # API endpoints
│   └── admin/                # Admin API endpoints
│
├── assets/                   # Static assets
│   ├── css/                  # Stylesheets
│   ├── js/                   # JavaScript files
│   └── images/               # Images
│
├── legal/                    # Legal pages
│   ├── terms.html            # Terms of Service
│   └── privacy.html          # Privacy Policy
│
├── templates/                # Email and document templates
│
├── index.php                 # Landing page
├── logout.php                # Logout handler
└── README.md                 # This file
```

## 🚀 Installation

### Requirements
- PHP 8.0 or higher
- MySQL 5.7 or higher
- Web server (Apache/Nginx)
- PDO PHP Extension

### Step 1: Clone the Repository
```bash
git clone https://github.com/Corestreamng/Rxhub.git
cd Rxhub
```

### Step 2: Database Setup
1. Create a MySQL database:
```sql
CREATE DATABASE rxhub_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

2. Import the schema:
```bash
mysql -u your_username -p rxhub_db < database/schema.sql
```

### Step 3: Configuration
Set environment variables or update `includes/config.php`:

```bash
# Database Configuration
export DB_HOST=localhost
export DB_NAME=rxhub_db
export DB_USER=your_username
export DB_PASS=your_password

# Application URL (optional)
export APP_URL=https://yourdomain.com
```

### Step 4: Web Server Configuration

#### Apache (.htaccess)
```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA,L]
```

#### Nginx
```nginx
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
```

### Step 5: File Permissions
```bash
chmod 755 -R /path/to/rxhub
chmod 777 -R /path/to/rxhub/uploads  # If using file uploads
```

## 🔐 Default Login Credentials

### Admin Login
- **URL**: `https://yourdomain.com/admin/login.php`
- **Email**: `admin@rxhub.com.ng`
- **Password**: `admin123`

⚠️ **IMPORTANT**: Change the admin password immediately after first login!

### Creating Test Users
Use the admin panel to create test healthcare users and investors, or register through the signup forms.

## 💻 Usage

### Healthcare Provider Dashboard
1. Navigate to `https://yourdomain.com/user/login.php`
2. Login or create an account
3. Browse products, add to cart, and place orders
4. Track orders and make payments

### Investor Dashboard
1. Navigate to `https://yourdomain.com/investor/login.php`
2. Login or create an investor account
3. Browse investment opportunities
4. Make investments and track returns

### Admin Dashboard
1. Navigate to `https://yourdomain.com/admin/login.php`
2. Login with admin credentials
3. Manage users, products, orders, and investments
4. Configure system settings

## 📊 Database Schema

### Key Tables
- `users` - Healthcare providers (pharmacies, hospitals, clinics)
- `investors` - Investment platform users
- `admins` - Administrative users
- `products` - Pharmaceutical products
- `product_categories` - Product categories
- `stock` - Inventory tracking
- `orders` - Customer orders
- `order_items` - Order line items
- `payments` - Payment records
- `invoices` - Invoice records
- `investment_options` - Available investment options
- `investor_investments` - Investor portfolio
- `settings` - System configuration
- `roles` - User roles and permissions
- `activity_log` - Audit trail

## 🔒 Security Features

- Password hashing using `password_hash()` with bcrypt
- Prepared statements for all database queries (SQL injection prevention)
- XSS protection with `htmlspecialchars()` output encoding
- CSRF protection for forms
- Secure session management
- Input validation and sanitization

## 🛠️ API Endpoints

### Authentication
- `POST /api/auth/login.php` - User login
- `POST /api/auth/register.php` - User registration
- `GET /logout.php` - Logout

### Orders
- `POST /api/process_order.php` - Create order
- `POST /api/process_payment.php` - Process payment

### Admin APIs
- `POST /api/admin/users.php` - User management
- `POST /api/admin/products.php` - Product management
- `POST /api/admin/investments.php` - Investment management
- `POST /api/admin/settings.php` - Settings management

## 🎨 Customization

### Branding
Update the following files to customize branding:
- `index.php` - Landing page content
- `assets/css/` - Stylesheets
- `assets/images/` - Logo and images

### Currency
Change currency in admin settings or update `includes/config.php`:
```php
define('DEFAULT_CURRENCY', 'NGN');
define('DEFAULT_CURRENCY_SYMBOL', '₦');
```

## 📱 Mobile Responsiveness

The platform is fully responsive and works on:
- Desktop computers
- Tablets
- Mobile phones

## 🤝 Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 📞 Support

For support, email info@rxhub.com.ng or visit our website at [rxhub.com.ng](https://rxhub.com.ng)

## 🙏 Acknowledgments

- Font Awesome for icons
- Google Fonts for typography
- All contributors and testers

---

**Built with ❤️ by Corestream Nigeria**
