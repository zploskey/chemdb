$(document).ready(function() {
    $(".sample_name").autocomplete("index.php/samples/search_names", {
        selectFirst: false,
        width: 260,
        scroll: true 
    });
});