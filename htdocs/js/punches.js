$(function() {
	$('.checks').hide();
	$('.checks').last().show();
    
    $('a#previous').click(function() {
        $('.checks:visible').hide().prev().show();
        return false;
    });
    $('a#next').click(function() {
        $('.checks:visible').hide().next().show();
        return false;
    });
});