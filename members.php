<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: index.php");
    exit;
}

require 'helpers.php';
$members = load_members();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Members List</title>
  <link rel="stylesheet" href="style.css" />
  <style>
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
      font-family: Arial, sans-serif;
    }
    th, td {
      border: 1px solid #ccc;
      padding: 8px 10px;
      text-align: left;
      vertical-align: middle;
    }
    th {
      background-color: #2980b9;
      color: white;
    }
    tr:nth-child(even) {
      background-color: #f4f7fa;
    }
    tr:hover {
      background-color: #d6e6f5;
    }
    img {
      max-width: 50px;
      max-height: 50px;
      border-radius: 4px;
      object-fit: cover;
    }
    a.view-profile {
      background-color: #2980b9;
      color: white;
      padding: 6px 12px;
      border-radius: 4px;
      text-decoration: none;
      font-weight: 600;
      transition: background-color 0.3s ease;
    }
    a.view-profile:hover {
      background-color: #3498db;
    }
  </style>
</head>
<body>
  <h1>Members List</h1>
  <table>
    <thead>
      <tr>
        <th>Photo</th>
        <th>First Name</th>
        <th>Last Name</th>
        <th>Date of Birth</th>
        <th>Age</th>
        <th>Location</th>
        <th>Occupation</th>
        <th>Phone</th>
        <th>NRC</th>
        <th>Gender</th>
        <th>Position</th>
        <th>Ministry</th>
        <th>Zone</th>
        <th>Tithe Number</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($members as $member): ?>
        <tr>
          <td>
            <?php if (!empty($member['photo'])): ?>
              <img src="<?= htmlspecialchars($member['photo']) ?>" alt="Photo" />
            <?php else: ?>
              No photo
            <?php endif; ?>
          </td>
          <td><?= htmlspecialchars($member['first_name'] ?? '') ?></td>
          <td><?= htmlspecialchars($member['last_name'] ?? '') ?></td>
          <td><?= htmlspecialchars($member['dob'] ?? '') ?></td>
          <td><?= htmlspecialchars($member['age'] ?? '') ?></td>
          <td><?= htmlspecialchars($member['location'] ?? '') ?></td>
          <td><?= htmlspecialchars($member['occupation'] ?? '') ?></td>
          <td><?= htmlspecialchars($member['phone'] ?? '') ?></td>
          <td><?= htmlspecialchars($member['nrc'] ?? '') ?></td>
          <td><?= htmlspecialchars($member['gender'] ?? '') ?></td>
          <td><?= htmlspecialchars($member['position'] ?? '') ?></td>
          <td><?= htmlspecialchars($member['ministry'] ?? '') ?></td>
          <td><?= htmlspecialchars($member['zone'] ?? '') ?></td>
          <td><?= htmlspecialchars($member['tithe_number'] ?? '') ?></td>
          <td>
            <a href="profile.php?tithe_number=<?= urlencode($member['tithe_number']) ?>" class="view-profile">View Profile</a>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</body>
</html>
