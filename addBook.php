<?php
require_once "library.php";
$bookObj = new Library();

$book = [];
$errors = [];
$submit_success = "";

if($_SERVER["REQUEST_METHOD"] == "POST") {
    $book["title"] = trim(htmlspecialchars($_POST["title"]));
    $book["author"] = trim(htmlspecialchars($_POST["author"]));
    $book["genre"] = trim(htmlspecialchars($_POST["genre"]));
    $book["publication_year"] = trim(htmlspecialchars($_POST["publication_year"]));
    $book["publisher"] = trim(htmlspecialchars($_POST["publisher"]));
    $book["copies"] = trim(htmlspecialchars($_POST["copies"]));

    if(empty($book["title"])) {
        $errors["title"] = "Title is required";
    } elseif($bookObj->isTitleExists($book["title"])) {
        $errors["title"] = "This title already exists";
    }

    if(empty($book["author"])) {
        $errors["author"] = "Author is required";
    }

    if(empty($book["genre"])) {
        $errors["genre"] = "Genre is required";
    }

    if(empty($book["publication_year"])) {
        $errors["publication_year"] = "Publication year is required";
    } elseif(!is_numeric($book["publication_year"])) {
        $errors["publication_year"] = "Publication year must be a number";
    } elseif($book["publication_year"] > date("Y")) {
        $errors["publication_year"] = "Publication year must not be in the future";
    }

    if(empty($book["copies"])) {
        $errors["copies"] = "Copies is required";
    } elseif(!is_numeric($book["copies"])) {
        $errors["copies"] = "Copies must be a number";
    }

    if(empty(array_filter($errors))) {
        $bookObj->title = $book["title"];
        $bookObj->author = $book["author"];
        $bookObj->genre = $book["genre"];
        $bookObj->publication_year = $book["publication_year"];
        $bookObj->publisher = $book["publisher"];
        $bookObj->copies = $book["copies"];

        if($bookObj->addBook()) {
            $submit_success = "Book was added successfully";
            $book = []; // Clear form
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Book</title>
    <link rel="stylesheet" href="addbook.css">
</head>
<body>
    <div class="container">
        <h1>Book Form</h1>
        <form action="" method="post">
            <label for="title">Title <span class="required">*</span></label>
            <input type="text" name="title" id="title" value="<?= $book["title"] ?? "" ?>">
            <p class="error"><?= $errors["title"] ?? "" ?></p>

            <label for="author">Author <span class="required">*</span></label>
            <input type="text" name="author" id="author" value="<?= $book["author"] ?? "" ?>">
            <p class="error"><?= $errors["author"] ?? "" ?></p>

            <label for="genre">Genre <span class="required">*</span></label>
            <select name="genre" id="genre">
                <option value="">--Select Genre--</option>
                <option value="History" <?= (isset($book["genre"]) && $book["genre"] == "History") ? "selected" : "" ?>>History</option>
                <option value="Science" <?= (isset($book["genre"]) && $book["genre"] == "Science") ? "selected" : "" ?>>Science</option>
                <option value="Fiction" <?= (isset($book["genre"]) && $book["genre"] == "Fiction") ? "selected" : "" ?>>Fiction</option>
            </select>
            <p class="error"><?= $errors["genre"] ?? "" ?></p>

            <label for="publication_year">Publication Year <span class="required">*</span></label>
            <input type="text" name="publication_year" id="publication_year" value="<?= $book["publication_year"] ?? "" ?>">
            <p class="error"><?= $errors["publication_year"] ?? "" ?></p>

            <label for="publisher">Publisher</label>
            <input type="text" name="publisher" id="publisher" value="<?= $book["publisher"] ?? "" ?>">

            <label for="copies">Copies <span class="required">*</span></label>
            <input type="text" name="copies" id="copies" value="<?= $book["copies"] ?? "" ?>">
            <p class="error"><?= $errors["copies"] ?? "" ?></p>

            <input type="submit" value="Add Book" class="submit-btn">
            <p class="success"><?= $submit_success ?></p>
        </form>

        <div class="nav-btn">
            <a href="viewBook.php">View Book List</a>
        </div>
    </div>
</body>
</html>
