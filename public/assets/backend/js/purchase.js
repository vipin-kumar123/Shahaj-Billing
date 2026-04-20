/*********************DATA TABLE********************* */
if ($("#purchaseTable").length) {
    $("#purchaseTable").DataTable({
        processing: true,
        serverSide: true,
        ajax: PURCHASE_INDEX_ROUTE,

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
            {
                data: "reference_no",
                name: "reference_no",
            },
            {
                data: "purchase_date",
                name: "purchase_date",
            },
            {
                data: "supplier",
                name: "supplier_id",
            },
            {
                data: "total_amount",
                name: "total_amount",
            },
            {
                data: "due_amount",
                name: "due_amount",
            },
            {
                data: "created_by",
                name: "created_by",
            },
            {
                data: "created_at",
                name: "created_at",
            },
            {
                data: "action",
                name: "action",
                searchable: false,
                orderable: false,
            },
        ],
    });
}
/*********************DATA TABLE CLOSED**************/

/********************** ITEM ADD & CALCULATION **************************************************/

function addRow() {
    let tmpl = document
        .querySelector("#itemRowTemplate")
        .content.cloneNode(true);
    document.querySelector("#itemsTable tbody").appendChild(tmpl);

    $("#itemsTable tbody tr:last-child .select2").select2({
        width: "100%",
    });
}

function deleteRow(btn) {
    btn.closest("tr").remove();
    calculateTotals();
}

// PRODUCT SELECT → AUTO FILL PRICE & GST
function productSelected(select) {
    let row = select.closest("tr");

    let price = select.selectedOptions[0].getAttribute("data-price") || 0;
    let gst = select.selectedOptions[0].getAttribute("data-gst") || 0;

    row.querySelector(".unit-cost").value = price;
    row.querySelector(".gst-percent").value = gst;

    updateRow(select);

    if (typeof aiData !== "undefined" && aiData[select.value]) {
        let info = aiData[select.value];

        Swal.fire({
            title: info.product + " - Suggestion",
            html: `
                Avg Monthly Sale: <b>${info.avg_sale}</b><br>
                Current Stock: <b>${info.stock}</b><br>
                Recommended Purchase: <b>${info.recommended}</b><br>
                Avg Purchase Rate (Last 5): <b>₹${info.avg_rate}</b>
            `,
            icon: "info",
        });
    }
}

/********************** MAIN ROW CALCULATION (FIXED — NO DOUBLE GST) ****************************/

function updateRow(input) {
    let row = input.closest("tr");

    let cost = parseFloat(row.querySelector(".unit-cost").value) || 0;
    let qty = parseFloat(row.querySelector(".quantity").value) || 0;
    let discount = parseFloat(row.querySelector(".discount").value) || 0;
    let discountType = row.querySelector(".discount-type").value;
    let gstp = parseFloat(row.querySelector(".gst-percent").value) || 0;

    // Base subtotal (NO GST added here)
    let subtotal = cost * qty;

    // Apply discount
    if (discountType === "percent") {
        subtotal -= (subtotal * discount) / 100;
    } else {
        subtotal -= discount;
    }

    // GST ONLY
    let gstAmount = (subtotal * gstp) / 100;

    // SET VALUES INTO FIELDS
    row.querySelector(".gst-amount").value = gstAmount.toFixed(2);

    // FIX: DO NOT add gst inside line-total
    row.querySelector(".line-total").value = subtotal.toFixed(2);

    calculateTotals();
}

/********************** TOTALS CALCULATION ********************************************************/

function calculateTotals() {
    let subtotal = 0,
        gstTotal = 0;

    // Sum only base totals
    document.querySelectorAll(".line-total").forEach((el) => {
        subtotal += parseFloat(el.value) || 0;
    });

    // Sum only GST
    document.querySelectorAll(".gst-amount").forEach((el) => {
        gstTotal += parseFloat(el.value) || 0;
    });

    document.getElementById("subtotal").value = subtotal.toFixed(2);
    document.getElementById("tax_amount").value = gstTotal.toFixed(2);

    calculateTotal();
}

/********************** GRAND TOTAL ***************************************************************/

function calculateTotal() {
    let subtotal = parseFloat(document.getElementById("subtotal").value) || 0;
    let gst = parseFloat(document.getElementById("tax_amount").value) || 0;
    let ship =
        parseFloat(document.getElementById("shipping_charges").value) || 0;
    let round = parseFloat(document.getElementById("rounding").value) || 0;

    let total = subtotal + gst + ship + round;

    document.getElementById("total_amount").value = total.toFixed(2);

    calculateDue();
}

