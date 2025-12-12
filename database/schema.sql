-- RxHub Database Schema
-- Pharmaceutical Supply Chain Platform - Production Ready

-- Create database
CREATE DATABASE IF NOT EXISTS rxhub_db;
USE rxhub_db;

-- Settings table for system configuration
CREATE TABLE IF NOT EXISTS settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value TEXT,
    setting_type ENUM('string', 'number', 'boolean', 'json') DEFAULT 'string',
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default settings
INSERT INTO settings (setting_key, setting_value, setting_type, description) VALUES
('currency', 'NGN', 'string', 'Default currency for the platform'),
('currency_symbol', '₦', 'string', 'Currency symbol'),
('company_name', 'RxHub', 'string', 'Company name'),
('company_email', 'info@rxhub.com.ng', 'string', 'Company email'),
('company_phone', '+234 800 123 4567', 'string', 'Company phone'),
('sales_cycle_days', '30', 'number', 'Days in a sales cycle for profit calculation'),
('tax_rate', '7.5', 'number', 'Tax rate percentage');

-- Roles and Permissions
CREATE TABLE IF NOT EXISTS roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    description TEXT,
    permissions JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default roles
INSERT INTO roles (name, description, permissions) VALUES
('super_admin', 'Full system access', '{"all": true}'),
('admin', 'Administrative access', '{"users": true, "products": true, "orders": true, "reports": true, "settings": false}'),
('manager', 'Manager access', '{"products": true, "orders": true, "reports": true}'),
('staff', 'Basic staff access', '{"orders": true, "products": {"view": true}}');

-- Admin users table
CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(50),
    role_id INT DEFAULT 2,
    avatar VARCHAR(255),
    last_login TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    is_active BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (role_id) REFERENCES roles(id),
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default admin (password: admin123)
-- IMPORTANT: Change this password immediately after deployment!
-- Consider implementing forced password change on first login
INSERT INTO admins (full_name, email, password, role_id) VALUES
('System Admin', 'admin@rxhub.com.ng', '$2y$10$uFqnecC3Ccij2Ff5gYKVZus7Dy1okEvr2vz1RkzYVj8T/1l89TsZq', 1);

