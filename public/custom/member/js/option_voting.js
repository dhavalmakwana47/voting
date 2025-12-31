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
        $(this).show();
        $(this).next().show();
        if ($(this).next().next().is("img")) {
            $(this).next().next().show();
        }
    });
});

$("#clear-all").on("click", function () {
    $("input[type=radio]").prop("checked", false);
    $("input[name=evsnidischecked]").val("N");
    voteCount();
});

$("#submitForm").on("click", function () {
    $(this).attr("disabled", true);
    $("#voting_form").submit();
});
