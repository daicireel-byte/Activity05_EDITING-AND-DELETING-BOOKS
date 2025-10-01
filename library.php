<?php
require_once "database.php";

class Library extends Database {
    public $title = "";
    public $author = "";
    public $genre = "";
    public $publication_year = "";
    public $publisher = "";
    public $copies = "";
    public $status = "";

    public function insertBook() {
        $sql = "INSERT INTO book (title, author, genre, publication_year, publisher, copies, status) 
                VALUES (:title, :author, :genre, :publication_year, :publisher, :copies, :status)";
        
        $stmt = $this->connect()->prepare($sql);
        $stmt->bindParam(":title", $this->title);
        $stmt->bindParam(":author", $this->author);
        $stmt->bindParam(":genre", $this->genre);
        $stmt->bindParam(":publication_year", $this->publication_year);
        $stmt->bindParam(":publisher", $this->publisher);
        $stmt->bindParam(":copies", $this->copies);
        $stmt->bindParam(":status", $this->status);

        return $stmt->execute();
    }

    public function getBooks($search_term = "", $genre_filter = "") {
        $sql = "SELECT * FROM book 
                WHERE title LIKE CONCAT('%', :search, '%') 
                AND genre LIKE CONCAT('%', :genre, '%') 
                ORDER BY title ASC";
                
        $stmt = $this->connect()->prepare($sql);
        $stmt->bindParam(":search", $search_term);
        $stmt->bindParam(":genre", $genre_filter);
        
        if ($stmt->execute()) {
            return $stmt->fetchAll();
        } else {
            return null;
        }
    }

    public function checkTitleExists($title, $exclude_id = null) {
        if ($exclude_id) {
            $sql = "SELECT COUNT(*) as count FROM book WHERE title = :title AND id != :id";
        } else {
            $sql = "SELECT COUNT(*) as count FROM book WHERE title = :title";
        }
        
        $stmt = $this->connect()->prepare($sql);
        $stmt->bindParam(":title", $title);
        
        if ($exclude_id) {
            $stmt->bindParam(":id", $exclude_id);
        }
        
        $result = null;
        
        if ($stmt->execute()) {
            $result = $stmt->fetch();
        }
        
        if($result["count"] > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function getBook($book_id) {
        $sql = "SELECT * FROM book WHERE id = :id";
        $stmt = $this->connect()->prepare($sql);
        $stmt->bindParam(":id", $book_id);
        
        if ($stmt->execute()) {
           return $stmt->fetch();
        } else {
            return null;
        }
    }

    public function updateBook($book_id) {
        $sql = "UPDATE book SET title = :title, author = :author, genre = :genre, 
                publication_year = :publication_year, publisher = :publisher, copies = :copies, status = :status
                WHERE id = :id";
        
        $stmt = $this->connect()->prepare($sql);
        $stmt->bindParam(":title", $this->title);
        $stmt->bindParam(":author", $this->author);
        $stmt->bindParam(":genre", $this->genre);
        $stmt->bindParam(":publication_year", $this->publication_year);
        $stmt->bindParam(":publisher", $this->publisher);
        $stmt->bindParam(":copies", $this->copies);
        $stmt->bindParam(":status", $this->status);
        $stmt->bindParam(":id", $book_id);

        return $stmt->execute();
    }

    public function removeBook($book_id) {
        $sql = "DELETE FROM book WHERE id = :id";
        
        $stmt = $this->connect()->prepare($sql);
        $stmt->bindParam(":id", $book_id);

        return $stmt->execute();
    }
}
