<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Book;

class BookController extends Controller
{
    public function index(Request $req){
        $books = Book::all();
        return view('books', [
            'books' => $books,
        ]);
    }

    public function add(Request $req){
        $book = new Book;
        $book->title = $req->title;
        $book->author = $req->author;
        $book->save();
        return redirect()->back();
    }
}
