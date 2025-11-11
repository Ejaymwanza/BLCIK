<?php
require 'helpers.php';

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add'])) {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $password_confirm = $_POST['password_confirm'] ?? '';

        if (!$username || !$password || !$password_confirm) {
            $error = "All fields are required.";
        } elseif ($password !== $password_confirm) {
            $error = "Passwords do not match.";
        } else {
            if (add_admin($username, $password)) {
                $message = "Admin '$username' added successfully.";
            } else {
                $error = "Admin username already exists.";
            }
        }
    } elseif (isset($_POST['remove'])) {
        $remove_user = $_POST['remove_user'] ?? '';
        if ($remove_user === 'admin') {
            $error = "Cannot remove default admin.";
        } else {
            remove_admin($remove_user);
            $message = "Admin '$remove_user' removed.";
        }
    }
}

$admins = load_admins();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Manage Admins - Bread of Life Kanyama</title>
<link rel="stylesheet" href="style.css" />
</head>
<body>
  <div class="container">
    <aside class="sidebar">
      <div class="logo-area">Bread of Life Kanyama</div>
      <nav>
        <a href="dashboard.php">Dashboard</a>
        <a href="add_member.php">Add Members</a>
        <a href="manage_admins.php" class="active">Manage Admins</a>
        <a href="export.php" target="_blank">Download Members List</a>
        <a href="reports.php">Reports</a>
        <a href="logout.php">Logout</a>
      </nav>
    </aside>

    <main class="main-content">
      <h1>Manage Admins</h1>
      <?php if ($error): ?>
        <div class="error-msg"><?=htmlspecialchars($error)?></div>
      <?php elseif ($message): ?>
        <div class="success-msg"><?=htmlspecialchars($message)?></div>
      <?php endif; ?>

      <h2>Add New Admin</h2>
      <form method="POST" class="member-form" style="max-width: 400px;">
        <label>
          Username <span class="required">*</span>
          <input type="text" name="username" required />
        </label>
        <label>
          Password <span class="required">*</span>
          <input type="password" name="password" required />
        </label>
        <label>
          Confirm Password <span class="required">*</span>
          <input type="password" name="password_confirm" required />
        </label>
        <button type="submit" name="add" class="btn-primary">Add Admin</button>
      </form>

      <h2>Existing Admins</h2>
      <table class="members-table" style="max-width: 400px;">
        <thead>
          <tr><th>Username</th><th>Action</th></tr>
        </thead>
        <tbody>
          <?php foreach ($admins as $a): ?>
            <tr>
              <td><?=htmlspecialchars($a['username'])?></td>
              <td>
                <?php if ($a['username'] !== 'admin'): ?>
                <form method="POST" style="display:inline;">
                  <input type="hidden" name="remove_user" value="<?=htmlspecialchars($a['username'])?>" />
                  <button type="submit" name="remove" class="btn-secondary btn-sm" onclick="return confirm('Remove admin <?=htmlspecialchars($a['username'])?>?');">Remove</button>
                </form>
                <?php else: ?>
                  <em>Default admin</em>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </main>
  </div>
</body>
</html>
