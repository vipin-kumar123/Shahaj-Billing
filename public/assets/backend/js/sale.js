/*********************DATA TABLE********************* */
if ($("#saleTable").length) {
    $("#saleTable").DataTable({
        processing: true,
        serverSide: true,
        ajax: SALE_INDEX_ROUTE,

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
                data: "sale_date",
                name: "sale_date",
            },
            {
                data: "customer",
                name: "customer",
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

/*********************ADD ITEM PRODUCT AND CALCULATION**************/
// ADD NEW ROW
function addRow() {
    const template = document.getElementById("itemRowTemplate");
    const clone = template.content.cloneNode(true);
    document.querySelector("#itemsTable tbody").appendChild(clone);

    initializeRowEvents();

    // Initialize select2 on the new row
    $("#itemsTable tbody tr:last-child .select2").select2({
        width: "100%",
    });
}

// DELETE ROW
function deleteRow(button) {
    button.closest("tr").remove();
    calculateTotal();
}

// PRODUCT SELECTED
function productSelected(select) {
    let row = select.closest("tr");
    let selectedOption = select.options[select.selectedIndex];

    let price = selectedOption.getAttribute("data-price") || 0;
    let gst = selectedOption.getAttribute("data-gst") || 0;

    row.querySelector(".unit-cost").value = price;
    row.querySelector(".gst-percent").value = gst;

    updateRow(select);
}

// UPDATE SINGLE ROW
function updateRow(element) {
    let row = element.closest("tr");

    let price = parseFloat(row.querySelector(".unit-cost").value) || 0;
    let qty = parseFloat(row.querySelector(".quantity").value) || 0;
    let discount = parseFloat(row.querySelector(".discount").value) || 0;
    let discountType = row.querySelector(".discount-type").value;
    let gstPercent = parseFloat(row.querySelector(".gst-percent").value) || 0;

    let subtotal = price * qty;

    // Discount
    if (discountType === "percent") {
        subtotal -= (subtotal * discount) / 100;
    } else {
        subtotal -= discount;
    }

    if (subtotal < 0) subtotal = 0;

    let gstAmount = (subtotal * gstPercent) / 100;
    let total = subtotal + gstAmount;

    row.querySelector(".gst-amount").value = gstAmount.toFixed(2);
    row.querySelector(".line-total").value = total.toFixed(2);

    calculateTotal();
}

// GRAND TOTAL
function calculateTotal() {
    let subtotal = 0;
    let taxTotal = 0;

    document.querySelectorAll("#itemsTable tbody tr").forEach((row) => {
        let lineTotal = parseFloat(row.querySelector(".line-total").value) || 0;
        let gstAmount = parseFloat(row.querySelector(".gst-amount").value) || 0;

        subtotal += lineTotal - gstAmount;
        taxTotal += gstAmount;
    });

    let shipping =
        parseFloat(document.getElementById("shipping_charges")?.value) || 0;
    let rounding = parseFloat(document.getElementById("rounding")?.value) || 0;

    let grandTotal = subtotal + taxTotal + shipping + rounding;

    document.getElementById("subtotal").value = subtotal.toFixed(2);
    document.getElementById("tax_amount").value = taxTotal.toFixed(2);
    document.getElementById("total_amount").value = grandTotal.toFixed(2);

    calculateDue();
}

// DUE
function calculateDue() {
    let total = parseFloat(document.getElementById("total_amount").value) || 0;
    let paid = parseFloat(document.getElementById("paid_amount").value) || 0;

    let due = total - paid;
    document.getElementById("due_amount").value = due.toFixed(2);
}

// INITIALIZE EVENTS (IMPORTANT FOR EDIT PAGE)
function initializeRowEvents() {
    document
        .querySelectorAll(".unit-cost, .quantity, .discount, .gst-percent")
        .forEach((input) => {
            input.removeEventListener("input", handleInputChange);
            input.addEventListener("input", handleInputChange);
        });

    document.querySelectorAll(".discount-type").forEach((select) => {
        select.removeEventListener("change", handleInputChange);
        select.addEventListener("change", handleInputChange);
    });

    document.querySelectorAll(".product-select").forEach((select) => {
        select.removeEventListener("change", handleProductChange);
        select.addEventListener("change", handleProductChange);
    });
}

// HANDLERS
function handleInputChange(e) {
    updateRow(e.target);
}

function handleProductChange(e) {
    productSelected(e.target);
}

// ON PAGE LOAD (WORKS FOR CREATE + EDIT)
document.addEventListener("DOMContentLoaded", function () {
    initializeRowEvents();

    // If no rows exist → create page
    if (document.querySelectorAll("#itemsTable tbody tr").length === 0) {
        addRow();
    }

    // Recalculate existing rows (for edit page)
    document.querySelectorAll("#itemsTable tbody tr").forEach((row) => {
        updateRow(row.querySelector(".unit-cost"));
    });
});

/*********************ADD ITEM PRODUCT AND CALCULATION**************/

/***********************form validation***************************/
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
/***********************form validation***************************/

/***********************sale form submit***************************/
$(document).on("submit", "#saleForm", async function (e) {
    e.preventDefault();

    let formData = new FormData(this);
    let btn = $("#addSaleBtn");
    let originalText = btn.html(); // spelling fixed

    btn.prop("disabled", true).html(
        '<span class="spinner-border spinner-border-sm me-1"></span> Saving...',
    );

    try {
        const response = await fetch(window.PURCHASE_STORE_ROUTE, {
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
/***********************sale form submit***************************/

/***********************update sale form submit***************************/
$(document).on("submit", "#saleUpdateForm", async function (e) {
    e.preventDefault();

    let formData = new FormData(this);
    formData.append("_method", "PUT");

    let btn = $("#editSaleBtn");
    let originalText = btn.html();

    btn.prop("disabled", true).html(
        '<span class="spinner-border spinner-border-sm me-1"></span> Saving...',
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
/***********************update sale form submit***************************/

/***********************************************************************/
/**********************RETURN CONVERT************************************/
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

// for edit page
document.addEventListener("DOMContentLoaded", function () {
    calculateTotalReturn();
});

// RETURN FORM SUBMIT
$(document).on("submit", "#saleReturnForm", async function (e) {
    e.preventDefault();

    let formData = new FormData(this);

    let btn = $("#returnSave");
    let originalText = btn.html();

    btn.prop("disabled", true).html(
        '<span class="spinner-border spinner-border-sm me-1"></span> Saving...',
    );

    try {
        const response = await fetch(window.SALE_RETURN_STORE_ROUTE, {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            body: formData,
        });

        const res = await response.json();
        //console.log(res);
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

        setTimeout(
            () => (location.href = window.SALE_RETURN_INDEX_ROUTE),
            2000,
        );
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

//sale invoice delete
$(document).on("click", ".sale-delete", function () {
    let sale_id = $(this).data("id");
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
            deleteSaleInvoice(sale_id);
        }
    });
});

async function deleteSaleInvoice(sale_id) {
    const response = await fetch(window.SALE_DELETE_ROUTE, {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            "Content-Type": "application/json",
        },
        body: JSON.stringify({ sale_id }),
    });

    const res = await response.json();
    //console.log(res);
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
        $("#saleTable").DataTable().ajax.reload(null, false);
    }, 2000);
}
//End sale invoice delete
/************************************************************************/
/***********************RECEIVE PAYMENT**********************************/
$(document).on("click", ".receivePaymentBtn", function () {
    let id = $(this).data("id");
    GetSaleData(id);
    $("#receivePaymentModal").modal("show");
});

async function GetSaleData(id) {
    const response = await fetch(window.GET_SALE_DATA_ROUTE, {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            "Content-Type": "application/json",
        },
        body: JSON.stringify({ id }),
    });

    const res = await response.json();
    //console.log(res);
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

    let sale = res.data;

    window.SALE_RECEIVE_PAYMENT_FINAL_ROUTE =
        window.SALE_RECEIVE_PAYMENT_ROUTE.replace(":id", id);

    $("#receivePaymentModal #rp_customer").val(
        sale.customer.first_name + " " + sale.customer.last_name,
    );
    $("#receivePaymentModal #rp_invoice_no").val(sale.invoice_no);
    $("#receivePaymentModal #rp_total").val(sale.total_amount);
    $("#receivePaymentModal #rp_due").val(sale.due_amount);
}

//save payment
$(document).on("submit", "#receivePaymentForm", async function (e) {
    e.preventDefault();

    let formData = new FormData(this);
    formData.append("_method", "PUT");
    let btn = $("#receivePaymentBtn");
    let originalText = btn.html();

    btn.prop("disabled", true).html(
        '<span class="spinner-border spinner-border-sm me-2"></span> Saving...',
    );

    try {
        const response = await fetch(window.SALE_RECEIVE_PAYMENT_FINAL_ROUTE, {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            body: formData,
        });

        const res = await response.json();
        //console.log(res);
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
            $("#receivePaymentForm")[0].reset();
            $("#saleTable").DataTable().ajax.reload(null, false);
            $("#receivePaymentModal").modal("hide");
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

/****************************************************************************/
/***********************END RECEIVE PAYMENT**********************************/

/****************************************************************************/
/***********************OPEN RECEIVE PAYMENT**********************************/
$(document).on("click", ".openHistory", function () {
    let sale_id = $(this).data("id");
    //global used
    window.CURRENT_SALE_ID = sale_id;
    openHistory(sale_id);
    $("#receivedHistoryModal").modal("show");
});

async function openHistory(id) {
    const response = await fetch(window.SALE_RECEIVE_HISTORY_ROUTE, {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            "Content-Type": "application/json",
        },
        body: JSON.stringify({ id }),
    });

    const res = await response.json();
    // console.log(res);

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

    // Correct Structure
    const main = res.data; // first layer
    const sale = main.data; // actual sale model

    const payments = main.payments;

    // TOP SUMMARY
    $("#receivedHistoryModal #rp_customer").text(
        sale.customer.first_name + " " + sale.customer.last_name,
    );

    $("#receivedHistoryModal #rp_invoice_no").text(sale.invoice_no);

    $("#receivedHistoryModal #rp_sale_date").text(main.sale_date);

    $("#rp_paid_amount").text(main.total_paid);
    $("#rp_balance").text(main.due_amount);

    // TABLE BODY
    let rows = "";
    let total = 0;

    payments.forEach((p) => {
        total += parseFloat(p.amount);

        rows += `
            <tr>
                <td class="text-start">${p.payment_date}</td>
                <td class="text-center">${p.payment_method}</td>
                <td class="text-center">₹${p.amount}</td>
                <td class="text-center">
                    <button class="btn btn-sm btn-primary"><i class="bi bi-printer"></i></button>
                    <button class="btn btn-sm btn-success"><i class="bi bi-file-pdf"></i></button>
                    <button class="btn btn-sm btn-danger sale-invoice-delete" data-id="${p.id}">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            </tr>
        `;
    });

    document.getElementById("rp_table_body").innerHTML = rows;
    document.getElementById("rp_total_amount").innerText = "₹" + total;
}
/*****************************************************************************/
/***********************END OPEN RECEIVE PAYMENT******************************/

/***********************DELETE RECEIVE AMOUNT RECORD******************************/
$(document).on("click", ".sale-invoice-delete", function () {
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
            deleteSaleInvoicePayment(payment_id);
        }
    });
});

//delete function call
async function deleteSaleInvoicePayment(payment_id) {
    const response = await fetch(window.SALE_RECEIVE_PAYMENT_DELETE_ROUTE, {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            "Content-Type": "application/json",
        },
        body: JSON.stringify({ payment_id }),
    });

    const result = await response.json();

    if (response.status === 422) {
        showErrors(result.errors);
        return;
    }

    if (!result.success) {
        Swal.fire({
            icon: "error",
            title: "Error",
            text: result.message || "Server Error",
        });
        return;
    }

    // SUCCESS FIXED ✔
    Swal.fire({
        toast: true,
        icon: "success",
        title: result.message || "Payment deleted successfully!",
        position: "top-end",
        showConfirmButton: false,
        timer: 2000,
    });

    setTimeout(function () {
        openHistory(window.CURRENT_SALE_ID);
        $("#saleTable").DataTable().ajax.reload(null, false);
    }, 2000);
}
/***********************DELETE RECEIVE AMOUNT RECORD******************************/

/*********************************************************************************
 *                 SALE RETURN CODE STATRT                                       *
 *                 this section for sale return                                  *
 *********************************************************************************/
//data table sale return
if ($("#saleReturn").length) {
    $("#saleReturn").DataTable({
        processing: true,
        serverSide: true,
        ajax: SALE_RETURN_INDEX_ROUTE,

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
                data: "return_no",
                name: "return_no",
            },
            {
                data: "return_date",
                name: "return_date",
            },
            {
                data: "customer",
                name: "customer",
            },
            {
                data: "total_return_amount",
                name: "total_return_amount",
            },
            {
                data: "refund_due",
                name: "refund_due",
            },

            {
                data: "creator",
                name: "creator",
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

// RETURN EDIT FORM SUBMIT
$(document).on("submit", "#editSaleReturnForm", async function (e) {
    e.preventDefault();

    let formData = new FormData(this);
    formData.append("_method", "PUT");
    let btn = $("#editReturnBtn");
    let originalText = btn.html();

    btn.prop("disabled", true).html(
        '<span class="spinner-border spinner-border-sm me-1"></span> Saving...',
    );

    try {
        const response = await fetch(window.SALE_RETURN_UPDATE_ROUTE, {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            body: formData,
        });

        const res = await response.json();
        //console.log(res);
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

        setTimeout(
            () => (location.href = window.SALE_RETURN_INDEX_ROUTE),
            2000,
        );
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
/**********************RETURN CONVERT************************************/

/* =========get sale return data Refund Payment ========= */
$(document).on("click", ".refund-payment", function () {
    let sale_return_id = $(this).data("id");
    refundPayment(sale_return_id);
    $("#refund-payment-modal").modal("show");
});

async function refundPayment(sale_return_id) {
    try {
        const response = await fetch(window.GET_SALE_RETURN_DATA, {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                "Content-Type": "application/json",
            },
            body: JSON.stringify({ sale_return_id }),
        });

        const result = await response.json();

        //console.log("DATA RESPONSE: ", result);

        if (response.status === 422) {
            showErrors(result.errors);
            return;
        }

        if (!result.success) {
            Swal.fire({
                icon: "error",
                title: "Error",
                text: result.message || "Failed to fetch return data",
            });
            return;
        }

        const data = result.data;

        // Set route
        window.SALE_RETURN_REFUND_PAYMENT_FINEL_ROUTE =
            window.MAKE_REFUND_PAYMENT_ROUTE.replace(":id", sale_return_id);

        // CORRECT IDs
        $("#refund-payment-modal #rf_id").val(data.id);
        $("#refund-payment-modal #rf_customer").val(
            `${data.customer.first_name} ${data.customer.last_name}`,
        );
        $("#refund-payment-modal #rf_return_no").val(data.return_no);
        $("#refund-payment-modal #rf_return_total").val(
            Number(data.total_return_amount).toFixed(2),
        );

        // refund_due
        let refunded = Number(data.refunded_amount ?? 0);
        let refundDue = (Number(data.total_return_amount) - refunded).toFixed(
            2,
        );

        $("#refund-payment-modal #rf_refund_due").val(refundDue);
    } catch (error) {
        console.error("Refund fetch error: ", error);
        Swal.fire({
            icon: "error",
            title: "Error",
            text: "Something went wrong!",
        });
    }
}
/* =========end get sale return ========= */

/* =========Refund Payment ============== */
$(document).on("submit", "#refundPaymentForm", async function (e) {
    e.preventDefault();

    let formData = new FormData(this);

    const response = await fetch(
        window.SALE_RETURN_REFUND_PAYMENT_FINEL_ROUTE,
        {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            body: formData,
        },
    );

    const result = await response.json();

    if (response.status === 422) {
        showErrors(result.errors);
        return;
    }

    if (!result.success) {
        Swal.fire({
            icon: "error",
            title: "Error",
            text: result.message,
        });
        return;
    }

    Swal.fire({
        toast: true,
        icon: "success",
        title: result.message,
        position: "top-end",
        showConfirmButton: false,
        timer: 2000,
    });

    setTimeout(() => {
        $("#refundPaymentForm")[0].reset();
        $("#saleReturn").DataTable().ajax.reload(null, false);
        $("#refund-payment-modal").modal("hide");
    }, 2000);
});
/* =========end Refund Payment ========= */

/* ========= Refund History ============ */
$(document).on("click", ".refund-history", function () {
    let sale_return_id = $(this).data("id");
    window.CURRENT_SALE_RETURN_ID = sale_return_id;
    loadRefundHistory(sale_return_id);
    $("#history-modal").modal("show");
});

async function loadRefundHistory(sale_return_id) {
    try {
        const response = await fetch(window.SALE_RETURN_REFUND_HISTORY_ROUTE, {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                "Content-Type": "application/json",
            },
            body: JSON.stringify({ sale_return_id }),
        });

        const result = await response.json();
        //console.log(result);

        if (response.status === 422) {
            showErrors(result.errors);
            return;
        }

        // Correct Variables
        const saleReturn = result.data; // we structured data already!
        const payments = result.data.payments;

        // -------------------------
        // SUMMARY FIELDS
        // -------------------------
        $("#history-modal #rf_customer").text(saleReturn.customer.name);
        $("#history-modal #rf_invoice_no").text(saleReturn.sale.invoice_no);
        $("#history-modal #rf_sale_date").text(saleReturn.sale.date);

        $("#history-modal #rf_paid_amount").text(saleReturn.refunded_amount);
        $("#history-modal #rf_balance").text(saleReturn.refund_due);

        // -------------------------
        // TABLE ROWS
        // -------------------------
        let rows = "";
        let total = 0;

        payments.forEach((p) => {
            total += parseFloat(p.amount);

            rows += `
                <tr>
                    <td>${p.payment_date}</td>
                    <td class="text-center">${p.payment_method}</td>
                    <td class="text-center">₹${p.amount}</td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-primary" title="Print"><i class="bi bi-printer"></i></button>
                        <button class="btn btn-sm btn-success" title="PDF"><i class="bi bi-file-pdf"></i></button>
                        <button class="btn btn-sm btn-danger deleteRefundPayment" title="Delete" data-id="${p.id}">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
        });

        $("#history-modal #rf_table_body").html(rows);
        $("#history-modal #rf_total_amount").text("₹" + total.toFixed(2));
    } catch (error) {
        console.error(error);
    }
}

/* =========end Refund History ========= */

/* =========delete refund payment ========= */
$(document).on("click", ".deleteRefundPayment", function () {
    let payment_id = $(this).data("id");

    Swal.fire({
        title: "Are you sure?",
        text: "You cannot revert this!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Yes, Delete!",
    }).then((res) => {
        if (res.isConfirmed) {
            deleteRefundPayment(payment_id);
        }
    });
});

async function deleteRefundPayment(payment_id) {
    try {
        const response = await fetch(window.DELETE_SALE_RETURN_REFUND_ROUTE, {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                "Content-Type": "application/json",
            },
            body: JSON.stringify({ payment_id }),
        });

        const result = await response.json();

        if (!result.success) {
            Swal.fire({
                icon: "error",
                title: "Error",
                text: result.message,
            });
            return;
        }

        Swal.fire({
            toast: true,
            icon: "success",
            title: result.message,
            position: "top-end",
            showConfirmButton: false,
            timer: 2000,
        });

        // Reload modal table
        loadRefundHistory(window.CURRENT_SALE_RETURN_ID);
        $("#saleReturn").DataTable().ajax.reload(null, false);
    } catch (error) {
        console.error(error);
    }
}
/* =========delete Refund payment ========= */
