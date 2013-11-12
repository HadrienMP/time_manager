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
    
    $('a.button.add').click(function() {
        this_tr = $(this).closest('tr');
        new_tr = this_tr.prev().clone();
        new_tr.find('input.minute').val( parseInt(new_tr.find('input.minute').val()) + 1);
        
        this_tr.before(new_tr);
        return false;
    });
});