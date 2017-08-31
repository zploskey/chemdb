$(document).ready(function() {
    // When one of the submit buttons is clicked, add a hidden hash field
    // that can be used to redirect the user back to this button.
    $(".ancBtn").click(function() {
        var id = $(this).attr("id");
        $(this).after("<input type=hidden name=hash value="+id+">");
    });

    // If any sample type is set to "BLANK" we make its sample_wt
    // to its tare wt and disable the sample weight input box.
    // Likewise, if it is set to "SAMPLE" then the sample weight
    // input box is made editable again.
    $(".type").change(function() {
        // get the sample index
        var i = $(".type").index(this);
        var sampleWt = $(".sampleWt").eq(i);
        if ($(this).val() == "BLANK") {
            weight = $(".tareWt").eq(i).val();
            sampleWt.val(weight);
            sampleWt.attr("disabled", true);
            sampleWt.after("<input type=hidden value=" + weight + " name=wt_diss_bottle_sample[] id=hidWt" + i + ">");
        } else {
            sampleWt.attr("disabled", false);
            $("#hidWt" + i).remove();
        }
    }).change();

    // Whenever the a blank tare weight is updated
    // update the corresponding sample weight to be the same.
    $(".tareWt").bind("change keyup", function() {
        var i = $(".tareWt").index(this);
        var sampleWt = $(".sampleWt").eq(i);
        if (sampleWt.attr("disabled")) {
            sampleWt.val($(this).val());
            $("#hidWt" + i).val($(this).val());
        }
    }).change();

    // Scroll the window down to the last-pressed button.
    if (clickedButtonId != "") {
        // Get the offset down the page of the pressed button.
        var btnOffset = $("\#" + clickedButtonId).offset().top;
        // Scroll the window so that the previously pressed button is
        // near the bottom of the screen.
        window.scrollTo(0, btnOffset - window.innerHeight * 4 / 5);
    }

});

