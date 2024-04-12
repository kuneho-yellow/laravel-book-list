<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Book;

class BookSearchTest extends TestCase
{
    use DatabaseTransactions;   // Prevent changes to the actual database

    /****** Tests ******/

    public function testSearchValid()
    {
        $this->setupDatabase();

        // Execute search through get
        $searchString = "Mark";
        $response = $this->sendGetRequest($searchString);

        // Assert books related to search strings are seen
        $response->assertSee("The Adventures of Tom Sawyer");
        $response->assertSee("The Adventures of Mark Twain");

        // Assert unrelated books not seen
        $response->assertDontSee("Lewis in Wonderland");
    }

    public function testSearchEmpty()
    {
        $this->setupDatabase();

        // Execute search through get
        $searchString = "";
        $response = $this->sendGetRequest($searchString);

        // Assert all books seen
        $response->assertSee("The Adventures of Tom Sawyer");
        $response->assertSee("The Adventures of Mark Twain");
        $response->assertSee("Lewis in Wonderland");
    }

    public function testSearchWithSortTitle()
    {
        $this->setupDatabase();

        // Execute search through get
        $searchString = "Mark";
        $response = $this->sendGetRequest($searchString, "title");

        // Assert books related to search strings are seen in ascending titles
        $response->assertSeeInOrder([
            "The Adventures of Mark Twain",
            "The Adventures of Tom Sawyer"]);
        
        // Assert unrelated books not seen
        $response->assertDontSee("Lewis in Wonderland");
    }

    public function testSearchWithSortTitleDescending()
    {
        $this->setupDatabase();

        // Execute search through get
        $searchString = "Mark";
        $response = $this->sendGetRequest($searchString, "title", "desc");

        // Assert books related to search strings are seen in descending titles
        $response->assertSeeInOrder([
            "The Adventures of Tom Sawyer",
            "The Adventures of Mark Twain"]);
        
        // Assert unrelated books not seen
        $response->assertDontSee("Lewis in Wonderland");
    }

    public function testSearchWithSortAuthor()
    {
        $this->setupDatabase();

        // Execute search through get
        $searchString = "Mark";
        $response = $this->sendGetRequest($searchString, "author");

        // Assert books related to search strings are seen in ascending authors
        $response->assertSeeInOrder([
            "The Adventures of Tom Sawyer",
            "William Shakespeare"]);
        
        // Assert unrelated books not seen
        $response->assertDontSee("Lewis in Wonderland");
    }

    public function testSearchWithSortAuthorDescending()
    {
        $this->setupDatabase();

        // Execute search through get
        $searchString = "Mark";
        $response = $this->sendGetRequest($searchString, "author", "desc");

        // Assert books related to search strings are seen in descending authors
        $response->assertSeeInOrder([
            "William Shakespeare",
            "The Adventures of Tom Sawyer"]);
        
        // Assert unrelated books not seen
        $response->assertDontSee("Lewis in Wonderland");
    }

    /****** Helper Functions ******/

    private function setupDatabase()
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
                "author" => "Alice Carroll"
            ],
            [
                "title" => "The Adventures of Mark Twain",
                "author" => "William Shakespeare"
            ],
        ];
        foreach ($testBooks as $testBook) {
            Book::create($testBook);
        }

        $this->assertEquals(Book::count(), 3);
    }

    private function sendGetRequest($searchString = null, $sortBy = null, $sortOrder = null)
    {
        $url = route("books") . "?search=$searchString&sortBy=$sortBy&sortOrder=$sortOrder";
        $response = $this->get($url);
        $response->assertStatus(302);
        $response = $this->followingRedirects()
            ->from(route("books"))
            ->get(route("index"));

        return $response;
    }
}
