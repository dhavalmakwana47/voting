function continueForModify() {
    var checked = true;
    var voteCount = 0;
    $("input[name=evsnidischecked]").each(function () {
        if ($(this).val() != "Y") {
            checked = false;
        } else {
            voteCount++;
        }
    });

    $("#totalVotingCount").text(voteCount);
    if (checked) {
        $("#continuedivId ").hide();
        $(".choicenav").hide();
        $(".rest_section").hide();
        $("#backdivId").show();
        $(".voting_input").each(function () {
            if (!$(this).is(":checked")) {
                $(this).hide();
                $(this).next().hide();
            }
        });
    } else {
        createMessage("Some voting options are still empty. Please check them.","error")
    }
}
function voteCount() {
    var voteCount = 0;
    $("input[name=evsnidischecked]").each(function () {
        if ($(this).val() == "Y") {
            voteCount++;
        }
    });

    $("#totalVotingCount").text(voteCount);
}

function resetButton(id) {
    $("input[type='radio'].resolution_choice" + id).prop("checked", false);
    $("#evsnischecked_" + id).val("N");
    voteCount();
}

function totalVotingCountallYesOrNo(value) {
    if (value == "yes") {
        $("input.selectyes").prop("checked", true);
    } else if (value == "no") {
        $("input.selectno").prop("checked", true);
    } else {
        $("input.selectabstain").prop("checked", true);
    }
    $('.evsnidischecked').val("Y")
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

    $(".voting_input").each(function () {
        $(this).show();
        $(this).next().show();
    });
});

$("#clear-all").on("click", function () {
    $("input[type=radio]").prop("checked", false);
    $("input[name=evsnidischecked]").val("N");
    voteCount();
});

$('#submitForm').on('click', function(){
    $(this).attr('disabled', true);
    $('#voting_form').submit()
});