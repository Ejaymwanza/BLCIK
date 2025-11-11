<?php
session_start();
require 'helpers.php'; // Include helpers.php for the load_members() function

// Ensure user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: index.php");
    exit;
}

// Fetch members data from the database
$members = load_members();

// Set headers for CSV download
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="members_list.csv"');

// Open output stream for CSV file
$output = fopen('php://output', 'w');

// Header row for CSV file
fputcsv($output, [
    'Name', 
    'Phone', 
    'NRC', 
    'Location', 
    'Church Position', 
    'Zone', 
    'Ministry', 
    'Family', 
    'Age', 
    'Gender', 
    'Date Joined', 
    'Date Baptism', 
    'Date Second Baptism',
    'ID Photo' // Optional
]);

// Data rows for CSV file
foreach ($members as $m) {
    fputcsv($output, [
        $m['name'], 
        $m['phone'], 
        $m['nrc'], 
        $m['location'], 
        $m['position'], 
        $m['zone'], 
        $m['ministry'], 
        $m['family'], 
        $m['age'], 
        $m['gender'], 
        $m['date_joined'], 
        $m['date_baptism'], 
        $m['date_second_baptism'],
        $m['photo'] // Assuming 'photo' contains the file path or URL of the photo
    ]);
}

// Close the output stream
fclose($output);
exit;
?>
