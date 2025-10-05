<?php
session_start();
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}

$user_name = $_SESSION['user_name'] ?? 'User';


$pdo = new PDO('mysql:host=localhost;dbname=newsdb;charset=utf8mb4', 'root', '', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

$total_categories = $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn();
$total_news = $pdo->query("SELECT COUNT(*) FROM news WHERE status='active'")->fetchColumn();
$total_deleted = $pdo->query("SELECT COUNT(*) FROM news WHERE status='deleted'")->fetchColumn();
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      min-height: 100vh;
      background: linear-gradient(135deg, #ffb6c1 0%, #b19cd9 100%);
      font-family: 'Poppins', sans-serif;
      color: #333;
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 40px 0;
    }

    .dashboard-container {
      background: rgba(255, 255, 255, 0.95);
      border-radius: 20px;
      box-shadow: 0 8px 24px rgba(0,0,0,0.15);
      width: 95%;
      max-width: 1000px;
      padding: 40px;
      text-align: center;
      animation: fadeIn 0.6s ease;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }

    h2 {
      color: #663399;
      font-weight: 700;
      margin-bottom: 30px;
    }

    .grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 20px;
      margin-bottom: 30px;
    }

    .card-box {
      background: linear-gradient(135deg, #ff69b4, #ba55d3);
      border-radius: 20px;
      padding: 25px;
      color: white;
      text-align: center;
      font-weight: 600;
      box-shadow: 0 6px 18px rgba(0,0,0,0.15);
      transition: all 0.3s ease;
      text-decoration: none;
      display: block;
    }

    .card-box:hover {
      transform: translateY(-5px) scale(1.02);
      background: linear-gradient(135deg, #ff85c1, #a26dd4);
    }

    .card-box h4 {
      margin-bottom: 10px;
      font-size: 20px;
      font-weight: 700;
    }

    .count {
      background: rgba(255,255,255,0.25);
      display: inline-block;
      padding: 6px 14px;
      border-radius: 15px;
      font-size: 14px;
      margin-top: 5px;
      color: #fff;
    }

    .logout-btn {
      background: #d11a2a;
      color: white;
      padding: 12px 30px;
      border-radius: 30px;
      font-weight: 600;
      border: none;
      text-decoration: none;
      transition: 0.3s;
      display: inline-block;
    }

    .logout-btn:hover {
      background: #b00f1d;
      transform: scale(1.05);
    }

  </style>
</head>
<body>

  <div class="dashboard-container">
    <h2>Welcome, <?= htmlspecialchars($user_name) ?> </h2>
    <p class="text-muted mb-4">Manage your news, categories, and more below</p>

    <div class="grid">
      <a href="add_category.php" class="card-box">
        <h4>Add Category</h4>
        <span class="count"><?= $total_categories ?> total</span>
      </a>

      <a href="show_categories.php" class="card-box">
        <h4>Show All Categories</h4>
        <span class="count"><?= $total_categories ?> total</span>
      </a>

      <a href="add_news.php" class="card-box">
        <h4>Add News</h4>
        <span class="count"><?= $total_news ?> active</span>
      </a>

      <a href="show_news.php" class="card-box">
        <h4>Show All News</h4>
        <span class="count"><?= $total_news ?> active</span>
      </a>

      <a href="show_deleted_news.php" class="card-box">
        <h4>Show Deleted News</h4>
        <span class="count"><?= $total_deleted ?> deleted</span>
      </a>
    </div>

    <a href="logout.php" class="logout-btn">Logout</a>
  </div>

</body>
</html>
