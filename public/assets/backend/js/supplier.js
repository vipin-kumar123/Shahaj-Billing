if ($("#supplierTable").length) {
    $("#supplierTable").DataTable({
        processing: true,
        serverSide: true,
        ajax: window.SUPPLIER_INDEX_ROUTE,

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

        autoWidth: false,
        responsive: true,

        columnDefs: [
            {
                targets: "_all",
                className: "text-start",
            },
        ],

        columns: [
            { data: "name", name: "name" },
            { data: "mobile_number", name: "mobile_number" },
            { data: "whatsapp_number", name: "whatsapp_number" },
            { data: "email", name: "email" },
            { data: "opening_balance", name: "opening_balance" },
            { data: "balance_type", name: "balance_type" },
            { data: "is_active", name: "is_active" },
            { data: "created_by", name: "created_by" },
            { data: "created_at", name: "created_at" },
            {
                data: "action",
                name: "action",
                orderable: false,
                searchable: false,
            },
        ],
    });
}

//status change
$(document).on("change", ".supplier-status-toggle", async function () {
    const id = $(this).data("id");
    const status = $(this).is(":checked") ? 1 : 0;

    const response = await fetch(window.SUPPLIER_STATUS_ROUTE, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        body: JSON.stringify({ id, status }),
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
    } else {
        Swal.fire({
            icon: "error",
            title: "Network Error",
            text: "Please try again!",
        });
    }
});

// delete customers
$(document).on("click", ".supplier-delete", function () {
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
            customerDelete(id);
        }
    });
});

async function customerDelete(id) {
    const response = await fetch(window.CUSTOMER_DELETE_ROUTE, {
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

        $("#customerTable").DataTable().ajax.reload();
    } else {
        Swal.fire({
            icon: "error",
            title: "Network Error",
            text: "Something went wrong!",
        });
    }
}

//state get city
async function loadCity(state_id, selectedCity = 0) {
    const response = await fetch(window.SUPPLIER_STATE_GET_CITY, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        body: JSON.stringify({ state_id }),
    });

    const res = await response.json();

    $("#city_id").empty();

    if (res.success) {
        $("#city_id").append(`<option value="0">Nothing Select</option>`);

        res.data.forEach((city) => {
            $("#city_id").append(
                `<option value="${city.id}">${city.name}</option>`,
            );
        });

        // ✔ Auto-select for EDIT mode
        if (selectedCity && selectedCity != 0) {
            $("#city_id").val(selectedCity);
        }
    } else {
        $("#city_id").append(`<option value="">No cities found</option>`);
    }
}

//selected state
$(document).on("change", "#state_id", function () {
    let state_id = $(this).val();
    loadCity(state_id);
});

if (window.SELECTED_STATE_ID) {
    loadCity(window.SELECTED_STATE_ID, window.SELECTED_CITY_ID);
}
