function continueForModify() {
    let allGroupsChecked = true;
    $(".comment_section").attr("readonly", true);

    for (const [id, optionType] of Object.entries(resDetailsArr)) {
        let inputName =
            optionType === "checkbox"
                ? `resolution_choice[${id}][]`
                : `resolution_choice[${id}]`;

        let inputs = document.querySelectorAll(`input[name="${inputName}"]`);
        let groupChecked = false;
        let isSkipped = false;

        // Check if this group is skipped
        for (const input of inputs) {
            if (input.dataset.skip === '1') {
                isSkipped = true;
                break;
            }
        }

        // Skip validation for optional items
        if (isSkipped) {
            continue;
        }

        for (const input of inputs) {
            if (input.checked) {
                groupChecked = true;
                break;
            }
        }

        if (!groupChecked) {
            allGroupsChecked = false;
            break;
        }
    }

    if (allGroupsChecked) {
        let valid = true;

        // Go through each unique group name
        let groupNames = new Set();
        $('input[type="checkbox"]').each(function () {
            groupNames.add($(this).attr("name"));
        });

        groupNames.forEach(function (name) {
            let groupCheckboxes = $('input[name="' + name + '"]');
            let min = parseInt(groupCheckboxes.first().data("min")) || 0;
            let max =
                parseInt(groupCheckboxes.first().data("max")) ||
                groupCheckboxes.length;
            let checkedCount = groupCheckboxes.filter(":checked").length;

            // Get the checkbox group container
            let groupIdMatch = name.match(/\[(\d+)\]/);
            let groupId = groupIdMatch ? groupIdMatch[1] : null;
            let groupContainer = $("#checkbox-group-" + groupId);

            // Remove old error state
            groupContainer.removeClass("checkbox-error");

            if (checkedCount < min || checkedCount > max) {
                valid = false;
                groupContainer.addClass("checkbox-error");
            }
        });

        if (valid) {
            $("#continuedivId ").hide();
            $(".choicenav").hide();
            $(".rest_section").hide();
            $("#backdivId").show();
            $(".voting_input").each(function () {
                if (!$(this).is(":checked")) {
                    $(this).hide();
                    $(this).next().hide();
                    $(this).next().next().hide();
                } else {
                    $(this).attr("onclick", "return false;");
                }
            });
        }
    } else {
        createMessage(
            "Some voting options are still empty. Please check them.",
            "error"
        );
    }
}
$('input[type="checkbox"]').on('change', function () {
    let groupName = $(this).attr('name'); // e.g., resolution_choice[5][]
    let groupCheckboxes = $('input[name="' + groupName + '"]');

    // Get min/max from any checkbox in the group
    let min = parseInt(groupCheckboxes.first().data('min')) || 0;
    let max = parseInt(groupCheckboxes.first().data('max')) || groupCheckboxes.length;

    let checkedCount = groupCheckboxes.filter(':checked').length;

    // Extract group ID from name and find container
    let groupIdMatch = groupName.match(/\[(\d+)\]/);
    let groupId = groupIdMatch ? groupIdMatch[1] : null;
    let groupContainer = $('#checkbox-group-' + groupId);
    
    // Remove existing error highlight
    groupContainer.removeClass('checkbox-error');

    if (checkedCount > max) {
        $(this).prop('checked', false); // Undo this check
        groupContainer.addClass('checkbox-error');
        createMessage(
           "You can select a maximum of " + max + " options.",
            "error"
        );
    } else if (checkedCount < min) {
        groupContainer.addClass('checkbox-error');
    } else {
        // Valid range: remove any highlight
        groupContainer.removeClass('checkbox-error');
    }
});

// function voteCount() {
//     var voteCount = 0;
//     $("input[name=evsnidischecked]").each(function () {
//         if ($(this).val() == "Y") {
//             voteCount++;
//         }
//     });

//     $("#totalVotingCount").text(voteCount);
// }

function resetButton(id) {
    $(".voting_input_" + id).prop("checked", false);
    // voteCount();
}

function totalVotingCountallYesOrNo(value) {
    if (value == "yes") {
        $("input.selectyes").prop("checked", true);
    } else if (value == "no") {
        $("input.selectno").prop("checked", true);
    } else {
        $("input.selectabstain").prop("checked", true);
    }
    $(".evsnidischecked").val("Y");
    voteCount();
}

function selectAllYesNo(id) {
    $("#evsnischecked_" + id).val("Y");
    voteCount();
}

$("#backId").on("click", function () {
    $("#continuedivId ").show();
    $("#backdivId").hide();
    $(".choicenav").show();
    $(".rest_section").show();
    $(".comment_section").removeAttr("readonly");
    $(".voting_input").each(function () {
        $(this).removeAttr("onclick");
        $(this).removeAttr("disabled");
        $(this).show();
        $(this).next().show();
    });
});

$("#clear-all").on("click", function () {
    $("input[type=radio]").prop("checked", false);
    $("input[name=evsnidischecked]").val("N");
    voteCount();
});

$("#submitForm").on("click", function () {
    // Check if this button already has a custom handler
    if ($(this).data('custom-handler')) {
        return; // Don't add default handler if custom one exists
    }
    $(this).attr("disabled", true);
    $("#voting_form").submit();
});

$("#verify-otp").on("click", function() {
    const otp = $('#otp-input').val();
    
    if (!otp) {
        $('#otp-error').text('Please enter OTP').show();
        return;
    }
    
    const memberId = $("input[name='member_id']").val();
    
    $.ajax({
        url: '/member/verify-voting-otp',
        method: 'POST',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
            member_id: memberId,
            otp: otp
        },
        success: function(response) {
            if (response.success) {
                $('#otp-modal').modal('hide');
                $('#submitForm').attr('disabled', true);
                $('#voting_form').submit();
            } else {
                $('#otp-error').text(response.message || 'Invalid OTP').show();
            }
        },
        error: function() {
            $('#otp-error').text('Error verifying OTP').show();
        }
    });
});

// Clear OTP error when modal is closed
$('#otp-modal').on('hidden.bs.modal', function () {
    $('#otp-input').val('');
    $('#otp-error').hide();
});
