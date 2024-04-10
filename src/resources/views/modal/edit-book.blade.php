<div id="editBookModal" class="modal">
    <div class="modal-content">
        <h2>Edit book</h2>
        <form id="editBookForm" action="/" method="post">
            @csrf
            @method('PUT')
            <label for="title">Title:</label>
            <input type="text" id="editBookTitle" name="title" readonly="readonly">
            <label for="author">Author:</label>
            <input type="text" id="editBookAuthor" name="author">
            <p></p>
            <button type="submit">Save</button>
            <button id="editBookCancelBtn" type="button">Cancel</button>
        </form>
    </div>
</div>