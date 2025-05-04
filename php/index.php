<?php

require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$current_user_id = $_SESSION['user_id'];
$error = '';
$success = '';

// Handle CRUD operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (isset($_POST['add_product'])) {
            if (empty($_POST['name'])) {
                throw new Exception("Product name is required");
            }
            if (!is_numeric($_POST['price'])) {
                throw new Exception("Price must be a number");
            }

            $stmt = $pdo->prepare("INSERT INTO products (name, description, price, created_by) VALUES (?, ?, ?, ?)");
            $stmt->execute([
                htmlspecialchars($_POST['name']),
                htmlspecialchars($_POST['description']),
                floatval($_POST['price']),
                $current_user_id
            ]);
            $success = "Product added successfully!";
        }

        if (isset($_POST['update_product'])) {
            $stmt = $pdo->prepare("SELECT created_by FROM products WHERE id = ?");
            $stmt->execute([$_POST['product_id']]);
            $product = $stmt->fetch();

            if (!$product) {
                throw new Exception("Product not found");
            }
            if ($product['created_by'] != $current_user_id) {
                throw new Exception("You can only update your own products");
            }

            $stmt = $pdo->prepare("UPDATE products SET name = ?, description = ?, price = ?, updated_by = ?, updated_at = NOW() WHERE id = ?");
            $stmt->execute([
                htmlspecialchars($_POST['name']),
                htmlspecialchars($_POST['description']),
                floatval($_POST['price']),
                $current_user_id,
                $_POST['product_id']
            ]);
            $success = "Product updated successfully!";
        }

        if (isset($_POST['delete_product'])) {
            $stmt = $pdo->prepare("SELECT created_by FROM products WHERE id = ?");
            $stmt->execute([$_POST['product_id']]);
            $product = $stmt->fetch();

            if (!$product) {
                throw new Exception("Product not found");
            }
            if ($product['created_by'] != $current_user_id) {
                throw new Exception("You can only delete your own products");
            }

            $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
            $stmt->execute([$_POST['product_id']]);
            $success = "Product deleted successfully!";
        }

        if (isset($_POST['add_review'])) {
            if (empty($_POST['rating']) || empty($_POST['comment'])) {
                throw new Exception("Rating and comment are required");
            }

            $stmt = $pdo->prepare("INSERT INTO reviews (product_id, user_id, rating, comment) VALUES (?, ?, ?, ?)");
            $stmt->execute([
                $_POST['product_id'],
                $current_user_id,
                $_POST['rating'],
                htmlspecialchars($_POST['comment'])
            ]);
            $success = "Review added successfully!";
        }

    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// Fetch all products with creator usernames
