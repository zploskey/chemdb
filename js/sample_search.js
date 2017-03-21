$(document).ready(function() {
    $(".sample_name").autocomplete({
        source: "index.php/samples/search_names",
        minLength: 2,
    });
});
