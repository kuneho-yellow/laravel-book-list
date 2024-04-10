<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://unpkg.com/mvp.css">
    <title>Books</title>
</head>
<body>
    <h1>Book Collection</h1>
    <div>
        <div>
            <h2>Add a book</h2>
            <form action="/add" method="post">
                @csrf
                <label for="title">Title *</label>
                <input type="text" id="title" name="title">
                <label for="author">Author *</label>
                <input type="text" id="author" name="author">
                <p>* Required field</p>
                <button type="Submit">Add</button>
            </form>
        </div>
    </div>
    <div>
        <h2>Available Books</h2>
        @if (isset($books) && count($books) > 0)
        <table>
            <thead>
                <tr>
                    <th scope="col">Title</th>
                    <th scope="col">Author</th>
                    <th scope="col">Delete</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($books as $book)
                <tr>
                    <th>{{ $book->title }}</th>
                    <th>{{ $book->author }}</th>
                    <th>
                        <form action="/book/{{ $book->id }}" method="post">
                            @csrf
                            @method('DELETE')
                            <button type="submit">Delete</button>
                        </form>
                    </th>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <h3>No books in collection</h3>
        @endif
    </div>
</body>
</html>