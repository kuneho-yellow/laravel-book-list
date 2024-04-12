<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Book;

class BookEditTest extends TestCase
{
    // TODO: Properly set up a database for testing 
    use DatabaseTransactions;   // Prevent changes to the actual database

    /****** Tests ******/

    public function testEditBookAuthorValid()
    {
        $origBookData = [
            "title" => "Alice's Adventures in Wonderland",
            "author" => "Lewis Carrol",
        ];
        $bookInstance = Book::create($origBookData);
        $origBookData["id"] = $bookInstance->id;
        $bookCount = Book::count();

        $this->assertBookFound($origBookData, $bookCount);

        $newBookData = [
            "id" => $origBookData["id"],
            "title" => $origBookData["title"],
            "author" => "Mark Twain",
        ];

        $this->sendPutRequest($newBookData);

        $this->assertBookFound($newBookData, $bookCount);
    }

    public function testEditBookInvalidId()
    {
        $bookCount = Book::count();
        $newBookData = [
            "id" => $bookCount + 1,
            "title" => "The Celebrated Jumping Frog of Calaveras County",
            "author" => "Mark Twain",
        ];
        $expectedHttpCode = 404; // Not found

        $this->sendPutRequest($newBookData, $expectedHttpCode);
    }

    public function testEditBookEmptyAuthor()
    {
        $origBookData = [
            "title" => "Alice's Adventures in Wonderland",
            "author" => "Lewis Carrol",
        ];
        $bookInstance = Book::create($origBookData);
        $origBookData["id"] = $bookInstance->id;
        $bookCount = Book::count();

        $this->assertBookFound($origBookData, $bookCount);

        $newBookData = [
            "id" => $origBookData["id"],
            "title" => $origBookData["title"],
            "author" => "",
        ];

        $this->sendPutRequest($newBookData);

        $this->assertBookFound($origBookData, $bookCount);
    }

    public function testEditBookMinAuthor()
    {
        $origBookData = [
            "title" => "Alice's Adventures in Wonderland",
            "author" => "Lewis Carrol",
        ];
        $bookInstance = Book::create($origBookData);
        $origBookData["id"] = $bookInstance->id;
        $bookCount = Book::count();

        $this->assertBookFound($origBookData, $bookCount);

        $newBookData = [
            "id" => $origBookData["id"],
            "title" => $origBookData["title"],
            "author" => str_repeat("L", Book::MIN_AUTHOR_LENGTH),
        ];

        $this->sendPutRequest($newBookData);

        $this->assertBookFound($newBookData, $bookCount);
    }

    public function testEditBookMaxAuthor()
    {
        $origBookData = [
            "title" => "Alice's Adventures in Wonderland",
            "author" => "Lewis Carrol",
        ];
        $bookInstance = Book::create($origBookData);
        $origBookData["id"] = $bookInstance->id;
        $bookCount = Book::count();

        $this->assertBookFound($origBookData, $bookCount);

        $newBookData = [
            "id" => $origBookData["id"],
            "title" => $origBookData["title"],
            "author" => str_repeat("L", Book::MAX_AUTHOR_LENGTH),
        ];

        $this->sendPutRequest($newBookData);

        $this->assertBookFound($newBookData, $bookCount);
    }

    public function testEditBookShortAuthor()
    {
        $origBookData = [
            "title" => "Alice's Adventures in Wonderland",
            "author" => "Lewis Carrol",
        ];
        $bookInstance = Book::create($origBookData);
        $origBookData["id"] = $bookInstance->id;
        $bookCount = Book::count();

        $this->assertBookFound($origBookData, $bookCount);

        $newBookData = [
            "id" => $origBookData["id"],
            "title" => $origBookData["title"],
            "author" => str_repeat("L", Book::MIN_AUTHOR_LENGTH - 1),
        ];

        $this->sendPutRequest($newBookData);

        $this->assertBookFound($origBookData, $bookCount);
    }

    public function testEditBookLongAuthor()
    {
        $origBookData = [
            "title" => "Alice's Adventures in Wonderland",
            "author" => "Lewis Carrol",
        ];
        $bookInstance = Book::create($origBookData);
        $origBookData["id"] = $bookInstance->id;
        $bookCount = Book::count();

        $this->assertBookFound($origBookData, $bookCount);

        $newBookData = [
            "id" => $origBookData["id"],
            "title" => $origBookData["title"],
            "author" => str_repeat("L", Book::MAX_AUTHOR_LENGTH + 1),
        ];

        $this->sendPutRequest($newBookData);

        $this->assertBookFound($origBookData, $bookCount);
    }

    /****** Helper Functions ******/

    private function sendPutRequest($bookDataWithId, $expectedHttpCode = 302)
    {
        // Add other items needed in the request
        $bookDataWithId["search"] = null;
        $bookDataWithId["sortBy"] = null;
        $bookDataWithId["sortOrder"] = null;

        // Disable CSRF token verification
        // Note: This is supposed to be disabled automatically by Laravel
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
        $route = route("edit", ["id" => $bookDataWithId["id"]]);
        $response = $this->put($route, $bookDataWithId);

        $response->assertStatus($expectedHttpCode);
    }

    private function assertBookFound($bookDataWithId, $initialBookCount)
    {
        // Assert book is found in the database
        $this->assertDatabaseHas("books", $bookDataWithId);

        // Assert NO change to the books database
        $this->assertEquals(Book::count(), $initialBookCount);
    }

    private function assertBookNotFound($bookDataWithId, $initialBookCount)
    {
        // Assert book is NOT found in the database
        $this->assertDatabaseMissing("books", $bookDataWithId);

        // Assert NO change to the books database
        $this->assertEquals(Book::count(), $initialBookCount);
    }
}
