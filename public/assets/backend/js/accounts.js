//data table loaded
if ($("#accountTable").length) {
    $("#accountTable").DataTable({
        processing: true,
        serverSide: true,
        ajax: window.ACCOUNT_INDEX_ROUTE,

        pagingType: "full_numbers",

        language: {
            paginate: {
                first: "«",
                previous: "‹",
                next: "›",
                last: "»",
            },
        },

        ordering: false,

        columnDefs: [
            {
                targets: "_all",
                className: "text-start",
            },
        ],

        columns: [
            { data: "name", name: "name" },
            { data: "type", name: "type" },
            { data: "parent_id", name: "parent_id" },
            { data: "is_active", name: "is_active" },
            {
                data: "action",
                name: "action",
                orderable: false,
                searchable: false,
            },
        ],
    });
}

// add account form
$(document).on("submit", "#addAccountForm", async function (e) {
    e.preventDefault();

    const form = this;
    const formData = new FormData(form);
    const $btn = $("#addBtn");
    let orginalText = $btn.html();

    $btn.prop("disabled", true).html(
        '<span class="spinner-border spinner-border-sm me-2"></span> Saving...',
    );

    try {
        const response = await fetch(window.ACCOUNT_STORE_ROUTE, {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            body: formData,
        });

        const res = await response.json();

        // 422 validation error
        if (response.status === 422) {
            showErrors(res.errors);
            $btn.prop("disabled", false).html(orginalText);
            return;
        }

        if (res.success) {
            Swal.fire({
                toast: true,
                icon: "success",
                title: res.message,
                position: "top-end",
                showConfirmButton: false,
                timer: 2000,
            });

            setTimeout(() => {
                form.reset();
                $("#accountTable").DataTable().ajax.reload(null, false);

                $("#addAccountModal").modal("hide");

                $("body").removeClass("modal-open");
                $("body").css("padding-right", "");
                $(".modal-backdrop").remove();
            }, 2000);
        }
    } catch (error) {
        Swal.fire("Network Error!", "Please try again!", "error");
    } finally {
        // Re-enable button
        $btn.prop("disabled", false).html(orginalText);
    }
});

//validation error
function showErrors(errors) {
    let errorList = "";

    $.each(errors, function (key, value) {
        $("#" + key + "_error").text(value[0]);
        errorList += `<li>${value[0]}</li>`;
    });

    // SweetAlert summary
    Swal.fire({
        icon: "error",
        title: "Please fix the errors",
        html: `<ul style="text-align:center;">${errorList}</ul>`,
    });
}

// edit account
$(document).on("click", ".editAccount", async function name() {
    let account_id = $(this).data("id");
    editAccount(account_id);
});

async function editAccount(account_id) {
    try {
        const response = await fetch(window.ACCOUNT_EDIT_ROUTE, {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": document.querySelector(
                    'meta[name="csrf-token"]',
                ).content,
                "Content-Type": "application/json",
                Accept: "application/json",
            },
            body: JSON.stringify({ account_id }),
        });

        const res = await response.json();

        // Laravel validation error (422)
        if (response.status === 422) {
            showErrors(res.errors);
            return;
        }

        // Other server errors
        if (!response.ok) {
            Swal.fire("Error!", res.message || "Something went wrong", "error");
            return;
        }

        //console.log("Account Data:", res);
        $("#editAccountModal #edit_account_id").val(res.data.id);
        // final route yahi banao
        window.ACCOUNT_UPDATE_ROUTE = `/accounts/update/${res.data.id}`;

        $('#editAccountModal input[name="name"]').val(res.data.name);

        $('#editAccountModal select[name="type"]')
            .val(res.data.type.toLowerCase())
            .trigger("change");

        $('#editAccountModal select[name="parent_id"]')
            .val(res.data.parent_id)
            .trigger("change.select2");

        $("#editAccountModal").modal("show");
    } catch (error) {
        console.error(error);
        Swal.fire("Network Error!", "Please try again!", "error");
    }
}

// update account form
$(document).on("submit", "#updateAccountForm", async function (e) {
    e.preventDefault();

    const form = this;
    const formData = new FormData(form);
    const $btn = $("#updateBtn");
    let orginalText = $btn.html();

    $btn.prop("disabled", true).html(
        '<span class="spinner-border spinner-border-sm me-2"></span> Updating...',
    );

    try {
        const response = await fetch(window.ACCOUNT_UPDATE_ROUTE, {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                Accept: "application/json",
            },
            body: formData,
        });

        const res = await response.json();

        // Validation error
        if (response.status === 422) {
            showErrors(res.errors);
            return;
        }

        if (!response.ok) {
            Swal.fire("Error!", res.message || "Something went wrong", "error");
            return;
        }

        if (res.success) {
            Swal.fire({
                toast: true,
                icon: "success",
                title: res.message,
                position: "top-end",
                showConfirmButton: false,
                timer: 2000,
            });

            setTimeout(() => {
                form.reset();
                $("#accountTable").DataTable().ajax.reload();
                $("#editAccountModal").modal("hide");
            }, 2000);
        }
    } catch (error) {
        console.error(error);
        Swal.fire("Network Error!", "Please try again!", "error");
    } finally {
        $btn.prop("disabled", false).html(orginalText);
    }
});

// delete record

$(document).on("click", ".accountDelete", async function () {
    let account_id = $(this).data("id");
    Swal.fire({
        title: "Are you sure?",
        text: "You won't be able to revert this!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, delete it!",
    }).then((result) => {
        if (result.isConfirmed) {
            deleteRecord(account_id);
        }
    });
});

async function deleteRecord(account_id) {
    const response = await fetch(window.ACCOUNT_DELETE_ROUTE, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        body: JSON.stringify({ account_id }),
    });

    const res = await response.json();

    if (response.status === 422) {
        showErrors(res.errors);
        return;
    }

    if (res.success) {
        Swal.fire({
            toast: true,
            icon: "success",
            title: res.message,
            position: "top-end",
            showConfirmButton: false,
            timer: 2000,
        });
        $("#accountTable").DataTable().ajax.reload();
        return;
    } else {
        Swal.fire({
            icon: "error",
            title: "Oops...",
            text: res.message,
            footer: '<a href="#">Why do I have this issue?</a>',
        });
        return;
    }
}
