<?php
session_start();
require 'helpers.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: index.php");
    exit;
}

if (!isset($_GET['tithe_number'])) {
    die('Tithe number missing.');
}

$tithe_number = $_GET['tithe_number'];
$members = load_members();
$memberIndex = null;
$member = null;

foreach ($members as $index => $m) {
    if ($m['tithe_number'] === $tithe_number) {
        $member = $m;
        $memberIndex = $index;
        break;
    }
}

if (!$member) {
    die('Member not found.');
}

$editMode = isset($_GET['edit']) && $_GET['edit'] == '1';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $editMode) {
    $updatedMember = [
        'full_name' => trim($_POST['full_name'] ?? ''),
        'dob' => trim($_POST['dob'] ?? ''),
        'year_born_again' => trim($_POST['year_born_again'] ?? ''),
        'date_baptism' => trim($_POST['date_baptism'] ?? ''),
        'marital_status' => trim($_POST['marital_status'] ?? ''),
        'year_joined_blci' => trim($_POST['year_joined_blci'] ?? ''),
        'occupation' => trim($_POST['occupation'] ?? ''),
        'ministry' => trim($_POST['ministry'] ?? ''),
        'contact_number' => trim($_POST['contact_number'] ?? ''),
        'phone_number' => trim($_POST['phone_number'] ?? ''),
        'nrc' => trim($_POST['nrc'] ?? ''),
        'email' => trim($_POST['email'] ?? ''),
        'gender' => trim($_POST['gender'] ?? ''),
        'cell_group' => trim($_POST['cell_group'] ?? ''),
        'cell_group_leader' => trim($_POST['cell_group_leader'] ?? ''),
        'cell_group_leader_phone' => trim($_POST['cell_group_leader_phone'] ?? ''),
        'cell_group_leader_address' => trim($_POST['cell_group_leader_address'] ?? ''),
        'area' => trim($_POST['area'] ?? ''),
        'cell_group_host' => trim($_POST['cell_group_host'] ?? ''),
        'cell_group_host_phone' => trim($_POST['cell_group_host_phone'] ?? ''),
        'cell_group_address' => trim($_POST['cell_group_address'] ?? ''),
        'zone' => trim($_POST['zone'] ?? ''),
        'tithe_number' => $tithe_number,
        'photo' => $member['photo'] ?? '',
        'children' => $member['children'] ?? []
    ];

    if (!empty($updatedMember['dob'])) {
        $dobDT = DateTime::createFromFormat('Y-m-d', $updatedMember['dob']);
        $updatedMember['age'] = $dobDT ? $dobDT->diff(new DateTime())->y : '';
    } else {
        $updatedMember['age'] = '';
    }

    // Handle photo upload
    if (!empty($_FILES['photo']['name'])) {
        $targetDir = "uploads/";
        $fileName = uniqid() . '_' . basename($_FILES['photo']['name']);
        $targetFilePath = $targetDir . $fileName;
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];

        if (in_array(mime_content_type($_FILES['photo']['tmp_name']), $allowedTypes)) {
            if (move_uploaded_file($_FILES['photo']['tmp_name'], $targetFilePath)) {
                $updatedMember['photo'] = $targetFilePath;
            }
        }
    }

    $members[$memberIndex] = $updatedMember;
    if (function_exists('save_members')) {
        save_members($members);
        header("Location: profile.php?tithe_number=" . urlencode($tithe_number) . "&success=Profile+updated");
        exit;
    } else {
        die("Save function not implemented.");
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title><?= $editMode ? 'Edit Profile - ' : 'Member Profile - ' ?><?= htmlspecialchars($member['full_name']) ?></title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f7fc; margin: 0; padding: 20px; }
        .container { background: #fff; max-width: 1000px; margin: auto; border-radius: 10px; padding: 30px; box-shadow: 0 0 15px rgba(0,0,0,0.1); }
        h1 { color: #003366; margin-bottom: 20px; }
        .profile-wrapper { display: flex; gap: 30px; flex-wrap: wrap; }
        .id-photo-container { flex: 0 0 180px; }
        .id-photo-container img { width: 150px; height: 150px; object-fit: cover; border-radius: 8px; border: 3px solid #007bff; }
        .details { flex: 1; display: grid; grid-template-columns: 180px 1fr; gap: 10px 20px; }
        .details label { font-weight: bold; color: #003366; }
        input, select { width: 100%; padding: 8px; font-size: 14px; border: 1px solid #ccc; border-radius: 4px; }
        .button-group { margin-top: 30px; text-align: right; }
        .button-group button, .button-group a {
            display: inline-block; padding: 10px 18px; margin-left: 10px;
            border: none; background-color: #003366; color: #fff;
            border-radius: 4px; text-decoration: none; font-weight: bold;
            cursor: pointer;
        }
        .alert-success { background: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin-bottom: 15px; }
        .children-section { margin-top: 30px; padding-top: 15px; border-top: 2px dashed #ccc; }
        .child-block {
            background: #f9f9f9; padding: 10px; margin-bottom: 15px;
            border: 1px solid #ddd; border-radius: 6px;
        }
        @media (max-width: 768px) {
            .details { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
<div class="container">
    <h1><?= $editMode ? 'Edit Profile' : 'Member Profile' ?></h1>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert-success"><?= htmlspecialchars($_GET['success']) ?></div>
    <?php endif; ?>

    <?php if ($editMode): ?>
        <form method="POST" enctype="multipart/form-data">
    <?php endif; ?>

    <div class="profile-wrapper">
        <div class="id-photo-container">
            <?php if (!empty($member['photo'])): ?>
                <img src="<?= htmlspecialchars($member['photo']) ?>" alt="Photo">
            <?php else: ?>
                <p>No photo available</p>
            <?php endif; ?>
            <?php if ($editMode): ?>
                <input type="file" name="photo" accept="image/*">
            <?php endif; ?>
        </div>

        <div class="details">
            <?php
            $fields = [
                'full_name' => 'Full Name',
                'dob' => 'Date of Birth',
                'age' => 'Age',
                'year_born_again' => 'Year Born Again',
                'date_baptism' => 'Date of Baptism',
                'marital_status' => 'Marital Status',
                'year_joined_blci' => 'Year Joined BLCI',
                'occupation' => 'Occupation',
                'ministry' => 'Ministry',
                'contact_number' => 'Contact Number',
                'phone_number' => 'Phone Number',
                'nrc' => 'NRC',
                'email' => 'Email',
                'gender' => 'Gender',
                'cell_group' => 'Cell Group',
                'cell_group_leader' => 'Cell Group Leader',
                'cell_group_leader_phone' => 'Leader Phone',
                'cell_group_leader_address' => 'Leader Address',
                'area' => 'Area',
                'cell_group_host' => 'Host',
                'cell_group_host_phone' => 'Host Phone',
                'cell_group_address' => 'Group Address',
                'zone' => 'Zone',
                'tithe_number' => 'Tithe Number'
            ];

            foreach ($fields as $key => $label):
                echo "<label>{$label}:</label><div>";
                if ($editMode && $key !== 'tithe_number' && $key !== 'age') {
                    if ($key === 'gender') {
                        echo '<select name="gender">
                                <option value="">Select</option>
                                <option value="Male"'.($member['gender'] === 'Male' ? ' selected' : '').'>Male</option>
                                <option value="Female"'.($member['gender'] === 'Female' ? ' selected' : '').'>Female</option>
                              </select>';
                    } elseif ($key === 'marital_status') {
                        $statuses = ['Single', 'Married', 'Divorced', 'Widowed'];
                        echo '<select name="marital_status"><option value=""></option>';
                        foreach ($statuses as $status) {
                            $sel = $member['marital_status'] === $status ? ' selected' : '';
                            echo "<option value=\"$status\"$sel>$status</option>";
                        }
                        echo '</select>';
                    } elseif (str_contains($key, 'date') || str_contains($key, 'dob')) {
                        echo '<input type="date" name="'.$key.'" value="'.htmlspecialchars($member[$key] ?? '').'">';
                    } else {
                        echo '<input type="text" name="'.$key.'" value="'.htmlspecialchars($member[$key] ?? '').'">';
                    }
                } else {
                    echo htmlspecialchars($member[$key] ?? '');
                }
                echo "</div>";
            endforeach;
            ?>
        </div>
    </div>

    <?php if (!empty($member['children'])): ?>
        <div class="children-section">
            <h2>Children</h2>
            <?php foreach ($member['children'] as $i => $child): ?>
                <div class="child-block">
                    <strong>Child <?= $i + 1 ?>:</strong><br>
                    Name: <?= htmlspecialchars($child['name']) ?><br>
                    DOB: <?= htmlspecialchars($child['dob']) ?><br>
                    Age: <?= htmlspecialchars($child['age']) ?><br>
                    Tithe Number: <?= htmlspecialchars($child['tithe_number']) ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="button-group">
        <?php if ($editMode): ?>
            <button type="submit">Save</button>
            <a href="profile.php?tithe_number=<?= urlencode($tithe_number) ?>">Cancel</a>
        <?php else: ?>
            <a href="dashboard.php">Back</a>
            <a href="profile.php?tithe_number=<?= urlencode($tithe_number) ?>&edit=1">Edit</a>
        <?php endif; ?>
    </div>

    <?php if ($editMode): ?>
        </form>
    <?php endif; ?>
</div>
</body>
</html>
