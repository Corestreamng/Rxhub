<?php
/**
 * RxHub User Signup Page
 */

require_once dirname(__DIR__) . '/includes/init.php';

// Redirect if already logged in
if (Session::isLoggedIn('user')) {
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
        'facility_name' => trim($_POST['facility_name'] ?? ''),
        'facility_type' => $_POST['facility_type'] ?? 'pharmacy',
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
        $result = $auth->registerUser($data);
        
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
    <meta name="description" content="Create a healthcare provider account with RxHub - Africa's leading pharmaceutical supply chain platform.">
    <title>Sign Up - RxHub</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #9900cc;
            --primary-dark: #7a00a3;
            --secondary: #ff3300;
            --accent: #006666;
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
            background: linear-gradient(135deg, var(--accent) 0%, var(--primary) 100%);
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
            background: linear-gradient(135deg, var(--accent) 0%, var(--primary) 100%);
            padding: 30px;
            text-align: center;
            color: white;
        }
        
        .signup-header h1 {
            font-size: 2rem;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        
        .signup-header p {
            color: rgba(255,255,255,0.9);
        }
        
        .signup-body {
            padding: 30px;
        }
        
        .alert {
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 0.9rem;
            display: none;
        }
        
        .alert.success {
            background: rgba(34,197,94,0.1);
            color: var(--success);
            border: 1px solid var(--success);
        }
        
        .alert.error {
            background: rgba(239,68,68,0.1);
            color: var(--danger);
            border: 1px solid var(--danger);
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--dark);
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
        
        .form-group input,
        .form-group select {
            width: 100%;
            padding: 14px 15px 14px 45px;
            border: 2px solid var(--light-gray);
            border-radius: 10px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }
        
        .form-group select {
            padding-left: 45px;
            appearance: none;
            background: white url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%2364748b' d='M6 8L1 3h10z'/%3E%3C/svg%3E") no-repeat right 15px center;
        }
        
        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: var(--primary);
        }
        
        .terms-group {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            margin-bottom: 20px;
        }
        
        .terms-group input[type="checkbox"] {
            margin-top: 4px;
        }
        
        .terms-group label {
            font-size: 0.9rem;
            color: var(--gray);
        }
        
        .terms-group a {
            color: var(--primary);
        }
        
        .btn {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.3s, box-shadow 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(153, 0, 204, 0.4);
        }
        
        .signup-footer {
            text-align: center;
            padding: 20px 30px 30px;
            border-top: 1px solid var(--light-gray);
        }
        
        .signup-footer p {
            color: var(--gray);
            margin-bottom: 10px;
        }
        
        .signup-footer a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
        }
        
        .back-link {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-top: 15px;
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
                <h1><i class="fas fa-clinic-medical"></i> RxHub</h1>
                <p>Create Healthcare Provider Account</p>
            </div>
            
            <div class="signup-body">
                <?php if ($error): ?>
                <div class="alert error" style="display: block;"><?php echo h($error); ?></div>
                <?php endif; ?>
                <?php if ($success): ?>
                <div class="alert success" style="display: block;"><?php echo h($success); ?> <a href="login.php">Login here</a></div>
                <?php endif; ?>
                
                <form method="POST" action="">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Full Name <span class="required">*</span></label>
                            <div class="input-group">
                                <i class="fas fa-user"></i>
                                <input type="text" name="full_name" placeholder="John Doe" required>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label>Email Address <span class="required">*</span></label>
                            <div class="input-group">
                                <i class="fas fa-envelope"></i>
                                <input type="email" name="email" placeholder="your@email.com" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>Phone Number</label>
                            <div class="input-group">
                                <i class="fas fa-phone"></i>
                                <input type="tel" name="phone" placeholder="+234 800 000 0000">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label>Facility Type <span class="required">*</span></label>
                            <div class="input-group">
                                <i class="fas fa-hospital"></i>
                                <select name="facility_type" required>
                                    <option value="pharmacy">Pharmacy</option>
                                    <option value="hospital">Hospital</option>
                                    <option value="clinic">Clinic</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Facility Name</label>
                        <div class="input-group">
                            <i class="fas fa-building"></i>
                            <input type="text" name="facility_name" placeholder="Your facility name">
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>Password <span class="required">*</span></label>
                            <div class="input-group">
                                <i class="fas fa-lock"></i>
                                <input type="password" name="password" placeholder="Min 8 characters" required minlength="8">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label>Confirm Password <span class="required">*</span></label>
                            <div class="input-group">
                                <i class="fas fa-lock"></i>
                                <input type="password" name="confirm_password" placeholder="Confirm password" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="terms-group">
                        <input type="checkbox" name="agree_terms" id="agreeTerms" required>
                        <label for="agreeTerms">
                            I agree to the <a href="../legal/terms.html" target="_blank">Terms of Service</a> and 
                            <a href="../legal/privacy.html" target="_blank">Privacy Policy</a>
                        </label>
                    </div>
                    
                    <button type="submit" class="btn">
                        <i class="fas fa-user-plus"></i>
                        Create Account
                    </button>
                </form>
            </div>
            
            <div class="signup-footer">
                <p>Already have an account?</p>
                <a href="login.php">Sign In</a>
                
                <a href="../index.php" class="back-link">
                    <i class="fas fa-arrow-left"></i>
                    Back to Website
                </a>
            </div>
        </div>
    </div>
</body>
</html>
