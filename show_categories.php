<?php
session_start();
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}

$pdo = new PDO('mysql:host=localhost;dbname=newsdb;charset=utf8mb4', 'root', '', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

// جلب كل الفئات من قاعدة البيانات
$stmt = $pdo->query("SELECT * FROM categories ORDER BY id DESC");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>All Categories</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      min-height: 100vh;
      background: linear-gradient(135deg, #ffb6c1 0%, #b19cd9 100%);
      font-family: 'Poppins', sans-serif;
      color: #333;
      display: flex;
      flex-direction: column;
      align-items: center;
      padding: 40px 0;
    }

    .categories-container {
      background: rgba(255, 255, 255, 0.95);
      border-radius: 20px;
      box-shadow: 0 8px 24px rgba(0,0,0,0.15);
      width: 90%;
      max-width: 700px;
      padding: 30px;
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

    thead {
      background: linear-gradient(135deg, #ff69b4, #ba55d3);
      color: white;
    }

    tbody tr:hover {
      background-color: #f9ecff;
    }

    .btn-back {
      margin-top: 20px;
      border-radius: 25px;
      background: linear-gradient(135deg, #ff69b4, #ba55d3);
      border: none;
      color: white;
      font-weight: 600;
      padding: 10px 25px;
      transition: all 0.3s;
      text-decoration: none;
      display: inline-block;
    }

    .btn-back:hover {
      background: linear-gradient(135deg, #ff85c1, #a26dd4);
      transform: scale(1.03);
    }

  </style>
</head>
<body>

  <div class="categories-container">
    <h3>All Categories</h3>

    <?php if (count($categories) > 0): ?>
      <div class="table-responsive">
        <table class="table table-striped align-middle">
          <thead>
            <tr>
              <th>ID</th>
              <th>Category Name</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($categories as $cat): ?>
              <tr>
                <td><?= htmlspecialchars($cat['id']) ?></td>
                <td><?= htmlspecialchars($cat['name']) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php else: ?>
      <p class="text-center text-muted">No categories found</p>
    <?php endif; ?>

    <div class="text-center">
      <a href="dashboard.php" class="btn-back">← Back to Dashboard</a>
    </div>
  </div>

</body>
</html>
