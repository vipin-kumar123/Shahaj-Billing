//table data
if ($("#productTable").length) {
    $("#productTable").DataTable({
        processing: true,
        serverSide: true,
        ajax: window.PRODUCT_INDEX_ROUTE,

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
            { data: "product_code", name: "product_code" },
            { data: "name", name: "name" },
            { data: "brand", name: "brand" },
            { data: "unit", name: "unit" },
            { data: "purchase_price", name: "purchase_price" },
            { data: "saleing_price", name: "saleing_price" },
            { data: "product_type", name: "product_type" },
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

// Get Subcategories on Category Select
$(document).on("change", "#category_id", async function () {
    const category_id = $(this).val();

    // Clear old subcategories
    $("#sub_category_id").html(`<option value="">Loading...</option>`);

    try {
        const response = await fetch(window.PRODUCT_GET_SUBCATEGORIES, {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                "Content-Type": "application/json",
            },
            body: JSON.stringify({ category_id }),
        });

        const res = await response.json();

        // 422 validation error
        if (response.status === 422) {
            showErrors(res.errors);
            return;
        }

        if (res.success) {
            let options = `<option value="">Select Sub Category</option>`;

            res.data.forEach((sub) => {
                options += `<option value="${sub.id}">${sub.name}</option>`;
            });

            $("select[name=sub_category_id]").html(options);
        }
    } catch (error) {
        Swal.fire("Network Error!", "Please try again!", "error");

        // if network error, reset dropdown
        $("#sub_category_id").html(
            `<option value="">Select Sub Category</option>`,
        );
    }
});

//
// ========== Load Subcategories (Reusable Function) ========== //
async function loadSubcategories(category_id, selected_subcategory = null) {
    $("#sub_category_id").html(`<option value="">Loading...</option>`);

    try {
        const response = await fetch(window.PRODUCT_GET_SUBCATEGORIES, {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                "Content-Type": "application/json",
            },
            body: JSON.stringify({ category_id }),
        });

        const res = await response.json();

        if (response.status === 422) {
            showErrors(res.errors);
            return;
        }

        let options = `<option value="">Select Sub Category</option>`;

        if (res.success) {
            res.data.forEach((sub) => {
                options += `<option value="${sub.id}" 
                    ${selected_subcategory == sub.id ? "selected" : ""}
                >
                    ${sub.name}
                </option>`;
            });
        }

        $("#sub_category_id").html(options);
    } catch (error) {
        Swal.fire("Network Error!", "Please try again!", "error");
        $("#sub_category_id").html(
            `<option value="">Select Sub Category</option>`,
        );
    }
}

// ========== On Category Change ========== //
$(document).on("change", "#category_id", function () {
    const category_id = $(this).val();
    loadSubcategories(category_id); // no preselected value
});

// ========== On Edit Page: Auto Load Subcategories ========== //
let selectedCategory = $("#category_id").data("selected");
let selectedSubcategory = $("#sub_category_id").data("selected");

if (selectedCategory) {
    loadSubcategories(selectedCategory, selectedSubcategory);
}

//delete record product item
$(document).on("click", ".item-delete", function () {
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
            deleteRecord(id);
        }
    });
});

async function deleteRecord(id) {
    try {
        const response = await fetch(window.PRODUCT_DESTROY_ROUTE, {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                "Content-Type": "application/json",
            },
            body: JSON.stringify({ id }),
        });

        const res = await response.json();

        if (res.success) {
            Swal.fire({
                toast: true,
                icon: "success",
                title: res.message,
                position: "top-end",
                showConfirmButton: false,
                timer: 2000,
            });

            $("#productTable").DataTable().ajax.reload();
            return;
        } else {
            Swal.fire(
                "Error!",
                res.message || "Something went wrong.",
                "error",
            );
            return;
        }
    } catch (error) {
        Swal.fire("Network Error!", "Please try again!", "error");
    }
}
