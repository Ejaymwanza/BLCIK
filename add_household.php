<?php
session_start();
require_once 'helpers.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: index.php");
    exit;
}

$error = '';
$success = '';

// CSRF protection
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die('Invalid CSRF token');
    }

    // Sanitize and validate inputs
    $house_number = isset($_POST['house_number']) ? intval($_POST['house_number']) : null;
    $household_head_name = trim(filter_var($_POST['household_head'] ?? '', FILTER_SANITIZE_STRING));
    $address = trim(filter_var($_POST['address'] ?? '', FILTER_SANITIZE_STRING));
    $contact_number = trim($_POST['contact_number'] ?? '');
    $family_members = $_POST['family_members'] ?? [];

    // Basic validation
    if ($house_number === null || $house_number < 0 || $house_number > 99) {
        $error = "House number must be between 0 and 99.";
    } elseif (empty($household_head_name)) {
        $error = "Head of Household name is required.";
    } elseif (empty($address)) {
        $error = "Address is required.";
    } elseif (!preg_match('/^\+?260\d{9}$/', $contact_number)) {
        $error = "Contact number must be in the format +260XXXXXXXXX.";
    }

    // Validate family members if any
    if (!$error && !empty($family_members)) {
        foreach ($family_members as $idx => $member) {
            $name = trim($member['name'] ?? '');
            $gender = $member['gender'] ?? '';
            $dob = $member['dob'] ?? '';

            if (empty($name)) {
                $error = "Family member #".($idx+1)." name is required.";
                break;
            }
            if (!in_array($gender, ['Male', 'Female'], true)) {
                $error = "Family member #".($idx+1)." gender must be Male or Female.";
                break;
            }
            if (!empty($dob) && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $dob)) {
                $error = "Family member #".($idx+1)." date of birth must be a valid date.";
                break;
            }
        }
    }

    if (!$error) {
        try {
            // Format house number as 2-digit
            $house_number_str = str_pad($house_number, 2, '0', STR_PAD_LEFT);

            // Load existing households and find max suffix
            $households = load_households();
            $max_suffix = 0;
            foreach ($households as $household) {
                if (isset($household['household_number']) && strpos($household['household_number'], $house_number_str) === 0) {
                    $suffix = intval(substr($household['household_number'], 2, 3));
                    if ($suffix > $max_suffix) $max_suffix = $suffix;
                }
            }
            $next_suffix = $max_suffix + 1;
            if ($next_suffix > 999) throw new Exception("Maximum households reached for house number $house_number_str.");

            $household_number = $house_number_str . str_pad($next_suffix, 3, '0', STR_PAD_LEFT);

            // Ensure unique household number
            foreach ($households as $h) {
                if (($h['household_number'] ?? '') === $household_number) {
                    throw new Exception("Household number $household_number already exists.");
                }
            }

            $timestamp = date('Y-m-d H:i:s');

            // Save household data
            $households[] = [
                'household_number' => $household_number,
                'household_name' => $household_head_name,
                'address' => $address,
                'contact_number' => $contact_number,
                'created_at' => $timestamp
            ];
            save_households($households);

            // Load members
            $members = load_members();

            // Helper for tithe number generation
            function get_next_tithe_number_full($members) {
                $max = 0;
                foreach ($members as $m) {
                    if (!empty($m['tithe_number']) && is_numeric($m['tithe_number'])) {
                        $num = intval($m['tithe_number']);
                        if ($num > $max) $max = $num;
                    }
                }
                return str_pad($max + 1, 4, '0', STR_PAD_LEFT);
            }

            // Add head of household as member
            $head_tithe_number = get_next_tithe_number_full($members);
            $members[] = [
                'id' => uniqid(),
                'name' => $household_head_name,
                'tithe_number' => $head_tithe_number,
                'gender' => 'N/A',
                'dob' => '',
                'role' => 'Household Head',
                'household_id' => $household_number,
                'added_on' => $timestamp,
                'nrc' => '',
                'phone' => $contact_number,
                'location' => $address,
                'zone' => '',
                'ministry' => '',
                'family' => '',
                'position' => '',
                'age' => '',
                'photo' => '',
                'date_joined' => '',
                'date_baptism' => '',
                'date_second_baptism' => ''
            ];

            // Add family members
            foreach ($family_members as $member) {
                $name = trim($member['name']);
                if (!$name) continue;
                $gender = $member['gender'] ?? 'N/A';
                $dob = $member['dob'] ?? '';
                $member_tithe_number = get_next_tithe_number_full($members);
                $members[] = [
                    'id' => uniqid(),
                    'name' => $name,
                    'tithe_number' => $member_tithe_number,
                    'gender' => $gender,
                    'dob' => $dob,
                    'role' => 'Family Member',
                    'household_id' => $household_number,
                    'added_on' => $timestamp,
                    'nrc' => '',
                    'phone' => '',
                    'location' => $address,
                    'zone' => '',
                    'ministry' => '',
                    'family' => '',
                    'position' => '',
                    'age' => '',
                    'photo' => '',
                    'date_joined' => '',
                    'date_baptism' => '',
                    'date_second_baptism' => ''
                ];
            }

            // Save members
            save_members($members);

            $success = "Household added successfully! Household Number: $household_number";

        } catch (Exception $ex) {
            $error = $ex->getMessage();
        }
    }
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Add Household</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f0f3f5;
            margin: 0; padding: 0;
            color: #333;
        }
        nav {
            background: #004080;
            padding: 10px 20px;
        }
        nav a {
            color: #fff;
            margin-right: 15px;
            text-decoration: none;
            font-weight: bold;
        }
        nav a:hover {
            text-decoration: underline;
        }
        div.container {
            max-width: 700px;
            background: white;
            margin: 30px auto;
            padding: 25px 30px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            border-radius: 8px;
        }
        h1 {
            margin-bottom: 20px;
            color: #004080;
        }
        label {
            display: block;
            margin-top: 15px;
            font-weight: 600;
        }
        input[type="text"],
        input[type="number"],
        input[type="date"],
        select {
            width: 100%;
            padding: 8px 10px;
            margin-top: 6px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
            font-size: 14px;
        }
        button, input[type="submit"] {
            margin-top: 20px;
            background: #004080;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            font-size: 16px;
        }
        button:hover, input[type="submit"]:hover {
            background: #002f5b;
        }
        .family-member {
            border: 1px solid #ccc;
            padding: 15px;
            margin-top: 15px;
            border-radius: 5px;
            position: relative;
            background: #fafafa;
        }
        .family-member label {
            margin-top: 10px;
        }
        .remove-member {
            position: absolute;
            top: 8px;
            right: 8px;
            background: #cc0000;
            border: none;
            color: white;
            font-weight: bold;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            cursor: pointer;
        }
        .error-msg {
            background: #f8d7da;
            color: #842029;
            border: 1px solid #f5c2c7;
            padding: 12px;
            margin-bottom: 15px;
            border-radius: 5px;
        }
        .success-msg {
            background: #d1e7dd;
            color: #0f5132;
            border: 1px solid #badbcc;
            padding: 12px;
            margin-bottom: 15px;
            border-radius: 5px;
        }
    </style>
    <script>
        // Add new family member input fields
        function addMemberField() {
            const container = document.getElementById('family-members');
            const index = container.children.length;
            const memberDiv = document.createElement('div');
            memberDiv.className = 'family-member';
            memberDiv.innerHTML = `
                <button type="button" class="remove-member" onclick="removeMemberField(this)" title="Remove Member">&times;</button>
                <label for="family_members_${index}_name">Member Name:</label>
                <input type="text" id="family_members_${index}_name" name="family_members[${index}][name]" required />
                <label for="family_members_${index}_gender">Gender:</label>
                <select id="family_members_${index}_gender" name="family_members[${index}][gender]" required>
                    <option value="">--Select--</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                </select>
                <label for="family_members_${index}_dob">Date of Birth:</label>
                <input type="date" id="family_members_${index}_dob" name="family_members[${index}][dob]" />
            `;
            container.appendChild(memberDiv);
        }

        // Remove a family member input group
        function removeMemberField(button) {
            button.parentElement.remove();
        }
    </script>
