<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
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
        $searchString = session("searchString", "");
        $sortBy = session("sortBy", "none");
        $sortOrder = session("sortOrder", "asc");
        $books = session("books", Book::all());

        session()->forget("searchString");
        session()->forget("sortBy");
        session()->forget("sortOrder");
        session()->forget("books");

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

        // Put query parameters and results in session
        session(["searchString" => $searchString]);
        session(["sortBy" => $sortBy]);
        session(["sortOrder" => $sortOrder]);
        session(["books" => $books]);

        return redirect("/");
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
        // TODO

        // Redo table query
        $searchString = $request->input("search");
        $sortBy = $request->input("sortBy");
        $sortOrder = $request->input("sortOrder");
        return redirect("/books?search={$searchString}&sortBy={$sortBy}&sortOrder={$sortOrder}");
    }
}
