$(document).ready(function () {

    const form = $("#regForm");
    const output = $("#resultBox");
    const btn = $("#submitBtn");

    function message(type, text) {
        const cls = type === "ok" ? "alertSuccess" : "alertError";
        output.hide().html(`<div class="${cls}">${text}</div>`).fadeIn(200);
    }

    function checkFields(data) {
        let err = [];
        if (!data.get("name").trim()) err.push("Name is required.");
        if (!data.get("email").trim()) err.push("Email is required.");
        if (!data.get("phone").trim()) err.push("Phone is required.");
        return err;
    }

    form.on("submit", function (e) {
        e.preventDefault();
        btn.prop("disabled", true).text("Saving...");

        let formData = new FormData(this);
        let errors = checkFields(formData);

        if (errors.length) {
            message("error", "<b>Validation Failed</b><ul><li>" + errors.join("</li><li>") + "</li></ul>");
            btn.prop("disabled", false).text("Submit");
            return;
        }

        $.ajax({
            url: "submit.php",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function (res) {
                message("ok", res);
                form[0].reset();
            },
            error: function () {
                message("error", "Server error. Try again.");
            },
            complete: function () {
                btn.prop("disabled", false).text("Submit");
            }
        });
    });

});
