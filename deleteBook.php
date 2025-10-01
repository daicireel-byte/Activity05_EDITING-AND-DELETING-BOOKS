<?php
require_once "library.php";
$bookObj = new Library();

if($_SERVER["REQUEST_METHOD"] == "GET"){
    if(isset($_GET["id"])) {
        $bid = trim(htmlspecialchars($_GET["id"]));
        $book = $bookObj->fetchBook($bid);
        if(!$book) {
            echo "<a href='viewBook.php'>View Books</a>";
            exit("Book not found");
        } else {
            $bookObj->deleteBook($bid);
            header("Location: viewBook.php");
            exit();
        }
    } else {
        echo "<a href='viewBook.php'>View Books</a>";
        exit("Book not found");
    }
}
