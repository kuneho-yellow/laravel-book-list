<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Book;

class BookAddTest extends TestCase
{
    // TODO: Properly set up a database for testing 
    use DatabaseTransactions;   // Prevent changes to the actual database

    /****** Tests ******/

    public function testAddBookValid()
    {
        $testBook = [
            "title" => "Pride and Prejudice",
            "author" => "Jane Austen",
        ];
        $bookCount = Book::count();

        $this->sendPostRequest($testBook);
        $this->assertBookAdded($testBook, $bookCount);
    }

    public function testAddBookEmptyTitle()
    {
        $testBook = [
            "title" => "",
            "author" => "Lewis Carroll",
        ];
        $bookCount = Book::count();

        $this->sendPostRequest($testBook);
        $this->assertBookNotAdded($testBook, $bookCount);
    }

    public function testAddBookEmptyAuthor()
    {
        $testBook = [
            "title" => "Adventures of Tom Sawyer",
            "author" => "",
        ];
        $bookCount = Book::count();

        $this->sendPostRequest($testBook);
        $this->assertBookNotAdded($testBook, $bookCount);
    }

    public function testAddBookValidDuplicate()
    {
        $testBook = [
            "title" => "Pride and Prejudice",
            "author" => "Jane Austen",
        ];
        $bookCount = Book::count();

        $this->sendPostRequest($testBook);
        $this->assertBookAdded($testBook, $bookCount);
        $this->sendPostRequest($testBook);
        $this->assertBookAdded($testBook, $bookCount + 1);
    }

    public function testAddBookValidWithExtraSpaces()
    {
        $testBook = [
            "title" => "   Pride and Prejudice  ",
            "author" => "      Jane Austen ",
        ];
        $bookCount = Book::count();

        $this->sendPostRequest($testBook);

        // Trim inputs before checking presence in database
        $testBook["title"] = trim($testBook["title"]);
        $testBook["author"] = trim($testBook["author"]);
        $this->assertBookAdded($testBook, $bookCount);
    }

    public function testAddBookWhitespaceOnly()
    {
        $testBook = [
            "title" => "     ",
            "author" => "   ",
        ];
        $bookCount = Book::count();

        $this->sendPostRequest($testBook);
        $this->assertBookNotAdded($testBook, $bookCount);
    }

    public function testAddBookMinTitle()
    {
        $testBook = [
            "title" => str_repeat("P", Book::MIN_TITLE_LENGTH),
            "author" => "Jane Austen",
        ];
        $bookCount = Book::count();

        $this->sendPostRequest($testBook);
        $this->assertBookAdded($testBook, $bookCount);
    }

    public function testAddBookMaxTitle()
    {
        $testBook = [
            "title" => str_repeat("P", Book::MAX_TITLE_LENGTH),
            "author" => "Jane Austen",
        ];
        $bookCount = Book::count();

        $this->sendPostRequest($testBook);
        $this->assertBookAdded($testBook, $bookCount);
    }

    public function testAddBookShortTitle()
    {
        $testBook = [
            "title" => str_repeat("P", Book::MIN_TITLE_LENGTH - 1),
            "author" => "Jane Austen",
        ];
        $bookCount = Book::count();

        $this->sendPostRequest($testBook);
        $this->assertBookNotAdded($testBook, $bookCount);
    }

    public function testAddBookLongTitle()
    {
        $testBook = [
            "title" => str_repeat("P", Book::MAX_TITLE_LENGTH + 1),
            "author" => "Jane Austen",
        ];
        $bookCount = Book::count();

        $this->sendPostRequest($testBook);
        $this->assertBookNotAdded($testBook, $bookCount);
    }

    public function testAddBookMinAuthor()
    {
        $testBook = [
            "title" => "Pride and Prejudice",
            "author" => str_repeat("J", Book::MIN_AUTHOR_LENGTH),
        ];
        $bookCount = Book::count();

        $this->sendPostRequest($testBook);
        $this->assertBookAdded($testBook, $bookCount);
    }

    public function testAddBookMaxAuthor()
    {
        $testBook = [
            "title" => "Pride and Prejudice",
            "author" => str_repeat("J", Book::MAX_AUTHOR_LENGTH),
        ];
        $bookCount = Book::count();

        $this->sendPostRequest($testBook);
        $this->assertBookAdded($testBook, $bookCount);
    }

    public function testAddBookShortAuthor()
    {
        $testBook = [
            "title" => "Pride and Prejudice",
            "author" => str_repeat("J", Book::MIN_AUTHOR_LENGTH - 1),
        ];
        $bookCount = Book::count();

        $this->sendPostRequest($testBook);
        $this->assertBookNotAdded($testBook, $bookCount);
    }

    public function testAddBookLongAuthor()
    {
        $testBook = [
            "title" => "Pride and Prejudice",
            "author" => str_repeat("J", Book::MAX_AUTHOR_LENGTH + 1),
        ];
        $bookCount = Book::count();

        $this->sendPostRequest($testBook);
        $this->assertBookNotAdded($testBook, $bookCount);
    }

    /****** Helper Functions ******/

    private function sendPostRequest($bookData)
    {
        // Add other items needed in the request
        $bookData["search"] = null;
        $bookData["sortBy"] = null;
        $bookData["sortOrder"] = null;

        // Disable CSRF token verification
        // Note: This is supposed to be disabled automatically by Laravel
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
        $response = $this->post("/add", $bookData);

        // Note: Expected status is HTTP_FOUND(302) instead of HTTP_CREATED(201) due to redirect
        $response->assertStatus(302);
    }

    private function assertBookAdded($bookData, $initialBookCount)
    {
        // Assert book is found in the database
        $this->assertDatabaseHas("books", $bookData);

        // Assert book entries increased by 1
        $this->assertEquals(Book::count(), $initialBookCount + 1);
    }

    private function assertBookNotAdded($bookData, $initialBookCount)
    {
        // Assert book is NOT found in the database
        $this->assertDatabaseMissing("books", $bookData);

        // Assert NO change to the books database
        $this->assertEquals(Book::count(), $initialBookCount);
    }
}
