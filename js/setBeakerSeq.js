$(document).ready(function() {

    /*
     * Set the sequence of beaker select inputs in order starting from the
     * first selection.
     */
    $("#setBkrSeq").click(function() {
        $(".bkr_select").each(function(i) {
            if (i == 0) return;
            var startId = $("select.bkr_select").val();
            var curBkr = $(this).find('option[value=' + startId + ']');
            for (j = 0; j < i; j++) {
                curBkr = curBkr.next();
            }
            curBkr.attr("selected", true);
        });
    });

});

