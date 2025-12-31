$("#resolution-form").validate({
    rules: {
        company: {
            required: true,
        },

        start_date: {
            required: true,
        },

        end_date: {
            required: true,
        },
        meeting_date: {
            required: true,
        },
        member_file: {
            required: true,
        },
    },
    errorPlacement: function (error, element) {
        var placement = $(element).data("error");
        if (placement) {
            $(placement).append(error);
        } else {
            error.insertAfter(element);
        }
    },
});

$("#member-form").validate({
    rules: {
        member_name: {
            required: true,
        },

        member_share: {
            required: true,
        },

        member_email: {
            required: true,
            email: true,
        },
    },
});

$.validator.addMethod(
    "filesize",
    function (value, element, param) {
        return (
            this.optional(element) || element.files[0].size <= param * 1000000
        );
    },
    "Maximum file size allowed is 5MB."
);

$(".select2").select2();
$(".select2bs4").select2({
    theme: "bootstrap4",
});
$("#resolution_startdate").datetimepicker({
    format: "DD-MM-YYYY h:mm:A",
    icons: {
        time: "far fa-clock",
    },
});
$("#resolution_enddate").datetimepicker({
    format: "DD-MM-YYYY h:mm:A",
    icons: {
        time: "far fa-clock",
    },
});
$("#resolution_meetingdate").datetimepicker({
    format: "DD-MM-YYYY h:mm:A",
    icons: {
        time: "far fa-clock",
    },
});

function addResolutionDetailsValidation() {
    $("[name^=description").each(function () {
        $(this).rules("add", {
            required: true,
        });
    });
    $("[name^=resolution_files").each(function () {
        if (!$(this).attr("data-id")) {
            $(this).rules("add", {
                required: true,
                extension: "pdf",
                filesize: 5,
                messages: {
                    extension:
                        "Selected file is not valid. Only PDF file format supported.",
                },
            });
        }
    });
}

function AddResolutionDetalisRaw() {
    randomCount++;
    $("#resolution-files").append(`
    <div class="row file-wrapper">
       <div class="col-6">
            <textarea cols="60" rows="10" class="resolution_description required-section"  name="description[${randomCount}]"></textarea>
        </div>
        <div class="col-4">
        <input type="hidden" name="resolution_details_id[]" value="">
            <input type="file" class="custom-file-input required-section" onchange="fileChange(this)"  data-error="#member-file-error-${randomCount}"  name="resolution_files[${randomCount}]">
            <label class="custom-file-label" >Choose
            file</label>
            <br>
            <span class="error" id="member-file-error-${randomCount}"></span>
        </div>
        <div class="col-2">
            <button type="button" class="btn btn-danger required-section reolution-delete-btn">Delete</button>
        </div>
        <br>
    </div>
    `);
    addResolutionDetailsValidation();
}

$("#resolution-files").on("click", ".reolution-delete-btn", function () {
    $(this).parent().parent().remove();
});

//memmber file js
$("#excel_table").hide();

function restExcelFile() {
    $("#member_file").val("");
    $("#excel_table").hide();
    $(".required-section").attr("disabled", "");
    $("#member_file_div").show();
    $("#member-verify-btn, #member_file").removeAttr("disabled", "");
    $("#member_file_label").text("");
    $("#excel_table").DataTable().destroy();
    $("#action-column, #add-member-section").remove();
}

function fileChange(element) {
    var target = $(element)[0];
    console.log();
    if (element.hasAttribute("data-id")) {
        id = element.getAttribute("data-id");
        // $("#resolution_details_id-" + id).val(0);
        element.removeAttribute("data-id");
        addResolutionDetailsValidation();
    }

    if (target.files.length > 0) {
        var selectedFile = target.files[0];

        if (selectedFile.name) {
            $(element).next().text(selectedFile.name);
        }
    } else {
        $(element).next().text("");
    }
}
function uploadFile() {
    if ($("#resolution-form").valid()) {
        var formData = new FormData();
        formData.append("member_file", $("#member_file")[0].files[0]);
        formData.append("_token", csrf_token);
        $.ajax({
            url: memberFileValidationRoute, // Specify the route where you handle the file upload in your Laravel controller
            method: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                $(".required-section").attr("disabled", "");
                if (response["member_file"]) {
                    $("#excel_table").hide();
                    $("#member_file").val("");
                    createMessage(response["member_file"][0], "error");
                } else if (response["errors"]) {
                    var sheetErrors = response["errors"];
                    var arrData = response["data"];
                    if (arrData.length) {
                        console.log(sheetErrors);
                        addRecordToTable(arrData, sheetErrors);
                        $("#excel_table").show();
                    }
                } else {
                    var arrData = response["data"];
                    if (arrData.length) {
                        addRecordToTable(arrData);
                        $("#excel_table").show();
                        $(".required-section").removeAttr("disabled");
                        $("#excel_table").DataTable({
                            responsive: true,
                            lengthChange: false,
                            autoWidth: false,
                        });
                    } else {
                        $("#excel_table").hide();
                        createMessage(
                            "Please ensure that you add at least one record to the sheet!",
                            "error"
                        );
                    }
                }
            },
            error: function (error) {
                $("#excel_table").hide();
                createMessage(
                    "Please ensure that you add at least one record to the sheet!",
                    "error"
                );
            },
        });
    }
}

