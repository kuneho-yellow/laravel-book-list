<div id="editBookModal" class="modal">
    <!-- Modal content -->
    <div class="modal-content">
        <h2>Edit Book</h2>
        <form id="editBookForm" action="/" method="post">
            @csrf
            @method('PUT')
            <label for="title">Title:</label>
            <input type="text" id="editBookTitle" name="title" readonly="readonly">
            <label for="author">Author:</label>
            <input type="text" id="editBookAuthor" name="author">
            <button type="submit">Save</button>
            <button id="editBookCloseBtn" type="button">Cancel</button>
        </form>
    </div>
</div>