-- Users table (Healthcare providers - pharmacies, hospitals, clinics)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    facility_name VARCHAR(255),
    facility_type ENUM('pharmacy', 'hospital', 'clinic', 'other') DEFAULT 'pharmacy',
    phone VARCHAR(50),
    address TEXT,
    city VARCHAR(100),
    state VARCHAR(100),
    license_number VARCHAR(100),
    balance DECIMAL(15, 2) DEFAULT 0.00,
    credit_limit DECIMAL(15, 2) DEFAULT 0.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    is_active BOOLEAN DEFAULT TRUE,
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Investors table
CREATE TABLE IF NOT EXISTS investors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(50),
    company_name VARCHAR(255),
    investor_type ENUM('individual', 'institutional', 'corporate', 'venture_capital') DEFAULT 'individual',
    investment_range ENUM('under_10k', '10k_50k', '50k_100k', '100k_500k', 'above_500k') DEFAULT 'under_10k',
    accredited BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    is_active BOOLEAN DEFAULT TRUE,
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Investment options table
CREATE TABLE IF NOT EXISTS investment_options (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    category ENUM('equity', 'debt', 'convertible', 'profit_sharing') DEFAULT 'equity',
    min_investment DECIMAL(15, 2) NOT NULL,
    max_investment DECIMAL(15, 2),
    target_amount DECIMAL(15, 2) NOT NULL,
    current_amount DECIMAL(15, 2) DEFAULT 0.00,
    expected_roi VARCHAR(50),
    duration_months INT,
    risk_level ENUM('low', 'medium', 'high') DEFAULT 'medium',
    status ENUM('open', 'closed', 'fully_funded') DEFAULT 'open',
    start_date DATE,
    end_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    is_active BOOLEAN DEFAULT TRUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Investor investments table (junction table for investor-investment relationship)
CREATE TABLE IF NOT EXISTS investor_investments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    investor_id INT NOT NULL,
    investment_option_id INT NOT NULL,
    amount DECIMAL(15, 2) NOT NULL,
    investment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pending', 'confirmed', 'cancelled', 'completed') DEFAULT 'pending',
    notes TEXT,
    interest_accrued DECIMAL(15, 2) DEFAULT 0.00,
    last_interest_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (investor_id) REFERENCES investors(id) ON DELETE CASCADE,
    FOREIGN KEY (investment_option_id) REFERENCES investment_options(id) ON DELETE CASCADE,
    INDEX idx_investor (investor_id),
    INDEX idx_investment (investment_option_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Product Categories
CREATE TABLE IF NOT EXISTS product_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    parent_id INT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (parent_id) REFERENCES product_categories(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default categories
INSERT INTO product_categories (name, description) VALUES
('Antibiotics', 'Antimicrobial medications'),
('Analgesics', 'Pain relief medications'),
('Cardiovascular', 'Heart and blood pressure medications'),
('Diabetes', 'Diabetes management medications'),
('Respiratory', 'Respiratory system medications'),
('Vitamins & Supplements', 'Nutritional supplements'),
('First Aid', 'First aid supplies'),
('Medical Equipment', 'Medical devices and equipment');

-- Products table
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sku VARCHAR(50) UNIQUE,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    category_id INT,
    manufacturer VARCHAR(255),
    unit VARCHAR(50) DEFAULT 'pack',
    purchase_price DECIMAL(15, 2) NOT NULL,
    selling_price DECIMAL(15, 2) NOT NULL,
    min_stock_level INT DEFAULT 10,
    image VARCHAR(255),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES product_categories(id),
    INDEX idx_sku (sku),
    INDEX idx_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Stock/Inventory table
CREATE TABLE IF NOT EXISTS stock (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    batch_number VARCHAR(100),
    expiry_date DATE,
    purchase_price DECIMAL(15, 2),
    date_added DATE DEFAULT (CURRENT_DATE),
    added_by INT,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    INDEX idx_product (product_id),
    INDEX idx_batch (batch_number)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert sample products
INSERT INTO products (sku, name, description, category_id, manufacturer, purchase_price, selling_price) VALUES
('AMX-500', 'Amoxicillin 500mg', 'Antibiotic capsules, 20 per pack', 1, 'Emzor Pharmaceuticals', 1500.00, 2200.00),
('PCM-500', 'Paracetamol 500mg', 'Pain relief tablets, 96 per pack', 2, 'Emzor Pharmaceuticals', 800.00, 1200.00),
('MET-500', 'Metformin 500mg', 'Diabetes medication, 30 tablets', 4, 'Fidson Healthcare', 2500.00, 3500.00),
('AML-5', 'Amlodipine 5mg', 'Blood pressure medication, 30 tablets', 3, 'M&B Pharmaceuticals', 1800.00, 2800.00),
('VIT-C', 'Vitamin C 1000mg', 'Immune support, 60 tablets', 6, 'Neimeth Pharmaceuticals', 3500.00, 5000.00),
('SAL-100', 'Salbutamol Inhaler', 'Asthma relief inhaler', 5, 'GSK Pharmaceuticals', 4500.00, 6500.00),
('IBU-400', 'Ibuprofen 400mg', 'Anti-inflammatory, 50 tablets', 2, 'Fidson Healthcare', 1200.00, 1800.00),
('OME-20', 'Omeprazole 20mg', 'Acid reflux medication, 30 capsules', 2, 'Tuyil Pharmaceuticals', 2000.00, 3000.00);

-- Orders table
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_number VARCHAR(50) UNIQUE,
    user_id INT NOT NULL,
    subtotal DECIMAL(15, 2) NOT NULL,
    tax DECIMAL(15, 2) DEFAULT 0.00,
    discount DECIMAL(15, 2) DEFAULT 0.00,
    total DECIMAL(15, 2) NOT NULL,
    status ENUM('pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    payment_status ENUM('unpaid', 'partial', 'paid') DEFAULT 'unpaid',
    payment_method ENUM('transfer', 'pos', 'cash', 'credit') DEFAULT 'transfer',
    shipping_address TEXT,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    INDEX idx_order_number (order_number),
    INDEX idx_user (user_id),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Order Items table
CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(15, 2) NOT NULL,
    total_price DECIMAL(15, 2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id),
    INDEX idx_order (order_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Payments table
CREATE TABLE IF NOT EXISTS payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    payment_reference VARCHAR(100) UNIQUE,
    order_id INT,
    user_id INT NOT NULL,
    amount DECIMAL(15, 2) NOT NULL,
    payment_method ENUM('transfer', 'pos', 'cash', 'credit') NOT NULL,
    status ENUM('pending', 'completed', 'failed', 'refunded') DEFAULT 'pending',
    transaction_reference VARCHAR(255),
    notes TEXT,
    payment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (user_id) REFERENCES users(id),
    INDEX idx_reference (payment_reference)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Invoices table
CREATE TABLE IF NOT EXISTS invoices (
    id INT AUTO_INCREMENT PRIMARY KEY,
    invoice_number VARCHAR(50) UNIQUE,
    order_id INT,
    user_id INT NOT NULL,
    subtotal DECIMAL(15, 2) NOT NULL,
    tax DECIMAL(15, 2) DEFAULT 0.00,
    discount DECIMAL(15, 2) DEFAULT 0.00,
    total DECIMAL(15, 2) NOT NULL,
    status ENUM('draft', 'sent', 'paid', 'overdue', 'cancelled') DEFAULT 'draft',
    due_date DATE,
    notes TEXT,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (created_by) REFERENCES admins(id),
    INDEX idx_invoice_number (invoice_number)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sales table (for tracking profits)
CREATE TABLE IF NOT EXISTS sales (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    purchase_price DECIMAL(15, 2) NOT NULL,
    selling_price DECIMAL(15, 2) NOT NULL,
    profit DECIMAL(15, 2) NOT NULL,
    sale_date DATE DEFAULT (CURRENT_DATE),
    sales_cycle VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (product_id) REFERENCES products(id),
    INDEX idx_sale_date (sale_date),
    INDEX idx_sales_cycle (sales_cycle)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Activity Log
CREATE TABLE IF NOT EXISTS activity_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_type ENUM('admin', 'user', 'investor') NOT NULL,
    user_id INT NOT NULL,
    action VARCHAR(255) NOT NULL,
    description TEXT,
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_user (user_type, user_id),
    INDEX idx_action (action)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert sample investment options
INSERT INTO investment_options (title, description, category, min_investment, max_investment, target_amount, expected_roi, duration_months, risk_level, status, start_date, end_date) VALUES
('RxHub Series A Equity', 'Participate in RxHub\'s Series A funding round. Invest in Africa\'s leading pharmaceutical supply chain platform.', 'equity', 10000.00, 500000.00, 5000000.00, '25-35% annually', 36, 'medium', 'open', CURDATE(), DATE_ADD(CURDATE(), INTERVAL 6 MONTH)),
('Supply Chain Expansion Fund', 'Fund the expansion of our last-mile delivery network across West Africa. Fixed returns with quarterly distributions.', 'debt', 5000.00, 100000.00, 2000000.00, '15% annually', 24, 'low', 'open', CURDATE(), DATE_ADD(CURDATE(), INTERVAL 3 MONTH)),
('Technology Innovation Bond', 'Support the development of our anti-counterfeit tracking technology. Convertible to equity at Series B.', 'convertible', 25000.00, 250000.00, 3000000.00, '12% + conversion option', 18, 'medium', 'open', CURDATE(), DATE_ADD(CURDATE(), INTERVAL 4 MONTH)),
('Manufacturer Partnership Program', 'Profit-sharing investment to onboard 50 new pharmaceutical manufacturers to the platform.', 'profit_sharing', 15000.00, 200000.00, 1500000.00, '18-22% profit share', 12, 'medium', 'open', CURDATE(), DATE_ADD(CURDATE(), INTERVAL 2 MONTH)),
('Cold Chain Infrastructure', 'Investment in temperature-controlled logistics infrastructure for vaccine and biologic distribution.', 'equity', 50000.00, 1000000.00, 8000000.00, '30-40% annually', 48, 'high', 'open', CURDATE(), DATE_ADD(CURDATE(), INTERVAL 8 MONTH));
