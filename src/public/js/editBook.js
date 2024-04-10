const ids = {
    Modal: "editBookModal",
    CancelBtn: "editBookCancelBtn",
    Form: "editBookForm",
    Title: "editBookTitle",
    Author: "editBookAuthor"
};

function showDialog() {
    document.getElementById(ids.Modal).style.display = "block";
}

function closeDialog() {
    document.getElementById(ids.Modal).style.display = "none";
}

function onClickEditButton(book) {
    document.getElementById(ids.Form).action = "/book/" + book.id;
    document.getElementById(ids.Title).value = book.title;
    document.getElementById(ids.Author).value = book.author;
    showDialog();
}

document.addEventListener("DOMContentLoaded", function() {
    // Cancel button
    document.getElementById(ids.CancelBtn).onclick = closeDialog;
});
