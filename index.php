<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="RxHub - Africa's leading B2B pharmaceutical procurement platform. Source authentic medicines directly from manufacturers with anti-counterfeit tracking and reliable last-mile delivery.">
    <title>RxHub - Pharmaceutical Procurement & Supply Chain Platform</title>
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
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            color: var(--dark);
            line-height: 1.6;
            background-color: #f9fafb;
        }
        
        .container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        /* Header Styles */
        header {
            background-color: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            z-index: 100;
        }
        
        .header-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
        }
        
        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 700;
            font-size: 24px;
            color: var(--primary);
        }
        
        .logo i {
            color: var(--secondary);
        }
        
        nav ul {
            display: flex;
            list-style: none;
            gap: 30px;
        }
        
        nav a {
            text-decoration: none;
            color: var(--dark);
            font-weight: 500;
            transition: color 0.3s;
        }
        
        nav a:hover {
            color: var(--primary);
        }
        
        .auth-buttons {
            display: flex;
            gap: 15px;
        }
        
        .btn {
            padding: 10px 20px;
            border-radius: 6px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s;
            border: none;
        }
        
        .btn-outline {
            background: transparent;
            border: 1px solid var(--primary);
            color: var(--primary);
        }
        
        .btn-outline:hover {
            background: var(--primary);
            color: white;
        }
        
        .btn-primary {
            background: var(--primary);
            color: white;
        }
        
        .btn-primary:hover {
            background: var(--primary-dark);
        }
        
        .btn-energy {
            background: var(--energy);
            color: var(--dark);
            font-weight: 600;
        }
        
        .btn-energy:hover {
            background: #2ab82a;
            transform: translateY(-2px);
        }
        
        .mobile-menu-btn {
            display: none;
            font-size: 24px;
            background: none;
            border: none;
            color: var(--dark);
            cursor: pointer;
        }
        
        /* Hero Section */
        .hero {
            background: linear-gradient(to right, var(--primary), var(--accent));
            color: white;
            padding: 80px 0;
        }
        
        .hero-content {
            display: flex;
            align-items: center;
            gap: 40px;
        }
        
        .hero-text {
            flex: 1;
        }
        
        .hero-text h1 {
            font-size: 3rem;
            margin-bottom: 20px;
            line-height: 1.2;
        }
        
        .hero-text p {
            font-size: 1.2rem;
            margin-bottom: 30px;
            opacity: 0.9;
        }
        
        .hero-image {
            flex: 1;
            display: flex;
            justify-content: center;
        }
        
        .hero-stats {
            display: flex;
            gap: 40px;
            margin-top: 40px;
        }
        
        .stat-item {
            display: flex;
            flex-direction: column;
        }
        
        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
        }
        
        .stat-label {
            font-size: 0.9rem;
            opacity: 0.8;
        }
        
        /* Features Section */
        .section-title {
            text-align: center;
            margin-bottom: 50px;
        }
        
        .section-title h2 {
            font-size: 2.5rem;
            color: var(--dark);
            margin-bottom: 15px;
        }
        
        .section-title p {
            color: var(--gray);
            max-width: 700px;
            margin: 0 auto;
        }
        
        .features {
            padding: 80px 0;
            background-color: white;
        }
        
        .features-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 30px;
        }
        
        .feature-card {
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s, box-shadow 0.3s;
            border-top: 4px solid var(--primary);
        }
        
        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }
        
        .feature-icon {
            width: 60px;
            height: 60px;
            background: var(--light);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            color: var(--primary);
            font-size: 24px;
        }
        
        .feature-card h3 {
            font-size: 1.3rem;
            margin-bottom: 15px;
            color: var(--dark);
        }
        
        .feature-card p {
            color: var(--gray);
        }
        
        /* How It Works Section */
        .how-it-works {
            padding: 80px 0;
            background-color: var(--light);
        }
        
        .process-steps {
            display: flex;
            justify-content: space-between;
            margin-top: 50px;
            position: relative;
        }
        
        .process-steps::before {
            content: '';
            position: absolute;
            top: 40px;
            left: 10%;
            width: 80%;
            height: 2px;
            background: var(--primary);
            z-index: 1;
        }
        
        .step {
            text-align: center;
            position: relative;
            z-index: 2;
            flex: 1;
        }
        
        .step-number {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: var(--primary);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            font-weight: 700;
            margin: 0 auto 20px;
        }
        
        .step h3 {
            margin-bottom: 10px;
            font-size: 1.2rem;
        }
        
        .step p {
            color: var(--gray);
            max-width: 250px;
            margin: 0 auto;
        }
        
        /* Testimonials Section */
        .testimonials {
            padding: 80px 0;
            background: linear-gradient(to bottom right, #f8fafc, #e2e8f0);
        }
        
        .testimonials-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 30px;
            margin-top: 50px;
        }
        
        .testimonial-card {
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            position: relative;
        }
        
        .testimonial-card::before {
            content: '"';
            font-size: 5rem;
            color: var(--light-gray);
            position: absolute;
            top: 10px;
            right: 20px;
            line-height: 1;
        }
        
        .testimonial-content {
            margin-bottom: 20px;
            color: var(--gray);
            font-style: italic;
        }
        
        .testimonial-author {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .author-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: var(--accent);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 1.2rem;
        }
        
        .author-details h4 {
            color: var(--dark);
            margin-bottom: 5px;
        }
        
        .author-details p {
            color: var(--gray);
            font-size: 0.9rem;
        }
        
        .rating {
            color: var(--secondary);
            margin-top: 5px;
        }
        
        /* Partners Section */
        .partners {
            padding: 80px 0;
            background: white;
        }
        
        .partners-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 30px;
            align-items: center;
        }
        
        .partner-logo {
            height: 100px;
            display: flex;
            align-items: center;
            justify-content: center;
            filter: grayscale(100%);
            opacity: 0.6;
            transition: all 0.3s;
        }
        
        .partner-logo:hover {
            filter: grayscale(0);
            opacity: 1;
        }
        
        .partner-logo img {
            max-width: 100%;
            max-height: 70px;
        }
        
        /* Contact Section */
        .contact {
            padding: 80px 0;
            background: var(--light);
        }
        
        .contact-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 50px;
            margin-top: 30px;
        }
        
        .contact-info {
            display: flex;
            flex-direction: column;
            gap: 25px;
        }
        
        .contact-method {
            display: flex;
            align-items: flex-start;
            gap: 15px;
        }
        
        .contact-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: var(--primary);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            flex-shrink: 0;
        }
        
        .contact-details h4 {
            margin-bottom: 5px;
            color: var(--dark);
        }
        
        .contact-details p {
            color: var(--gray);
        }
        
        .contact-form {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: var(--dark);
            font-weight: 500;
        }
        
        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid var(--light-gray);
            border-radius: 6px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }
        
        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: var(--primary);
        }
        
        .form-group textarea {
            min-height: 120px;
            resize: vertical;
        }
        
        /* CTA Section */
        .cta {
            padding: 80px 0;
            background: linear-gradient(to right, var(--accent), var(--primary));
            color: white;
            text-align: center;
        }
        
        .cta h2 {
            font-size: 2.5rem;
            margin-bottom: 20px;
        }
        
        .cta p {
            font-size: 1.2rem;
            max-width: 700px;
            margin: 0 auto 30px;
            opacity: 0.9;
        }
        
        .cta-buttons {
            display: flex;
            gap: 20px;
            justify-content: center;
        }
        
        .btn-light {
            background: white;
            color: var(--dark);
        }
        
        .btn-light:hover {
            background: var(--light);
        }
        
        /* Footer */
        footer {
            background: var(--dark);
            color: white;
            padding: 60px 0 30px;
        }
        
        .footer-grid {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr;
            gap: 40px;
            margin-bottom: 40px;
        }
        
        .footer-info h3 {
            font-size: 1.8rem;
            margin-bottom: 20px;
            color: white;
        }
        
        .footer-info p {
            color: var(--light-gray);
            margin-bottom: 20px;
        }
        
        .social-links {
            display: flex;
            gap: 15px;
        }
        
        .social-links a {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-decoration: none;
            transition: background 0.3s;
        }
        
        .social-links a:hover {
            background: var(--primary);
        }
        
        .footer-links h4 {
            font-size: 1.2rem;
            margin-bottom: 20px;
            color: white;
        }
        
        .footer-links ul {
            list-style: none;
        }
        
        .footer-links li {
            margin-bottom: 10px;
        }
        
        .footer-links a {
            color: var(--light-gray);
            text-decoration: none;
            transition: color 0.3s;
        }
        
        .footer-links a:hover {
            color: white;
        }
        
        .footer-bottom {
            text-align: center;
            padding-top: 30px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            color: var(--light-gray);
            font-size: 0.9rem;
        }
        
        /* Responsive Design */
        @media (max-width: 992px) {
            .features-grid,
            .testimonials-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .hero-content {
                flex-direction: column;
                text-align: center;
            }
            
            .hero-stats {
                justify-content: center;
            }
            
            .footer-grid {
                grid-template-columns: 1fr 1fr;
            }
            
            .contact-grid {
                grid-template-columns: 1fr;
                gap: 40px;
            }
        }
        
        @media (max-width: 768px) {
            .features-grid,
            .testimonials-grid {
                grid-template-columns: 1fr;
            }
            
            .process-steps {
                flex-direction: column;
                gap: 40px;
            }
            
            .process-steps::before {
                display: none;
            }
            
            nav ul {
                display: none;
            }
            
            .mobile-menu-btn {
                display: block;
            }
            
            .auth-buttons {
                display: none;
            }
            
            .footer-grid {
                grid-template-columns: 1fr;
            }
            
            .cta-buttons {
                flex-direction: column;
                align-items: center;
            }
        }
        
        /* Mobile Menu */
        .mobile-menu {
            position: fixed;
            top: 0;
            right: -300px;
            width: 300px;
            height: 100vh;
            background: white;
            z-index: 200;
            padding: 50px 20px;
            transition: right 0.3s;
            box-shadow: -5px 0 15px rgba(0, 0, 0, 0.1);
        }
        
        .mobile-menu.active {
            right: 0;
        }
        
        .mobile-menu ul {
            list-style: none;
        }
        
        .mobile-menu li {
            margin-bottom: 20px;
        }
        
        .mobile-menu a {
            text-decoration: none;
            color: var(--dark);
            font-weight: 500;
            font-size: 1.1rem;
        }
        
        .close-menu {
            position: absolute;
            top: 20px;
            right: 20px;
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
        }
        
        /* Hero Buttons */
        .hero-buttons {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }
        
        .hero-buttons .btn-light {
            background: rgba(255, 255, 255, 0.15);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.3);
            backdrop-filter: blur(10px);
        }
        
        .hero-buttons .btn-light:hover {
            background: rgba(255, 255, 255, 0.25);
        }
        
        /* Demo Dashboard Button */
        .dashboard-demo {
            position: fixed;
            bottom: 30px;
            right: 30px;
            z-index: 99;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 50px;
            padding: 15px 25px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 500;
            transition: all 0.3s;
        }
        
        .dashboard-demo:hover {
            background: var(--primary-dark);
            transform: translateY(-3px);
        }
        
        /* Chatbot Button */
        .chatbot-btn {
            position: fixed;
            bottom: 30px;
            left: 30px;
            z-index: 99;
            background: var(--accent);
            color: white;
            border: none;
            border-radius: 50%;
            width: 60px;
            height: 60px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            transition: all 0.3s;
        }
        
        .chatbot-btn:hover {
            transform: scale(1.1);
        }
        
        /* Chatbot Widget */
        .chatbot-widget {
            position: fixed;
            bottom: 100px;
            left: 30px;
            width: 350px;
            max-height: 500px;
            background: white;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            z-index: 100;
            display: none;
            flex-direction: column;
            overflow: hidden;
        }
        
        .chatbot-widget.active {
            display: flex;
        }
        
        .chatbot-header {
            background: linear-gradient(to right, var(--primary), var(--accent));
            color: white;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .chatbot-header h4 {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .chatbot-close {
            background: none;
            border: none;
            color: white;
            font-size: 20px;
            cursor: pointer;
        }
        
        .chatbot-messages {
            flex: 1;
            overflow-y: auto;
            padding: 20px;
            max-height: 300px;
        }
        
        .chatbot-message {
            margin-bottom: 15px;
        }
        
        .chatbot-message.bot {
            text-align: left;
        }
        
        .chatbot-message.user {
            text-align: right;
        }
        
        .chatbot-message .bubble {
            display: inline-block;
            padding: 10px 15px;
            border-radius: 15px;
            max-width: 80%;
            font-size: 0.9rem;
        }
        
        .chatbot-message.bot .bubble {
            background: var(--light);
            color: var(--dark);
            border-bottom-left-radius: 5px;
        }
        
        .chatbot-message.user .bubble {
            background: var(--primary);
            color: white;
            border-bottom-right-radius: 5px;
        }
        
        .chatbot-faq {
            padding: 15px 20px;
            border-top: 1px solid var(--light-gray);
        }
        
        .chatbot-faq p {
            font-size: 0.8rem;
            color: var(--gray);
            margin-bottom: 10px;
        }
        
        .faq-btn {
            display: block;
            width: 100%;
            padding: 10px 15px;
            margin-bottom: 8px;
            background: var(--light);
            border: 1px solid var(--light-gray);
            border-radius: 8px;
            text-align: left;
            cursor: pointer;
            font-size: 0.85rem;
            transition: all 0.3s;
        }
        
        .faq-btn:hover {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
        }
        
        /* Video Modal */
        .video-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.9);
            z-index: 2000;
            justify-content: center;
            align-items: center;
        }
        
        .video-modal.active {
            display: flex;
        }
        
        .video-modal-content {
            position: relative;
            width: 90%;
            max-width: 900px;
        }
        
        .video-modal-close {
            position: absolute;
            top: -40px;
            right: 0;
            background: none;
            border: none;
            color: white;
            font-size: 30px;
            cursor: pointer;
        }
        
        .video-container {
            position: relative;
            padding-bottom: 56.25%;
            height: 0;
            overflow: hidden;
            border-radius: 12px;
        }
        
        .video-container iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }
        
        /* Investors Navigation Link */
        .nav-investors {
            background: linear-gradient(to right, var(--primary), var(--accent));
            color: white !important;
            padding: 8px 16px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        
        .nav-investors:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }
        
        /* Modal Styles */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            z-index: 1000;
            justify-content: center;
            align-items: center;
            backdrop-filter: blur(4px);
        }
        
        .modal-overlay.active {
            display: flex;
        }
        
        .modal {
            background: white;
            border-radius: 16px;
            width: 90%;
            max-width: 450px;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.25);
            animation: modalSlideIn 0.3s ease;
        }
        
        @keyframes modalSlideIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .modal-header {
            padding: 25px 30px 20px;
            border-bottom: 1px solid var(--light-gray);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .modal-header h3 {
            font-size: 1.5rem;
            color: var(--dark);
        }
        
        .modal-close {
            background: none;
            border: none;
            font-size: 28px;
            cursor: pointer;
            color: var(--gray);
            transition: color 0.3s;
        }
        
        .modal-close:hover {
            color: var(--dark);
        }
        
        .modal-body {
            padding: 25px 30px;
        }
        
        .modal-body .form-group {
            margin-bottom: 20px;
        }
        
        .modal-body .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--dark);
        }
        
        .modal-body .input-group {
            position: relative;
        }
        
        .modal-body .input-group i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gray);
        }
        
        .modal-body .input-group input,
        .modal-body .input-group select {
            width: 100%;
            padding: 12px 15px 12px 42px;
            border: 2px solid var(--light-gray);
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s;
        }
        
        .modal-body .input-group input:focus,
        .modal-body .input-group select:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(153, 0, 204, 0.1);
        }
        
        .modal-body .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--gray);
            cursor: pointer;
        }
        
        .modal-body .alert {
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: none;
            font-size: 0.9rem;
        }
        
        .modal-body .alert.success {
            background: rgba(34, 197, 94, 0.1);
            color: #22c55e;
            border: 1px solid #22c55e;
        }
        
        .modal-body .alert.error {
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
            border: 1px solid #ef4444;
        }
        
        .modal-body .submit-btn {
            width: 100%;
            padding: 14px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        
        .modal-body .submit-btn:hover {
            background: var(--primary-dark);
        }
        
        .modal-body .submit-btn:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }
        
        .modal-footer {
            padding: 20px 30px;
            background: var(--light);
            border-top: 1px solid var(--light-gray);
            text-align: center;
        }
        
        .modal-footer p {
            color: var(--gray);
            margin-bottom: 5px;
        }
        
        .modal-footer a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
        }
        
        .modal-footer a:hover {
            text-decoration: underline;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        
        @media (max-width: 500px) {
            .form-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header>
        <div class="container header-container">
            <div class="logo">
                <i class="fas fa-clinic-medical"></i>
                <span>RxHub</span>
            </div>
            
            <nav>
                <ul>
                    <li><a href="#features">Features</a></li>
                    <li><a href="#how-it-works">How It Works</a></li>
                    <li><a href="#testimonials">Testimonials</a></li>
                    <li><a href="#partners">Manufacturers</a></li>
                    <li><a href="#contact">Contact</a></li>
                    <li><a href="investor/login.php" class="nav-investors"><i class="fas fa-chart-line"></i> Investors</a></li>
                </ul>
            </nav>
            
            <div class="auth-buttons">
                <button class="btn btn-outline" onclick="openLoginModal()">Login</button>
                <button class="btn btn-primary" onclick="openSignupModal()">Sign Up</button>
            </div>
            
            <button class="mobile-menu-btn">
                <i class="fas fa-bars"></i>
            </button>
        </div>
    </header>
    
    <!-- Mobile Menu -->
    <div class="mobile-menu" id="mobile-menu">
        <button class="close-menu">
            <i class="fas fa-times"></i>
        </button>
        <ul>
            <li><a href="#features">Features</a></li>
            <li><a href="#how-it-works">How It Works</a></li>
            <li><a href="#testimonials">Testimonials</a></li>
            <li><a href="#partners">Manufacturers</a></li>
            <li><a href="#contact">Contact</a></li>
            <li><a href="investor/login.php"><i class="fas fa-chart-line"></i> Investors</a></li>
            <li><a href="#" onclick="closeMobileMenu(); openLoginModal();">Login</a></li>
            <li><a href="#" onclick="closeMobileMenu(); openSignupModal();">Sign Up</a></li>
        </ul>
    </div>
    
    <!-- Hero Section -->
    <section class="hero">
        <div class="container hero-content">
            <div class="hero-text">
                <h1>Streamline Pharmaceutical Procurement for Healthcare Providers</h1>
                <p>Africa's leading B2B platform connecting pharmacies and hospitals directly to verified manufacturers with anti-counterfeit tracking and reliable last-mile delivery.</p>
                <div class="hero-buttons">
                    <button class="btn btn-energy" onclick="openSignupModal()">Get Started Today</button>
                    <a href="tel:+2348001234567" class="btn btn-light"><i class="fas fa-phone"></i> Speak to Consultant</a>
                </div>
                <div class="hero-stats">
                    <div class="stat-item">
                        <span class="stat-number">150+</span>
                        <span class="stat-label">Manufacturer Partners</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">100+</span>
                        <span class="stat-label">Healthcare Providers</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">10,000+</span>
                        <span class="stat-label">Medicines Sourced</span>
                    </div>
                </div>
            </div>
            <div class="hero-image">
                <img src="images/RxHub.jpg" alt="RxHub B2B Platform Dashboard" style="border-radius: 10px; box-shadow: 0 20px 40px rgba(0,0,0,0.2);">
            </div>
        </div>
    </section>
    
    <!-- Features Section -->
    <section class="features" id="features">
        <div class="container">
            <div class="section-title">
                <h2>Powerful Features for Healthcare Providers</h2>
                <p>Designed specifically for pharmacies, hospitals, and clinics to streamline procurement and ensure medicine authenticity</p>
            </div>
            
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-industry"></i>
                    </div>
                    <h3>Direct Manufacturer Sourcing</h3>
                    <p>Source pharmaceuticals directly from 150+ verified manufacturers, eliminating middlemen and reducing costs.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h3>Anti-Counterfeit Tracking</h3>
                    <p>Every product comes with verified tracking to ensure authenticity and protect your patients from counterfeit drugs.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-truck-fast"></i>
                    </div>
                    <h3>Last-Mile Delivery</h3>
                    <p>Reliable delivery directly to your facility with real-time tracking and temperature-controlled logistics.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-boxes"></i>
                    </div>
                    <h3>Bulk Order Management</h3>
                    <p>Easily manage large inventory orders with our specialized bulk procurement tools and volume pricing.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h3>Inventory Analytics</h3>
                    <p>Gain insights into your medication usage patterns and optimize inventory with our predictive analytics.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-file-invoice"></i>
                    </div>
                    <h3>Automated Compliance</h3>
                    <p>Stay compliant with pharmaceutical regulations with automated documentation and audit trails.</p>
                </div>
            </div>
        </div>
    </section>
    
    <!-- How It Works Section -->
    <section class="how-it-works" id="how-it-works">
        <div class="container">
            <div class="section-title">
                <h2>How RxHub Works for Healthcare Providers</h2>
                <p>Streamline your pharmaceutical procurement in four simple steps</p>
            </div>
            
            <div class="process-steps">
                <div class="step">
                    <div class="step-number">1</div>
                    <h3>Create Account</h3>
                    <p>Register your healthcare facility and complete verification</p>
                </div>
                
                <div class="step">
                    <div class="step-number">2</div>
                    <h3>Place Orders</h3>
                    <p>Browse our catalog and place bulk orders through our platform</p>
                </div>
                
                <div class="step">
                    <div class="step-number">3</div>
                    <h3>Track & Verify</h3>
                    <p>Monitor your order status and verify authenticity upon arrival</p>
                </div>
                
                <div class="step">
                    <div class="step-number">4</div>
                    <h3>Receive Delivery</h3>
                    <p>Get your medications delivered directly to your facility</p>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Testimonials Section -->
    <section class="testimonials" id="testimonials">
        <div class="container">
            <div class="section-title">
                <h2>What Our Partners Say</h2>
                <p>Hear from healthcare providers who are transforming their procurement with RxHub</p>
            </div>
            
            <div class="testimonials-grid">
                <div class="testimonial-card">
                    <div class="testimonial-content">
                        <p>RxHub has revolutionized how we source medications. Our procurement costs have decreased by 22% while ensuring we always have the authentic medicines our patients need.</p>
                    </div>
                    <div class="testimonial-author">
                        <div class="author-avatar">CA</div>
                        <div class="author-details">
                            <h4>Dr. Chinedu Abacha</h4>
                            <p>Medical Director, Royal Hospital</p>
                            <div class="rating">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="testimonial-card">
                    <div class="testimonial-content">
                        <p>The anti-counterfeit tracking gives us peace of mind. We know exactly where every medicine comes from, which is crucial for maintaining trust with our patients.</p>
                    </div>
                    <div class="testimonial-author">
                        <div class="author-avatar">AO</div>
                        <div class="author-details">
                            <h4>Amaka Okoro</h4>
                            <p>Pharmacy Manager, MedPlus Chain</p>
                            <div class="rating">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="testimonial-card">
                    <div class="testimonial-content">
                        <p>As a clinic manager, RxHub saves me hours each week. The bulk ordering system is intuitive, and deliveries always arrive on schedule with proper documentation.</p>
                    </div>
                    <div class="testimonial-author">
                        <div class="author-avatar">TE</div>
                        <div class="author-details">
                            <h4>Tunde Emmanuel</h4>
                            <p>Operations Manager, Community Health Clinic</p>
                            <div class="rating">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star-half-alt"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Partners Section -->
    <section class="partners" id="partners">
        <div class="container">
            <div class="section-title">
                <h2>Trusted by Leading Manufacturers</h2>
                <p>We partner with verified pharmaceutical manufacturers across Africa and globally</p>
            </div>
            
            <div class="partners-grid">
                <div class="partner-logo">
                    <img src="images/tuyil_logo.png" alt="Manufacturer 1">
                </div>
                <div class="partner-logo">
                    <img src="images/Emzor_Logo.jpg" alt="Manufacturer 2">
                </div>
                <div class="partner-logo">
                    <img src="images/fidson_logo.webp" alt="Manufacturer 3">
                </div>
                <div class="partner-logo">
                    <img src="images/m&b_logo.png" alt="Manufacturer 4">
                </div>
                <div class="partner-logo">
                    <img src="images/Neimeth_logo.png" alt="Manufacturer 5">
                </div>
                <div class="partner-logo">
                    <img src="images/peaceLogo.png" alt="Manufacturer 6">
                </div>
                <div class="partner-logo">
                    <img src="images/drugfield_logo.jpeg" alt="Manufacturer 6">
                </div>
                <div class="partner-logo">
                    <img src="images/bioraj_logo.png" alt="Manufacturer 6">
                </div>
                <div class="partner-logo">
                    <img src="images/swipha_logo.gif" alt="Manufacturer 6">
                </div>
                <div class="partner-logo">
                    <img src="images/gsk_logo.webp" alt="Manufacturer 6">
                </div>
            </div>
        </div>
    </section>
    
    <!-- Contact Section -->
    <section class="contact" id="contact">
        <div class="container">
            <div class="section-title">
                <h2>Get in Touch</h2>
                <p>Have questions about how RxHub can transform your pharmaceutical procurement? Contact our team today.</p>
            </div>
            
            <div class="contact-grid">
                <div class="contact-info">
                    <div class="contact-method">
                        <div class="contact-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div class="contact-details">
                            <h4>Our Office</h4>
                            <p>123 Healthcare Avenue, Victoria Island, Lagos, Nigeria</p>
                        </div>
                    </div>
                    
                    <div class="contact-method">
                        <div class="contact-icon">
                            <i class="fas fa-phone-alt"></i>
                        </div>
                        <div class="contact-details">
                            <h4>Phone</h4>
                            <p>+234 800 123 4567</p>
                            <p>+234 800 765 4321</p>
                        </div>
                    </div>
                    
                    <div class="contact-method">
                        <div class="contact-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="contact-details">
                            <h4>Email</h4>
                            <p>info@rxhub.com.ng</p>
                            <p>support@rxhub.com.ng</p>
                        </div>
                    </div>
                    
                    <div class="contact-method">
                        <div class="contact-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="contact-details">
                            <h4>Business Hours</h4>
                            <p>Monday - Friday: 8:00 AM - 6:00 PM</p>
                            <p>Saturday: 9:00 AM - 2:00 PM</p>
                        </div>
                    </div>
                </div>
                
                <div class="contact-form">
                    <h3>Send us a Message</h3>
                    <form id="contactForm">
                        <div class="form-group">
                            <label for="name">Full Name</label>
                            <input type="text" id="name" placeholder="Your name" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email Address</label>
                            <input type="email" id="email" placeholder="Your email" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="company">Healthcare Facility</label>
                            <input type="text" id="company" placeholder="Your hospital or pharmacy name">
                        </div>
                        
                        <div class="form-group">
                            <label for="subject">Subject</label>
                            <select id="subject" required>
                                <option value="">Select a subject</option>
                                <option value="partnership">Partnership Inquiry</option>
                                <option value="demo">Request a Demo</option>
                                <option value="support">Technical Support</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="message">Message</label>
                            <textarea id="message" placeholder="How can we help you?" required></textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-primary" style="width: 100%">Send Message</button>
                    </form>
                </div>
            </div>
        </div>
    </section>
    
    <!-- CTA Section -->
    <section class="cta">
        <div class="container">
            <h2>Transform Your Pharmaceutical Procurement</h2>
            <p>Join hundreds of healthcare providers across Africa who trust RxHub for their medicine sourcing needs</p>
            <div class="cta-buttons">
                <button class="btn btn-light" onclick="openSignupModal()">Request a Demo</button>
                <button class="btn btn-outline" onclick="openSignupModal()" style="color: white; border-color: white;">Create Account</button>
            </div>
        </div>
    </section>
    
    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="footer-grid">
                <div class="footer-info">
                    <h3>RxHub</h3>
                    <p>Africa's leading B2B pharmaceutical procurement and supply chain platform, connecting healthcare providers directly to verified manufacturers.</p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
                
                <div class="footer-links">
                    <h4>Company</h4>
                    <ul>
                        <li><a href="#">About Us</a></li>
                        <li><a href="#">Our Team</a></li>
                        <li><a href="#">Careers</a></li>
                        <li><a href="#">Press</a></li>
                    </ul>
                </div>
                
                <div class="footer-links">
                    <h4>Resources</h4>
                    <ul>
                        <li><a href="#">Blog</a></li>
                        <li><a href="#">Case Studies</a></li>
                        <li><a href="#">Webinars</a></li>
                        <li><a href="#">FAQ</a></li>
                    </ul>
                </div>
                
                <div class="footer-links">
                    <h4>Contact</h4>
                    <ul>
                        <li><a href="#">info@rxhub.com.ng</a></li>
                        <li><a href="#">+234 800 000 0000</a></li>
                        <li><a href="#">Lagos, Nigeria</a></li>
                        <li><a href="#">Support</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; 2024 RxHub. All rights reserved. | <a href="legal/terms.html" style="color: inherit;">Terms of Service</a> | <a href="legal/privacy.html" style="color: inherit;">Privacy Policy</a></p>
            </div>
        </div>
    </footer>
    
    <!-- Demo Dashboard Button -->
    <button class="dashboard-demo" onclick="openVideoModal()">
        <i class="fas fa-play-circle"></i> View Demo Dashboard
    </button>
    
    <!-- Chatbot Button -->
    <button class="chatbot-btn" onclick="toggleChatbot()">
        <i class="fas fa-comments"></i>
    </button>
    
    <!-- Chatbot Widget -->
    <div class="chatbot-widget" id="chatbotWidget">
        <div class="chatbot-header">
            <h4><i class="fas fa-robot"></i> RxHub Assistant</h4>
            <button class="chatbot-close" onclick="toggleChatbot()">&times;</button>
        </div>
        <div class="chatbot-messages" id="chatbotMessages">
            <div class="chatbot-message bot">
                <div class="bubble">Hello! 👋 How can I help you today? Select a question below or type your message.</div>
            </div>
        </div>
        <div class="chatbot-faq">
            <p>Frequently Asked Questions:</p>
            <button class="faq-btn" onclick="askFAQ('What is RxHub?')">What is RxHub?</button>
            <button class="faq-btn" onclick="askFAQ('How do I place an order?')">How do I place an order?</button>
            <button class="faq-btn" onclick="askFAQ('What payment methods do you accept?')">Payment methods?</button>
            <button class="faq-btn" onclick="askFAQ('How can I become an investor?')">How to invest?</button>
        </div>
    </div>
    
    <!-- Video Modal -->
    <div class="video-modal" id="videoModal">
        <div class="video-modal-content">
            <button class="video-modal-close" onclick="closeVideoModal()">&times;</button>
            <div class="video-container">
                <iframe id="youtubeVideo" src="" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
            </div>
        </div>
    </div>
    
    <!-- Login Modal -->
    <div class="modal-overlay" id="loginModal">
        <div class="modal">
            <div class="modal-header">
                <h3>Welcome Back</h3>
                <button class="modal-close" onclick="closeLoginModal()">&times;</button>
            </div>
            <div class="modal-body">
                <div class="alert success" id="loginSuccessAlert"></div>
                <div class="alert error" id="loginErrorAlert"></div>
                
                <form id="loginForm">
                    <div class="form-group">
                        <label for="loginEmail">Email Address</label>
                        <div class="input-group">
                            <i class="fas fa-envelope"></i>
                            <input type="email" id="loginEmail" name="email" placeholder="your@email.com" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="loginPassword">Password</label>
                        <div class="input-group">
                            <i class="fas fa-lock"></i>
                            <input type="password" id="loginPassword" name="password" placeholder="Enter your password" required>
                            <button type="button" class="password-toggle" onclick="toggleLoginPassword()">
                                <i class="fas fa-eye" id="loginToggleIcon"></i>
                            </button>
                        </div>
                    </div>
                    
                    <button type="submit" class="submit-btn" id="loginBtn">
                        <i class="fas fa-sign-in-alt"></i>
                        <span>Sign In</span>
                    </button>
                </form>
            </div>
            <div class="modal-footer">
                <p>Don't have an account?</p>
                <a href="#" onclick="closeLoginModal(); openSignupModal();">Create Account</a>
                <br><br>
                <a href="investor/login.php"><i class="fas fa-chart-line"></i> Investor Login</a>
            </div>
        </div>
    </div>
    
    <!-- Signup Modal -->
    <div class="modal-overlay" id="signupModal">
        <div class="modal">
            <div class="modal-header">
                <h3>Create Account</h3>
                <button class="modal-close" onclick="closeSignupModal()">&times;</button>
            </div>
            <div class="modal-body">
                <div class="alert success" id="signupSuccessAlert"></div>
                <div class="alert error" id="signupErrorAlert"></div>
                
                <form id="signupForm">
                    <div class="form-group">
                        <label for="signupName">Full Name</label>
                        <div class="input-group">
                            <i class="fas fa-user"></i>
                            <input type="text" id="signupName" name="full_name" placeholder="John Doe" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="signupEmail">Email Address</label>
                        <div class="input-group">
                            <i class="fas fa-envelope"></i>
                            <input type="email" id="signupEmail" name="email" placeholder="your@email.com" required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="signupFacility">Healthcare Facility</label>
                            <div class="input-group">
                                <i class="fas fa-hospital"></i>
                                <input type="text" id="signupFacility" name="facility_name" placeholder="Facility name">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="signupFacilityType">Facility Type</label>
                            <div class="input-group">
                                <i class="fas fa-building"></i>
                                <select id="signupFacilityType" name="facility_type">
                                    <option value="pharmacy">Pharmacy</option>
                                    <option value="hospital">Hospital</option>
                                    <option value="clinic">Clinic</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="signupPhone">Phone Number</label>
                        <div class="input-group">
                            <i class="fas fa-phone"></i>
                            <input type="tel" id="signupPhone" name="phone" placeholder="+234 800 000 0000">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="signupPassword">Password</label>
                        <div class="input-group">
                            <i class="fas fa-lock"></i>
                            <input type="password" id="signupPassword" name="password" placeholder="Minimum 8 characters" required minlength="8">
                            <button type="button" class="password-toggle" onclick="toggleSignupPassword()">
                                <i class="fas fa-eye" id="signupToggleIcon"></i>
                            </button>
                        </div>
                    </div>
                    
                    <button type="submit" class="submit-btn" id="signupBtn">
                        <i class="fas fa-user-plus"></i>
                        <span>Create Account</span>
                    </button>
                </form>
            </div>
            <div class="modal-footer">
                <p>Already have an account?</p>
                <a href="#" onclick="closeSignupModal(); openLoginModal();">Sign In</a>
                <br><br>
                <a href="investor/signup.php"><i class="fas fa-chart-line"></i> Become an Investor</a>
            </div>
        </div>
    </div>
    
    <script>
        // Mobile Menu Toggle
        const mobileMenuBtn = document.querySelector('.mobile-menu-btn');
        const closeMenuBtn = document.querySelector('.close-menu');
        const mobileMenu = document.getElementById('mobile-menu');
        
        mobileMenuBtn.addEventListener('click', () => {
            mobileMenu.classList.add('active');
            document.body.style.overflow = 'hidden';
        });
        
        closeMenuBtn.addEventListener('click', () => {
            mobileMenu.classList.remove('active');
            document.body.style.overflow = 'auto';
        });
        
        function closeMobileMenu() {
            mobileMenu.classList.remove('active');
            document.body.style.overflow = 'auto';
        }
        
        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                const targetId = this.getAttribute('href');
                if (targetId === '#') return;
                
                e.preventDefault();
                
                const targetElement = document.querySelector(targetId);
                if (targetElement) {
                    // Close mobile menu if open
                    mobileMenu.classList.remove('active');
                    document.body.style.overflow = 'auto';
                    
                    window.scrollTo({
                        top: targetElement.offsetTop - 80,
                        behavior: 'smooth'
                    });
                }
            });
        });
        
        // Simple animation for feature cards on scroll
        const featureCards = document.querySelectorAll('.feature-card');
        
        function checkScroll() {
            featureCards.forEach(card => {
                const cardPosition = card.getBoundingClientRect().top;
                const screenPosition = window.innerHeight / 1.3;
                
                if (cardPosition < screenPosition) {
                    card.style.opacity = 1;
                    card.style.transform = 'translateY(0)';
                }
            });
        }
        
        // Initialize feature card opacity for animation
        featureCards.forEach(card => {
            card.style.opacity = 0;
            card.style.transform = 'translateY(20px)';
            card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
        });
        
        window.addEventListener('scroll', checkScroll);
        window.addEventListener('load', checkScroll);
        
        // Video Modal Functions
        function openVideoModal() {
            document.getElementById('videoModal').classList.add('active');
            // Replace with your YouTube video ID
            document.getElementById('youtubeVideo').src = 'https://www.youtube.com/embed/dQw4w9WgXcQ?autoplay=1';
            document.body.style.overflow = 'hidden';
        }
        
        function closeVideoModal() {
            document.getElementById('videoModal').classList.remove('active');
            document.getElementById('youtubeVideo').src = '';
            document.body.style.overflow = 'auto';
        }
        
        // Close video modal on outside click
        document.getElementById('videoModal').addEventListener('click', function(e) {
            if (e.target === this) closeVideoModal();
        });
        
        // Chatbot Functions
        function toggleChatbot() {
            document.getElementById('chatbotWidget').classList.toggle('active');
        }
        
        const faqAnswers = {
            'What is RxHub?': 'RxHub is Africa\'s leading B2B pharmaceutical procurement platform. We connect healthcare providers directly to verified manufacturers with anti-counterfeit tracking and reliable last-mile delivery.',
            'How do I place an order?': 'Simply sign up for an account, browse our product catalog, add items to your cart, and checkout. You can pay via bank transfer, POS, or cash on delivery.',
            'What payment methods do you accept?': 'We accept bank transfers, POS payments, and cash on delivery. For larger orders, we also offer credit terms for verified healthcare facilities.',
            'How can I become an investor?': 'Click on the "Investors" button in our navigation or visit investor/login.php to create an investor account. You\'ll have access to various investment opportunities in pharmaceutical supply chain infrastructure.'
        };
        
        function askFAQ(question) {
            const messagesDiv = document.getElementById('chatbotMessages');
            
            // Add user message
            messagesDiv.innerHTML += `
                <div class="chatbot-message user">
                    <div class="bubble">${question}</div>
                </div>
            `;
            
            // Add bot response
            setTimeout(() => {
                const answer = faqAnswers[question] || 'I\'m not sure about that. Please contact our support team at support@rxhub.com.ng for more help.';
                messagesDiv.innerHTML += `
                    <div class="chatbot-message bot">
                        <div class="bubble">${answer}</div>
                    </div>
                `;
                messagesDiv.scrollTop = messagesDiv.scrollHeight;
            }, 500);
            
            messagesDiv.scrollTop = messagesDiv.scrollHeight;
        }
        
        // Contact Form Submission
        const contactForm = document.getElementById('contactForm');
        if (contactForm) {
            contactForm.addEventListener('submit', function(e) {
                e.preventDefault();
                alert('Thank you for your message! Our team will contact you shortly.');
                this.reset();
            });
        }
        
        // Modal Functions
        function openLoginModal() {
            document.getElementById('loginModal').classList.add('active');
            document.body.style.overflow = 'hidden';
        }
        
        function closeLoginModal() {
            document.getElementById('loginModal').classList.remove('active');
            document.body.style.overflow = 'auto';
            document.getElementById('loginForm').reset();
            document.getElementById('loginSuccessAlert').style.display = 'none';
            document.getElementById('loginErrorAlert').style.display = 'none';
        }
        
        function openSignupModal() {
            document.getElementById('signupModal').classList.add('active');
            document.body.style.overflow = 'hidden';
        }
        
        function closeSignupModal() {
            document.getElementById('signupModal').classList.remove('active');
            document.body.style.overflow = 'auto';
            document.getElementById('signupForm').reset();
            document.getElementById('signupSuccessAlert').style.display = 'none';
            document.getElementById('signupErrorAlert').style.display = 'none';
        }
        
        // Close modals when clicking outside
        document.getElementById('loginModal').addEventListener('click', function(e) {
            if (e.target === this) closeLoginModal();
        });
        
        document.getElementById('signupModal').addEventListener('click', function(e) {
            if (e.target === this) closeSignupModal();
        });
        
        // Password toggle functions
        function toggleLoginPassword() {
            const passwordInput = document.getElementById('loginPassword');
            const toggleIcon = document.getElementById('loginToggleIcon');
            
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
        
        function toggleSignupPassword() {
            const passwordInput = document.getElementById('signupPassword');
            const toggleIcon = document.getElementById('signupToggleIcon');
            
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
        
        // Login Form Submission
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const email = document.getElementById('loginEmail').value.trim();
            const password = document.getElementById('loginPassword').value;
            const loginBtn = document.getElementById('loginBtn');
            const successAlert = document.getElementById('loginSuccessAlert');
            const errorAlert = document.getElementById('loginErrorAlert');
            
            // Hide any existing alerts
            successAlert.style.display = 'none';
            errorAlert.style.display = 'none';
            
            // Disable button during submission
            loginBtn.disabled = true;
            loginBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Signing in...';
            
            // Submit to API
            fetch('login.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ email, password })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    successAlert.textContent = 'Login successful! Redirecting to dashboard...';
                    successAlert.style.display = 'block';
                    
                    // Store user info in localStorage
                    localStorage.setItem('rxhub_user', JSON.stringify(data.user));
                    
                    // Redirect to dashboard after success
                    setTimeout(() => {
                        window.location.href = 'user/dashboard.php';
                    }, 1500);
                } else {
                    errorAlert.textContent = data.message;
                    errorAlert.style.display = 'block';
                    
                    loginBtn.disabled = false;
                    loginBtn.innerHTML = '<i class="fas fa-sign-in-alt"></i> Sign In';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                errorAlert.textContent = 'An error occurred. Please try again.';
                errorAlert.style.display = 'block';
                
                loginBtn.disabled = false;
                loginBtn.innerHTML = '<i class="fas fa-sign-in-alt"></i> Sign In';
            });
        });
        
        // Signup Form Submission
        document.getElementById('signupForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const signupBtn = document.getElementById('signupBtn');
            const successAlert = document.getElementById('signupSuccessAlert');
            const errorAlert = document.getElementById('signupErrorAlert');
            
            // Hide any existing alerts
            successAlert.style.display = 'none';
            errorAlert.style.display = 'none';
            
            // Prepare form data
            const formData = {
                full_name: document.getElementById('signupName').value.trim(),
                email: document.getElementById('signupEmail').value.trim(),
                password: document.getElementById('signupPassword').value,
                facility_name: document.getElementById('signupFacility').value.trim(),
                facility_type: document.getElementById('signupFacilityType').value,
                phone: document.getElementById('signupPhone').value.trim()
            };
            
            // Disable button during submission
            signupBtn.disabled = true;
            signupBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating account...';
            
            // Submit to API
            fetch('api/register.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(formData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    successAlert.textContent = 'Account created successfully! Please log in.';
                    successAlert.style.display = 'block';
                    
                    // Switch to login modal after 2 seconds
                    setTimeout(() => {
                        closeSignupModal();
                        openLoginModal();
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