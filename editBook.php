<?php
require_once "library.php";

session_start();
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$library = new Library();

$book_data = [];
$error_messages = [];
$update_success = "";

if($_SERVER["REQUEST_METHOD"] == "GET"){
    if(isset($_GET["id"])) {
        $book_id = trim(htmlspecialchars($_GET["id"]));
        $book_data = $library->getBook($book_id);
        if(!$book_data) {
            echo "<a href='viewBook.php'>Back to Book List</a>";
            exit("The requested book was not found");
        }
    } else {
        echo "<a href='viewBook.php'>Back to Book List</a>";
        exit("No book ID provided");
    }
}
elseif($_SERVER["REQUEST_METHOD"] == "POST") {

    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Security validation failed");
    }

    $book_data["title"] = trim(htmlspecialchars($_POST["title"]));
    $book_data["author"] = trim(htmlspecialchars($_POST["author"]));
    $book_data["genre"] = trim(htmlspecialchars($_POST["genre"]));
    $book_data["publication_year"] = trim(htmlspecialchars($_POST["publication_year"]));
    $book_data["publisher"] = trim(htmlspecialchars($_POST["publisher"]));
    $book_data["copies"] = trim(htmlspecialchars($_POST["copies"]));
    $book_data["status"] = trim(htmlspecialchars($_POST["status"]));

    $allowed_genres = ['History', 'Science', 'Fiction'];
    if(empty($book_data["title"])) {
        $error_messages["title"] = "Book title is required";
    } elseif($library->checkTitleExists($book_data["title"], $_GET["id"])) {
        $error_messages["title"] = "This book title is already in the system";
    }

    if(empty($book_data["author"])) {
        $error_messages["author"] = "Author name is required";
    }

    if(empty($book_data["genre"])) {
        $error_messages["genre"] = "Please select a genre";
    } elseif (!in_array($book_data["genre"], $allowed_genres)) {
        $error_messages["genre"] = "Please select a valid genre";
    }

    if(empty($book_data["publication_year"])) {
        $error_messages["publication_year"] = "Publication year is required";
    } elseif(!is_numeric($book_data["publication_year"])) {
        $error_messages["publication_year"] = "Year must be a valid number";
    } elseif($book_data["publication_year"] > date("Y")) {
        $error_messages["publication_year"] = "Publication year cannot be in the future";
    }

    if(empty($book_data["copies"])) {
        $error_messages["copies"] = "Number of copies is required";
    } elseif(!is_numeric($book_data["copies"])) {
        $error_messages["copies"] = "Copies must be a valid number";
    }
    $allowed_statuses = ['Available', 'Checked Out', 'Maintenance'];
    if(empty($book_data["status"])) {
        $error_messages["status"] = "Please select a status";
    } elseif (!in_array($book_data["status"], $allowed_statuses)) {
        $error_messages["status"] = "Please select a valid status";
    }

    if(empty(array_filter($error_messages))) {
        $library->title = $book_data["title"];
        $library->author = $book_data["author"];
        $library->genre = $book_data["genre"];
        $library->publication_year = $book_data["publication_year"];
        $library->publisher = $book_data["publisher"];
        $library->copies = $book_data["copies"];
        $library->status = $book_data["status"];

        if($library->updateBook($_GET["id"])){
            header("Location: viewBook.php");
            exit();
        } else {
            echo "An error occurred while updating";
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Edit Book Information</title>
    <link rel="stylesheet" href="addbook.css" />
</head>
<body>
    <div class="form-container">
        <h1>Edit Book Details</h1>
        <form method="post" action="">
            
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            
            <label for="title" class="form-label">Book Title <span class="mandatory">*</span></label>
            <input type="text" name="title" id="title" class="form-input" value="<?= htmlspecialchars($book_data["title"] ?? "") ?>" />
            <span class="validation-error"><?= $error_messages["title"] ?? "" ?></span>

            <label for="author" class="form-label">Author <span class="mandatory">*</span></label>
            <input type="text" name="author" id="author" class="form-input" value="<?= htmlspecialchars($book_data["author"] ?? "") ?>" />
            <span class="validation-error"><?= $error_messages["author"] ?? "" ?></span>

            <label for="genre" class="form-label">Genre <span class="mandatory">*</span></label>
            <select name="genre" id="genre" class="form-select">
                <option value="">-- Choose Genre --</option>
                <option value="History" <?= (isset($book_data["genre"]) && $book_data["genre"] == "History") ? "selected" : "" ?>>History</option>
                <option value="Science" <?= (isset($book_data["genre"]) && $book_data["genre"] == "Science") ? "selected" : "" ?>>Science</option>
                <option value="Fiction" <?= (isset($book_data["genre"]) && $book_data["genre"] == "Fiction") ? "selected" : "" ?>>Fiction</option>
            </select>
            <span class="validation-error"><?= $error_messages["genre"] ?? "" ?></span>

            <label for="publication_year" class="form-label">Publication Year <span class="mandatory">*</span></label>
            <input type="text" name="publication_year" id="publication_year" class="form-input" value="<?= htmlspecialchars($book_data["publication_year"] ?? "") ?>" />
            <span class="validation-error"><?= $error_messages["publication_year"] ?? "" ?></span>

            <label for="publisher" class="form-label">Publisher</label>
            <input type="text" name="publisher" id="publisher" class="form-input" value="<?= htmlspecialchars($book_data["publisher"] ?? "") ?>" />

            <label for="copies" class="form-label">Available Copies <span class="mandatory">*</span></label>
            <input type="text" name="copies" id="copies" class="form-input" value="<?= htmlspecialchars($book_data["copies"] ?? "") ?>" />
            <span class="validation-error"><?= $error_messages["copies"] ?? "" ?></span>

            <label for="status" class="form-label">Book Status <span class="mandatory">*</span></label>
            <select name="status" id="status" class="form-select">
                <option value="">-- Select Status --</option>
                <option value="Available" <?= (isset($book_data["status"]) && $book_data["status"] == "Available") ? "selected" : "" ?>>Available</option>
                <option value="Checked Out" <?= (isset($book_data["status"]) && $book_data["status"] == "Checked Out") ? "selected" : "" ?>>Checked Out</option>
                <option value="Maintenance" <?= (isset($book_data["status"]) && $book_data["status"] == "Maintenance") ? "selected" : "" ?>>Maintenance</option>
            </select>
            <span class="validation-error"><?= $error_messages["status"] ?? "" ?></span>

            <input type="submit" value="Update Book Information" class="submit-button" />
            <p class="success-message"><?= $update_success ?></p>
        </form>

        <div class="action-button">
            <a href="viewBook.php">Back to Book List</a>
        </div>
    </div>
</body>
</html>