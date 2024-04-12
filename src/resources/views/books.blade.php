<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="css/styles.css">
    <!-- Icons -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20,400,0,0" />
    <title>Books</title>
</head>
<body>
    <div class="container">

        <h1>Book Collection</h1>

        <!-- Add Book -->
        <section class="add-book">
            <h2>Add a book</h2>
            <form action="{{ route('add') }}" method="post">
                @csrf
                <label for="title">Title *</label>
                <input type="text" id="title" name="title"
                    minlength="{{ App\Book::MIN_TITLE_LENGTH }}"
                    maxlength="{{ App\Book::MAX_TITLE_LENGTH }}" required>
                <label for="author">Author *</label>
                <input type="text" id="author" name="author"
                    minlength="{{ App\Book::MIN_AUTHOR_LENGTH }}"
                    maxlength="{{ App\Book::MAX_AUTHOR_LENGTH }}" required>
                <!-- Table filter and sort data -->
                <input type="hidden" name="search" value="{{ $searchString }}">
                <input type="hidden" name="sortBy" value="{{ $sortBy }}">
                <input type="hidden" name="sortOrder" value="{{ $sortOrder }}">
                <div class="form-btns">
                    <button type="Submit">Add</button>
                </div>
            </form>
        </section>

        <section>
            <h2>Available books</h2>

            <!-- Search Bar -->
            @if ((isset($books) && count($books) > 0) || $searchString)
            <div class="div-spacer">
                <form action="/books" class="single-line" method="post">
                    @csrf
                    @method('GET')
                    <input type="text" id="search" name="search" maxlength="255" value="{{ $searchString }}" placeholder="Search...">
                    
                    <!-- Table sort data -->
                    <input type="hidden" name="sortBy" value="{{ $sortBy }}">
                    <input type="hidden" name="sortOrder" value="{{ $sortOrder }}">
                    
                    <button type="submit" class="search-btn">
                        <span class="material-symbols-outlined">search</span>
                    </button>
                </form>
            </div>
            @endif

            <!-- Book Table -->
            @if (isset($books) && count($books) > 0)
            <table>
                <thead>
                    <tr>
                        <th scope="col">
                            @if ($sortBy == "title" && $sortOrder != "desc")
                            <a href="/books?search={{ $searchString }}&sortBy=title&sortOrder=desc">
                                Title <span class="material-symbols-outlined">arrow_downward</span>
                            </a>
                            @elseif ($sortBy == "title" && $sortOrder == "desc")
                            <a href="/books?search={{ $searchString }}&sortBy=none">
                                Title <span class="material-symbols-outlined">arrow_upward</span>
                            </a>
                            @else
                            <a href="/books?search={{ $searchString }}&sortBy=title&sortOrder=asc">
                                Title <span class="material-symbols-outlined">sort_by_alpha</span>
                            </a>
                            @endif
                        </th>
                        <th scope="col">
                            @if ($sortBy == "author" && $sortOrder != "desc")
                            <a href="/books?search={{ $searchString }}&sortBy=author&sortOrder=desc">
                                Author <span class="material-symbols-outlined">arrow_downward</span>
                            </a>
                            @elseif ($sortBy == "author" && $sortOrder == "desc")
                            <a href="/books?search={{ $searchString }}&sortBy=none">
                                Author <span class="material-symbols-outlined">arrow_upward</span>
                            </a>
                            @else
                            <a href="/books?search={{ $searchString }}&sortBy=author&sortOrder=asc">
                                Author <span class="material-symbols-outlined">sort_by_alpha</span>
                            </a>
                            @endif
                        </th>
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
                            <form action="{{ route('delete', ['id' => $book->id]) }}" method="post">
                                @csrf
                                @method('DELETE')
                                <!-- Table filter and sort data -->
                                <input type="hidden" name="search" value="{{ $searchString }}">
                                <input type="hidden" name="sortBy" value="{{ $sortBy }}">
                                <input type="hidden" name="sortOrder" value="{{ $sortOrder }}">
                                <button type="submit">
                                    <span class="material-symbols-outlined">delete</span>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            
            <!-- Export -->
            <div class="div-spacer">
                <button onclick="onClickExportButton()">Export Table Data</button>
            </div>

            @elseif ($searchString)
            <h3>No matching books found</h3>
            @else
            <h3>No books in collection</h3>
            @endif
        </section>
    </div>

    <!-- Edit Book Modal Dialog -->
    @include('modal.edit-book')
    <script defer src="{{ asset('js/editBook.js') }}"></script>

    <!-- Export Modal Dialog -->
    @include('modal.export')
    <script defer src="{{ asset('js/export.js') }}"></script>
</body>
</html>