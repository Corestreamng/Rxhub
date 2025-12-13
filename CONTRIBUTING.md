# Contributing to RxHub

Thank you for your interest in contributing to RxHub! This guide will help you get started.

## Table of Contents
- [Code of Conduct](#code-of-conduct)
- [Getting Started](#getting-started)
- [Development Setup](#development-setup)
- [Coding Standards](#coding-standards)
- [Making Changes](#making-changes)
- [Submitting Changes](#submitting-changes)
- [Testing](#testing)
- [Documentation](#documentation)

## Code of Conduct

We are committed to providing a welcoming and inclusive environment. Please be respectful and professional in all interactions.

## Getting Started

1. **Fork the Repository**
   ```bash
   # Click the "Fork" button on GitHub
   ```

2. **Clone Your Fork**
   ```bash
   git clone https://github.com/YOUR_USERNAME/Rxhub.git
   cd Rxhub
   ```

3. **Add Upstream Remote**
   ```bash
   git remote add upstream https://github.com/Corestreamng/Rxhub.git
   ```

4. **Create a Branch**
   ```bash
   git checkout -b feature/your-feature-name
   ```

## Development Setup

### Prerequisites
- PHP 8.0+
- MySQL 5.7+ or MariaDB 10.3+
- Apache/Nginx web server
- Composer (optional, for dependencies)
- Git

### Local Environment Setup

1. **Database Setup**
   ```bash
   mysql -u root -p
   CREATE DATABASE rxhub_dev CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   exit;
   
   mysql -u root -p rxhub_dev < database/schema.sql
   ```

2. **Configuration**
   ```bash
   # Set environment variables
   export APP_ENV=development
   export DB_HOST=localhost
   export DB_NAME=rxhub_dev
   export DB_USER=your_username
   export DB_PASS=your_password
   ```

3. **File Permissions**
   ```bash
   chmod 755 -R .
   chmod 777 logs/
   chmod 777 uploads/  # If exists
   ```

4. **Start Development Server**
   ```bash
   # Using PHP built-in server
   php -S localhost:8000
   
   # Or configure Apache/Nginx virtual host
   ```

## Coding Standards

### PHP Standards

#### PSR-12 Extended Coding Style
We follow PSR-12 with some custom rules:

```php
<?php
/**
 * File description
 * 
 * @package RxHub
 * @author Your Name
 */

namespace RxHub\Module;

class ExampleClass
{
    private $property;
    
    /**
     * Constructor
     * 
     * @param string $param Parameter description
     */
    public function __construct($param)
    {
        $this->property = $param;
    }
    
    /**
     * Method description
     * 
     * @return string
     */
    public function getProperty()
    {
        return $this->property;
    }
}
```

#### Naming Conventions
- **Classes**: PascalCase (`UserManager`, `OrderProcessor`)
- **Methods**: camelCase (`getUserById`, `processPayment`)
- **Variables**: snake_case (`$user_id`, `$order_total`)
- **Constants**: UPPER_SNAKE_CASE (`MAX_ATTEMPTS`, `DEFAULT_TIMEOUT`)
- **Database Tables**: snake_case (`users`, `order_items`)
- **Database Columns**: snake_case (`user_id`, `created_at`)

#### Security Best Practices

**Always validate and sanitize input:**
```php
// ✅ GOOD
$email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
$name = Security::sanitizeInput($_POST['name']);

// ❌ BAD
$email = $_POST['email'];
```

**Use prepared statements:**
```php
// ✅ GOOD
$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$email]);

// ❌ BAD
$query = "SELECT * FROM users WHERE email = '$email'";
```

**Escape output:**
```php
// ✅ GOOD
echo htmlspecialchars($user_input, ENT_QUOTES, 'UTF-8');
// or use helper
echo h($user_input);

// ❌ BAD
echo $user_input;
```

**Include CSRF protection:**
```php
// In forms
<form method="POST">
    <?php echo Security::csrfField(); ?>
    <!-- form fields -->
</form>

// In handlers
if (!Security::verifyCSRFToken($_POST['csrf_token'])) {
    die('CSRF validation failed');
}
```

### JavaScript Standards

```javascript
// Use modern ES6+ syntax
const fetchData = async (url) => {
    try {
        const response = await fetch(url);
        const data = await response.json();
        return data;
    } catch (error) {
        console.error('Error fetching data:', error);
        throw error;
    }
};

// Use descriptive variable names
const userProfile = getUserProfile();
const orderTotal = calculateOrderTotal(items);

// Add comments for complex logic
// Calculate discount based on user tier and order value
const discount = calculateDiscount(userTier, orderTotal);
```

### CSS Standards

```css
/* Use BEM naming convention */
.product-card {
    /* Block */
}

.product-card__title {
    /* Element */
}

.product-card--featured {
    /* Modifier */
}

/* Use CSS variables for theming */
:root {
    --primary-color: #9900cc;
    --text-color: #1e293b;
}

/* Mobile-first approach */
.container {
    width: 100%;
}

@media (min-width: 768px) {
    .container {
        max-width: 720px;
    }
}
```

### SQL Standards

```sql
-- Use meaningful table and column names
CREATE TABLE user_profiles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    full_name VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Add indexes for frequently queried columns
CREATE INDEX idx_user_id ON user_profiles(user_id);
CREATE INDEX idx_created_at ON user_profiles(created_at);

-- Use transactions for multiple related operations
START TRANSACTION;
    INSERT INTO orders (...) VALUES (...);
    INSERT INTO order_items (...) VALUES (...);
    UPDATE products SET stock = stock - 1 WHERE id = ?;
COMMIT;
```

## Making Changes

### Branch Naming
- **Features**: `feature/description` (e.g., `feature/add-payment-gateway`)
- **Bug Fixes**: `fix/description` (e.g., `fix/login-validation`)
- **Documentation**: `docs/description` (e.g., `docs/update-api-guide`)
- **Refactoring**: `refactor/description` (e.g., `refactor/database-queries`)
- **Security**: `security/description` (e.g., `security/fix-xss-vulnerability`)

### Commit Messages

Follow the Conventional Commits specification:

```
type(scope): subject

body

footer
```

**Types:**
- `feat`: New feature
- `fix`: Bug fix
- `docs`: Documentation changes
- `style`: Code style changes (formatting, no code changes)
- `refactor`: Code refactoring
- `test`: Adding or updating tests
- `chore`: Maintenance tasks
- `security`: Security improvements

**Examples:**
```
feat(user): add password reset functionality

Implemented password reset via email with secure token generation.
Users can now request password reset from login page.

Closes #123
```

```
fix(orders): prevent duplicate order submissions

Added order deduplication check to prevent double-charging.
Includes rate limiting on order submission endpoint.

Fixes #456
```

## Submitting Changes

### Before Submitting

1. **Update your branch**
   ```bash
   git fetch upstream
   git rebase upstream/main
   ```

2. **Run tests**
   ```bash
   # Add when test suite is available
   # php vendor/bin/phpunit
   ```

3. **Check syntax**
   ```bash
   php -l file.php
   ```

4. **Review your changes**
   ```bash
   git diff
   ```

### Creating Pull Request

1. **Push to your fork**
   ```bash
   git push origin feature/your-feature-name
   ```

2. **Create Pull Request on GitHub**
   - Go to the original repository
   - Click "New Pull Request"
   - Select your branch
   - Fill in the PR template

3. **PR Description Template**
   ```markdown
   ## Description
   Brief description of changes
   
   ## Type of Change
   - [ ] Bug fix
   - [ ] New feature
   - [ ] Breaking change
   - [ ] Documentation update
   
   ## How Has This Been Tested?
   Description of testing performed
   
   ## Checklist
   - [ ] Code follows project style guidelines
   - [ ] Self-review completed
   - [ ] Comments added for complex code
   - [ ] Documentation updated
   - [ ] No new warnings generated
   - [ ] Tests added/updated
   - [ ] Security considerations addressed
   
   ## Related Issues
   Closes #issue_number
   ```

## Testing

### Manual Testing Checklist

#### User Dashboard
- [ ] Login works correctly
- [ ] Product browsing works
- [ ] Cart functionality works
- [ ] Order placement works
- [ ] Payment processing works
- [ ] Invoice viewing works
- [ ] Reports generate correctly
- [ ] Profile update works
- [ ] Settings save correctly

#### Investor Dashboard
- [ ] Login works correctly
- [ ] Investment opportunities display
- [ ] Investment process works
- [ ] Portfolio displays correctly
- [ ] Statements generate

#### Admin Dashboard
- [ ] Login works correctly
- [ ] User management works
- [ ] Product management works
- [ ] Order management works
- [ ] Investment management works
- [ ] Reports generate correctly

### Security Testing
- [ ] SQL injection attempts blocked
- [ ] XSS attempts sanitized
- [ ] CSRF tokens validated
- [ ] Session hijacking prevented
- [ ] Rate limiting works
- [ ] Password requirements enforced
- [ ] Account lockout works

## Documentation

### Code Comments
```php
/**
 * Process a payment for an order
 * 
 * This method validates the payment details, processes the payment
 * through the payment gateway, and updates the order status.
 * 
 * @param int $order_id The order ID to process payment for
 * @param array $payment_data Payment details (method, amount, reference)
 * @return array Result with success status and payment reference
 * @throws Exception If payment processing fails
 * 
 * @example
 * $result = processPayment(123, [
 *     'method' => 'transfer',
 *     'amount' => 50000.00,
 *     'reference' => 'TXN-123456'
 * ]);
 */
function processPayment($order_id, $payment_data)
{
    // Implementation
}
```

### API Documentation
When adding new API endpoints, update `API.md`:

```markdown
### Endpoint Name
**METHOD** `/api/endpoint.php`

**Request Headers:**
\`\`\`
Authorization: Bearer <token>
Content-Type: application/json
\`\`\`

**Request Body:**
\`\`\`json
{
    "param": "value"
}
\`\`\`

**Response (200 OK):**
\`\`\`json
{
    "success": true,
    "data": {}
}
\`\`\`
```

## Questions?

If you have questions, please:
1. Check existing documentation
2. Search existing issues
3. Create a new issue with the "question" label
4. Email: dev@rxhub.com.ng

## License

By contributing, you agree that your contributions will be licensed under the MIT License.

---

**Thank you for contributing to RxHub!** 🎉
