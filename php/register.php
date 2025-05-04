<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $hashed_password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
    $stmt->execute([$_POST['username'], $hashed_password]);
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --dark-bg: #0f0e17;
            --card-bg: #1a1a2e;
            --lavender: #a786df;
            --light-lavender: #d1bcf3;
            --text-primary: #e6e6e6;
            --text-secondary: #b8b8b8;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--dark-bg);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            color: var(--text-primary);
            background-image: radial-gradient(circle at 75% 50%, rgba(167, 134, 223, 0.1) 0%, transparent 50%);
        }
        
        .register-container {
            background-color: var(--card-bg);
            padding: 2.5rem;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            width: 100%;
            max-width: 420px;
            margin: 1rem;
            border: 1px solid rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(5px);
        }
        
        .register-title {
            text-align: center;
            margin-bottom: 2rem;
            color: var(--lavender);
            font-weight: 600;
            font-size: 1.8rem;
            letter-spacing: -0.5px;
            text-shadow: 0 2px 10px rgba(167, 134, 223, 0.3);
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-control {
            border-radius: 8px;
            padding: 0.75rem 1rem;
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
            font-size: 0.95rem;
            background-color: rgba(255, 255, 255, 0.05);
            color: var(--text-primary);
        }
        
        .form-control::placeholder {
            color: var(--text-secondary);
            opacity: 0.6;
        }
        
        .form-control:focus {
            border-color: var(--lavender);
            box-shadow: 0 0 0 3px rgba(167, 134, 223, 0.2);
            background-color: rgba(255, 255, 255, 0.08);
            color: var(--text-primary);
        }
        
        .form-label {
            font-weight: 500;
            margin-bottom: 0.5rem;
            color: var(--light-lavender);
            font-size: 0.9rem;
        }
        
        .btn-register {
            background: linear-gradient(135deg, var(--lavender) 0%, #8a6ddf 100%);
            border: none;
            width: 100%;
            border-radius: 8px;
            padding: 0.75rem;
            font-weight: 500;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            margin-top: 0.5rem;
            color: #0f0e17;
            box-shadow: 0 4px 15px rgba(167, 134, 223, 0.3);
        }
        
        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(167, 134, 223, 0.4);
        }
        
        .btn-register:active {
            transform: translateY(0);
        }
        
        .login-link {
            text-align: center;
            margin-top: 1.5rem;
            color: var(--text-secondary);
            font-size: 0.9rem;
        }
        
        .login-link a {
            color: var(--light-lavender);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.2s ease;
            position: relative;
        }
        
        .login-link a:hover {
            color: var(--lavender);
        }
        
        .login-link a::after {
            content: '';
            position: absolute;
            width: 100%;
            height: 1px;
            bottom: -2px;
            left: 0;
            background-color: var(--lavender);
            transform: scaleX(0);
            transform-origin: right;
            transition: transform 0.3s ease;
        }
        
        .login-link a:hover::after {
            transform: scaleX(1);
            transform-origin: left;
        }
        
        /* Animation for the container */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .register-container {
            animation: fadeIn 0.6s cubic-bezier(0.22, 1, 0.36, 1) forwards;
        }
        
        /* Floating particles */
        .particle {
            position: absolute;
            background-color: rgba(167, 134, 223, 0.3);
            border-radius: 50%;
            pointer-events: none;
        }
        
        /* Password requirements */
        .password-hints {
            color: var(--text-secondary);
            font-size: 0.8rem;
            margin-top: 0.5rem;
            padding-left: 1rem;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <h2 class="register-title">Create Account</h2>
        <form method="POST">
            <div class="form-group">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" placeholder="Choose your username" required>
            </div>
            <div class="form-group">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="Create a password" required>
                <ul class="password-hints">
                    <li>At least 8 characters</li>
                    <li>One uppercase letter</li>
                    <li>One number</li>
                </ul>
            </div>
            <button type="submit" class="btn btn-register">Register</button>
        </form>
        <p class="login-link">Already have an account? <a href="login.php">Sign in</a></p>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const body = document.body;
            const particleCount = 15;
            
            for (let i = 0; i < particleCount; i++) {
                const particle = document.createElement('div');
                particle.classList.add('particle');
                
                const size = Math.random() * 4 + 2;
                particle.style.width = `${size}px`;
                particle.style.height = `${size}px`;
                
                particle.style.left = `${Math.random() * 100}vw`;
                particle.style.top = `${Math.random() * 100}vh`;
                
                const duration = Math.random() * 20 + 10;
                particle.style.animation = `float ${duration}s linear infinite`;
                
                document.body.appendChild(particle);
            }
        });
    </script>
    <style>
        @keyframes float {
            0% {
                transform: translateY(0) translateX(0);
                opacity: 0;
            }
            10% {
                opacity: 1;
            }
            90% {
                opacity: 1;
            }
            100% {
                transform: translateY(-100vh) translateX(20px);
                opacity: 0;
            }
        }
    </style>
</body>
</html>