<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Book;

class BookExportTest extends TestCase
{
    use DatabaseTransactions;   // Prevent changes to the actual database

    /****** Tests ******/

    public function testCsvExportBooks()
    {
        $this->setupDatabase();

        $response = $this->post(route("books.export"), [
            "exportAs" => "csv",
            "exportOption" => "books"
        ]);
        $response->assertStatus(200);
        $response->assertSeeInOrder([
            'Title,Author',
            'Untitled,"The Author"',
            '"""Quotes""","The ""Quote Lover"", With Commas <And More>!"'
        ]);
    }

    public function testCsvExportTitles()
    {
        $this->setupDatabase();

        $response = $this->post(route("books.export"), [
            "exportAs" => "csv",
            "exportOption" => "titles"
        ]);
        $response->assertStatus(200);
        $response->assertSeeInOrder([
            'Title',
            'Untitled',
            '"""Quotes"""'
        ]);
        $response->assertDontSee('Author');
        $response->assertDontSee('Quote Lover');
        $response->assertDontSee('Commas');
    }

    public function testCsvExportAuthors()
    {
        $this->setupDatabase();

        $response = $this->post(route("books.export"), [
            "exportAs" => "csv",
            "exportOption" => "authors"
        ]);
        $response->assertStatus(200);
        $response->assertSeeInOrder([
            'Author',
            '"The Author"',
            '"The ""Quote Lover"", With Commas <And More>!"'
        ]);
        $response->assertDontSee('Title');
        $response->assertDontSee('Untitled');
        $response->assertDontSee('Quotes');
    }

    public function testXmlExportBooks()
    {
        $this->setupDatabase();

        $response = $this->post(route("books.export"), [
            "exportAs" => "xml",
            "exportOption" => 'books'
        ]);
        $response->assertStatus(200);
        $response->assertSeeInOrder([
            '<books>',
            '<book><title>Untitled</title><author>The Author</author></book>',
            '<book><title>"Quotes"</title><author>The "Quote Lover", With Commas &lt;And More&gt;!</author></book>',
            '</books>'
        ]);
    }

    public function testXmlExportTitles()
    {
        $this->setupDatabase();

        $response = $this->post(route("books.export"), [
            "exportAs" => "xml",
            "exportOption" => 'titles'
        ]);
        $response->assertStatus(200);
        $response->assertSeeInOrder([
            '<books>',
            '<book><title>Untitled</title></book>',
            '<book><title>"Quotes"</title></book>',
            '</books>'
        ]);
        $response->assertDontSee('<author>');
        $response->assertDontSee('The Author');
        $response->assertDontSee('The "Quote Lover", With Commas &lt;And More&gt;!');
    }

    public function testXmlExportAuthors()
    {
        $this->setupDatabase();

        $response = $this->post(route("books.export"), [
            "exportAs" => "xml",
            "exportOption" => 'authors'
        ]);
        $response->assertStatus(200);
        $response->assertSeeInOrder([
            '<books>',
            '<book><author>The Author</author></book>',
            '<book><author>The "Quote Lover", With Commas &lt;And More&gt;!</author></book>',
            '</books>'
        ]);
        $response->assertDontSee('<title>');
        $response->assertDontSee('Untitled');
        $response->assertDontSee('"Quotes"');
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
                "title" => "Untitled",
                "author" => "The Author"
            ],
            [
                "title" => "\"Quotes\"",
                "author" => "The \"Quote Lover\", With Commas <And More>!"
            ]
        ];
        foreach ($testBooks as $testBook) {
            Book::create($testBook);
        }

        $this->assertEquals(Book::count(), 2);
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
    }
}
