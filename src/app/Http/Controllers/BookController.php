<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Response;
use App\Book;
use App\Http\Requests\StoreUpdateBookRequest;
use App\Http\Requests\QueryBookRequest;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Look through session variables if prior book query has been done
        // TODO: Handle session expiring
        $books = session("books", Book::all());
        $searchString = session("searchString", "");
        $sortBy = session("sortBy", "none");
        $sortOrder = session("sortOrder", "asc");

        // TODO: Limit number of books returned and displayed at once
        return view("books", compact(
            "books",
            "searchString",
            "sortBy",
            "sortOrder"));
    }

    /**
     * Queries the resource.
     *
     * @param  \App\Http\Requests\QueryBookRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function query(QueryBookRequest $request)
    {
        // Retrieve the validated input data
        $validated = $request->validated();
    
        // Use default values for any missing query parameters
        $searchString = $validated["search"] ?? "";
        $sortBy = $validated["sortBy"] ?? "none";
        $sortOrder =  $validated["sortOrder"] ?? "asc";

        // Start a new query
        $query = Book::query();

        if (!empty($searchString)) {
            $query->where("title", "LIKE", "%{$searchString}%")
                ->orWhere("author", "LIKE", "%{$searchString}%");
        }

        if ($sortBy != "none") {
            $query->orderBy($sortBy, $sortOrder);
        }

        $books = $query->get();

        // Save query parameters and results in session
        session(["searchString" => $searchString]);
        session(["sortBy" => $sortBy]);
        session(["sortOrder" => $sortOrder]);
        session(["books" => $books]);

        return redirect(route("index"));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreUpdateBookRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreUpdateBookRequest $request) : RedirectResponse
    {
        // Retrieve the validated input data
        $validated = $request->validated();

        // Create and store the new book entry
        $book = new Book;
        $book->title = $validated["title"];
        $book->author = $validated["author"];
        $book->save();

        $searchString = $validated["search"];
        $sortBy = $validated["sortBy"];
        $sortOrder = $validated["sortOrder"];

        // If the new book does not contain the search string,
        // clear the search filter
        if (!empty($searchString)) {
            $search = mb_strtolower($searchString);
            $title = mb_strtolower($book->title);
            $author = mb_strtolower($book->author);
            if (strpos($title, $search) === false &&
                strpos($author, $search) === false) {
                    $searchString = "";
            }
        }

        // Redo table query
        return redirect("/books?search={$searchString}&sortBy={$sortBy}&sortOrder={$sortOrder}");
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\StoreUpdateBookRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(StoreUpdateBookRequest $request, $id) : RedirectResponse
    {
        // Retrieve the validated input data
        $validated = $request->validated();

        // Find and update the book entry
        $book = Book::findOrFail($id);
        // $book->title = $validated["title"]; // Title is readonly
        $book->author = $validated["author"];
        $book->save();

        // Redo table query
        $searchString = $validated["search"];
        $sortBy = $validated["sortBy"];
        $sortOrder = $validated["sortOrder"];
        return redirect("/books?search={$searchString}&sortBy={$sortBy}&sortOrder={$sortOrder}");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Http\Requests\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        // Find and delete the book entry
        $book = Book::findOrFail($id);
        $book->delete();

        // Redo table query
        $searchString = $request->input("search");
        $sortBy = $request->input("sortBy");
        $sortOrder = $request->input("sortOrder");
        return redirect("/books?search={$searchString}&sortBy={$sortBy}&sortOrder={$sortOrder}");
    }

    /**
     * Export a listing of the resource.
     *
     * @param  \App\Http\Requests\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function export(Request $request)
    {
        $exportAs = $request->input("exportAs");
        switch ($exportAs) {
            case "xml":
                return $this->exportAsXml($request);

            case "csv":
                default:
                return $this->exportAsCsv($request);
        }
    }

    /**
     * Export a listing of the resource as a csv file.
     *
     * @param  \App\Http\Requests\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function exportAsCsv(Request $request)
    {
        // Export books in session
        $books = session("books", Book::all());
        $exportOption = $request->input("exportOption");

        switch ($exportOption) {
            case "titles":
                $data[] = ["Title"];
                foreach ($books as $book) {
                    $data[] = [$book->title];
                }
                break;

            case "authors":
                $data[] = ["Author"];
                foreach ($books as $book) {
                    $data[] = [$book->author];
                }
                break;

            default:
                $data[] = ["Title", "Author"];
                foreach ($books as $book) {
                    $data[] = [$book->title, $book->author];
                }
                break;

        }

        // Use helper function for escaping logic
        $csvData = $this->arrayToCsv($data);

        return Response::make($csvData, 200, [
            "Content-Type" => "text/csv",
            "Content-Disposition" => "attachment; filename='data.csv'",
        ]);
    }

    /**
     * Export a listing of the resource as an xml file.
     *
     * @param  \App\Http\Requests\Request  $request
     * @return \Illuminate\Http\Response
     */
    private function exportAsXml(Request $request)
    {
        // Export books in session
        $books = session("books", Book::all());
        $exportOption = $request->input("exportOption");

        // Build up the xml like this:
        // <books>
        //   <book>
        //     <title>My Book</title>
        //     <author>The Author</author>
        //   </book>
        // ...
        // </books>
        $data = new \SimpleXMLElement("<books></books>");
        switch ($exportOption) {
            case "titles":
                foreach ($books as $book) {
                    $child = $data->addChild("book");
                    $child->addChild("title", htmlspecialchars($book->title));
                }
                break;

            case "authors":
                foreach ($books as $book) {
                    $child = $data->addChild("book");
                    $child->addChild("author", htmlspecialchars($book->author));
                }
                break;

            default:
                foreach ($books as $book) {
                    $child = $data->addChild("book");
                    $child->addChild("title", htmlspecialchars($book->title));
                    $child->addChild("author", htmlspecialchars($book->author));
                }
                break;
        }

        $xmlData = $data->asXML();

        return Response::make($xmlData, 200, [
            "Content-Type" => "text/xml",
            "Content-Disposition" => "attachment; filename='data.xml'",
        ]);
    }

    private function arrayToCsv(array $data) {
        $output = fopen("php://temp", "w");
        foreach ($data as $row) {
            fputcsv($output, $row);
        }
        rewind($output);
        $csvData = stream_get_contents($output);
        fclose($output);
        return $csvData;
    }
}