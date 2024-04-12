<div id="editBookModal" class="modal">
    <div class="modal-content">
        <h2>Edit book</h2>
        <form id="editBookForm" action="#" method="post">
            @csrf
            @method('PUT')
            <label for="title">Title</label>
            <input type="text" id="editBookTitle" name="title" readonly="readonly">
            <label for="author">Author *</label>
            <input type="text" id="editBookAuthor" name="author"
                minlength="{{ App\Book::MIN_AUTHOR_LENGTH }}"
                maxlength="{{ App\Book::MAX_AUTHOR_LENGTH }}" required>
            
            <!-- Table filter and sort data -->
            <input type="hidden" name="search" value="{{ $searchString }}">
            <input type="hidden" name="sortBy" value="{{ $sortBy }}">
            <input type="hidden" name="sortOrder" value="{{ $sortOrder }}">
            
            <div class="form-btns">
                <button id="editBookCancelBtn" type="button">Cancel</button>
                <button type="submit">Save</button>
            </div>
        </form>
    </div>
</div>