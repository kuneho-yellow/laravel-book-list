const exportIds = {
    Modal: "exportModal",
    Form: "exportForm",
    CancelBtn: "exportCancelBtn",
    SubmitBtn: "exportSubmitBtn",
    ExportOption: "exportOption",
    ExportAs: "exportAs",
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

function onClickSubmitBtn() {
    closeExportDialog();
}

document.addEventListener("DOMContentLoaded", function() {
    // Cancel button
    document.getElementById(exportIds.CancelBtn).onclick = closeExportDialog;
    document.getElementById(exportIds.SubmitBtn).onclick = onClickSubmitBtn;
});

document.getElementById(exportIds.Form).addEventListener("submit", function(event) {
    event.preventDefault();

    let radios = document.getElementsByName(exportIds.ExportAs);
    let exportAs;
    for (const radioBtn of radios) {
        if (radioBtn.checked) {
            exportAs = radioBtn.value;
            break;
        }
    }
    radios = document.getElementsByName(exportIds.ExportOption);
    let exportOption;
    for (const radioBtn of radios) {
        if (radioBtn.checked) {
            exportOption = radioBtn.value;
            break;
        }
    }

    let filename = exportOption + "." + exportAs;

    fetch(this.action, {
        method: this.method,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            exportAs: exportAs,
            exportOption: exportOption
        })
    })
    .then(response => response.blob())
    .then(blob => {
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = filename;
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(url);
    })
    .catch(error => console.error('Error:', error));
});