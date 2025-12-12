<?php
/**
 * RxHub Admin Login Page
 */

require_once dirname(__DIR__) . '/includes/init.php';

// Redirect if already logged in
if (Session::isLoggedIn('admin')) {
    header('Location: dashboard.php');
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $error = 'Please enter both email and password';
    } else {
        $auth = new Auth();
        $result = $auth->loginAdmin($email, $password);
        
        if ($result['success']) {
            header('Location: dashboard.php');
            exit();
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
    <title>Admin Login - RxHub</title>
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
            --danger: #ef4444;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .login-container {
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            width: 100%;
            max-width: 420px;
            overflow: hidden;
        }
        
        .login-header {
            background: linear-gradient(135deg, var(--dark) 0%, #0f172a 100%);
            padding: 40px 30px;
            text-align: center;
            color: white;
        }
        
        .login-header h1 {
            font-size: 2rem;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        
        .login-header h1 i {
            color: var(--secondary);
        }
        
        .login-header p {
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.95rem;
        }
        
        .login-header .badge {
            display: inline-block;
            background: var(--danger);
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.8rem;
            margin-top: 15px;
        }
        
        .login-body {
            padding: 40px 30px;
        }
        
        .alert {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid var(--danger);
            color: var(--danger);
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--dark);
        }
        
        .form-group input {
            width: 100%;
            padding: 14px 15px;
            border: 2px solid var(--light-gray);
            border-radius: 10px;
            font-size: 1rem;
            transition: border-color 0.3s, box-shadow 0.3s;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(153, 0, 204, 0.1);
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
        
        .login-footer {
            text-align: center;
            padding: 0 30px 30px;
        }
        
        .login-footer a {
            color: var(--primary);
            text-decoration: none;
            font-size: 0.9rem;
        }
        
        .login-footer a:hover {
            text-decoration: underline;
        }
        
        .back-link {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            color: var(--gray);
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h1><i class="fas fa-clinic-medical"></i> RxHub</h1>
            <p>Administration Portal</p>
            <span class="badge"><i class="fas fa-shield-alt"></i> Secure Access</span>
        </div>
        
        <div class="login-body">
            <?php if ($error): ?>
            <div class="alert">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo h($error); ?>
            </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" placeholder="admin@rxhub.com.ng" required autofocus>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Enter your password" required>
                </div>
                
                <button type="submit" class="btn">
                    <i class="fas fa-sign-in-alt"></i>
                    Sign In to Dashboard
                </button>
            </form>
        </div>
        
        <div class="login-footer">
            <a href="../index.php" class="back-link">
                <i class="fas fa-arrow-left"></i>
                Back to Website
            </a>
        </div>
    </div>
</body>
</html>
