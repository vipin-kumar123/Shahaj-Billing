document.addEventListener("DOMContentLoaded", function () {
    companyDataDisplay();
});

// --------------------------
// FETCH GENERAL SETTINGS
// --------------------------
async function companyDataDisplay() {
    const response = await fetch(window.GET_COMPANY_DATA, {
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
    $("#companyForm input[name=name]").val(data.name);
    $("#companyForm input[name=mobile]").val(data.mobile);
    $("#companyForm input[name=email]").val(data.email);
    $("#companyForm input[name=tax_number]").val(data.tax_number);
    $("#companyForm textarea[name=address]").val(data.address);

    $("#companyForm select[name=state_id]")
        .val(data.state_id)
        .trigger("change");

    $("#companyForm #selectedCity").val(data.city_id);

    // --------------------------
    // LOGO LOAD
    // --------------------------
    if (data.company_logo) {
        $("#company-logo-preview")
            .attr("src", window.ASSET + data.company_logo)
            .show(); // << IMPORTANT
        $("#logo-placeholder").hide();
    } else {
        $("#company-logo-preview").hide();
        $("#logo-placeholder").show();
    }
}
/*********************************************************************/
/*********************************************************************/

// company data store
$(document).on("submit", "#companyForm", async function (e) {
    e.preventDefault();

    let formData = new FormData(this);
    // formData.append("_method", "PUT");

    let btn = $("#companyBtn");
    let originalText = btn.html();

    btn.prop("disabled", true).html(
        '<span class="spinner-border spinner-border-sm"></span> Saving...',
    );

    try {
        const response = await fetch(window.COMPANY_STORE_DATA, {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            body: formData,
        });

        const res = await response.json();
        console.log(res);
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
            companyDataDisplay();
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

// state select append city
$("#stateSelect").on("change", async function () {
    let stateId = $(this).val();

    let citySelect = $("#citySelect");

    citySelect.html('<option value="">Select City</option>');

    if (!stateId) return;

    try {
        const response = await fetch(window.COMPANY_GET_CITY_DATA, {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                "Content-Type": "application/json",
            },
            body: JSON.stringify({
                stateId,
            }),
        });

        const data = await response.json();

        if (response.status === 422) {
            showErrors(res.errors);
            return;
        }

        data.cities.forEach((city) => {
            citySelect.append(
                `<option value="${city.id}">${city.name}</option>`,
            );
        });

        //AUTO SELECT CITY ON EDIT
        let selectedCityId = $("#selectedCity").val();
        if (selectedCityId) {
            citySelect.val(selectedCityId).trigger("change");
        }
    } catch (error) {
        console.error("Error loading cities:", error);
    }
});

//If you need image preview on file select, just add:
$("#logoFile").on("change", function () {
    let file = this.files[0];

    if (!file) return;

    let reader = new FileReader();
    reader.onload = function (e) {
        $("#company-logo-preview").attr("src", e.target.result).show();
        $("#logo-placeholder").hide();
    };
    reader.readAsDataURL(file);
});
