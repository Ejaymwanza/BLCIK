<?php
session_start();
require 'helpers.php';

// Redirect if not logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: index.php");
    exit;
}

// Format name function
function formatName($name) {
    return ucwords(strtolower(trim($name)));
}

$members = load_members();
$genderCounts = ['Male' => 0, 'Female' => 0];
$ministryCounts = [];
$ageGroupCounts = ['35 and below' => 0, 'Above 35' => 0];
$zoneCounts = [];
$today = new DateTime();

foreach ($members as $index => $member) {
    $gender = $member['gender'] ?? 'Unknown';
    if ($gender === 'Male') $genderCounts['Male']++;
    elseif ($gender === 'Female') $genderCounts['Female']++;

    $ministry = trim($member['ministry'] ?? 'Unknown') ?: 'Unknown';
    $ministryCounts[$ministry] = ($ministryCounts[$ministry] ?? 0) + 1;

    $age = 0;
    if (!empty($member['dob'])) {
        try {
            $dob = new DateTime($member['dob']);
            $age = $dob->diff($today)->y;
        } catch (Exception $e) {
            $age = 0;
        }
    }

    if ($age <= 35) $ageGroupCounts['35 and below']++;
    else $ageGroupCounts['Above 35']++;

    $zone = trim($member['zone'] ?? 'Unknown') ?: 'Unknown';
    $zoneCounts[$zone] = ($zoneCounts[$zone] ?? 0) + 1;

    $member['age'] = $age;
    $members[$index] = $member;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <title>Dashboard</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"/>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    * { box-sizing: border-box; }
    body {
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
      background: #f4f7fa;
      color: #002147;
    }

    .container {
      display: flex;
      min-height: 100vh;
      flex-wrap: wrap;
    }

    .sidebar {
      width: 240px;
      background: #002147;
      color: white;
      padding: 20px;
      flex-shrink: 0;
      display: flex;
      flex-direction: column;
    }

    .logo-area {
      font-size: 1.8rem;
      font-weight: bold;
      margin-bottom: 40px;
      text-align: center;
    }

    nav {
      display: flex;
      flex-direction: column;
    }

    nav a {
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 12px 16px;
      margin: 6px 0;
      color: white;
      text-decoration: none;
      font-size: 16px;
      border-radius: 5px;
      transition: background 0.3s ease;
    }

    nav a.active,
    nav a:hover {
      background: #004080;
    }

    .main-content {
      flex: 1;
      padding: 40px;
    }

    h1 {
      color: #002147;
      margin-bottom: 20px;
    }

    .charts-container {
      display: flex;
      justify-content: center;
      gap: 30px;
      margin-bottom: 40px;
      flex-wrap: wrap;
    }

    .chart-container {
      width: 45%;
      min-width: 300px;
      max-width: 480px;
      background: #f0f8ff;
      border-radius: 8px;
      padding: 20px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
      height: 350px;
    }

    .chart-container h2 {
      text-align: center;
      color: #003366;
      margin-bottom: 10px;
    }

    canvas {
      width: 100% !important;
      height: calc(100% - 40px) !important;
    }

    #searchInput {
      width: 100%;
      max-width: 400px;
      padding: 10px;
      margin: 20px 0;
      font-size: 16px;
      border: 1px solid #ccc;
      border-radius: 5px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      background: #fff;
      box-shadow: 0 0 10px rgba(0,0,0,0.05);
      margin-top: 10px;
    }

    table, th, td {
      border: 1px solid #ccc;
    }

    th, td {
      padding: 12px;
      text-align: left;
      vertical-align: middle;
    }

    th {
      background: #002147;
      color: white;
    }

    td img {
      width: 50px;
      height: 50px;
      object-fit: cover;
      border-radius: 50%;
    }

    .action-btn {
      display: inline-block;
      padding: 6px 10px;
      margin: 2px;
      border: none;
      border-radius: 4px;
      font-size: 14px;
      color: white;
      text-decoration: none;
      cursor: pointer;
    }

    .action-btn:hover { opacity: 0.85; }
    .delete-btn { background: #d9534f; }
    .view-btn { background: #007bff; }

    form { display: inline; margin: 0; padding: 0; }

    @media (max-width: 768px) {
      .charts-container {
        flex-direction: column;
        align-items: center;
      }

      .chart-container {
        width: 90%;
      }

      .sidebar {
        width: 100%;
        flex-direction: row;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        padding: 10px 20px;
      }

      nav {
        flex-direction: row;
        flex-wrap: wrap;
        justify-content: center;
      }

      nav a {
        padding: 10px;
        font-size: 14px;
      }

      .main-content {
        padding: 20px;
      }
    }
  </style>
</head>
<body>
<div class="container">
  <aside class="sidebar">
    <div class="logo-area">Bread of Life</div>
    <nav>
      <a href="dashboard.php" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
      <a href="add_member.php"><i class="fas fa-user-plus"></i> Add Member</a>
      <a href="manage_admins.php"><i class="fas fa-cogs"></i> Manage Admins</a>
      <a href="export.php"><i class="fas fa-download"></i> Download List</a>
      <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </nav>
  </aside>

  <main class="main-content">
    <h1>Dashboard</h1>

    <div class="charts-container">
      <div class="chart-container">
        <h2>Gender Distribution</h2>
        <canvas id="pieChart"></canvas>
      </div>
      <div class="chart-container">
        <h2>Members by Ministry</h2>
        <canvas id="ministryChart"></canvas>
      </div>
      <div class="chart-container">
        <h2>Members by Age Group</h2>
        <canvas id="ageChart"></canvas>
      </div>
      <div class="chart-container">
        <h2>Members by Zone</h2>
        <canvas id="zoneChart"></canvas>
      </div>
    </div>

    <input type="text" id="searchInput" placeholder="Search by name, zone, or tithe number...">

    <table id="membersTable">
      <thead>
        <tr>
          <th>Photo</th>
          <th>Name</th>
          <th>Gender</th>
          <th>Age</th>
          <th>Zone</th>
          <th>Ministry</th>
          <th>Phone</th>
          <th>Tithe #</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($members as $m): ?>
          <tr>
            <td>
              <?php if (!empty($m['photo'])): ?>
                <img src="<?= htmlspecialchars($m['photo']) ?>" alt="Photo">
              <?php else: ?>
                <img src="assets/default-avatar.png" alt="No Photo">
              <?php endif; ?>
            </td>
            <td><?= htmlspecialchars(formatName($m['full_name'] ?? 'Unknown')) ?></td>
            <td><?= htmlspecialchars($m['gender'] ?? 'Unknown') ?></td>
            <td><?= htmlspecialchars($m['age'] ?? 'N/A') ?></td>
            <td><?= htmlspecialchars($m['zone'] ?? 'Unknown') ?></td>
            <td><?= htmlspecialchars($m['ministry'] ?? 'Unknown') ?></td>
            <td><?= htmlspecialchars($m['contact_number'] ?? '') ?></td>
            <td><?= htmlspecialchars($m['tithe_number'] ?? '') ?></td>
            <td>
              <a href="profile.php?tithe_number=<?= urlencode($m['tithe_number'] ?? '') ?>" class="action-btn view-btn">View</a>
              <form method="POST" action="delete_member.php" onsubmit="return confirm('Delete this member?');">
                <input type="hidden" name="tithe_number" value="<?= htmlspecialchars($m['tithe_number'] ?? '') ?>">
                <button type="submit" class="action-btn delete-btn">Delete</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </main>
</div>

<script>
const genderChart = new Chart(document.getElementById('pieChart'), {
    type: 'pie',
    data: {
        labels: <?= json_encode(array_keys($genderCounts)) ?>,
        datasets: [{
            data: <?= json_encode(array_values($genderCounts)) ?>,
            backgroundColor: ['#007B8A', '#FF6B6B']
        }]
    },
    options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
});

const ministryChart = new Chart(document.getElementById('ministryChart'), {
    type: 'pie',
    data: {
        labels: <?= json_encode(array_keys($ministryCounts)) ?>,
        datasets: [{
            data: <?= json_encode(array_values($ministryCounts)) ?>,
            backgroundColor: ['#FFB400','#6BCB77','#4D96FF','#845EC2','#FF6F91','#008E9B','#E07A5F','#FF8066']
        }]
    },
    options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
});

const ageChart = new Chart(document.getElementById('ageChart'), {
    type: 'pie',
    data: {
        labels: <?= json_encode(array_keys($ageGroupCounts)) ?>,
        datasets: [{
            data: <?= json_encode(array_values($ageGroupCounts)) ?>,
            backgroundColor: ['#FF6F91', '#845EC2']
        }]
    },
    options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
});

const zoneChart = new Chart(document.getElementById('zoneChart'), {
    type: 'pie',
    data: {
        labels: <?= json_encode(array_keys($zoneCounts)) ?>,
        datasets: [{
            data: <?= json_encode(array_values($zoneCounts)) ?>,
            backgroundColor: ['#4D96FF','#6BCB77','#FFB400','#FF6B6B','#845EC2','#008E9B','#E07A5F','#FF8066']
        }]
    },
    options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
});

// Search functionality
document.getElementById('searchInput').addEventListener('input', function () {
    const query = this.value.toLowerCase();
    document.querySelectorAll('#membersTable tbody tr').forEach(row => {
        const name = row.cells[1].textContent.toLowerCase();
        const zone = row.cells[4].textContent.toLowerCase();
        const tithe = row.cells[7].textContent.toLowerCase();
        row.style.display = (name.includes(query) || zone.includes(query) || tithe.includes(query)) ? '' : 'none';
    });
});
</script>
</body>
</html>
