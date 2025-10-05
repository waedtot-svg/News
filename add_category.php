<?php
session_start();

if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}

$pdo = new PDO('mysql:host=localhost;dbname=newsdb;charset=utf8mb4', 'root', '', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

$errors = [];
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');

    if ($name === '') {
        $errors[] = "Category name is required.";
    } else {
        // Check if category already exists
        $stmt = $pdo->prepare("SELECT id FROM categories WHERE name = ?");
        $stmt->execute([$name]);
        if ($stmt->fetch()) {
            $errors[] = "This category already exists.";
        } else {
            // Insert new category
            $stmt = $pdo->prepare("INSERT INTO categories (name) VALUES (?)");
            $stmt->execute([$name]);
            $success = "Category added successfully!";
        }
    }
}
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Add Category</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      min-height: 100vh;
      background: linear-gradient(135deg, #ffb6c1 0%, #b19cd9 100%);
      display: flex;
      align-items: center;
      justify-content: center;
      font-family: 'Poppins', sans-serif;
    }

    .category-card {
      background: rgba(255, 255, 255, 0.95);
      border-radius: 20px;
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
      padding: 2.5rem;
      width: 100%;
      max-width: 450px;
      backdrop-filter: blur(10px);
      animation: fadeIn 0.6s ease;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }

    h3 {
      color: #663399;
      font-weight: 700;
      text-align: center;
      margin-bottom: 1.5rem;
    }

    .form-label {
      color: #444;
      font-weight: 500;
    }

    .form-control {
      border-radius: 12px;
      padding: 10px 14px;
      border: 1px solid #ccc;
    }

    .btn-primary {
      background: linear-gradient(135deg, #ff69b4, #ba55d3);
      border: none;
      border-radius: 25px;
      font-weight: 600;
      padding: 10px;
      transition: all 0.3s ease;
    }

    .btn-primary:hover {
      background: linear-gradient(135deg, #ff85c1, #a26dd4);
      transform: scale(1.02);
    }

    .back-link {
      text-align: center;
      margin-top: 15px;
    }

    .back-link a {
      color: #ba55d3;
      font-weight: 600;
      text-decoration: none;
    }

    .back-link a:hover {
      text-decoration: underline;
    }

    .alert-success {
      background-color: #e9c6f3;
      border-color: #ba55d3;
      color: #663399;
    }
  </style>
</head>
<body>
  <div class="category-card">
    <h3>Add New Category</h3>

    <?php if ($errors): ?>
      <div class="alert alert-danger">
        <ul class="mb-0">
          <?php foreach ($errors as $e) echo "<li>$e</li>"; ?>
        </ul>
      </div>
    <?php endif; ?>

    <?php if ($success): ?>
      <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form method="post">
      <div class="mb-3">
        <label class="form-label">Category Name</label>
        <input type="text" name="name" class="form-control" required 
               value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
      </div>
      <button class="btn btn-primary w-100">Add Category</button>
    </form>

    <div class="back-link">
      <a href="dashboard.php">← Back to Dashboard</a>
    </div>
  </div>
</body>
</html>
