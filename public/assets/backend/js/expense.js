//Dynamic index (edit + add)
let rowIndex = $(".amount").length;

//Page load pe calculation force
$(document).ready(function () {
    calculateTotal();
});

// Add Row
$("#addRow").click(function () {
    addRow();
});

function addRow() {
    let row = `
    <tr>
        <td>
            <input type="text" name="items[${rowIndex}][description]" 
                class="form-control" placeholder="Enter description">
        </td>
        <td>
            <input type="number" name="items[${rowIndex}][amount]" 
                class="form-control amount" step="0.01">
        </td>
        <td>
            <input type="number" name="items[${rowIndex}][tax_amount]" 
                class="form-control tax" step="0.01">
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-danger btn-sm removeRow">X</button>
        </td>
    </tr>`;

    $("#itemsTable tbody").append(row);

    rowIndex++;

    calculateTotal();
}

$(document).on("click", ".removeRow", function () {
    $(this).closest("tr").remove();
    calculateTotal();
});

$(document).on("keyup change", ".amount, .tax", function () {
    calculateTotal();
});

$("#paid_amount").on("keyup change", function () {
    calculateTotal();
});

function calculateTotal() {
    let total = 0;

    $(".amount").each(function () {
        total += parseFloat($(this).val()) || 0;
    });

    $(".tax").each(function () {
        total += parseFloat($(this).val()) || 0;
    });

    total = parseFloat(total.toFixed(2));

    $("#total_amount").val(total);

    let paid = parseFloat($("#paid_amount").val()) || 0;

    let due = total - paid;

    $("#due_amount").val(due.toFixed(2));
}
/*********************store expense******************************************/
$(document).on("submit", "#expenseForm", async function (e) {
    e.preventDefault();

    let formData = new FormData(this);
    formData.append("_token", $('meta[name="csrf-token"]').attr("content"));

    let btn = $("#expenseBtn");
    let old = btn.html();
    btn.prop("disabled", true).html(
        '<span class="spinner-border spinner-border-sm me-2"></span> Saving...',
    );

    try {
        const response = await fetch(window.EXPENSE_STORE, {
            method: "POST",
            body: formData,
        });

        const res = await response.json();

        // 422 validation error
        if (response.status === 422) {
            showErrors(res.errors);
            btn.prop("disabled", false).html(old);
            return;
        }

        //console.log(res);

        if (res.success) {
            Swal.fire({
                toast: true,
                icon: "success",
                title: res.message,
                position: "top-end",
                showConfirmButton: false,
                timer: 2000,
            });

            $("#expenseForm")[0].reset();

            // setTimeout(() => (location.href = window.EXPENSE_INDEX), 2000);
        }
    } catch (err) {
        Swal.fire({ icon: "error", title: "Error", text: err.message });
    } finally {
        btn.prop("disabled", false).html(old);
    }
});
/*********************store expense******************************************/

/*********************error display******************************************/
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

// display data table
if ($("#expenseTable").length) {
    $("#expenseTable").DataTable({
        processing: true,
        serverSide: true,
        ajax: window.EXPENSE_LIST_ROUTE,

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
            { data: "expense_date", name: "expense_date" },
            { data: "expense_no", name: "expense_no" },
            { data: "category", name: "category" },
            { data: "total_amount", name: "total_amount" },
            { data: "paid_amount", name: "paid_amount" },
            { data: "due_amount", name: "due_amount" },
            { data: "payment_status", name: "payment_status" },
            {
                data: "action",
                name: "action",
                orderable: false,
                searchable: false,
            },
        ],
    });
}

/*********************update expense******************************************/
$(document).on("submit", "#expenseUpdate", async function (e) {
    e.preventDefault();

    let formData = new FormData(this);

    let btn = $("#editExpenseBtn");
    let old = btn.html();

    btn.prop("disabled", true).html(
        '<span class="spinner-border spinner-border-sm me-2"></span> Updating...',
    );

    try {
        const response = await fetch(window.EXPENSE_UPDATE_ROUTE, {
            method: "POST",
            body: formData,
        });

        const text = await response.text();
        //console.log("RAW RESPONSE:", text);

        let res = {};
        if (text) {
            res = JSON.parse(text);
        } else {
            throw new Error("Empty response from server");
        }

        if (response.status === 422) {
            showErrors(res.errors);
            return;
        }

        if (res.success) {
            Swal.fire({
                icon: "success",
                title: res.message,
                timer: 2000,
                showConfirmButton: false,
            });
            setTimeout(() => {
                window.location.href = window.EXPENSE_INDEX;
            }, 2000);
        }
    } catch (err) {
        console.error(err);
        Swal.fire({
            icon: "error",
            title: "Error",
            text: err.message,
        });
    } finally {
        btn.prop("disabled", false).html(old);
    }
});
/*********************update expense******************************************/
