<?php
session_start();

$_SESSION = array();
session_destroy();
header("Location: login.php");
exit;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logging Out...</title>
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
            background-image: radial-gradient(circle at 25% 50%, rgba(167, 134, 223, 0.1) 0%, transparent 50%);
        }

        .logout-container {
            background-color: var(--card-bg);
            padding: 2.5rem;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            width: 100%;
            max-width: 420px;
            margin: 1rem;
            border: 1px solid rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(5px);
            text-align: center;
        }

        .logout-title {
            color: var(--lavender);
            font-weight: 600;
            font-size: 1.8rem;
            margin-bottom: 1.5rem;
            letter-spacing: -0.5px;
            text-shadow: 0 2px 10px rgba(167, 134, 223, 0.3);
        }

        .logout-message {
            color: var(--text-secondary);
            font-size: 1rem;
            margin-bottom: 1.5rem;
        }

        .logout-link {
            color: var(--light-lavender);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.2s ease;
            position: relative;
        }

        .logout-link:hover {
            color: var(--lavender);
        }

        .logout-link::after {
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

        .logout-link:hover::after {
            transform: scaleX(1);
            transform-origin: left;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .logout-container {
            animation: fadeIn 0.6s cubic-bezier(0.22, 1, 0.36, 1) forwards;
        }
    </style>
</head>
<body>
    <div class="logout-container">
        <h2 class="logout-title">Logging Out</h2>
        <p class="logout-message">You are now being logged out.</p>
        <p class="logout-message">Redirecting to <a href="login.php" class="logout-link">login page</a>...</p>
    </div>

    <script>
        setTimeout(function() {
            window.location.href = "login.php";
        }, 1500);
    </script>
</body>
</html>