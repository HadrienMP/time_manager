$(function() {
	$('.day').hide();
	$('.day').last().show();
    
    $('a#previous').click(function() {
        if ($('.day').index($('.day:visible')) != 0) {
            $('.day:visible').hide().prev().show();
        }
        return false;
    });
    $('a#next').click(function() {
        if ($('.day').index($('.day:visible')) != $('.day').length -1) {
            $('.day:visible').hide().next().show();
        }
        return false;
    });
});