<?php
require 'helpers.php';

$id = $_GET['id'] ?? '';
$member = get_member($id);

if (!$member) {
    echo "Member not found.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Member Profile - <?=htmlspecialchars($member['first_name'] . ' ' . $member['last_name'])?></title>
<style>
  body {
    font-family: Arial, sans-serif;
    background-color: #fafafa;
    margin: 20px auto;
    max-width: 800px;
    color: #333;
  }
  .container {
    background: #fff;
    padding: 20px 30px;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
    border-radius: 6px;
  }
  h1 {
    color: #2c3e50;
    margin-bottom: 20px;
  }
  table.profile-table {
    width: 100%;
    border-collapse: collapse;
  }
  table.profile-table th,
  table.profile-table td {
    text-align: left;
    padding: 10px 15px;
    border-bottom: 1px solid #ddd;
    vertical-align: top;
  }
  table.profile-table th {
    background-color: #2980b9;
    color: white;
    width: 180px;
  }
  button, a.btn-secondary {
    display: inline-block;
    margin-top: 20px;
    padding: 10px 18px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-weight: 600;
    text-decoration: none;
  }
  button.btn-primary {
    background-color: #2980b9;
    color: white;
  }
  button.btn-primary:hover {
    background-color: #3498db;
  }
  a.btn-secondary {
    background-color: #eee;
    color: #333;
    margin-left: 10px;
  }
  a.btn-secondary:hover {
    background-color: #ddd;
  }
  img.photo {
    max-width: 150px;
    border-radius: 6px;
    margin-top: 15px;
  }
  @media print {
    body * { visibility: hidden; }
    .container, .container * { visibility: visible; }
    .container {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      box-shadow: none;
      border-radius: 0;
      padding: 0;
      margin: 0;
    }
    button, a.btn-secondary {
      display: none;
    }
  }
</style>
</head>
<body>

<div class="container">
  <h1>Member Profile</h1>
  <table class="profile-table">
    <tr><th>First Name:</th><td><?=htmlspecialchars($member['first_name'] ?? '')?></td></tr>
    <tr><th>Last Name:</th><td><?=htmlspecialchars($member['last_name'] ?? '')?></td></tr>
    <tr><th>Date of Birth:</th><td><?=htmlspecialchars($member['dob'] ?? '')?></td></tr>
    <tr><th>Age:</th><td><?=htmlspecialchars($member['age'] ?? '')?></td></tr>
    <tr><th>Area of Residence:</th><td><?=htmlspecialchars($member['location'] ?? '')?></td></tr>
    <tr><th>Occupation:</th><td><?=htmlspecialchars($member['occupation'] ?? '')?></td></tr>
    <tr><th>Phone Number:</th><td><?=htmlspecialchars($member['phone'] ?? '')?></td></tr>
    <tr><th>NRC:</th><td><?=htmlspecialchars($member['nrc'] ?? '')?></td></tr>
    <tr><th>Gender:</th><td><?=htmlspecialchars($member['gender'] ?? '')?></td></tr>
    <tr><th>Church Position:</th><td><?=htmlspecialchars($member['position'] ?? '')?></td></tr>
    <tr><th>Ministry:</th><td><?=htmlspecialchars($member['ministry'] ?? '')?></td></tr>
    <tr><th>Zone:</th><td><?=htmlspecialchars($member['zone'] ?? '')?></td></tr>
  </table>

  <?php if (!empty($member['photo'])): ?>
    <img src="<?=htmlspecialchars($member['photo'])?>" alt="ID Photo" class="photo" />
  <?php endif; ?>

  <button onclick="window.print();" class="btn-primary">Print Profile</button>
  <a href="dashboard.php" class="btn-secondary">Back to Dashboard</a>
</div>

</body>
</html>
