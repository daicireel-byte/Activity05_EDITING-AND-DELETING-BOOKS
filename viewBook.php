<?php
require_once "library.php";
$library = new Library();

$search_query = "";
$selected_genre = "";

if($_SERVER["REQUEST_METHOD"] == "GET") {
    $search_query = isset($_GET["search"]) ? trim(htmlspecialchars($_GET["search"])) : "";
    $selected_genre = isset($_GET["genre"]) ? trim(htmlspecialchars($_GET["genre"])) : "";
}

$book_list = $library->getBooks($search_query, $selected_genre);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library Books</title>
    <link rel="stylesheet" href="viewbook.css">
</head>
<body>
    <div class="page-container">
        <h1>Library Book Collection</h1>
        
        <form method="get" action="" class="search-form">
            <label class="search-label">Search Books:</label>
            <input type="search" name="search" id="search" class="search-input" value="<?= htmlspecialchars($search_query) ?>">
            <select name="genre" id="genre" class="genre-filter">
                <option value="">All Genres</option>
                <option value="History" <?= ($selected_genre == "History") ? "selected" : "" ?>>History</option>
                <option value="Science" <?= ($selected_genre == "Science") ? "selected" : "" ?>>Science</option>
                <option value="Fiction" <?= ($selected_genre == "Fiction") ? "selected" : "" ?>>Fiction</option>
            </select>
            <input type="submit" value="Find Books" class="search-submit">
        </form>

        <?php if($book_list && count($book_list) > 0): ?>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Genre</th>
                        <th>Year</th>
                        <th>Publisher</th>
                        <th>Copies</th>
                        <th>Status</th>
                        <th>Options</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $counter = 1;
                    foreach($book_list as $book_item):
                       $delete_confirmation = "Are you sure you want to remove '". $book_item["title"] . "' from the library?";
                    ?>
                    <tr>
                        <td><?= $counter++ ?></td>
                        <td><?= htmlspecialchars($book_item["title"]) ?></td>
                        <td><?= htmlspecialchars($book_item["author"]) ?></td>
                        <td><?= htmlspecialchars($book_item["genre"]) ?></td>
                        <td><?= htmlspecialchars($book_item["publication_year"]) ?></td>
                        <td><?= htmlspecialchars($book_item["publisher"]) ?></td>
                        <td><?= htmlspecialchars($book_item["copies"]) ?></td>
                        <td><?= htmlspecialchars($book_item["status"]) ?></td>
                        <td>
                            <a href="editBook.php?id=<?= $book_item["id"]?>" class="edit-action">Edit</a>
                            <a href="deleteBook.php?id=<?= $book_item["id"]?>" class="delete-action" 
                               onclick="return confirm('<?= htmlspecialchars($delete_confirmation) ?>')">Remove</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="empty-state">
                <p>No books found in the library. Start by adding a new book!</p>
            </div>
        <?php endif; ?>

        <div class="action-container">
            <a href="addBook.php" class="primary-button">Add New Book</a>
        </div>
    </div>
</body>
</html>