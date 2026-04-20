//data table loaded
if ($("#brandTable").length) {
    $("#brandTable").DataTable({
        processing: true,
        serverSide: true,
        ajax: window.BRAND_INDEX_ROUTE,

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

//add modal open
$(document).on("click", "#addBrand", function () {
    $("#addBrandModal").modal("show");
});

// add brand form
$(document).on("submit", "#addBrandForm", async function (e) {
    e.preventDefault();

    const form = this;
    const formData = new FormData(form);
    const $btn = $("#addBrandBtn");
    let orginalText = $btn.html();

    $btn.prop("disabled", true).html(
        '<span class="spinner-border spinner-border-sm"></span> Saving...',
    );

    try {
        const response = await fetch(window.BRAND_STORE_ROUTE, {
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

            form.reset();

            $("#brandTable").DataTable().ajax.reload();
            closeModal("#addBrandModal");
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

// delete record
$(document).on("click", ".brand-delete", function () {
    const id = $(this).data("id");

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
            brandDelete(id);
        }
    });
});

async function brandDelete(id) {
    const response = await fetch(window.BRAND_DELETE_ROUTE, {
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
        $("#brandTable").DataTable().ajax.reload();
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

// record edit and open modal

$(document).on("click", ".edit-brand", async function () {
    const id = $(this).data("id");

    const response = await fetch(window.BRAND_EDIT_ROUTE, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        body: JSON.stringify({ id }),
    });

    const res = await response.json();
    //422 validation
    if (response.status === 422) {
        showErrors(res.errors);
        return;
    }

    if (res.success) {
        let b = res.data;
        $("#editBrandModal input[name=name]").val(b.name);
        $("#editBrandModal input[name=brand_id]").val(b.id);
        $("#editBrandModal textarea[name=description]").val(b.description);

        $("#editBrandModal").modal("show");
    } else {
        Swal.fire({
            icon: "error",
            title: "Oops...",
            text: res.message,
            footer: '<a href="#">Why do I have this issue?</a>',
        });
        return;
    }
});

//update brand
$(document).on("submit", "#editBrandForm", async function (e) {
    e.preventDefault();

    const form = this;
    const formData = new FormData(form);
    const $btn = $("#editBrandBtn");
    let originalText = $btn.html();

    // Disable button + show spinner
    $btn.prop("disabled", true).html(
        '<span class="spinner-border spinner-border-sm"></span> Saving...',
    );

    try {
        const response = await fetch(window.BRAND_UPDATE_ROUTE, {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            body: formData,
        });

        const res = await response.json();

        //422 validation
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

            $("#brandTable").DataTable().ajax.reload();
            closeModal("editBrandModal");
        } else {
            Swal.fire({
                icon: "error",
                title: "Oops...",
                text: res.message,
                footer: '<a href="#">Why do I have this issue?</a>',
            });
        }
    } catch (error) {
        console.error("Error:", error);
        Swal.fire({
            icon: "error",
            title: "Server Error",
            text: "Something went wrong. Please try again.",
        });
    } finally {
        // ALWAYS runs — success OR error
        $btn.prop("disabled", false).html(originalText);
    }
});

// show data
$(document).on("click", ".show-brand", async function () {
    const id = $(this).data("id");

    try {
        const response = await fetch(window.BRAND_SHOW_ROUTE, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            body: JSON.stringify({ id }),
        });

        const res = await response.json();

        //422 validation
        if (response.status === 422) {
            showErrors(res.errors);
            return;
        }

        if (res.success) {
            let b = res.data;

            let html = `
        <div class="container-fluid px-1">
            <div class="row mb-3">
                <div class="col-md-6">
                    <strong>Name:</strong><br>
                    <span>${b.name}</span>
                </div>
                <div class="col-md-6">
                    <strong>Slug:</strong><br>
                    <span>${b.slug}</span>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-12">
                    <strong>Description:</strong><br>
                    <div class="mt-1">${b.description ?? "<em>No description</em>"}</div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <strong>Status:</strong><br>
                    <span class="badge bg-${b.is_active ? "success" : "danger"} mt-1">
                        ${b.is_active ? "Active" : "Inactive"}
                    </span>
                </div>

                <div class="col-md-6">
                    <strong>Created By:</strong><br>
                    <span class="mt-1 d-inline-block">${b.user.name}</span>
                </div>
            </div>

            <div class="row mb-1">
                <div class="col-md-12">
                    <strong>IP Address:</strong><br>
                    <span>${b.ip}</span>
                </div>
            </div>
        </div>
    `;

            $("#showBrand .modal-body").html(html);
            $("#showBrand").modal("show");
        }
    } catch (error) {
        Swal.fire("Error!", error.message || "Something went wrong!", "error");
    }
});
