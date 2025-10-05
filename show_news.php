<?php
session_start();
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}

$pdo = new PDO('mysql:host=localhost;dbname=newsdb;charset=utf8mb4', 'root', '', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    $stmt = $pdo->prepare("UPDATE news SET status = 'deleted' WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: show_news.php");
    exit;
}

$stmt = $pdo->query("
    SELECT news.*, categories.name AS category_name, users.name AS author
    FROM news
    JOIN categories ON news.category_id = categories.id
    JOIN users ON news.user_id = users.id
    WHERE news.status = 'active'
    ORDER BY news.id DESC
");
$news = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>All News</title>
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

    .news-table {
      background: rgba(255, 255, 255, 0.95);
      border-radius: 20px;
      box-shadow: 0 8px 24px rgba(0,0,0,0.15);
      width: 95%;
      max-width: 1000px;
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

    .edit-btn {
      background-color: #663399;
    }

    .delete-btn {
      background-color: #d11a2a;
    }

    .edit-btn:hover {
      background-color: #7b3fcf;
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

  <div class="news-table">
    <h3>All Active News</h3>

    <?php if (count($news) > 0): ?>
      <div class="table-responsive">
        <table class="table table-striped align-middle">
          <thead>
            <tr>
              <th>ID</th>
              <th>Title</th>
              <th>Category</th>
              <th>Author</th>
              <th>Image</th>
              <th>Created At</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($news as $n): ?>
              <tr>
                <td><?= htmlspecialchars($n['id']) ?></td>
                <td><?= htmlspecialchars($n['title']) ?></td>
                <td><?= htmlspecialchars($n['category_name']) ?></td>
                <td><?= htmlspecialchars($n['author']) ?></td>
                <td>
                  <?php if ($n['image_path']): ?>
                    <img src="<?= htmlspecialchars($n['image_path']) ?>" class="thumb">
                  <?php else: ?>
                    <span class="text-muted">No image</span>
                  <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($n['created_at']) ?></td>
                <td class="action-btns">
                  <a href="edit_news.php?id=<?= $n['id'] ?>" class="edit-btn">Edit</a>
                  <a href="show_news.php?delete=<?= $n['id'] ?>" 
                     onclick="return confirm('Are you sure you want to delete this news?')" 
                     class="delete-btn">Delete</a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php else: ?>
      <p class="text-center text-muted">No active news found</p>
    <?php endif; ?>

    <div class="text-center">
      <a href="dashboard.php" class="btn-back">← Back to Dashboard</a>
    </div>
  </div>

</body>
</html>
