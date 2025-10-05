<?php
session_start();

if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}

$pdo = new PDO('mysql:host=localhost;dbname=newsdb;charset=utf8mb4', 'root', '', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

if (isset($_GET['restore'])) {
    $id = (int) $_GET['restore'];
    $stmt = $pdo->prepare("UPDATE news SET status = 'active' WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: show_deleted_news.php");
    exit;
}

if (isset($_GET['permanent_delete'])) {
    $id = (int) $_GET['permanent_delete'];
    $stmt = $pdo->prepare("DELETE FROM news WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: show_deleted_news.php");
    exit;
}

$stmt = $pdo->query("
    SELECT news.*, categories.name AS category_name, users.name AS author
    FROM news
    JOIN categories ON news.category_id = categories.id
    JOIN users ON news.user_id = users.id
    WHERE news.status = 'deleted'
    ORDER BY news.id DESC
");
$deleted_news = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Deleted News</title>
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

    .deleted-news {
      background: rgba(255, 255, 255, 0.95);
      border-radius: 20px;
      box-shadow: 0 8px 24px rgba(0,0,0,0.15);
      width: 95%;
      max-width: 1100px;
      padding: 25px;
      backdrop-filter: blur(8px);
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

    .action-btns a {
      text-decoration: none;
      margin: 0 5px;
      font-weight: 600;
      border-radius: 6px;
      padding: 6px 12px;
      color: white;
      transition: all 0.3s ease;
    }

    .restore-btn {
      background-color: #28a745;
    }

    .delete-btn {
      background-color: #d11a2a;
    }

    .restore-btn:hover {
      background-color: #23913d;
    }

    .delete-btn:hover {
      background-color: #b00f1d;
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

    img.thumb {
      width: 70px;
      height: 50px;
      object-fit: cover;
      border-radius: 8px;
    }
  </style>
</head>
<body>

  <div class="deleted-news">
    <h3>Deleted News</h3>

    <?php if (count($deleted_news) > 0): ?>
      <div class="table-responsive">
        <table class="table table-striped align-middle">
          <thead>
            <tr>
              <th>ID</th>
              <th>Title</th>
              <th>Category</th>
              <th>Details</th>
              <th>Author (User)</th>
              <th>Image</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($deleted_news as $n): ?>
              <tr>
                <td><?= htmlspecialchars($n['id']) ?></td>
                <td><?= htmlspecialchars($n['title']) ?></td>
                <td><?= htmlspecialchars($n['category_name']) ?></td>
                <td style="max-width:300px;"><?= htmlspecialchars($n['details']) ?></td>
                <td><?= htmlspecialchars($n['user_id']) ?></td>
                <td>
                  <?php if ($n['image_path']): ?>
                    <img src="<?= htmlspecialchars($n['image_path']) ?>" class="thumb">
                  <?php else: ?>
                    <span class="text-muted">No image</span>
                  <?php endif; ?>
                </td>
                <td class="action-btns">
                  <a href="show_deleted_news.php?restore=<?= $n['id'] ?>" 
                     onclick="return confirm('Are you sure you want to restore this news?')" 
                     class="restore-btn">Restore</a>
                  <a href="show_deleted_news.php?permanent_delete=<?= $n['id'] ?>" 
                     onclick="return confirm('Are you sure you want to delete this permanently?')" 
                     class="delete-btn">Delete</a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php else: ?>
      <p class="text-center text-muted">No deleted news found</p>
    <?php endif; ?>

    <div class="text-center">
      <a href="dashboard.php" class="btn-back">← Back to Dashboard</a>
    </div>
  </div>

</body>
</html>
