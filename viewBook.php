<?php
require_once "library.php";
$bookObj = new Library();

$search = "";
$genre = "";

if($_SERVER["REQUEST_METHOD"] == "GET") {
    $search = isset($_GET["search"]) ? trim(htmlspecialchars($_GET["search"])) : "";
    $genre = isset($_GET["genre"]) ? trim(htmlspecialchars($_GET["genre"])) : "";
}

$books = $bookObj->viewBooks($search, $genre);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Books</title>
    <link rel="stylesheet" href="viewbook.css">
</head>
<body>
    <div class="container">
        <h1>Book List</h1>
        
        <form action="" method="get">
            <label for="">Search:</label>
            <input type="search" name="search" id="search" value="<?= htmlspecialchars($search) ?>">
            <select name="genre" id="genre">
                <option value="">All</option>
                <option value="History" <?= ($genre == "History") ? "selected" : "" ?>>History</option>
                <option value="Science" <?= ($genre == "Science") ? "selected" : "" ?>>Science</option>
                <option value="Fiction" <?= ($genre == "Fiction") ? "selected" : "" ?>>Fiction</option>
            </select>
            <input type="submit" value="Search">
        </form>

        <?php if($books && count($books) > 0): ?>
        <table class="table">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Genre</th>
                    <th>Year</th>
                    <th>Publisher</th>
                    <th>Copies</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $no = 1;
                foreach($books as $book): 
                    $message = "Are you sure you want to delete the book titled ". $book["title"] . "?";
                ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= htmlspecialchars($book["title"]) ?></td>
                    <td><?= htmlspecialchars($book["author"]) ?></td>
                    <td><?= htmlspecialchars($book["genre"]) ?></td>
                    <td><?= htmlspecialchars($book["publication_year"]) ?></td>
                    <td><?= htmlspecialchars($book["publisher"]) ?></td>
                    <td><?= htmlspecialchars($book["copies"]) ?></td>
                    <td>
                        <a href="editBook.php?id=<?= $book["id"]?>">Edit</a>
                        <a href="deleteBook.php?id=<?= $book["id"]?>" onclick="return confirm('<?= htmlspecialchars($message) ?>')">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
        <div class="no-books">
            <p>No books found. Add your first book!</p>
        </div>
        <?php endif; ?>

        <div class="nav-btn">
            <a href="addBook.php">Add Book</a>
        </div>
    </div>
</body>
</html>
