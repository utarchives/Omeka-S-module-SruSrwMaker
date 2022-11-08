$(document).ready( function() {
    $("#rdf-xml-download").click(function(){
        window.location.href = $(this).data('url');
    });
});