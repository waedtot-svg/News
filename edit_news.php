<?php
session_start();
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}

$pdo = new PDO('mysql:host=localhost;dbname=newsdb;charset=utf8mb4', 'root', '', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

$id = $_GET['id'] ?? null;
if (!$id) {
  header("Location: show_news.php");
  exit;
}

$stmt = $pdo->prepare("SELECT * FROM news WHERE id = ?");
$stmt->execute([$id]);
$news = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$news) {
  die("News not found.");
}

$categories = $pdo->query("SELECT id, name FROM categories ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);

$errors = [];
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $category_id = $_POST['category_id'] ?? '';
    $body = trim($_POST['body'] ?? '');
    $image_path = $news['image_path'];

    if ($title === '' || $body === '' || $category_id === '') {
        $errors[] = "Please fill in all fields.";
    }

    if (!empty($_FILES['image']['name'])) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $file_name = $_FILES['image']['name'];
        $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        if (in_array($ext, $allowed)) {
            $new_name = uniqid() . "." . $ext;
            $target = "uploads/" . $new_name;
            if (!is_dir("uploads")) mkdir("uploads");
            move_uploaded_file($_FILES['image']['tmp_name'], $target);
            $image_path = $target;
        } else {
            $errors[] = "Only JPG, PNG, or GIF images allowed.";
        }
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("UPDATE news SET title=?, category_id=?, body=?, image_path=?, updated_at=NOW() WHERE id=?");
        $stmt->execute([$title, $category_id, $body, $image_path, $id]);
        $success = "News updated successfully!";
        $stmt = $pdo->prepare("SELECT * FROM news WHERE id = ?");
        $stmt->execute([$id]);
        $news = $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Edit News</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      min-height: 100vh;
      background: linear-gradient(135deg, #ffb6c1 0%, #b19cd9 100%);
      display: flex;
      align-items: center;
      justify-content: center;
      font-family: 'Poppins', sans-serif;
      padding: 20px;
    }

    .edit-card {
      background: rgba(255, 255, 255, 0.95);
      border-radius: 20px;
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
      padding: 2.5rem;
      width: 100%;
      max-width: 600px;
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

    .form-control, .form-select {
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

    img.preview {
      width: 120px;
      height: 90px;
      object-fit: cover;
      border-radius: 10px;
      margin-top: 10px;
      border: 2px solid #ba55d3;
    }

    .alert-success {
      background-color: #e9c6f3;
      border-color: #ba55d3;
      color: #663399;
    }
  </style>
</head>
<body>
  <div class="edit-card">
    <h3>Edit News</h3>

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

    <form method="post" enctype="multipart/form-data">
      <div class="mb-3">
        <label class="form-label">Title</label>
        <input type="text" name="title" class="form-control" required 
               value="<?= htmlspecialchars($news['title']) ?>">
      </div>

      <div class="mb-3">
        <label class="form-label">Category</label>
        <select name="category_id" class="form-select" required>
          <option value="">-- Select Category --</option>
          <?php foreach ($categories as $cat): ?>
            <option value="<?= $cat['id'] ?>" <?= ($cat['id'] == $news['category_id']) ? 'selected' : '' ?>>
              <?= htmlspecialchars($cat['name']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="mb-3">
        <label class="form-label">Details</label>
        <textarea name="body" class="form-control" rows="5" required><?= htmlspecialchars($news['body']) ?></textarea>
      </div>

      <div class="mb-3">
        <label class="form-label">Current Image</label><br>
        <?php if ($news['image_path']): ?>
          <img src="<?= htmlspecialchars($news['image_path']) ?>" class="preview">
        <?php else: ?>
          <p class="text-muted">No image uploaded</p>
        <?php endif; ?>
      </div>

      <div class="mb-3">
        <label class="form-label">Change Image (optional)</label>
        <input type="file" name="image" class="form-control" accept="image/*">
      </div>

      <button class="btn btn-primary w-100">Update News</button>
    </form>

    <div class="back-link">
      <a href="show_news.php">← Back to All News</a>
    </div>
  </div>
</body>
</html>
