const exportIds = {
    Modal: "exportModal",
    CancelBtn: "exportCancelBtn",
};

function showExportDialog() {
    document.getElementById(exportIds.Modal).style.display = "block";
}

function closeExportDialog() {
    document.getElementById(exportIds.Modal).style.display = "none";
}

function onClickExportButton() {
    showExportDialog();
}

document.addEventListener("DOMContentLoaded", function() {
    // Cancel button
    document.getElementById(exportIds.CancelBtn).onclick = closeExportDialog;
});
