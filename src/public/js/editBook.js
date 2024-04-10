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

function onClickEditButton(id, title, author) {
    document.getElementById(ids.Form).action = "/book/" + id;
    document.getElementById(ids.Title).value = title;
    document.getElementById(ids.Author).value = author;
    showDialog();
}

document.addEventListener("DOMContentLoaded", function() {
    // Cancel button
    document.getElementById(ids.CancelBtn).onclick = closeDialog;
});
