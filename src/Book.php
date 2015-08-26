<?php

  class Book
  {

      private $title;
      private $id;

      function __construct($title, $id=null)
      {
          $this->title = $title;
          $this->id = $id;
      }

      function getTitle()
      {
          return $this->title;
      }

      function setTitle($new_title)
      {
          $this->title = $new_title;
      }

      function getId()
      {
          return $this->id;
      }

      function save()
      {
          $GLOBALS['DB']->exec("INSERT INTO books (title) VALUES ('{$this->getTitle()}');");
          $this->id=$GLOBALS['DB']->lastInsertId();
      }

      function update($new_title)
      {
          $GLOBALS['DB']->exec("UPDATE books SET title = '{$new_title}' WHERE id = {$this->getId()};");
          $this->setTitle($new_title);
      }

      function delete()
      {
          $GLOBALS['DB']->exec("DELETE FROM books WHERE id = {$this->getId()};");
          $GLOBALS['DB']->exec("DELETE FROM authors_books WHERE book_id = {$this->getId()};");
      }

      function addAuthor($author)
      {
          $GLOBALS['DB']->exec("INSERT INTO authors_books (author_id, book_id) VALUES ({$author->getId()}, {$this->getId()});");
      }

      function getAuthors()
      {
          $returned_authors = $GLOBALS['DB']->query("SELECT authors.* FROM books JOIN authors_books ON (books.id = authors_books.book_id) JOIN authors ON (authors_books.author_id = authors.id) WHERE books.id = {$this->getId()} ORDER BY authors.name;");

          $authors = array();
          foreach($returned_authors as $author) {
              $name = $author['name'];
              $id = $author['id'];
              $new_author = new Author($name, $id);
              array_push($authors, $new_author);
          }
          return $authors;
      }

      static function getAll()
      {
          $returned_books = $GLOBALS['DB']->query("SELECT * FROM books ORDER BY title;");
          $books = array();
          foreach($returned_books as $book) {
              $title = $book['title'];
              $id = $book['id'];
              $new_book = new Book($title, $id);
              array_push($books, $new_book);
          }
          return $books;
      }

      static function deleteAll()
      {
          $GLOBALS['DB']->exec("DELETE FROM books;");
      }

      static function find($search_id)
      {
          $found = null;
          $books = Book::getAll();
          foreach($books as $book){
              $book_id = $book->getId();
              if($book_id == $search_id){
                  $found = $book;
              }

          }//end foreach
          return $found;
      }

  }


 ?>
