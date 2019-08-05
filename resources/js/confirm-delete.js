$('[data-action=toBeConfirmed]').submit(function (event) {
    event.preventDefault();
    let form = this;
    bootbox.confirm({
        title: "Are you sure?",
        message: "Do you really want to delete this record? This cannot be undone.",
        size: "small",
        buttons: {
            cancel: {
                label: 'Cancel'
            },
            confirm: {
                label: 'Confirm'
            }
        },
        callback: function (confirmed) {
            if (confirmed) {
                form.submit();
            }
        }
    });
});