$products = $pdo->query("
    SELECT p.*, u.username as creator_name
    FROM products p
    LEFT JOIN users u ON p.created_by = u.id
    ORDER BY p.id DESC
")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --dark-bg: #0f0e17;
            --card-bg: #1a1a2e;
            --lavender: #a786df;
            --light-lavender: #d1bcf3;
            --text-primary: #e6e6e6;
            --text-secondary: #b8b8b8;
            --accent: #6247aa;
            --danger: #e63946;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--dark-bg);
            color: var(--text-primary);
            padding-top: 2rem;
            background-image: radial-gradient(circle at 25% 50%, rgba(167, 134, 223, 0.1) 0%, transparent 50%);
        }

        .container {
            max-width: 1200px;
            animation: fadeIn 0.6s cubic-bezier(0.22, 1, 0.36, 1) forwards;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .card {
            background-color: var(--card-bg);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            margin-bottom: 1.5rem;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            overflow: visible !important
            min-height: auto !important;
        }

        .card-body {
            padding: 1.25rem;
            overflow: visible !important; 
            color: var(--text-primary) !important;
        }

        .card-text {
            margin-bottom: 1rem;
            white-space: pre-wrap;
            display: block !important; 
            min-height: auto !important; 
            color: var(--text-primary) !important;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.4);
        }

        .card-header {
            background-color: rgba(0, 0, 0, 0.2);
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 12px 12px 0 0 !important;
            padding: 1.25rem;
        }

        .form-section {
            background-color: var(--card-bg);
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            margin-bottom: 2rem;
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .form-label {
            color: var(--text-primary) !important;
        }
        .btn-primary {
            background: linear-gradient(135deg, var(--lavender) 0%, var(--accent) 100%);
            border: none;
            border-radius: 8px;
            padding: 0.75rem 1.5rem;
            font-weight: 500;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(167, 134, 223, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(167, 134, 223, 0.4);
            background: linear-gradient(135deg, var(--light-lavender) 0%, var(--lavender) 100%);
        }

        .btn-outline-danger {
            color: var(--danger);
            border-color: var(--danger);
        }

        .btn-outline-danger:hover {
            background-color: var(--danger);
            color: white;
        }

        .form-control, .form-select, .form-control:focus {
            background-color: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: var(--text-primary);
            border-radius: 8px;
            padding: 0.75rem 1rem;
        }

        .form-control:focus {
            border-color: var(--lavender);
            box-shadow: 0 0 0 3px rgba(167, 134, 223, 0.2);
            background-color: rgba(255, 255, 255, 0.08);
        }

        .form-control::placeholder {
            color: var(--text-secondary);
            opacity: 0.6;
        }

        .alert {
            border-radius: 8px;
            border: none;
        }

        .alert-danger {
            background-color: rgba(230, 57, 70, 0.15);
            color: #ff6b6b;
            border: 1px solid rgba(230, 57, 70, 0.2);
        }

        .alert-success {
            background-color: rgba(46, 213, 115, 0.15);
            color: #6bff9e;
            border: 1px solid rgba(46, 213, 115, 0.2);
        }

        .alert-info {
            background-color: rgba(41, 128, 185, 0.15);
            color: #6bb9ff;
            border: 1px solid rgba(41, 128, 185, 0.2);
        }

        .creator-badge {
            background-color: rgba(167, 134, 223, 0.2);
            color: var(--light-lavender);
            padding: 0.25rem 0.75rem;
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .review-card {
            background-color: rgba(0, 0, 0, 0.2);
            border-left: 4px solid var(--lavender);
            border-radius: 8px;
            margin-bottom: 1rem;
        }

        .star-rating {
            color: var(--lavender);
            font-size: 1.1rem;
        }

        .text-muted {
            color: var(--text-secondary) !important;
        }

        h1, h2, h3, h4, h5, h6 {
            color: var(--light-lavender);
            font-weight: 600;
        }

        .nav-divider {
            border-top: 1px solid rgba(255, 255, 255, 0.05);
            margin: 1.5rem 0;
        }

        .particle {
            position: absolute;
            background-color: rgba(167, 134, 223, 0.3);
            border-radius: 50%;
            pointer-events: none;
        }
    </style>
</head>
<body>
    <div id="particles"></div>

    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="mb-0">
                <i class="fas fa-box-open me-2" style="color: var(--lavender);"></i>
                Product Management
            </h1>
            <div>
                <span class="me-3" style="color: var(--light-lavender);">
                    <i class="fas fa-user-circle me-1"></i> User #<?= $current_user_id ?>
                </span>
                <a href="logout.php" class="btn btn-outline-danger">
                    <i class="fas fa-sign-out-alt me-1"></i> Logout
                </a>
            </div>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger mb-4"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success mb-4"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <div class="form-section">
            <h2 class="mb-4">
                <i class="fas fa-plus-circle me-2" style="color: var(--lavender);"></i>
                Add New Product
            </h2>
            <form method="POST" class="needs-validation" novalidate>
                <div class="mb-3">
                    <label for="name" class="form-label">Product Name</label>
                    <input type="text" class="form-control" id="name" name="name" placeholder="Enter product name" required>
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control" id="description" name="description" rows="3" placeholder="Enter product description"></textarea>
                </div>
                <div class="mb-3">
                    <label for="price" class="form-label">Price</label>
                    <input type="number" class="form-control" id="price" name="price" step="0.01" placeholder="0.00" required>
                </div>
                <button type="submit" name="add_product" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i> Add Product
                </button>
            </form>
        </div>

        <h2 class="mb-4">
            <i class="fas fa-list me-2" style="color: var(--lavender);"></i>
            Product List
        </h2>

        <?php if (empty($products)): ?>
            <div class="alert alert-info">No products found. Add your first product above!</div>
        <?php else: ?>
            <?php foreach ($products as $product): ?>
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><?= htmlspecialchars($product['name']) ?></h5>
                        <span class="creator-badge">
                            <i class="fas fa-user me-1"></i> <?= htmlspecialchars($product['creator_name'] ?? 'Unknown') ?>
                        </span>
                    </div>
                    <div class="card-body">
                        <p class="card-text"><?= htmlspecialchars($product['description']) ?></p>
                        <h6 class="card-subtitle mb-3" style="color: var(--lavender);">
                            Price: $<?= number_format($product['price'], 2) ?>
                        </h6>

                        <?php if ($product['created_by'] == $current_user_id): ?>
                            <form method="POST" class="mb-4">
                                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                <div class="row g-3">
                                    <div class="col-md-5">
                                        <input type="text" class="form-control" name="name" value="<?= htmlspecialchars($product['name']) ?>" required>
                                    </div>
                                    <div class="col-md-5">
                                        <input type="number" class="form-control" name="price" step="0.01" value="<?= $product['price'] ?>" required>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="d-flex gap-2">
                                            <button type="submit" name="update_product" class="btn btn-primary btn-sm">
                                                <i class="fas fa-edit"></i></button>
                                            <button type="submit" name="delete_product" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this product?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <textarea class="form-control" name="description" required><?= htmlspecialchars($product['description']) ?></textarea>
                                </div>
                            </form>
                        <?php endif; ?>

                        <div class="mt-4">
                            <h6>
                                <i class="fas fa-comments me-2" style="color: var(--lavender);"></i>
                                Reviews
                            </h6>

                            <form method="POST" class="mb-4">
                                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                <div class="row g-3 align-items-end">
                                    <div class="col-md-2">
                                        <label class="form-label">Rating</label>
                                        <select class="form-select" name="rating" required>
                                            <option value="">Select...</option>
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <option value="<?= $i ?>"><?= $i ?> Star<?= $i > 1 ? 's' : '' ?></option>
                                            <?php endfor; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-8">
                                        <label class="form-label">Your Review</label>
                                        <textarea class="form-control" name="comment" placeholder="Share your thoughts..." required></textarea>
                                    </div>
                                    <div class="col-md-2">
                                        <button type="submit" name="add_review" class="btn btn-primary w-100">
                                            <i class="fas fa-paper-plane me-1"></i> Submit
                                        </button>
                                    </div>
                                </div>
                            </form>

                            <?php
                            $reviews = $pdo->prepare("SELECT r.*, u.username
                                                    FROM reviews r
                                                    JOIN users u ON r.user_id = u.id
                                                    WHERE product_id = ?
                                                    ORDER BY r.created_at DESC");
                            $reviews->execute([$product['id']]);
                            $product_reviews = $reviews->fetchAll();

                            if (!empty($product_reviews)): ?>
                                <?php foreach ($product_reviews as $review): ?>
                                    <div class="card review-card mb-2">
                                        <div class="card-body p-3">
                                            <div class="d-flex justify-content-between mb-2">
                                                <div class="star-rating">
                                                    <?= str_repeat('<i class="fas fa-star"></i>', $review['rating']) ?>
                                                    <?= str_repeat('<i class="far fa-star"></i>', 5 - $review['rating']) ?>
                                                </div>
                                                <small class="text-muted">
                                                    <?= date('M j, Y g:i a', strtotime($review['created_at'])) ?>
                                                </small>
                                            </div>
                                            <p class="mb-1"><?= htmlspecialchars($review['comment']) ?></p>
                                            <small class="text-muted">Posted by <?= htmlspecialchars($review['username']) ?></small>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="alert alert-info">No reviews yet. Be the first to review!</div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const particlesContainer = document.getElementById('particles');
            const particleCount = 20;

            for (let i = 0; i < particleCount; i++) {
                const particle = document.createElement('div');
                particle.classList.add('particle');

                const size = Math.random() * 4 + 2;
                particle.style.width = `${size}px`;
                particle.style.height = `${size}px`;

                particle.style.left = `${Math.random() * 100}vw`;
                particle.style.top = `${Math.random() * 100}vh`;

                const duration = Math.random() * 20 + 10;
                const delay = Math.random() * 5;
                particle.style.animation = `float ${duration}s linear ${delay}s infinite`;

                particlesContainer.appendChild(particle);
            }
        });

        // Form validation
        (function () {
            'use strict'
            var forms = document.querySelectorAll('.needs-validation')
            Array.prototype.slice.call(forms)
                .forEach(function (form) {
                    form.addEventListener('submit', function (event) {
                        if (!form.checkValidity()) {
                            event.preventDefault()
                            event.stopPropagation()
                        }
                        form.classList.add('was-validated')
                    }, false)
                })
        })()
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