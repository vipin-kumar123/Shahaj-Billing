if ($("#categoriesTable").length) {
    $("#categoriesTable").DataTable({
        processing: true,
        serverSide: true,
        ajax: window.CATEGORY_INDEX_ROUTE,

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
            { data: "slug", name: "slug" },
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

//modal open add new category
$(document).on("click", "#addcat", function () {
    const modalEl = document.getElementById("addCategory");
    const modal =
        bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
    modal.show();
});

// Add Category
$("#addcatForm").on("submit", async function (e) {
    e.preventDefault();

    const form = this;
    const formData = new FormData(form);
    let $btn = $("#addcatBtn");
    let originalText = $btn.html();

    // Disable button + spinner
    $btn.prop("disabled", true).html(
        '<span class="spinner-border spinner-border-sm"></span> Saving...',
    );

    try {
        const response = await fetch(window.CATEGORY_STORE_ROUTE, {
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
            return;
        }

        // success
        Swal.fire({
            toast: true,
            icon: "success",
            title: res.message,
            position: "top-end",
            showConfirmButton: false,
            timer: 2000,
        });

        // reset form + reload table
        form.reset();
        $("#categoriesTable").DataTable().ajax.reload();

        // close modal
        closeModal("addCategory");
    } catch (error) {
        Swal.fire({
            icon: "error",
            title: "Network Error",
            text: "Please try again!",
        });
    } finally {
        $btn.prop("disabled", false).html(originalText);
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

function clearErrors() {
    $(".error-text").text("");
}

//status change category
$(document).on("change", ".user-status-toggle", async function () {
    const id = $(this).data("id");
    const status = $(this).is(":checked") ? 1 : 0;

    const response = await fetch(window.CATEGORY_STATUS_ROUTE, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        body: JSON.stringify({
            id,
            status,
        }),
    });

    const res = await response.json();
    //console.log(res);

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
    }
});

// edit category
$(document).on("click", ".edit-category", async function () {
    let id = $(this).data("id");
    editcategory(id);

    $("#editCategory").modal("show");
});

async function editcategory(id) {
    const response = await fetch(window.CATEGORY_EDIT_ROUTE, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        body: JSON.stringify({ id }),
    });

    const res = await response.json();

    if (response.status === 422) {
        showErrors(res.errors);
        return;
    }

    if (res.success) {
        let category = res.data[0];

        $("#editCategory input[name=name]").val(category.name);
        $("#editCategory input[name=slug]").val(category.slug);
        $("#editCategory textarea[name=description]").val(category.description);
        $("#editCategory input[name=catid]").val(category.id);
    } else {
        Swal.fire({
            icon: "error",
            title: "Server Error",
            text: "Something went wrong. Please try again!",
        });
        return;
    }
}

//update category
$(document).on("submit", "#editCatForm", async function (e) {
    e.preventDefault();

    const form = this;
    const formData = new FormData(form);

    let $btn = $("#editcatBtn");

    let originalText = $btn.html();

    // Disable button + spinner
    $btn.prop("disabled", true).html(
        '<span class="spinner-border spinner-border-sm"></span> Saving...',
    );

    try {
        const response = await fetch(window.CATEGORY_UPDATE_ROUTE, {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            body: formData,
        });

        const res = await response.json();

        // Validation error
        if (response.status === 422) {
            showErrors(res.errors);
            return;
        }

        // Success
        if (res.success) {
            Swal.fire({
                toast: true,
                icon: "success",
                title: res.message,
                position: "top-end",
                showConfirmButton: false,
                timer: 2000,
            });

            form.reset();
            $("#categoriesTable").DataTable().ajax.reload();

            closeModal("editCategory");
        }
    } catch (error) {
        Swal.fire({
            icon: "error",
            title: "Network Error",
            text: "Please try again!",
        });
    } finally {
        // Always restore button
        $btn.prop("disabled", false).html(originalText);
    }
});

// category display in modal
$(document).on("click", ".show-category", async function () {
    let id = $(this).data("id");

    showdata(id);

    $("#showCategory").modal("show");
});

//show category
async function showdata(id) {
    try {
        const response = await fetch(window.CATEGORY_SHOW_ROUTE, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            body: JSON.stringify({ id }),
        });

        const res = await response.json();

        if (response.status === 422) {
            showErrors(res.errors);
            return;
        }

        if (res.success) {
            let category = res.data[0];

            $("#editCategoryBody").html(`
                <p><strong>Name:</strong> ${category.name}</p>
                <p><strong>Slug:</strong> ${category.slug}</p>
                <p><strong>Description:</strong> ${category.description ?? ""}</p>
            `);
        }
    } catch (error) {
        Swal.fire({
            icon: "error",
            title: "Network Error",
            text: "Please try again!",
        });
    }
}

$(document).on("click", ".cat-delete", async function () {
    let id = $(this).data("id");

    Swal.fire({
        title: "Are you sure?",
        text: "You won't be able to revert this!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, delete it!",
    }).then(async (result) => {
        if (result.isConfirmed) {
            await deleteData(id);
        }
    });
});

async function deleteData(id) {
    const response = await fetch(window.CATEGORY_DELETE_ROUTE, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        body: JSON.stringify({ id }),
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

        $("#categoriesTable").DataTable().ajax.reload();
        return;
    }

    // Server fail
    Swal.fire({
        icon: "error",
        title: "Server Error",
        text: res.message,
    });
}
