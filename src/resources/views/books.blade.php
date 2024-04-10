<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/styles.css">
    <!-- Edit and Delete Icons -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20,400,0,0" />
    <title>Books</title>
</head>
<body>
    <div class="container">

        <h1>Book Collection</h1>

        <section class="add-book">
            <h2>Add a book</h2>
            <form action="/add" method="post">
                @csrf
                <label for="title">Title *</label>
                <input type="text" id="title" name="title" minlength="{{ $minTitleLength }}" maxlength="{{ $maxTitleLength }}" required>
                <label for="author">Author *</label>
                <input type="text" id="author" name="author" minlength="{{ $minAuthorLength }}" maxlength="{{ $maxAuthorLength }}" required>
                <div class="form-btns">
                    <button type="Submit">Add</button>
                </div>
            </form>
        </section>

        <section>
            <h2>Available books</h2>
            @if (isset($books) && count($books) > 0)
            <table>
                <thead>
                    <tr>
                        <th scope="col">Title</th>
                        <th scope="col">Author</th>
                        <th scope="col">Edit</th>
                        <th scope="col">Delete</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($books as $book)
                    <tr>
                        <td>{{ $book->title }}</th>
                        <td>{{ $book->author }}</th>
                        <td>
                            <button onclick="onClickEditButton({{ $book }})" class="editBookBtn">
                                <span class="material-symbols-outlined">edit</span>
                            </button>
                        </td>
                        <td>
                            <form action="/book/{{ $book->id }}" method="post">
                                @csrf
                                @method('DELETE')
                                <button type="submit">
                                    <span class="material-symbols-outlined">delete</span>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <h3>No books in collection</h3>
            @endif
        </section>
    </div>

    <!-- Edit Book Modal Dialog -->
    @include('modal.edit-book')
    <script defer src="{{ asset('js/editBook.js') }}"></script>
</body>
</html>