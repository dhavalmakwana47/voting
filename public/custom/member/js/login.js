let loginType = 1;

function changeLoginType(type) {
    loginType = type;
    if (type == 2) {
        $(".useremail_div").show();
        $(".user_div, .userphone_div").hide();
        $("#verifyBtn").text("Get OTP");
    } else if (type == 3) {
        $(".userphone_div").show();
        $(".user_div, .useremail_div").hide();
        $("#verifyBtn").text("Get OTP");
    } else {
        $(".userphone_div, .useremail_div").hide();
        $(".user_div").show();
        $("#verifyBtn").text("Continue");
    }
}

function validateValue() {
    if (loginType == 2) {
        inputValue = $("#email").val();
    } else if (loginType == 3) {
        inputValue = $("#phone").val();
    } else {
        inputValue = $("#user_id").val();
    }
    return inputValue;
}

$("#verifyBtn").on("click", function () {
    var userInput = document.getElementById("captchaInput").value;
    var captchaNumber = document.getElementById("captchaNumber").textContent;

    if (userInput !== captchaNumber) {
        createMessage("CAPTCHA verification failed. Please try again.","error")
        event.preventDefault(); // Prevent the form from being submitted
        generateCaptcha(); // Generate a new CAPTCHA
    } else {
        // Continue with form submission or other actions
        validateInput = validateValue();
        $.ajax({
            type: "post",
            url: validateRoute,
            data: {
                _token: csrfToken,
                login_type: loginType,
                input: validateInput,
            },
            success: function (res) {
                console.log(res);
                if (res["count"] > 0) {
                    $("#verifyBtnDiv, #login-type-div").hide();
                    $("#submitBtnDiv").show();
                    if (loginType == 2) {
                        $("#email").attr("readonly", true);
                        $(".otp_div").show();
                    } else if (loginType == 3) {
                        $("#phone").attr("readonly", true);
                        $(".otp_div").show();
                    } else {
                        $("#user_id").attr("readonly", true);
                        $(".password_div").show();
                    }
                } else {
                    createMessage(res["error"],"error")
                }
            },
        });
    }
});

$("#resend-otp").on("click", function () {
    validateInput = validateValue();
    $.ajax({
        type: "post",
        url: otpResendRoute,
        data: {
            _token: csrfToken,
            login_type: loginType,
            input: validateInput,
        },
        success: function (res) {
            console.log(res["otp"]);
        },
    });
});

$("#signBtn").on("click", function () {
    validateInput = validateValue();
    var otp = $("#otp").val();
    var password = $("#password").val();
    if (loginType == 1) {
        data = {
            _token: csrfToken,
            login_type: loginType,
            user_id: validateInput,
            password: password,
        };
    } else if (loginType == 2) {
        data = {
            _token: csrfToken,
            login_type: loginType,
            email: validateInput,
            otp: otp,
        };
    } else {
        data = {
            _token: csrfToken,
            login_type: loginType,
            phone: validateInput,
            otp: otp,
        };
    }

    $.ajax({
        type: "post",
        url: loginRoute,
        data: data,
        success: function (res) {
            if (res["error"] !== undefined) {
                createMessage(res["error"],"error")
            } else {
                $("#member-login-form").submit();
            }
        },
        error: function (error) {
            if (error.responseJSON.errors['otp'] != undefined) {
                createMessage(error.responseJSON.errors['otp'],"error")
            }else if (error.responseJSON.errors['password'] != undefined) {
                createMessage(error.responseJSON.errors['password'],"error")
            }else if (error.responseJSON.errors['email'] != undefined) {
                createMessage(error.responseJSON.errors['password'],"error")
            }else{
                createMessage("Something went wrong please try again.","error")
            }

        }
    });
});
