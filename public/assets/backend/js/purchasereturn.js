/*********************DATA TABLE********************* */
if ($("#purchaseReturn").length) {
    $("#purchaseReturn").DataTable({
        processing: true,
        serverSide: true,
        ajax: PURCHASE_RETURN_INDEX_ROUTE,

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
            // Return Number
            {
                data: "return_no",
                name: "return_no",
            },

            // Return Date
            {
                data: "return_date",
                name: "return_date",
            },

            // Supplier Name
            {
                data: "supplier",
                name: "supplier_id",
            },

            // Total Return Amount
            {
                data: "total_return_amount",
                name: "total_return_amount",
            },
            {
                data: "refund_due",
                name: "refund_due",
            },

            // Creator (User Name)
            {
                data: "creator",
                name: "creator",
            },

            // Created At
            {
                data: "created_at",
                name: "created_at",
            },

            // Action Buttons
            {
                data: "action",
                name: "action",
                orderable: false,
                searchable: false,
            },
        ],
    });
}

/*********************DATA TABLE CLOSED********************* */

function calculateRow(input) {
    let qty = parseFloat(input.value) || 0;
    let cost = parseFloat(input.dataset.cost);
    let gst = parseFloat(input.dataset.gst);

    let gstAmt = (cost * qty * gst) / 100;
    let total = cost * qty + gstAmt;

    input.closest("tr").querySelector(".line-total").value = total.toFixed(2);

    calculateTotalReturn();
}

function calculateTotalReturn() {
    let total = 0;
    document.querySelectorAll(".line-total").forEach((t) => {
        total += parseFloat(t.value) || 0;
    });

    document.getElementById("total_return_amount").value = total.toFixed(2);
}

// edit page
document.addEventListener("DOMContentLoaded", function () {
    calculateTotalReturn();
});

// Validation error
function showErrors(errors) {
    let errorList = "";

    $.each(errors, function (key, value) {
        $("#" + key + "_error").text(value[0]);
        errorList += `<li>${value[0]}</li>`;
    });

    Swal.fire({
        icon: "error",
        title: "Please fix the errors",
        html: `<ul style="text-align:center;">${errorList}</ul>`,
    });
}
//store return data
//store return data
let purchaseReturnForm = document.getElementById("purchaseReturnForm");

if (purchaseReturnForm) {
    purchaseReturnForm.addEventListener("submit", async function (e) {
        e.preventDefault();

        let formData = new FormData(this);

        let response = await fetch(window.PURCHASE_RETURN_STORE_ROUTE, {
            method: "POST",
            body: formData,
        });

        let res = await response.json();

        // Validation error
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

            setTimeout(
                () => (location.href = window.PURCHASE_RETURN_INDEX_ROUTE),
                2000,
            );
        } else {
            Swal.fire("Error!", res.message, "error");
        }
    });
}

//receive payment
$(document).on("click", ".return-receive-payment", function () {
    let returnId = $(this).data("id");
    purchaseReturnData(returnId);
    $("#receivePaymentModal").modal("show");
});

async function purchaseReturnData(returnId) {
    try {
        const response = await fetch(window.GET_PURCHASE_RETURN_DATA, {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": document.querySelector(
                    'meta[name="csrf-token"]',
                ).content,
                "Content-Type": "application/json",
            },
            body: JSON.stringify({ returnId }),
        });

        const result = await response.json();

        // Validation
        if (response.status === 422) {
            showErrors(result.errors);
            return;
        }

        const data = result.data;

        // FINAL ROUTE
        window.PURCHASE_RETURN_MAKE_PAYMENT_FINAL_ROUTE =
            window.MAKE_PAYMENT_PURCHASE_RETURN.replace(":id", returnId);

        // ===============================
        // FILL CORRECT PURCHASE RETURN DATA
        // ===============================

        // Supplier
        $("#mp_supplier").val(
            `${data.supplier.first_name} ${data.supplier.last_name}`,
        );

        // Return No (NOT purchase bill no)
        $("#mp_bill_no").val(data.return_no);

        // Total Return Amount
        $("#mp_total").val(Number(data.total_return_amount).toFixed(2));

        // Refund Due Amount
        $("#mp_due").val(Number(data.refund_due).toFixed(2));

        // Hidden ID (if using)
        $("#purchase_return_id").val(data.id);
    } catch (error) {
        console.error("Error fetching purchase return data:", error);
    }
}

