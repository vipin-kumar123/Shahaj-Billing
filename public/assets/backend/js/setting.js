// --------------------------
// PREVIEW: LOGO
// --------------------------
document
    .getElementById("logoInput")
    .addEventListener("change", function (event) {
        const preview = document.getElementById("previewLogo");
        preview.src = URL.createObjectURL(event.target.files[0]);
    });

document.getElementById("resetLogo").addEventListener("click", function () {
    const preview = document.getElementById("previewLogo");
    preview.src = preview.getAttribute("data-default");
    document.getElementById("logoInput").value = "";
});

// --------------------------
// PREVIEW: FAVICON
// --------------------------
document
    .getElementById("faviconInput")
    .addEventListener("change", function (event) {
        const preview = document.getElementById("previewFavicon");
        preview.src = URL.createObjectURL(event.target.files[0]);
    });

document.getElementById("resetFavicon").addEventListener("click", function () {
    const preview = document.getElementById("previewFavicon");
    preview.src = preview.getAttribute("data-default");
    document.getElementById("faviconInput").value = "";
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

// --------------------------
// LOAD DATA ON PAGE OPEN
// --------------------------
document.addEventListener("DOMContentLoaded", function () {
    getAppDataDisplay();
});

// --------------------------
// FETCH GENERAL SETTINGS
// --------------------------
async function getAppDataDisplay() {
    const response = await fetch(window.GENERAL_SETTINGS_GET_DATA, {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')
                .content,
            "Content-Type": "application/json",
        },
    });

    const res = await response.json();
    //console.log("GENERAL SETTINGS:", res);

    if (!res.success) return;

    const data = res.data;

    // --------------------------
    // GENERAL FORM FIELDS
    // --------------------------
    $("#GeneralForm input[name=app_name]").val(data.app_name);
    $("#GeneralForm input[name=footer_text]").val(data.footer_text);
    $("#GeneralForm select[name=language]")
        .val(data.language)
        .trigger("change");
    $("#GeneralForm select[name=timezone]")
        .val(data.timezone)
        .trigger("change");
    $("#GeneralForm select[name=date_format]")
        .val(data.date_format)
        .trigger("change");
    $("#GeneralForm select[name=time_format]")
        .val(data.time_format)
        .trigger("change");

    // --------------------------
    // LOGO LOAD
    // --------------------------
    $("#previewLogo")
        .attr("src", data.logo ?? window.DEFAULT_IMAGE)
        .attr("data-default", data.logo ?? window.DEFAULT_IMAGE);

    // --------------------------
    // FAVICON LOAD
    // --------------------------
    $("#previewFavicon")
        .attr("src", data.favicon ?? window.DEFAULT_IMAGE)
        .attr("data-default", data.favicon ?? window.DEFAULT_IMAGE);
}

//general form submit
$(document).on("submit", "#GeneralForm", async function (e) {
    e.preventDefault();

    const form = this;
    const formData = new FormData(form);

    let $btn = $("#generalBtn");
    let orginalText = $btn.html();

    $btn.prop("disabled", true).html(
        '<span class="spinner-border spinner-border-sm"></span> Saving...',
    );

    try {
        const response = await fetch(window.GENERAL_SETTINGS_UPDATE, {
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

        if (!res.success) {
            Swal.fire({
                icon: "error",
                title: "Server Error",
                text: res.message,
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
            getAppDataDisplay();
        }, 2000);
    } catch (error) {
        Swal.fire({
            icon: "error",
            title: "Error",
            text: error.message || "Something went wrong!",
        });
    } finally {
        $btn.prop("disabled", false).html(orginalText);
    }
});

//logo update form
$(document).on("submit", "#appLogoForm", async function (e) {
    e.preventDefault();

    const form = this;
    const formData = new FormData(form);
    let $btn = $("#logoBtn");
    let orginalText = $btn.html();

    $btn.prop("disabled", true).html(
        '<span class="spinner-border spinner-border-sm"></span> Saving...',
    );

    try {
        const response = await fetch(window.LOGO_SETTINGS_UPDATE, {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            body: formData,
        });

        const res = await response.json();
        //422 validation error
        if (response.status === 422) {
            showErrors(res.errors);
            return;
        }

        if (!res.success) {
            Swal.fire({
                icon: "error",
                title: "Server Error",
                text: res.message,
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
            getAppDataDisplay();
        }, 2000);
    } catch (error) {
        Swal.fire({
            icon: "error",
            title: "Error",
            text: error.message || "Something went wrong!",
        });
    } finally {
        $btn.prop("disabled", false).html(orginalText);
    }
});
