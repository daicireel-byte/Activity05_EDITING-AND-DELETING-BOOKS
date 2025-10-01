<?php
require_once "library.php";

// Start session for CSRF protection
session_start();
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$library = new Library();

$form_data = [];
$validation_errors = [];
$success_message = "";

if($_SERVER["REQUEST_METHOD"] == "POST") {
    // CSRF validation
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Security validation failed");
    }

    $form_data["title"] = trim(htmlspecialchars($_POST["title"]));
    $form_data["author"] = trim(htmlspecialchars($_POST["author"]));
    $form_data["genre"] = trim(htmlspecialchars($_POST["genre"]));
    $form_data["publication_year"] = trim(htmlspecialchars($_POST["publication_year"]));
    $form_data["publisher"] = trim(htmlspecialchars($_POST["publisher"]));
    $form_data["copies"] = trim(htmlspecialchars($_POST["copies"]));
    $form_data["status"] = trim(htmlspecialchars($_POST["status"]));

    // Validate title
    if(empty($form_data["title"])) {
        $validation_errors["title"] = "Book title is required";
    } elseif($library->checkTitleExists($form_data["title"])) {
        $validation_errors["title"] = "This book title is already in the system";
    }

    // Validate author
    if(empty($form_data["author"])) {
        $validation_errors["author"] = "Author name is required";
    }

    // Validate genre with allowed values
    $allowed_genres = ['History', 'Science', 'Fiction'];
    if(empty($form_data["genre"])) {
        $validation_errors["genre"] = "Please select a genre";
    } elseif (!in_array($form_data["genre"], $allowed_genres)) {
        $validation_errors["genre"] = "Please select a valid genre";
    }

    // Validate publication year
    if(empty($form_data["publication_year"])) {
        $validation_errors["publication_year"] = "Publication year is required";
    } elseif(!is_numeric($form_data["publication_year"])) {
        $validation_errors["publication_year"] = "Year must be a valid number";
    } elseif($form_data["publication_year"] > date("Y")) {
        $validation_errors["publication_year"] = "Publication year cannot be in the future";
    }

    // Validate copies
    if(empty($form_data["copies"])) {
        $validation_errors["copies"] = "Number of copies is required";
    } elseif(!is_numeric($form_data["copies"])) {
        $validation_errors["copies"] = "Copies must be a valid number";
    }

    // Validate status
    $allowed_statuses = ['Available', 'Checked Out', 'Maintenance'];
    if(empty($form_data["status"])) {
        $validation_errors["status"] = "Please select a status";
    } elseif (!in_array($form_data["status"], $allowed_statuses)) {
        $validation_errors["status"] = "Please select a valid status";
    }

    // If no errors, proceed with adding book
    if(empty(array_filter($validation_errors))) {
        $library->title = $form_data["title"];
        $library->author = $form_data["author"];
        $library->genre = $form_data["genre"];
        $library->publication_year = $form_data["publication_year"];
        $library->publisher = $form_data["publisher"];
        $library->copies = $form_data["copies"];
        $library->status = $form_data["status"];

        if($library->insertBook()) {
            $success_message = "Book successfully added to library";
            $form_data = []; // Reset form
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Book</title>
    <link rel="stylesheet" href="addbook.css">
</head>
<body>
    <div class="form-container">
        <h1>Add New Book</h1>
        <form method="post" action="">
            <!-- CSRF Token -->
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            
            <label for="title" class="form-label">Book Title <span class="mandatory">*</span></label>
            <input type="text" name="title" id="title" class="form-input" value="<?= $form_data["title"] ?? "" ?>">
            <span class="validation-error"><?= $validation_errors["title"] ?? "" ?></span>

            <label for="author" class="form-label">Author <span class="mandatory">*</span></label>
            <input type="text" name="author" id="author" class="form-input" value="<?= $form_data["author"] ?? "" ?>">
            <span class="validation-error"><?= $validation_errors["author"] ?? "" ?></span>

            <label for="genre" class="form-label">Genre <span class="mandatory">*</span></label>
            <select name="genre" id="genre" class="form-select">
                <option value="">-- Choose Genre --</option>
                <option value="History" <?= (isset($form_data["genre"]) && $form_data["genre"] == "History") ? "selected" : "" ?>>History</option>
                <option value="Science" <?= (isset($form_data["genre"]) && $form_data["genre"] == "Science") ? "selected" : "" ?>>Science</option>
                <option value="Fiction" <?= (isset($form_data["genre"]) && $form_data["genre"] == "Fiction") ? "selected" : "" ?>>Fiction</option>
            </select>
            <span class="validation-error"><?= $validation_errors["genre"] ?? "" ?></span>

            <label for="publication_year" class="form-label">Publication Year <span class="mandatory">*</span></label>
            <input type="text" name="publication_year" id="publication_year" class="form-input" value="<?= $form_data["publication_year"] ?? "" ?>">
            <span class="validation-error"><?= $validation_errors["publication_year"] ?? "" ?></span>

            <label for="publisher" class="form-label">Publisher</label>
            <input type="text" name="publisher" id="publisher" class="form-input" value="<?= $form_data["publisher"] ?? "" ?>">

            <label for="copies" class="form-label">Available Copies <span class="mandatory">*</span></label>
            <input type="text" name="copies" id="copies" class="form-input" value="<?= $form_data["copies"] ?? "" ?>">
            <span class="validation-error"><?= $validation_errors["copies"] ?? "" ?></span>

            <label for="status" class="form-label">Book Status <span class="mandatory">*</span></label>
            <select name="status" id="status" class="form-select">
                <option value="">-- Select Status --</option>
                <option value="Available" <?= (isset($form_data["status"]) && $form_data["status"] == "Available") ? "selected" : "" ?>>Available</option>
                <option value="Checked Out" <?= (isset($form_data["status"]) && $form_data["status"] == "Checked Out") ? "selected" : "" ?>>Checked Out</option>
                <option value="Maintenance" <?= (isset($form_data["status"]) && $form_data["status"] == "Maintenance") ? "selected" : "" ?>>Maintenance</option>
            </select>
            <span class="validation-error"><?= $validation_errors["status"] ?? "" ?></span>

            <input type="submit" value="Add Book to Library" class="submit-button">
            <p class="success-message"><?= $success_message ?></p>
        </form>

        <div class="action-button">
            <a href="viewBook.php">View All Books</a>
        </div>
    </div>
</body>
</html>