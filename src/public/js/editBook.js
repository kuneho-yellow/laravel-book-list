const editBookIds = {
    Modal: "editBookModal",
    CancelBtn: "editBookCancelBtn",
    Form: "editBookForm",
    Title: "editBookTitle",
    Author: "editBookAuthor"
};

function showDialog() {
    document.getElementById(editBookIds.Modal).style.display = "block";
}

function closeDialog() {
    document.getElementById(editBookIds.Modal).style.display = "none";
}

function onClickEditButton(book) {
    document.getElementById(editBookIds.Form).action = "/book/" + book.id;
    document.getElementById(editBookIds.Title).value = book.title;
    document.getElementById(editBookIds.Author).value = book.author;
    showDialog();
}

document.addEventListener("DOMContentLoaded", function() {
    // Cancel button
    document.getElementById(editBookIds.CancelBtn).onclick = closeDialog;
});
