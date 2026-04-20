/*********************CURRENT DATA TABLE********************* */
if ($("#stockTable").length) {
    $("#stockTable").DataTable({
        processing: true,
        serverSide: true,
        ajax: INVENTORY_STOCK_ROUTE,

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
                data: "DT_RowIndex",
                name: "DT_RowIndex",
                searchable: false,
                orderable: false,
            },
            {
                data: "name",
                name: "products.name",
            },
            {
                data: "stock",
                name: "stock",
                searchable: false,
            },
        ],
        dom: "lBfrtip",

        buttons: [
            { extend: "copy", className: "btn btn-secondary btn-sm" },
            { extend: "excel", className: "btn btn-success btn-sm" },
            { extend: "pdf", className: "btn btn-danger btn-sm" },
            { extend: "print", className: "btn btn-primary btn-sm" },
        ],
    });
}
/*********************CURRENT DATA TABLE CLOSED**************/

/*********************LADGER DATA TABLE********************* */
if ($("#ledgerTable").length) {
    $("#ledgerTable").DataTable({
        processing: true,
        serverSide: true,
        ajax: INVENTORY_LADGER_ROUTE,

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
            { data: "DT_RowIndex", searchable: false },
            { data: "created_at", name: "stock_movements.created_at" },
            { data: "name", name: "products.name" },
            { data: "reference", searchable: false },
            { data: "in", searchable: false },
            { data: "out", searchable: false },
        ],
        dom: "lBfrtip",

        buttons: [
            { extend: "copy", className: "btn btn-secondary btn-sm" },
            { extend: "excel", className: "btn btn-success btn-sm" },
            { extend: "pdf", className: "btn btn-danger btn-sm" },
            { extend: "print", className: "btn btn-primary btn-sm" },
        ],
    });
}
/*********************LADGER DATA TABLE CLOSED**************/

/*********************LOW LADGER DATA TABLE********************* */
if ($("#lowStockTable").length) {
    $("#lowStockTable").DataTable({
        processing: true,
        serverSide: true,
        ajax: INVENTORY_LOW_STOCK_ROUTE,

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
            { data: "name", name: "products.name" },
            { data: "stock", name: "stock" },
            { data: "low_stock_alert", name: "products.low_stock_alert" },
            {
                data: "status",
                name: "status",
                orderable: false,
                searchable: false,
            },
        ],

        dom: "lBfrtip",

        buttons: [
            { extend: "copy", className: "btn btn-secondary btn-sm" },
            { extend: "excel", className: "btn btn-success btn-sm" },
            { extend: "pdf", className: "btn btn-danger btn-sm" },
            { extend: "print", className: "btn btn-primary btn-sm" },
        ],
    });
}
/*********************LOW STOCK DATA TABLE**************/

/*********************ADJUSTMENT STOCK**********************/
$(document).on("input", "#quantity", function () {
    calculateNewStock();
});

function calculateNewStock() {
    let currentStock = parseFloat($("#current_stock").val()) || 0;
    let adjustmentQty = parseFloat($("#quantity").val()) || 0;

    let newStock = currentStock + adjustmentQty;

    $("#new_stock").val(newStock);
}

// fetch stock
$(document).on("change", "#product_id", function () {
    let productId = $(this).val();

    if (!productId) return;

    productStock(productId);
});

async function productStock(productId) {
    const response = await fetch(window.INVENTORY_GETSTOCKPRODUCT_ROUTE, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        body: JSON.stringify({ productId }),
    });

    const res = await response.json();

    $("#current_stock").val(res.stock);
    $("#current_stock_hidden").val(res.stock);

    calculateNewStock();
}

/*********************ADJUSTMENT STOCK**********************/
