<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Book;

class BookDeleteTest extends TestCase
{
    // TODO: Properly set up a database for testing 
    use DatabaseTransactions;   // Prevent changes to the actual database

    /****** Tests ******/

    public function testDeleteBookValid()
    {
        $testBook = [
            "title" => "Adventures of Tom Sawyer",
            "author" => "Mark Twain",
        ];
        $bookInstance = Book::create($testBook);
        $bookId = $bookInstance->id;
        $bookCount = Book::count();
        $expectedHttpCode = 302; // Redirect

        // Assert id is not found in the books database
        // Note: Duplicates (non-unique title and author pairs) are currently allowed
        $this->assertDatabaseHas("books", ["id" => $bookId]);

        $this->sendDeleteRequest($bookId, $expectedHttpCode);

        // Assert id is not found in the books database
        $this->assertDatabaseMissing("books", ["id" => $bookId]);

        // Assert book entries decreased by 1
        $this->assertEquals(Book::count(), $bookCount - 1);
    }

    public function testDeleteBookInvalidId()
    {
        $bookCount = Book::count();
        $bookId = $bookCount + 1;
        $expectedHttpCode = 404; // Not found

        $this->sendDeleteRequest($bookId, $expectedHttpCode);

        // Assert NO change to the books database
        $this->assertEquals(Book::count(), $bookCount);
    }

    /****** Helper Functions ******/

    private function sendDeleteRequest($idToDelete, $expectedHttpCode)
    {
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
        $response = $this->delete("/book/{$idToDelete}");

        $response->assertStatus($expectedHttpCode);
    }
}
