<?php
session_start();
require 'helpers.php';

// Redirect if not logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: index.php");
    exit;
}

// CSRF setup
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$error = '';
$success = '';
$generated_tithe_number = '';

$input = [
    'full_name' => '', 'dob' => '', 'year_born_again' => '', 'date_baptism' => '',
    'marital_status' => '', 'year_joined_blci' => '', 'occupation' => '', 'ministry' => '',
    'contact_number' => '', 'phone_number' => '', 'nrc' => '', 'email' => '',
    'gender' => '', 'cell_group' => '', 'cell_group_leader' => '', 'cell_group_leader_phone' => '',
    'cell_group_leader_address' => '', 'area' => '', 'cell_group_host' => '', 'cell_group_host_phone' => '',
    'cell_group_address' => '', 'zone' => '', 'number_of_children' => 0, 'children' => []
];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die('Invalid CSRF token');
    }

    foreach ($input as $k => $v) {
        if ($k !== 'children') $input[$k] = htmlspecialchars(trim($_POST[$k] ?? ''));
    }
    $input['number_of_children'] = intval($_POST['number_of_children'] ?? 0);

    // Children
    $input['children'] = [];
    if ($input['number_of_children'] > 0) {
        for ($i = 0; $i < $input['number_of_children']; $i++) {
            $child = [
                'name' => htmlspecialchars(trim($_POST['children']['name'][$i] ?? '')),
                'dob' => htmlspecialchars(trim($_POST['children']['dob'][$i] ?? '')),
                'age' => htmlspecialchars(trim($_POST['children']['age'][$i] ?? '')),
                'tithe_number' => htmlspecialchars(trim($_POST['children']['tithe_number'][$i] ?? '')),
            ];
            $input['children'][] = $child;
        }
    }

    // Validation
    if (!empty($input['email']) && !filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email format.';
    }

    if (!$error && !empty($input['dob']) && !DateTime::createFromFormat('Y-m-d', $input['dob'])) {
        $error = 'Invalid Date of Birth format.';
    }

    if (!$error && !empty($input['date_baptism']) && !DateTime::createFromFormat('Y-m-d', $input['date_baptism'])) {
        $error = 'Invalid Baptism Date format.';
    }

    foreach ($input['children'] as $c) {
        if (!empty($c['dob']) && !DateTime::createFromFormat('Y-m-d', $c['dob'])) {
            $error = 'One or more children have invalid Date of Birth format.';
            break;
        }
    }

    // Handle photo upload
    $photo = '';
    if (!$error && isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $type = mime_content_type($_FILES['photo']['tmp_name']);
        if (!in_array($type, ['image/jpeg', 'image/png', 'image/gif'])) {
            $error = 'Invalid photo type.';
        } else {
            $dir = __DIR__ . '/uploads/';
            if (!is_dir($dir)) mkdir($dir, 0755, true);
            $ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
            $filename = uniqid('id_') . '.' . $ext;
            if (move_uploaded_file($_FILES['photo']['tmp_name'], $dir . $filename)) {
                $photo = 'uploads/' . $filename;
            } else {
                $error = 'Failed to save uploaded photo.';
            }
        }
    }

    // Save member
    if (!$error) {
        $dobDT = new DateTime($input['dob']);
        $age = $dobDT->diff(new DateTime())->y;
        $tithe_number = $age < 16 ? 'NILL' : get_next_tithe_number();

        foreach ($input['children'] as &$child) {
            if (!empty($child['dob'])) {
                $childDob = new DateTime($child['dob']);
                $childAge = $childDob->diff(new DateTime())->y;
                $child['age'] = $childAge;
                $child['tithe_number'] = $childAge < 16 ? 'NILL' : get_next_tithe_number();
            }
        }

        $member = array_merge($input, [
            'age' => $age,
            'photo' => $photo,
            'tithe_number' => $tithe_number,
        ]);

        add_member($member);
        $success = 'Member added successfully!';
        $generated_tithe_number = $tithe_number;

        // Reset form
        foreach ($input as $k => $v) if ($k !== 'children') $input[$k] = '';
        $input['children'] = [];
        $input['number_of_children'] = 0;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Add Individual Member</title>
<style>
body { margin:0; font-family:'Segoe UI',sans-serif; background:#f4f7fa; color:#002147; }
.container { display:flex; min-height:100vh; }
.sidebar { width:240px; background-color:#002147; color:white; padding:20px; flex-shrink:0; }
.logo-area { font-size:1.3rem; font-weight:bold; margin-bottom:30px; }
.sidebar nav a { display:block; color:white; text-decoration:none; margin:10px 0; font-size:16px; }
.sidebar nav a:hover, .sidebar nav a.active { background:#004080; padding:10px; border-radius:5px; }
.main-content { flex:1; padding:40px; }
h1 { color:#002147; margin-bottom:20px; }
.form-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(280px,1fr)); gap:30px 40px; margin-bottom:30px; }
.form-group { display:flex; flex-direction:column; }
.form-group label { font-weight:bold; margin-bottom:8px; color:#002147; }
.form-group input, .form-group select { padding:10px; font-size:16px; border:1px solid #ccc; border-radius:4px; }
.form-group input:focus, .form-group select:focus { border-color:#002147; outline:none; }
.btn-primary { padding:12px 24px; background-color:#002147; border:none; color:white; font-size:16px; cursor:pointer; border-radius:4px; transition:.3s; }
.btn-primary:hover { background-color:#003366; }
.error { color:#d8000c; background:#ffd2d2; padding:10px; margin-bottom:15px; border-radius:4px; }
.success { color:#4BB543; background:#D4EDDA; padding:10px; margin-bottom:15px; border-radius:4px; }
.highlight { background:#fff3cd; color:#856404; padding:10px; margin-top:10px; border:1px solid #ffeeba; border-radius:4px; }
.child-group { border:1px solid #ccc; padding:15px; border-radius:6px; margin-bottom:15px; background:#fff; }
.child-group h3 { margin-top:0; font-weight:600; color:#002147; }
</style>
</head>
<body>
<div class="container">
<aside class="sidebar">
    <div class="logo-area">Bread of Life Kanyama</div>
    <nav>
        <a href="dashboard.php">Dashboard</a>
        <a href="add_member.php" class="active">Add Member</a>
        <a href="manage_admins.php">Manage Admins</a>
        <a href="export.php">Download List</a>
        <a href="logout.php">Logout</a>
    </nav>
</aside>
<main class="main-content">
    <h1>Add Individual Member</h1>
    <?php if ($error): ?><div class="error"><?= $error ?></div><?php endif ?>
    <?php if ($success): ?>
        <div class="success"><?= $success ?></div>
        <div class="highlight">Tithe Number: <strong><?= htmlspecialchars($generated_tithe_number) ?></strong></div>
    <?php endif ?>

    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>" />
        <div class="form-grid">
            <?php
            $fields = [
                ['Full Name','full_name','text',true],
                ['Date of Birth','dob','date',true],
                ['Year Born Again','year_born_again','number',false],
                ['Date of Baptism','date_baptism','date',false],
                ['Marital Status','marital_status','select',['Single','Married','Divorced','Widowed']],
                ['Year Joined BLCI','year_joined_blci','number',false],
                ['Occupation','occupation','text',false],
                ['Ministry','ministry','text',false],
                ['Contact Number','contact_number','tel',false],
                ['Phone Number','phone_number','tel',false],
                ['NRC','nrc','text',false],
                ['Email','email','email',false],
                ['Gender','gender','select',['Male','Female','Other']],
                ['Cell Group','cell_group','text',false],
                ['Cell Group Leader','cell_group_leader','text',false],
                ['Leader Phone','cell_group_leader_phone','tel',false],
                ['Leader Address','cell_group_leader_address','text',false],
                ['Area','area','text',false],
                ['Cell Host','cell_group_host','text',false],
                ['Host Phone','cell_group_host_phone','tel',false],
                ['Cell Address','cell_group_address','text',false],
                ['Zone','zone','text',false],
            ];

            foreach ($fields as $f) {
                echo '<div class="form-group">';
                echo "<label for='{$f[1]}'>{$f[0]}</label>";
                if ($f[2] === 'select') {
                    echo "<select id='{$f[1]}' name='{$f[1]}'>";
                    echo "<option value=''>Select</option>";
                    foreach ($f[3] as $opt) {
                        $sel = $input[$f[1]] === $opt ? 'selected' : '';
                        echo "<option $sel>$opt</option>";
                    }
                    echo "</select>";
                } else {
                    $req = !empty($f[3]) ? 'required' : '';
                    echo "<input id='{$f[1]}' name='{$f[1]}' type='{$f[2]}' value='".htmlspecialchars($input[$f[1]])."' $req />";
                }
                echo '</div>';
            }
            ?>
            <div class="form-group">
                <label for="photo">Photo Upload</label>
                <input id="photo" name="photo" type="file" accept="image/*" />
            </div>
            <div class="form-group">
                <label for="number_of_children">Number of Children</label>
                <input id="number_of_children" name="number_of_children" type="number" min="0" max="10" value="<?= intval($input['number_of_children']) ?>" />
            </div>
        </div>

        <div id="childrenContainer">
            <?php foreach ($input['children'] as $i => $child): ?>
                <div class="child-group">
                    <h3>Child <?= $i+1 ?></h3>
                    <label>Name</label><input type="text" name="children[name][]" value="<?= htmlspecialchars($child['name']) ?>" />
                    <label>Date of Birth</label><input type="date" name="children[dob][]" value="<?= htmlspecialchars($child['dob']) ?>" />
                    <label>Age</label><input type="text" name="children[age][]" value="<?= htmlspecialchars($child['age']) ?>" readonly />
                    <label>Tithe Number</label><input type="text" name="children[tithe_number][]" value="<?= htmlspecialchars($child['tithe_number']) ?>" readonly />
                </div>
            <?php endforeach ?>
        </div>

        <button type="submit" class="btn-primary">Add Member</button>
    </form>
</main>
</div>

<script>
const numberOfChildrenInput = document.getElementById('number_of_children');
const childrenContainer = document.getElementById('childrenContainer');

function createChildGroup(index) {
    const div = document.createElement('div');
    div.className = 'child-group';
    div.innerHTML = `
        <h3>Child ${index + 1}</h3>
        <label>Name</label><input type="text" name="children[name][]" />
        <label>Date of Birth</label><input type="date" name="children[dob][]" />
        <label>Age</label><input type="text" name="children[age][]" readonly />
        <label>Tithe Number</label><input type="text" name="children[tithe_number][]" readonly />
    `;
    return div;
}

function updateChildrenInputs() {
    childrenContainer.innerHTML = '';
    const count = parseInt(numberOfChildrenInput.value) || 0;
    for (let i = 0; i < count; i++) {
        childrenContainer.appendChild(createChildGroup(i));
    }
}
numberOfChildrenInput.addEventListener('change', updateChildrenInputs);
</script>
</body>
</html>
