<?php
session_start();
$error = '';

if (isset($_POST['login'])) {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($username === 'admin' && $password === 'admin') {
        $_SESSION['logged_in'] = true;
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Invalid username or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Bread of Life Kanyama - Login</title>
  <style>
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body.login-body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: linear-gradient(135deg, #dff1ff, #c1e0ff);
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      overflow: hidden;
    }

    .login-container {
      background-color: white;
      border-radius: 15px;
      padding: 40px 30px;
      width: 100%;
      max-width: 420px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
      animation: fadeIn 1s ease-in;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(-20px); }
      to { opacity: 1; transform: translateY(0); }
    }

    .logo-area {
      font-size: 24px;
      font-weight: bold;
      color: #004aad;
      text-align: center;
      margin-bottom: 10px;
    }

    .animated-title {
      font-size: 20px;
      color: #003366;
      text-align: center;
      white-space: nowrap;
      overflow: hidden;
      border-right: 3px solid #003366;
      width: 0;
      animation: typing 4s steps(40, end) forwards, blink 0.75s step-end infinite;
      margin: 0 auto 25px;
    }

    @keyframes typing {
      from { width: 0; }
      to { width: 100%; }
    }

    @keyframes blink {
      50% { border-color: transparent; }
    }

    .login-form {
      display: flex;
      flex-direction: column;
    }

    .input-group {
      position: relative;
      margin-bottom: 18px;
    }

    .input-group input {
      width: 100%;
      padding: 12px 12px 12px 40px;
      border: 1px solid #b3cce6;
      border-radius: 6px;
      font-size: 16px;
      transition: border-color 0.3s;
    }

    .input-group input:focus {
      outline: none;
      border-color: #0073e6;
      box-shadow: 0 0 6px rgba(0, 115, 230, 0.3);
    }

    .input-group i {
      position: absolute;
      left: 12px;
      top: 50%;
      transform: translateY(-50%);
      color: #7da5cc;
    }

    .btn-primary {
      background-color: #0073e6;
      color: white;
      padding: 12px;
      width: 100%;
      border: none;
      border-radius: 6px;
      font-size: 16px;
      font-weight: bold;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }

    .btn-primary:hover {
      background-color: #005bb5;
    }

    .error-msg {
      background-color: #ffdddd;
      color: #cc0000;
      border: 1px solid #cc0000;
      padding: 10px;
      margin-bottom: 15px;
      border-radius: 5px;
      text-align: center;
    }

    footer {
      margin-top: 25px;
      text-align: center;
      font-size: 12px;
      color: #666;
    }

    @media (max-width: 480px) {
      .login-container {
        padding: 30px 20px;
      }
    }
  </style>
  <!-- Icons from Font Awesome CDN -->
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</head>
<body class="login-body">
  <div class="login-container">
    <div class="logo-area">Bread of Life Kanyama</div>
    <div class="animated-title">Welcome, Admin. Please Log In</div>

    <?php if ($error): ?>
      <div class="error-msg"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" class="login-form">
      <div class="input-group">
        <i class="fas fa-user"></i>
        <input type="text" name="username" placeholder="Username" required autofocus />
      </div>
      <div class="input-group">
        <i class="fas fa-lock"></i>
        <input type="password" name="password" placeholder="Password" required />
      </div>
      <button type="submit" name="login" class="btn-primary">Login</button>
    </form>

    <footer>© <?= date('Y') ?> Bread of Life Church - Kanyama</footer>
  </div>
</body>
</html>
