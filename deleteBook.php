<?php
require_once "library.php";


session_start();

$library = new Library();

if($_SERVER["REQUEST_METHOD"] == "GET"){
    if(isset($_GET["id"])) {
        $book_id = trim(htmlspecialchars($_GET["id"]));
        
        
        if (!is_numeric($book_id)) {
            $_SESSION['error_message'] = "Invalid book identifier";
            header("Location: viewBook.php");
            exit();
        }
        
        $book = $library->getBook($book_id);
        if(!$book) {
            $_SESSION['error_message'] = "Book record not found in database";
            header("Location: viewBook.php");
            exit();
        } else {
            
            if ($library->removeBook($book_id)) {
                $_SESSION['success_message'] = "Book successfully removed from library";
            } else {
                $_SESSION['error_message'] = "Failed to remove book from database";
            }
            header("Location: viewBook.php");
            exit();
        }
    } else {
        $_SESSION['error_message'] = "No book identifier provided";
        header("Location: viewBook.php");
        exit();
    }
}

