$(document).ready(function() {
    var options = '<select name="proj[]">'+defaultSelect+"</select><br/>\n";
    $("#add_select").click(function(event) {
        event.preventDefault();
        $(options).insertBefore($(this));
    });
});
