<?php
require 'helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo "Method not allowed.";
    exit;
}

$tithe_number = trim($_POST['tithe_number'] ?? '');

if (empty($tithe_number)) {
    // Redirect back with error message or show error
    header('Location: dashboard.php?error=Missing+tithe+number');
    exit;
}

// Load members
$members = load_members();

// Find and remove the member with matching tithe_number
$found = false;
foreach ($members as $key => $member) {
    if (($member['tithe_number'] ?? '') === $tithe_number) {
        $found = true;
        unset($members[$key]);
        break;
    }
}

if (!$found) {
    header('Location: dashboard.php?error=Member+not+found');
    exit;
}

// Save the updated members list
// Assuming save_members() is a function in helpers.php that saves the array back
if (function_exists('save_members')) {
    save_members($members);
} else {
    // If you don't have a save function, you'll need to implement one based on how you store data
    header('Location: dashboard.php?error=Saving+members+not+implemented');
    exit;
}

// Redirect back to dashboard with success message
header('Location: dashboard.php?success=Member+deleted+successfully');
exit;
