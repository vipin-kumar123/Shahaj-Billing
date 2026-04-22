let rowIndex = 0;

function addRow() {
    let row = `
    <tr>
        <td>
            <input type="text" name="items[${rowIndex}][description]" class="form-control" placeholder="Enter description">
        </td>
        <td>
            <input type="number" name="items[${rowIndex}][amount]" class="form-control amount" step="0.01">
        </td>
        <td>
            <input type="number" name="items[${rowIndex}][tax_amount]" class="form-control tax" step="0.01">
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-danger btn-sm removeRow">X</button>
        </td>
    </tr>`;

    $("#itemsTable tbody").append(row);
    rowIndex++;
}

$(document).ready(function () {
    addRow();
});
// Add Row
$("#addRow").click(function () {
    addRow();
});

// Remove Row
$(document).on("click", ".removeRow", function () {
    $(this).closest("tr").remove();
    calculateTotal();
});

// Calculate Total
$(document).on("keyup change", ".amount", function () {
    calculateTotal();
});

$(document).on("keyup change", ".tax", function () {
    calculateTotal();
});

$("#paid_amount").on("keyup change", function () {
    calculateTotal();
});

function calculateTotal() {
    let total = 0;
    let dueAmount = 0;

    $(".amount").each(function () {
        total += parseFloat($(this).val()) || 0;
    });

    $(".tax").each(function () {
        total += parseFloat($(this).val()) || 0;
    });

    $("#total_amount").val(total);

    let paid = parseFloat($("#paid_amount").val()) || 0;

    dueAmount = total - paid;

    $("#due_amount").val(dueAmount);
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

        console.log(res);

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