const fieldPostionArr = {
    name: 2,
    share: 3,
    email: 4,
    phone: 5,
};

function addRecordToTable(arrData, errors = []) {
    $("#excel_body, #table_footer").html("");
    var dataIndex = 1;
    var shareTotal = 0;
    arrData.forEach(function (value) {
        $("#excel_body").append(`
        <tr>
            <td>${dataIndex}</td>
            <td>${value["name"]}</td>
            <td>${value["share"]}</td>
            <td>${value["email"]}</td>
            <td>${value["phone"]}</td>
        </tr>
        `);
        dataIndex++;
        shareTotal += value["share"];
    });
    $("#table_footer").append(`
    <tr>
        <td colspan="2" class="text-center">Grand Total</td>
        <td>${shareTotal}</td>
        <td></td>
        <td></td>
    </tr>
    `);
    for (const key in errors) {
        var field = key.split(".");
        console.log(field);
        $(
            "tbody#excel_body tr:nth-child(" +
                (parseInt(field[0]) + 1) +
                ") td:nth-child(" +
                fieldPostionArr[field[1]] +
                ")"
        ).addClass("invalid-value");
    }
    $("#member_file_div").hide();
    $("#member-verify-btn").attr("disabled", "");
    addResolutionDetailsValidation();
}

function remove_member(member_id, share) {
    var token = $("meta[name='csrf-token']").attr("content");

    Swal.fire({
        title: "Are you sure?",
        text: "You want to delete this resolution",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, delete it!",
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                type: "POST",
                url: memberDeleteRoute,
                data: { _token: csrf_token, id: member_id },
                success: function (res) {
                    if (
                        res !=
                        "At least one member is required so you can't delete it."
                    ) {
                        table.ajax.reload(null, false);
                    }
                    createMessage(res);
                },
                beforeSend: function () {},
            });
        }
    });
}

function openMemberFormModal(form_type, url = "") {
    $("#member-form").validate().resetForm();
    var targetForm = $("#member-form");
    if (form_type == "add") {
        $("#form-title").text("Add Voter");
        targetForm.attr("type", "add");
        $(
            "#member-id, #member-name, #member-share, #member-email, #member-phone"
        ).val("");
        $("#add-member-modal").modal();
    } else {
        $("#form-title").text("Edit Voter");
        targetForm.attr("type", "edit");

        $.ajax({
            type: "GET",
            url: url,
            success: function (res) {
                if (typeof res["member"] !== "undefined") {
                    var memberDetails = res["member"];
                    $("#member-id").val(memberDetails["id"]);
                    $("#member-name").val(memberDetails["name"]);
                    $("#member-share").val(memberDetails["share"]);
                    $("#member-email").val(memberDetails["email"]);
                    $("#member-phone").val(memberDetails["phone"]);
                    $("#add-member-modal").modal();
                } else {
                    createMessage(
                        "Something went wrong please try again !",
                        "error"
                    );
                }
            },
        });
    }
}

function memberFormSubmit() {
    if ($("#member-form").valid()) {
        formType = $("#member-form").attr("type");
        if (formType == "add") {
            url = $("#member-form").attr("action");
        } else {
            url = memberUpdateRoute;
        }
        member_id = $("#member-id").val();
        member_name = $("#member-name").val();
        member_share = $("#member-share").val();
        member_email = $("#member-email").val();
        member_phone = $("#member-phone").val();
        resolution_id = $("#resolution-id").val();

        $.ajax({
            type: "POST",
            url: url,
            data: {
                _token: csrf_token,
                member_id,
                resolution_id,
                member_name,
                member_share,
                member_email,
                member_phone,
            },
            success: function (res) {
                if (typeof res["success"] !== "undefined") {
                    createMessage(res["success"]);
                    table.ajax.reload(null, false);
                } else {
                    createMessage(res["error"], "error");
                }
                $("#add-member-modal").modal("hide");
            },
        });
    }
}
