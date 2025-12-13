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
│   ├── signup.php            # User registration
│   └── sections/             # Dashboard sections
│       ├── products.php      # Product listing
│       ├── orders.php        # Order management
│       ├── invoices.php      # Invoice viewing
│       ├── payments.php      # Payment history
│       ├── reports.php       # Sales reports
│       ├── analytics.php     # Analytics dashboard
│       ├── profile.php       # User profile
│       └── settings.php      # User settings
│
├── includes/                 # Core PHP files
│   ├── config.php            # Application configuration
│   ├── database.php          # Database connection class
│   ├── session.php           # Session management
│   ├── security.php          # Security functions and classes
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
├── logs/                     # Application logs
│   └── security.log          # Security event logs
│
├── index.php                 # Landing page
├── logout.php                # Logout handler
├── API.md                    # API documentation
├── SECURITY.md               # Security documentation
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

- **Password Security**: Argon2ID hashing with high memory and time costs
- **SQL Injection Prevention**: Prepared statements for all database queries
- **XSS Protection**: Output encoding and Content Security Policy headers
- **CSRF Protection**: Token-based protection for all forms
- **Session Security**: Secure session handling with HTTPOnly and Secure flags
- **Rate Limiting**: 100 requests per minute per IP address
- **Account Lockout**: Automatic lockout after 5 failed login attempts
- **JWT Authentication**: Secure token-based auth for mobile apps
- **Security Headers**: X-Frame-Options, HSTS, CSP, and more
- **Input Validation**: Comprehensive validation and sanitization
- **Security Logging**: All security events logged for auditing

For detailed security information, see [SECURITY.md](SECURITY.md)

## 🛠️ API Endpoints

The RxHub API provides comprehensive REST endpoints for web and mobile applications.

**Full API documentation:** [API.md](API.md)

### Quick Reference
- **Authentication**: Login, register, JWT tokens, refresh tokens
- **Products**: Browse products, search, filter by category
- **Orders**: Create orders, get order details, order history
- **Payments**: Process payments, payment history
- **Invoices**: View invoices, download PDFs
- **Analytics**: User analytics, spending trends
- **Investments**: Browse opportunities, make investments

### Authentication
All API requests (except login/register) require authentication via:
- **Web**: Session-based authentication
- **Mobile**: JWT Bearer token in Authorization header

### Example Request
```bash
curl -X GET https://rxhub.com.ng/api/products.php \
  -H "Authorization: Bearer YOUR_JWT_TOKEN" \
  -H "Content-Type: application/json"
```

### Rate Limiting
- 100 requests per minute per IP
- 1000 requests per hour per authenticated user

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
