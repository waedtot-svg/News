<?php
$pdo = new PDO('mysql:host=localhost;dbname=newsdb;charset=utf8mb4', 'root', '', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Invalid email address.";
    if ($password === '') $errors[] = "Password is required.";

    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        if (!$user || !password_verify($password, $user['password'])) {
            $errors[] = "Incorrect email or password.";
        } else {
            session_start();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            header("Location: dashboard.php");
            exit;
        }
    }
}
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Login</title>
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

    .login-card {
      background: rgba(255, 255, 255, 0.95);
      border-radius: 20px;
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
      padding: 2.5rem;
      width: 100%;
      max-width: 420px;
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

    .text-link a {
      color: #ba55d3;
      text-decoration: none;
      font-weight: 600;
    }

    .text-link a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <div class="login-card">
    <h3>Welcome Back</h3>

    <?php if ($errors): ?>
      <div class="alert alert-danger">
        <ul class="mb-0">
          <?php foreach ($errors as $e) echo "<li>$e</li>"; ?>
        </ul>
      </div>
    <?php endif; ?>

    <form method="post">
      <div class="mb-3">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control"
               value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Password</label>
        <input type="password" name="password" class="form-control" required>
      </div>

      <button class="btn btn-primary w-100">Login</button>

      <p class="text-center mt-3 text-link">
        Don’t have an account? <a href="register.php">Sign up</a>
      </p>
    </form>
  </div>
</body>
</html>
