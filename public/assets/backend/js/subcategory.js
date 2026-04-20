if ($("#subcategoryTable").length) {
    $("#subcategoryTable").DataTable({
        processing: true,
        serverSide: true,
        ajax: window.SUBCATEGORY_INDEX_ROUTE,

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
            { data: "category", name: "category" },
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

// sub category add modal
$(document).on("click", "#addSubCat", function () {
    const modalEl = document.getElementById("addSubCategoryModal");
    const modal =
        bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
    modal.show();
});

// SLUG FUNCTION
function makeSlug(text) {
    return text
        .toLowerCase()
        .trim()
        .replace(/[^a-z0-9]+/g, "-")
        .replace(/^-+|-+$/g, "");
}

// ADD MORE ROW
$(document).on("click", "#addMoreSub", function () {
    let row = `
            <div class="row row-item mb-3">

                <div class="col-md-10">
                    <label class="form-label fw-semibold">Sub Category Name</label>
                    <input type="text" name="name[]" class="form-control sub_name" placeholder="Name">
                </div>

                <input type="hidden" name="slug[]" class="form-control sub_slug">

                <div class="col-md-2 d-flex align-items-center mt-2">
                    <button type="button" class="btn btn-danger btn-sm removeRow mt-4">X</button>
                </div>

                <div class="col-md-12 mt-2">
                    <label class="form-label fw-semibold">Description</label>
                    <textarea name="description[]" class="form-control" rows="2" placeholder="Optional"></textarea>
                </div>

            </div>
            `;

    $("#subContainer").append(row);
});

// AUTO SLUG ON NAME INPUT
$(document).on("keyup", ".sub_name", function () {
    let val = $(this).val();
    $(this).closest(".row-item").find(".sub_slug").val(makeSlug(val));
});

// REMOVE ROW
$(document).on("click", ".removeRow", function () {
    $(this).closest(".row-item").remove();
});

//add subcategory
$(document).on("submit", "#addsubcatForm", async function (e) {
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
        const response = await fetch(window.SUBCATEGORY_STORE_ROUTE, {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            body: formData,
        });

        const res = await response.json();
        //console.log(res);

        // 422 validation error
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

            form.reset();
            $("#subcategoryTable").DataTable().ajax.reload();

            // close modal
            closeModal("addSubCategoryModal");
        }
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

//edit subcat modal
$(document).on("click", ".edit-subcat", async function () {
    let id = $(this).data("id");

    editSubcat(id);

    $("#editSubCategoryModal").modal("show");
});

async function editSubcat(id) {
    const response = await fetch(window.SUBCATEGORY_EDIT_ROUTE, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        body: JSON.stringify({ id }),
    });

    const result = await response.json();

    if (response.status === 422) {
        showErrors(result.errors);
        return;
    }

    if (result.success) {
        let sub = result.data; // FIXED

        $("#editSubCategoryModal select[name=category_id]")
            .val(sub.category_id)
            .trigger("change");

        $("#editSubCategoryModal input[name=name]").val(sub.name);
        $("#editSubCategoryModal input[name=slug]").val(sub.slug);
        $("#editSubCategoryModal textarea[name=description]").val(
            sub.description,
        );
        $("#editSubCategoryModal input[name=subcatid]").val(sub.id);
    } else {
        Swal.fire({
            icon: "error",
            title: "Server Error",
            text: "Something went wrong. Please try again!",
        });
    }
}

//update subcategory
$(document).on("submit", "#editsubcatForm", async function (e) {
    e.preventDefault();

    const form = this;
    const formData = new FormData(form);

    let $btn = $("#editSubcatBtn");
    let originalText = $btn.html();
    // Disable button + spinner
    $btn.prop("disabled", true).html(
        '<span class="spinner-border spinner-border-sm"></span> Updating...',
    );

    try {
        const response = await fetch(window.SUBCATEGORY_UPDATE_ROUTE, {
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

        Swal.fire({
            toast: true,
            icon: "success",
            title: res.message,
            position: "top-end",
            showConfirmButton: false,
            timer: 2000,
        });

        form.reset();
        $("#subcategoryTable").DataTable().ajax.reload();

        // close modal
        closeModal("editSubCategoryModal");
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

//subcategory staus change active/ inactive
$(document).on("change", ".sub-status-toggle", async function () {
    const id = $(this).data("id");
    const status = $(this).is(":checked") ? 1 : 0;

    const response = await fetch(window.SUBCATEGORY_STATUS_ROUTE, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        body: JSON.stringify({ id, status }),
    });

    const res = await response.json();

    // 422 validation error
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

        $("#subcategoryTable").DataTable().ajax.reload();
    } else {
        Swal.fire({
            icon: "error",
            title: "Server Error",
            text: "Something went wrong. Please try again!",
        });
    }
});

//delete sub category
$(document).on("click", ".delete-subcat", async function () {
    const id = $(this).data("id");

    Swal.fire({
        title: "Are you sure?",
        text: "This subcategory will be permanently deleted!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Yes, delete it!",
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                const response = await fetch(window.SUBCATEGORY_DELETE_ROUTE, {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                            "content",
                        ),
                        "Content-Type": "application/json",
                    },
                    body: JSON.stringify({ id }),
                });

                const res = await response.json();

                if (response.status === 422) {
                    Swal.fire(
                        "Validation Error!",
                        "Invalid ID provided",
                        "error",
                    );
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

                    $("#subcategoryTable").DataTable().ajax.reload();
                } else {
                    Swal.fire("Error!", res.message ?? "Server error", "error");
                }
            } catch (error) {
                Swal.fire("Network Error!", "Please try again!", "error");
            }
        }
    });
});

// subcategory showing
$(document).on("click", ".show-subcat", async function () {
    const id = $(this).data("id");

    try {
        const response = await fetch(window.SUBCATEGORY_SHOW_ROUTE, {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                "Content-Type": "application/json",
            },
            body: JSON.stringify({ id }),
        });

        const res = await response.json();

        if (response.status === 422) {
            showErrors(res.errors);
            return;
        }

        if (res.success) {
            let sub = res.data;

            let html = `
                <p><strong>Category:</strong> ${sub.category?.name ?? "N/A"}</p>
                <p><strong>Name:</strong> ${sub.name}</p>
                <p><strong>Slug:</strong> ${sub.slug}</p>
                <p><strong>Description:</strong> ${sub.description ?? "No description"}</p>
                <p><strong>Status:</strong> ${sub.status == 1 ? "Active" : "Inactive"}</p>
                <p><strong>Created:</strong> ${formatDate(sub.created_at)}</p>
                <p><strong>Updated:</strong> ${formatDate(sub.updated_at)}</p>
            `;

            // Insert HTML into modal body
            $("#showSubCategoryModal .modal-body").html(html);

            // Show modal
            $("#showSubCategoryModal").modal("show");
        } else {
            Swal.fire("Error!", res.message ?? "Server error", "error");
        }
    } catch (error) {
        Swal.fire("Network Error!", "Please try again!", "error");
    }
});
