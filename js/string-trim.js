
$(function() {
    $(".item-details").text(function() {
        return $(this).text().trim().length > 200 ? $(this).text().trim().substr(0, 200)+'...' : $(this).text().trim();
    });
});