//make payment purchase return form submit form data
$(document).on("submit", "#makeReturnPaymentForm", async function (e) {
    e.preventDefault();

    let formData = new FormData(this);
    formData.append("_method", "PUT");

    let $btn = $("#returnPaymentBtn");
    let originalText = $btn.html();

    $btn.prop("disabled", true).html(
        '<span class="spinner-border spinner-border-sm me-2"></span> Saving...',
    );

    // Clear old errors
    $(".text-danger").remove();
    $("input, select").removeClass("is-invalid");

    try {
        const response = await fetch(
            window.PURCHASE_RETURN_MAKE_PAYMENT_FINAL_ROUTE,
            {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content",
                    ),
                },
                body: formData,
            },
        );

        const result = await response.json();

        // Validation errors (422)
        if (response.status === 422) {
            showErrors(result.errors);
            return;
        }

        // API or logical error
        if (!response.ok || !result.success) {
            Swal.fire({
                icon: "error",
                title: "Error",
                text: result.message || "Something went wrong!",
            });
            return;
        }

        // SUCCESS MESSAGE
        Swal.fire({
            toast: true,
            icon: "success",
            title: result.message || "Refund saved successfully!",
            position: "top-end",
            showConfirmButton: false,
            timer: 2000,
        });

        // REDIRECT IF PROVIDED
        if (result.redirect) {
            setTimeout(() => {
                window.location.href = result.redirect;
            }, 2000);
        }
    } catch (error) {
        Swal.fire({
            icon: "error",
            title: "Error",
            text: error.message || "Network Error",
        });
    } finally {
        $btn.prop("disabled", false).html(originalText);
    }
});

//receive payment history
$(document).on("click", ".receivedHistory", function () {
    let purchase_return_id = $(this).data("id");
    // GLOBAL — VERY IMPORTANT
    window.CURRENT_RETURN_ID = purchase_return_id;
    purchaseReturnHistory(purchase_return_id);
    $("#paymentHistoryModal").modal("show");
});

//open history modal
async function purchaseReturnHistory(purchase_return_id) {
    try {
        const response = await fetch(window.PURCHASE_RETURN_PAYMENT_HISTORY, {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": document.querySelector(
                    'meta[name="csrf-token"]',
                ).content,
                "Content-Type": "application/json",
            },
            body: JSON.stringify({ purchase_return_id }),
        });

        const result = await response.json();

        if (response.status === 422) {
            showErrors(result.errors);
            return;
        }

        const data = result.data;

        // ============================
        // FILL TOP SUMMARY
        // ============================

        $("#ph_supplier").text(
            `${data.supplier.first_name} ${data.supplier.last_name}`,
        );
        $("#ph_bill_code").text(data.return_no);
        $("#ph_bill_date").text(data.return_date);

        $("#ph_paid_amount").text(Number(data.refunded_amount).toFixed(2));
        $("#ph_balance").text(Number(data.refund_due).toFixed(2));

        // ============================
        // TABLE DATA
        // ============================

        let rows = "";
        let totalAmount = 0;

        data.transactions.forEach((t) => {
            totalAmount += Number(t.amount);

            rows += `
                <tr>
                    <td class="text-start">${t.payment_date}</td>
                    <td class="text-center">${t.type === "credit" ? "Refund Received" : t.type}</td>
                    <td class="text-center">₹${Number(t.amount).toFixed(2)}</td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-primary" title="Print"><i class="bi bi-printer"></i></button>
                        <button class="btn btn-sm btn-success" title="PDF"><i class="bi bi-file-pdf"></i></button>
                        <button class="btn btn-sm btn-danger delete-return-payment" title="Delete" data-id="${t.id}">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>

                </tr>
            `;
        });

        $("#ph_table_body").html(rows);
        $("#ph_total_amount").text(totalAmount.toFixed(2));
    } catch (error) {
        console.error("Error fetching purchase return data:", error);
    }
}

//delete return payment record
$(document).on("click", ".delete-return-payment", function () {
    let payment_id = $(this).data("id");
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
            deleteReturnPayment(payment_id);
        }
    });
});

async function deleteReturnPayment(payment_id) {
    const response = await fetch(window.DELETE_RETURN_PAYMENT_HISTORY, {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')
                .content,
            "Content-Type": "application/json",
        },
        body: JSON.stringify({ payment_id }),
    });

    const result = await response.json();

    if (response.status === 422) {
        showErrors(result.errors);
        return;
    }

    // API or logical error
    if (!response.ok || !result.success) {
        Swal.fire({
            icon: "error",
            title: "Error",
            text: result.message || "Something went wrong!",
        });
        return;
    }

    // SUCCESS MESSAGE
    Swal.fire({
        toast: true,
        icon: "success",
        title: "Refund deleted successfully!",
        position: "top-end",
        showConfirmButton: false,
        timer: 2000,
    });

    // REDIRECT IF PROVIDED
    setTimeout(() => {
        purchaseReturnHistory(window.CURRENT_RETURN_ID); // SAME TABLE REFRESH
    }, 700);
}
