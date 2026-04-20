// user profile image preview
let input = document.getElementById("photoInput");
let preview = document.getElementById("previewImage");
let resetBtn = document.getElementById("resetImage");

input.addEventListener("change", () => {
    if (input.files && input.files[0]) {
        preview.src = URL.createObjectURL(input.files[0]);
    }
});

resetBtn.onclick = () => {
    input.value = "";
    preview.src = preview.dataset.default; // ← THIS WORKS 100%
};

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

function clearErrors() {
    $(".error-text").text("");
}

//profile update
$(document).on("submit", "#profileForm", async function (e) {
    e.preventDefault();

    const form = this;
    const formData = new FormData(form);
    let $btn = $("#profileBtn");
    let originalText = $btn.html();
    // Disable button + spinner
    $btn.prop("disabled", true).html(
        '<span class="spinner-border spinner-border-sm"></span> Saving...',
    );

    try {
        const response = await fetch(window.PROFILE_UPDATE, {
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

            setTimeout(function () {
                window.location.reload();
            }, 2000);
        } else {
            Swal.fire({
                icon: "error",
                title: "Server Error",
                text: res.message,
            });
        }
    } catch (error) {
        Swal.fire({
            icon: "error",
            title: "Error",
            text: error.message || "Something went wrong!",
        });
    } finally {
        $btn.prop("disabled", false).html(originalText);
    }
});

//password change
$(document).on("submit", "#passwordForm", async function (e) {
    e.preventDefault();

    const form = this;
    const formData = new FormData(form);
    let $btn = $("#passwordBtn");
    let originalText = $btn.html();
    // Disable button + spinner
    $btn.prop("disabled", true).html(
        '<span class="spinner-border spinner-border-sm"></span> Saving...',
    );

    try {
        const response = await fetch(window.PROFILE_PASSWORD_CHANGE, {
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

        if (res.success) {
            Swal.fire({
                toast: true,
                icon: "success",
                title: res.message,
                position: "top-end",
                showConfirmButton: false,
                timer: 2000,
            });

            setTimeout(function () {
                window.location.reload();
            }, 2000);
        } else {
            Swal.fire({
                icon: "error",
                title: "Server Error",
                text: res.message,
            });
        }
    } catch (error) {
        Swal.fire({
            icon: "error",
            title: "Error",
            text: error.message || "Something went wrong!",
        });
    } finally {
        $btn.prop("disabled", false).html(originalText);
    }
});