/********************** DUE CALCULATION **********************************************************/

function calculateDue() {
    let total = parseFloat(document.getElementById("total_amount").value) || 0;
    let paid = parseFloat(document.getElementById("paid_amount").value) || 0;

    document.getElementById("due_amount").value = (total - paid).toFixed(2);
}

/********************** ON PAGE LOAD *************************************************************/

document.addEventListener("DOMContentLoaded", function () {
    calculateTotals();
    calculateTotal();
    calculateDue();
});

/**********************ITEM ADD AND CALCULATION**************************************************/
/************************************************************************************************/

// Purchase data save
$(document).on("submit", "#purchaseForm", async function (e) {
    e.preventDefault();
    let formData = new FormData(this);
    formData.append("_token", $('meta[name="csrf-token"]').attr("content"));

    let btn = $("#purchaseBtn");
    let old = btn.html();
    btn.prop("disabled", true).html(
        '<span class="spinner-border spinner-border-sm"></span> Saving...',
    );

    try {
        const response = await fetch(window.PURCHASE_STORE_ROUTE, {
            method: "POST",
            body: formData,
        });

        const res = await response.json();
        //console.log("RAW:", res);

        if (response.status === 422) {
            showErrors(res.errors);
            return;
        }

        if (!res.success) {
            Swal.fire({ icon: "error", title: "Error", text: res.message });
            return;
        }

        Swal.fire({
            toast: true,
            icon: "success",
            title: res.message,
            timer: 2000,
            position: "top-end",
            showConfirmButton: false,
        });

        setTimeout(() => {
            window.location.href = res.redirect;
        }, 2000);
    } catch (err) {
        Swal.fire({ icon: "error", title: "Error", text: err.message });
    } finally {
        btn.prop("disabled", false).html(old);
    }
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

// update purchase form
$(document).on("submit", "#purchaseUpdateForm", async function (e) {
    e.preventDefault();

    let formData = new FormData(this);
    formData.append("_method", "PUT");

    let btn = $("#purchaseBtn");
    let originalText = btn.html();

    btn.prop("disabled", true).html(
        '<span class="spinner-border spinner-border-sm"></span> Saving...',
    );

    try {
        const response = await fetch(window.PURCHASE_UPDATE_ROUTE, {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            body: formData,
        });

        const res = await response.json();

        if (response.status === 422) {
            showErrors(res.errors);
            return;
        }

        // Server error (500 etc)
        if (!res.success) {
            Swal.fire({
                icon: "error",
                title: "Error",
                text: res.message || "Server Error",
            });
            return;
        }

        // SUCCESS
        Swal.fire({
            toast: true,
            icon: "success",
            title: res.message,
            position: "top-end",
            showConfirmButton: false,
            timer: 2000,
        });

        setTimeout(function () {
            if (res.success) {
                window.location.href = res.redirect; // ← REDIRECT FROM AJAX
            }
        }, 2000);
    } catch (error) {
        Swal.fire({
            icon: "error",
            title: "Error",
            text: error.message || "Something went wrong!",
        });
    } finally {
        btn.prop("disabled", false).html(originalText);
    }
});

/************************************************************************/
/***********************MAKE PAYMENT*************************************/
$(document).on("click", ".makePaymentBtn", function () {
    let id = $(this).data("id");
    GetPurchaseData(id);
    $("#paymentModal").modal("show");
});

async function GetPurchaseData(id) {
    const response = await fetch(window.GET_PURCHASE_DATA_ROUTE, {
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

    if (!res.success) {
        Swal.fire({
            icon: "error",
            title: "Error",
            text: res.message || "Server Error",
        });
        return;
    }

    let purchase = res.data;

    window.PURCHASE_MAKE_PAYMENT_FINAL_ROUTE =
        window.PURCHASE_MAKE_PAYMENT_ROUTE.replace(":id", id);

    $("#paymentModal #mp_supplier").val(
        purchase.supplier.first_name + " " + purchase.supplier.last_name,
    );
    $("#paymentModal #mp_bill_no").val(purchase.bill_no);
    $("#paymentModal #mp_total").val(purchase.total_amount);
    $("#paymentModal #mp_due").val(purchase.due_amount);
}

$(document).on("submit", "#makePaymentForm", async function (e) {
    e.preventDefault();

    let formData = new FormData(this);
    formData.append("_method", "PUT");
    let btn = $("#makePaymentBtn");
    let originalText = btn.html();

    btn.prop("disabled", true).html(
        '<span class="spinner-border spinner-border-sm me-2"></span> Saving...',
    );

    try {
        const response = await fetch(window.PURCHASE_MAKE_PAYMENT_FINAL_ROUTE, {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            body: formData,
        });

        const res = await response.json();

        if (response.status === 422) {
            showErrors(res.errors);
            return;
        }

        if (!res.success) {
            Swal.fire({
                icon: "error",
                title: "Error",
                text: res.message || "Server Error",
            });
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

        setTimeout(function () {
            $("#makePaymentForm")[0].reset();
            $("#purchaseTable").DataTable().ajax.reload(null, false);
            $("#paymentModal").modal("hide");
        }, 2000);
    } catch (error) {
        Swal.fire({
            icon: "error",
            title: "Error",
            text: error.message || "Something went wrong!",
        });
    } finally {
        btn.prop("disabled", false).html(originalText);
    }
});

/************************************************************************/
/***********************MAKE PAYMENT*************************************/

/************************************************************************/
/***********************PAYMENT HISTORY**********************************/
$(document).on("click", ".openHistory", function () {
    let purchaseId = $(this).data("id");
    window.CURRENT_PURCHASE_ID = purchaseId;
    openHistory(purchaseId);
    $("#paymentHistoryModal").modal("show");
});

async function openHistory(purchaseId) {
    try {
        const response = await fetch(window.PURCHASE_PAYMENT_HISTORY_ROUTE, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            body: JSON.stringify({ purchaseId }), // FIXED
        });

        const res = await response.json();

        if (response.status === 422) {
            showErrors(res.errors);
            return;
        }

        if (!res.success) {
            Swal.fire({
                icon: "error",
                title: "Error",
                text: res.message || "Server Error",
            });
            return;
        }

        const purchase = res.data;

        // TOP SUMMARY (use .text(), not .val())
        $("#ph_supplier").text(
            purchase.supplier.first_name + " " + purchase.supplier.last_name,
        );
        $("#ph_bill_code").text(purchase.reference_no);
        $("#ph_bill_date").text(purchase.purchase_date);
        $("#ph_paid_amount").text(purchase.paid_amount);
        $("#ph_balance").text(purchase.due_amount);

        // TABLE BODY
        let rows = "";
        let total = 0;

        res.payments.forEach((p) => {
            total += parseFloat(p.amount);

            rows += `
                <tr>
                    <td class="text-start">${p.payment_date}</td>
                    <td class="text-center">${p.payment_method}</td>
                    <td class="text-center">₹${p.amount}</td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-primary" title="Print"><i class="bi bi-printer"></i></button>
                        <button class="btn btn-sm btn-success" title="PDF"><i class="bi bi-file-pdf"></i></button>
                        <button class="btn btn-sm btn-danger delete-purchase-payment" title="Delete" data-id="${p.id}">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
        });

        document.getElementById("ph_table_body").innerHTML = rows;
        document.getElementById("ph_total_amount").innerText = "₹" + total;
    } catch (error) {
        console.error("Payment history load error:", error);
    }
}

/************************************************************************/
/***********************PAYMENT HISTORY**********************************/

/*************************DELETE PURCHASE PAYMENT RECORD****************/
$(document).on("click", ".delete-purchase-payment", function () {
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
            deletePurchasePayment(payment_id);
        }
    });
});

//delete function call
async function deletePurchasePayment(payment_id) {
    const response = await fetch(window.PURCHASE_PAYMENT_DELETE_ROUTE, {
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
        title: "Purchase payment entry deleted successfully",
        position: "top-end",
        showConfirmButton: false,
        timer: 2000,
    });

    // REDIRECT IF PROVIDED
    setTimeout(() => {
        openHistory(window.CURRENT_PURCHASE_ID);
        $("#purchaseTable").DataTable().ajax.reload(null, false); // SAME TABLE REFRESH
    }, 700);
}
/*************************DELETE PURCHASE PAYMENT RECORD****************/

/************************PURCHASE DELETE*********************************/
$(document).on("click", ".purchase-delete", function () {
    let purchase_id = $(this).data("id");
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
            purchaseDelete(purchase_id);
        }
    });
});

async function purchaseDelete(purchase_id) {
    const response = await fetch(window.PURCHASE_DELETE_ROUTE, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        body: JSON.stringify({ purchase_id }),
    });

    const res = await response.json();

    if (response.status === 422) {
        showErrors(res.errors);
        return;
    }

    if (!res.success) {
        Swal.fire({
            icon: "error",
            title: "Error",
            text: res.message || "Server Error",
        });
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

    setTimeout(function () {
        $("#purchaseTable").DataTable().ajax.reload(null, false);
    }, 2000);
}
/************************PURCHASE DELETE*********************************/
