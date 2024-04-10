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
    public function index(?QueryBookRequest $request)
    {
        // Retrieve the validated input data
        $validated = $request->validated();

        $query = Book::query();
    
        // Additional query validations
        $searchString = $validated["search"] ?? "";
        $sortBy = $validated["sortBy"] ?? "none";
        if ($sortBy !== "title" && $sortBy !== "author") {
            $sortBy = "none";
        }
        $isDescending =  $validated["isDescending"] ?? false;

        // Find and sort book entries according to the query
        $query = Book::query();

        if (!empty($searchString)) {
            $query->where("title", "LIKE", "%{$searchString}%")
                ->orWhere("author", "LIKE", "%{$searchString}%");
        }

        if ($sortBy != "none") {
            $sortOrder = $isDescending ? "desc" : "asc";
            $query->orderBy($sortBy, $sortOrder);
        }

        $books = $query->get();

        return view("books", [
            "books" => $books,
            "minTitleLength" => Book::MIN_TITLE_LENGTH,
            "maxTitleLength" => Book::MAX_TITLE_LENGTH,
            "minAuthorLength" => Book::MIN_AUTHOR_LENGTH,
            "maxAuthorLength" => Book::MAX_AUTHOR_LENGTH,
            "searchString" => $searchString,
            "sortBy" => $sortBy,
            "isDescending" => $isDescending,
        ]);
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

        return redirect("/");
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

        return redirect("/");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $book = Book::findOrFail($id);
        $book->delete();
        return redirect()->back();
    }
}
