$(function() {
	$('.checks').hide();
	$('.checks').last().show();
    
    $('a#previous').click(function() {
        if ($('.checks').index($('.checks:visible')) != 0) {
            $('.checks:visible').hide().prev().show();
        }
        return false;
    });
    $('a#next').click(function() {
        if ($('.checks').index($('.checks:visible')) != $('.checks').length -1) {
            $('.checks:visible').hide().next().show();
        }
        return false;
    });
});