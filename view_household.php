<?php
session_start();
require 'helpers.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: index.php");
    exit;
}

$household_id = $_GET['household_id'] ?? null;
if (!$household_id) {
    die("No household specified.");
}

// Load all members (or load from DB via a helper function)
$allMembers = load_members();

// Filter members belonging to this household
$householdMembers = [];
foreach ($allMembers as $member) {
    if (isset($member['household_id']) && $member['household_id'] == $household_id) {
        $householdMembers[] = $member;
    }
}

if (empty($householdMembers)) {
    die("No members found for this household.");
}

// Find the head of household
$headOfHousehold = "Unknown";
foreach ($householdMembers as $m) {
    if (isset($m['role']) && strtolower($m['role']) === 'head of household') {
        $headOfHousehold = $m['name'];
        break;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>View Household: <?= htmlspecialchars($headOfHousehold) ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f9f9f9;
        }
        h1, h2 {
            color: #002147;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            background: white;
            box-shadow: 0 0 5px #ccc;
        }
        th, td {
            padding: 12px;
            border: 1px solid #ccc;
            text-align: left;
        }
        th {
            background-color: #002147;
            color: white;
        }
        a.btn-back {
            display: inline-block;
            margin-bottom: 15px;
            padding: 8px 12px;
            background-color: #002147;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }
        a.btn-back:hover {
            background-color: #003366;
        }
    </style>
</head>
<body>
    <a href="dashboard.php" class="btn-back">&larr; Back to Dashboard</a>
    <h1>Household: <?= htmlspecialchars($headOfHousehold) ?></h1>
    <h2>Members</h2>
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Tithe Number</th>
                <th>Age</th>
                <th>Gender</th>
                <th>Phone</th>
                <th>Role</th>
                <th>Zone</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($householdMembers as $member): ?>
            <tr>
                <td><?= htmlspecialchars($member['name']) ?></td>
                <td><?= htmlspecialchars($member['tithe_number']) ?></td>
                <td><?= htmlspecialchars($member['age']) ?></td>
                <td><?= htmlspecialchars($member['gender']) ?></td>
                <td><?= htmlspecialchars($member['phone']) ?></td>
                <td><?= htmlspecialchars($member['role'] ?? '') ?></td>
                <td><?= htmlspecialchars($member['zone'] ?? '') ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