</head>
<body>
<nav>
    <a href="dashboard.php">Dashboard</a>
    <a href="add_member.php">Add Member</a>
    <a href="add_household.php">Add Household</a>
    <a href="manage_admins.php">Manage Admins</a>
    <a href="export.php">Download Members</a>
    <a href="logout.php">Logout</a>
</nav>

<div class="container">
    <h1>Add Household</h1>

    <?php if ($error): ?>
        <div class="error-msg"><?= htmlspecialchars($error) ?></div>
    <?php elseif ($success): ?>
        <div class="success-msg"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form method="POST" action="add_household.php" novalidate>
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>" />

        <label for="house_number">House Number (0-99):</label>
        <input type="number" id="house_number" name="house_number" min="0" max="99" required value="<?= htmlspecialchars($_POST['house_number'] ?? '') ?>" />

        <label for="household_head">Head of Household Name:</label>
        <input type="text" id="household_head" name="household_head" required value="<?= htmlspecialchars($_POST['household_head'] ?? '') ?>" />

        <label for="address">Address:</label>
        <input type="text" id="address" name="address" required value="<?= htmlspecialchars($_POST['address'] ?? '') ?>" />

        <label for="contact_number">Contact Number (+260XXXXXXXXX):</label>
        <input type="text" id="contact_number" name="contact_number" required placeholder="+260XXXXXXXXX" value="<?= htmlspecialchars($_POST['contact_number'] ?? '') ?>" />

        <h3>Family Members</h3>
        <div id="family-members">
            <?php
            if (!empty($_POST['family_members']) && is_array($_POST['family_members'])):
                foreach ($_POST['family_members'] as $idx => $member):
                    $mName = htmlspecialchars($member['name'] ?? '');
                    $mGender = htmlspecialchars($member['gender'] ?? '');
                    $mDob = htmlspecialchars($member['dob'] ?? '');
            ?>
                <div class="family-member">
                    <button type="button" class="remove-member" onclick="removeMemberField(this)" title="Remove Member">&times;</button>
                    <label for="family_members_<?= $idx ?>_name">Member Name:</label>
                    <input type="text" id="family_members_<?= $idx ?>_name" name="family_members[<?= $idx ?>][name]" required value="<?= $mName ?>" />
                    <label for="family_members_<?= $idx ?>_gender">Gender:</label>
                    <select id="family_members_<?= $idx ?>_gender" name="family_members[<?= $idx ?>][gender]" required>
                        <option value="">--Select--</option>
                        <option value="Male" <?= $mGender === 'Male' ? 'selected' : '' ?>>Male</option>
                        <option value="Female" <?= $mGender === 'Female' ? 'selected' : '' ?>>Female</option>
                    </select>
                    <label for="family_members_<?= $idx ?>_dob">Date of Birth:</label>
                    <input type="date" id="family_members_<?= $idx ?>_dob" name="family_members[<?= $idx ?>][dob]" value="<?= $mDob ?>" />
                </div>
            <?php
                endforeach;
            endif;
            ?>
        </div>

        <button type="button" onclick="addMemberField()">Add Family Member</button>
        <br />
        <input type="submit" value="Add Household" />
    </form>
</div>
</body>
</html>
