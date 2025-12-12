<?php
/**
 * RxHub Investor Signup Page
 */

require_once dirname(__DIR__) . '/includes/init.php';

// Redirect if already logged in
if (Session::isLoggedIn('investor')) {
    header('Location: dashboard.php');
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'full_name' => trim($_POST['full_name'] ?? ''),
        'email' => trim($_POST['email'] ?? ''),
        'phone' => trim($_POST['phone'] ?? ''),
        'company_name' => trim($_POST['company_name'] ?? ''),
        'investor_type' => $_POST['investor_type'] ?? 'individual',
        'investment_range' => $_POST['investment_range'] ?? 'under_10k',
        'password' => $_POST['password'] ?? ''
    ];
    
    $confirm_password = $_POST['confirm_password'] ?? '';
    $agree_terms = isset($_POST['agree_terms']);
    
    if (!$agree_terms) {
        $error = 'You must agree to the Terms of Service and Privacy Policy';
    } elseif ($data['password'] !== $confirm_password) {
        $error = 'Passwords do not match';
    } elseif (strlen($data['password']) < 8) {
        $error = 'Password must be at least 8 characters';
    } else {
        $auth = new Auth();
        $result = $auth->registerInvestor($data);
        
        if ($result['success']) {
            $success = 'Account created successfully! You can now login.';
        } else {
            $error = $result['message'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Create an investor account with RxHub - Africa's leading pharmaceutical supply chain platform.">
    <title>Investor Sign Up - RxHub</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #9900cc;
            --primary-dark: #7a00a3;
            --secondary: #ff3300;
            --accent: #006666;
            --energy: #32cd32;
            --dark: #1e293b;
            --light: #f8fafc;
            --gray: #64748b;
            --light-gray: #e2e8f0;
            --success: #22c55e;
            --danger: #ef4444;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .signup-container {
            width: 100%;
            max-width: 550px;
        }
        
        .signup-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
        }
        
        .signup-header {
            background: linear-gradient(to right, var(--primary), var(--primary-dark));
            padding: 35px 30px;
            text-align: center;
            color: white;
        }
        
        .logo {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 15px;
        }
        
        .logo i {
            color: var(--secondary);
        }
        
        .signup-header h2 {
            font-size: 1.5rem;
            font-weight: 500;
            opacity: 0.9;
        }
        
        .investor-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255, 255, 255, 0.15);
            padding: 8px 16px;
            border-radius: 20px;
            margin-top: 15px;
            font-size: 0.9rem;
        }
        
        .signup-form {
            padding: 35px 30px;
        }
        
        .alert {
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: none;
            font-size: 0.9rem;
        }
        
        .alert.success {
            background: rgba(34, 197, 94, 0.1);
            color: var(--success);
            border: 1px solid var(--success);
        }
        
        .alert.error {
            background: rgba(239, 68, 68, 0.1);
            color: var(--danger);
            border: 1px solid var(--danger);
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group.full-width {
            grid-column: 1 / -1;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--dark);
            font-size: 0.9rem;
        }
        
        .form-group label .required {
            color: var(--danger);
        }
        
        .input-group {
            position: relative;
        }
        
        .input-group i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gray);
        }
        
        .input-group input,
        .input-group select {
            width: 100%;
            padding: 12px 15px 12px 42px;
            border: 2px solid var(--light-gray);
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.3s;
        }
        
        .input-group input:focus,
        .input-group select:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(153, 0, 204, 0.1);
        }
        
        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--gray);
            cursor: pointer;
        }
        
        .password-strength {
            margin-top: 8px;
            height: 4px;
            border-radius: 2px;
            background: var(--light-gray);
            overflow: hidden;
        }
        
        .password-strength .bar {
            height: 100%;
            width: 0;
            transition: all 0.3s;
        }
        
        .password-strength .bar.weak {
            width: 33%;
            background: var(--danger);
        }
        
        .password-strength .bar.medium {
            width: 66%;
            background: var(--warning, #f59e0b);
        }
        
        .password-strength .bar.strong {
            width: 100%;
            background: var(--success);
        }
        
        .terms-checkbox {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            margin-bottom: 25px;
        }
        
        .terms-checkbox input {
            width: 18px;
            height: 18px;
            margin-top: 2px;
            accent-color: var(--primary);
        }
        
        .terms-checkbox label {
            font-size: 0.9rem;
            color: var(--gray);
            line-height: 1.4;
        }
        
        .terms-checkbox a {
            color: var(--primary);
            text-decoration: none;
        }
        
        .terms-checkbox a:hover {
            text-decoration: underline;
        }
        
        .signup-btn {
            width: 100%;
            padding: 14px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        
        .signup-btn:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(153, 0, 204, 0.3);
        }
        
        .signup-btn:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }
        
        .signup-footer {
            text-align: center;
            padding: 25px 30px;
            background: var(--light);
            border-top: 1px solid var(--light-gray);
        }
        
        .signup-footer p {
            color: var(--gray);
        }
        
        .signup-footer a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
        }
        
        .signup-footer a:hover {
            text-decoration: underline;
        }
        
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-top: 20px;
            color: white;
            text-decoration: none;
            opacity: 0.8;
            transition: opacity 0.3s;
        }
        
        .back-link:hover {
            opacity: 1;
        }
        
        @media (max-width: 600px) {
            .form-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="signup-container">
        <div class="signup-card">
            <div class="signup-header">
                <div class="logo">
                    <i class="fas fa-clinic-medical"></i>
                    <span>RxHub</span>
                </div>
                <h2>Create Investor Account</h2>
                <div class="investor-badge">
                    <i class="fas fa-chart-line"></i>
                    <span>Join Our Investment Community</span>
                </div>
            </div>
            
            <form class="signup-form" id="signupForm" method="POST" action="">
                <?php if ($error): ?>
                <div class="alert error" style="display: block;"><?php echo h($error); ?></div>
                <?php endif; ?>
                <?php if ($success): ?>
                <div class="alert success" style="display: block;"><?php echo h($success); ?> <a href="login.php">Login here</a></div>
                <?php endif; ?>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="fullName">Full Name <span class="required">*</span></label>
                        <div class="input-group">
                            <i class="fas fa-user"></i>
                            <input type="text" id="fullName" name="full_name" placeholder="John Doe" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email Address <span class="required">*</span></label>
                        <div class="input-group">
                            <i class="fas fa-envelope"></i>
                            <input type="email" id="email" name="email" placeholder="investor@example.com" required>
                        </div>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <div class="input-group">
                            <i class="fas fa-phone"></i>
                            <input type="tel" id="phone" name="phone" placeholder="+234 800 000 0000">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="companyName">Company Name</label>
                        <div class="input-group">
                            <i class="fas fa-building"></i>
                            <input type="text" id="companyName" name="company_name" placeholder="Your company (optional)">
                        </div>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="investorType">Investor Type</label>
                        <div class="input-group">
                            <i class="fas fa-user-tie"></i>
                            <select id="investorType" name="investor_type">
                                <option value="individual">Individual Investor</option>
                                <option value="institutional">Institutional Investor</option>
                                <option value="corporate">Corporate Investor</option>
                                <option value="venture_capital">Venture Capital</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="investmentRange">Investment Range</label>
                        <div class="input-group">
                            <i class="fas fa-dollar-sign"></i>
                            <select id="investmentRange" name="investment_range">
                                <option value="under_10k">Under $10,000</option>
                                <option value="10k_50k">$10,000 - $50,000</option>
                                <option value="50k_100k">$50,000 - $100,000</option>
                                <option value="100k_500k">$100,000 - $500,000</option>
                                <option value="above_500k">Above $500,000</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="password">Password <span class="required">*</span></label>
                    <div class="input-group">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="password" name="password" placeholder="Minimum 8 characters" required minlength="8">
                        <button type="button" class="password-toggle" onclick="togglePassword('password', 'toggleIcon1')">
                            <i class="fas fa-eye" id="toggleIcon1"></i>
                        </button>
                    </div>
                    <div class="password-strength">
                        <div class="bar" id="strengthBar"></div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="confirmPassword">Confirm Password <span class="required">*</span></label>
                    <div class="input-group">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="confirmPassword" name="confirm_password" placeholder="Confirm your password" required>
                        <button type="button" class="password-toggle" onclick="togglePassword('confirmPassword', 'toggleIcon2')">
                            <i class="fas fa-eye" id="toggleIcon2"></i>
                        </button>
                    </div>
                </div>
                
                <div class="terms-checkbox">
                    <input type="checkbox" id="terms" name="agree_terms" required>
                    <label for="terms">
                        I agree to the <a href="../legal/terms.html" target="_blank">Terms of Service</a> and <a href="../legal/privacy.html" target="_blank">Privacy Policy</a>. 
                        I understand that investments involve risk.
                    </label>
                </div>
                
                <button type="submit" class="signup-btn" id="signupBtn">
                    <i class="fas fa-user-plus"></i>
                    <span>Create Account</span>
                </button>
            </form>
            
            <div class="signup-footer">
                <p>Already have an investor account?</p>
                <a href="investor_login.html">Sign In</a>
            </div>
        </div>
        
        <a href="index.html" class="back-link">
            <i class="fas fa-arrow-left"></i>
            <span>Back to Homepage</span>
        </a>
    </div>
    
    <script>
        function togglePassword(inputId, iconId) {
            const passwordInput = document.getElementById(inputId);
            const toggleIcon = document.getElementById(iconId);
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }
        
        // Password strength indicator
        document.getElementById('password').addEventListener('input', function() {
            const password = this.value;
            const strengthBar = document.getElementById('strengthBar');
            
            if (password.length === 0) {
                strengthBar.className = 'bar';
            } else if (password.length < 8) {
                strengthBar.className = 'bar weak';
            } else if (password.length < 12 || !/[A-Z]/.test(password) || !/[0-9]/.test(password)) {
                strengthBar.className = 'bar medium';
            } else {
                strengthBar.className = 'bar strong';
            }
        });
        
        document.getElementById('signupForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            const signupBtn = document.getElementById('signupBtn');
            const successAlert = document.getElementById('successAlert');
            const errorAlert = document.getElementById('errorAlert');
            
            // Hide any existing alerts
            successAlert.style.display = 'none';
            errorAlert.style.display = 'none';
            
            // Validate passwords match
            if (password !== confirmPassword) {
                errorAlert.textContent = 'Passwords do not match';
                errorAlert.style.display = 'block';
                return;
            }
            
            // Disable button during submission
            signupBtn.disabled = true;
            signupBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating account...';
            
            // Prepare form data
            const formData = {
                full_name: document.getElementById('fullName').value.trim(),
                email: document.getElementById('email').value.trim(),
                password: password,
                phone: document.getElementById('phone').value.trim(),
                company_name: document.getElementById('companyName').value.trim(),
                investor_type: document.getElementById('investorType').value,
                investment_range: document.getElementById('investmentRange').value
            };
            
            // Submit to API
            fetch('investor_register.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(formData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    successAlert.textContent = data.message + ' Redirecting to login...';
                    successAlert.style.display = 'block';
                    
                    // Redirect to login
                    setTimeout(() => {
                        window.location.href = 'investor_login.html?registered=true';
                    }, 2000);
                } else {
                    errorAlert.textContent = data.message;
                    errorAlert.style.display = 'block';
                    
                    signupBtn.disabled = false;
                    signupBtn.innerHTML = '<i class="fas fa-user-plus"></i> Create Account';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                errorAlert.textContent = 'An error occurred. Please try again.';
                errorAlert.style.display = 'block';
                
                signupBtn.disabled = false;
                signupBtn.innerHTML = '<i class="fas fa-user-plus"></i> Create Account';
            });
        });
    </script>
</body>
</html>
