<?php
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

// Directory where uploaded files are stored
$uploadsDir = "uploads/";

// Fetch all `.xlsx` files from the uploads folder
$files = glob($uploadsDir . "*.xlsx");
if (empty($files)) {
    die("<script>alert('No uploaded Excel files found! Please upload a file first.'); window.location.href='index.php';</script>");
}

// Sort files by modification time (most recent first)
usort($files, function($a, $b) {
    return filemtime($b) - filemtime($a);
});

// Get the most recent file
$latestFile = $files[0];

// Load the Excel file and specify the "Centers" sheet
$spreadsheet = IOFactory::load($latestFile);
$worksheet = $spreadsheet->getSheetByName('Center'); // Load data from the "Centers" sheet
if (!$worksheet) {
    die("<script>alert('The Centers sheet is not found in the uploaded Excel file!'); window.location.href='index.php';</script>");
}

// Convert sheet data to an array
$data = $worksheet->toArray();

// Count total rows, excluding the header row
$totalRows = count($data) > 1 ? count($data) - 1 : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Centers</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center mb-4">View Centers - Centers Sheet Data</h2>
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <?php
                    // Display headers
                    if (!empty($data)) {
                        foreach ($data[0] as $header) {
                            echo "<th>" . htmlspecialchars($header) . "</th>";
                        }
                        array_shift($data); // Remove the headers from data
                    }
                    ?>
                </tr>
            </thead>
            <tbody>
                <?php
                // Display rows
                foreach ($data as $row) {
                    echo "<tr>";
                    foreach ($row as $cell) {
                        echo "<td>" . htmlspecialchars($cell) . "</td>";
                    }
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
        <!-- Total row count -->
        <div class="mt-3 text-end">
            <strong>Total Rows: <?php echo $totalRows; ?></strong>
        </div>
        <div class="text-center mt-4">
            <a href="index.php" class="btn btn-primary">Back to Home</a>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>