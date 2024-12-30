<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Training Scheduler</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome CSS for Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container my-5">
        <!-- Heading -->
        <div class="text-center mb-4">
            <h1 class="display-4 fw-bold text-primary">Training Scheduler</h1>
            <p class="text-secondary">Manage candidates, centers, and scheduling in one place</p>
        </div>

        <!-- Button Section -->
        <div class="d-flex justify-content-center gap-3 mb-4 flex-wrap">
            <!-- Upload & Schedule -->
            <a href="training_scheduler.php" class="btn btn-warning btn-lg d-flex align-items-center gap-2 text-white">
                <i class="fas fa-upload fa-2x"></i> Upload & Schedule
            </a>

            <!-- Show All Candidates -->
            <a href="view_candidates.php" class="btn btn-primary btn-lg d-flex align-items-center gap-2">
                <i class="fas fa-users fa-2x"></i> Show All Candidates
            </a>

            <!-- Show Available Centers -->
            <a href="show_center.php" class="btn btn-success btn-lg d-flex align-items-center gap-2">
                <i class="fas fa-map-marker-alt fa-2x"></i> Show Available Centers
            </a>

            <!-- Show Files and Schedule -->
            <a href="view_files.php" class="btn btn-info btn-lg d-flex align-items-center gap-2 text-white">
                <i class="fas fa-file-alt fa-2x"></i> Show Files and Schedule
            </a>
        </div>

        <!-- Additional Info Section -->
        <div class="card shadow">
            <div class="card-body text-center">
                <p class="text-secondary">
                    Use the buttons above to navigate between managing candidates, centers, uploading files, viewing schedules, or generating the training schedule.
                </p>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
