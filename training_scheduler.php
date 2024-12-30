<?php
require 'vendor/autoload.php'; // Include PhpSpreadsheet autoload if using Composer

use PhpOffice\PhpSpreadsheet\IOFactory;

// Function to schedule training
function scheduleTraining($filePath)
{
    // Load the Excel file
    $spreadsheet = IOFactory::load($filePath);

    // Read the 'sheet1' and 'center' sheets
    $candidateSheet = $spreadsheet->getSheetByName('sheet1');
    $centerSheet = $spreadsheet->getSheetByName('center');

    // Extract candidate data
    $candidates = [];
    foreach ($candidateSheet->getRowIterator(2) as $row) {  // Assuming data starts from row 2
        $data = [];
        $cellIterator = $row->getCellIterator();
        $cellIterator->setIterateOnlyExistingCells(false);
        foreach ($cellIterator as $cell) {
            $data[] = $cell->getFormattedValue();
        }

        // Skip rows with "Grand Total" or empty names
        if (stripos($data[0], 'Grand Total') !== false || empty($data[0])) {
            continue;
        }

        $candidates[] = [
            'name' => $data[0],      // Candidate Name
            'city' => $data[1],      // City
            'gender' => $data[2],    // Gender (Male/Female)
            'category' => $data[3],  // Category (PH, OBC, UR, etc.)
            'ph' => $data[4],        // PH (Yes/No)
            'trade' => $data[5],     // Trade
            'day' => $data[6],       // Day (for scheduling)
        ];
    }

    // Extract center data
    $centers = [];
    foreach ($centerSheet->getRowIterator(2) as $row) {  // Assuming data starts from row 2
        $data = [];
        $cellIterator = $row->getCellIterator();
        $cellIterator->setIterateOnlyExistingCells(false);
        foreach ($cellIterator as $cell) {
            $data[] = $cell->getFormattedValue();
        }

        // Skip rows with "Grand Total" or empty center names
        if (stripos($data[0], 'Grand Total') !== false || empty($data[0])) {
            continue;
        }

        $centers[] = [
            'name' => $data[0],   // Center Name
            'city' => $data[1],   // City
            'max_count' => $data[2], // Max Candidates per center
        ];
    }

    // Step 1: Prioritize PH, Female, Male candidates
    usort($candidates, function ($a, $b) {
        $priority = [
            'PH' => 1,    // Priority for PH candidates
            'Female' => 2, // Priority for Female candidates
            'Male' => 3,   // Priority for Male candidates
            'Other' => 4,  // Default priority for others (if category is not recognized)
        ];

        $aCategory = isset($a['category']) ? $a['category'] : '';
        $bCategory = isset($b['category']) ? $b['category'] : '';

        // If category is not in the priority list, assign it a default priority
        if (!isset($priority[$aCategory])) {
            $aCategory = 'Other';
        }
        if (!isset($priority[$bCategory])) {
            $bCategory = 'Other';
        }

        return $priority[$aCategory] <=> $priority[$bCategory];
    });

    // Step 2: Distribute candidates across centers
    $scheduledCandidates = [];
    $centerCounts = array_fill(0, count($centers), 0);  // Track the number of candidates in each center

    foreach ($candidates as $candidate) {
        foreach ($centers as $index => $center) {
            if ($centerCounts[$index] < $center['max_count']) {
                $scheduledCandidates[] = [
                    'candidate' => $candidate['name'],
                    'city' => $center['city'],  // Include city from center sheet
                    'center' => $center['name'],
                    'trade' => $candidate['trade'],
                    'gender' => $candidate['gender'], // Include gender from sheet1
                    'day' => $candidate['day'],
                ];
                $centerCounts[$index]++;
                break;
            }
        }
    }

    return $scheduledCandidates;
}

// Check if file is uploaded
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['file'])) {
    if ($_FILES['file']['error'] !== UPLOAD_ERR_OK) {
        echo "Error: " . $_FILES['file']['error'];
    } else {
        // Create the uploads directory if it doesn't exist
        $uploadsDir = __DIR__ . '/uploads/';
        if (!is_dir($uploadsDir)) {
            mkdir($uploadsDir, 0777, true); // Ensure the directory is writable
        }

        // Define the target path for the uploaded file
        $targetPath = $uploadsDir . basename($_FILES['file']['name']);

        // Move the uploaded file to the uploads folder
        if (move_uploaded_file($_FILES['file']['tmp_name'], $targetPath)) {
            // File saved successfully, schedule training
            $scheduledCandidates = scheduleTraining($targetPath);
        } else {
            echo "Failed to save the uploaded file.";
        }
    }
} else {
    $scheduledCandidates = null;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Training Scheduler</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container my-5">
        <h1 class="text-center mb-4">Training Scheduler</h1>

        <!-- File Upload Form -->
        <div class="card shadow mb-4">
            <div class="card-header bg-primary text-white">
                Upload Excel File
            </div>
            <div class="card-body">
                <form action="training_scheduler.php" method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="file" class="form-label">Choose Excel File:</label>
                        <input type="file" name="file" id="file" class="form-control" accept=".xlsx,.xls" required>
                    </div>
                    <button type="submit" class="btn btn-success w-100">Upload and Schedule</button>
                </form>
            </div>
        </div>

        <!-- Displaying the Scheduled Candidates -->
        <?php if ($scheduledCandidates): ?>
            <div class="card shadow">
                <div class="card-header bg-success text-white">
                    Scheduled Candidates
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Candidate Name</th>
                                <th>City</th>
                                <th>Training Center</th>
                                <th>Trade</th>
                                <th>Gender</th>
                                <th>Day</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($scheduledCandidates as $schedule): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($schedule['candidate']); ?></td>
                                    <td><?php echo htmlspecialchars($schedule['city']); ?></td>
                                    <td><?php echo htmlspecialchars($schedule['center']); ?></td>
                                    <td><?php echo htmlspecialchars($schedule['trade']); ?></td>
                                    <td><?php echo htmlspecialchars($schedule['gender']); ?></td>
                                    <td><?php echo htmlspecialchars($schedule['day']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
