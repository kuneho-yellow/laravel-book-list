<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Book;

class BookSearchTest extends TestCase
{
    // TODO: Properly set up a database for testing 
    // use DatabaseTransactions;   // Prevent changes to the actual database

    /****** Tests ******/

    public function testSearchValid()
    {
        // Make sure there are no entries first
        Book::query()->delete();
        $this->assertEquals(Book::count(), 0);

        // Add multiple books
        $testBooks = [
            [
                "title" => "The Adventures of Tom Sawyer",
                "author" => "Mark Twain"
            ],
            [
                "title" => "Lewis in Wonderland",
                "author" => "Lewis"
            ],
            [
                "title" => "The Adventures of Mark Twain",
                "author" => "Alice Carroll"
            ],
        ];
        foreach ($testBooks as $testBook) {
            Book::create($testBook);
        }

        $this->assertEquals(Book::count(), 3);

        // Execute search through get
        $searchString = "Mark";
        $response = $this->get("/?search={ $searchString }");
        $response->assertStatus(200);

        // Note: This only asserts that the search string has been passed to the view
        $response->assertSee($searchString);

        // TODO: Implement something that can test for the following 
        // $response->assertSee("The Adventures of Tom Sawyer");
        // $response->assertSee("The Adventures of Mark Twain");
        // $response->assertDontSee("Lewis in Wonderland");
    }
}
