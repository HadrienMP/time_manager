$(function() {
	$('th a').click(function() {
		$('th a.active').removeClass('active');
		$(this).addClass('active');
		$('tbody.period').hide();
		$('tbody.period').eq($(this).index()).show();
	});
	
	$('tbody.day, tbody.week').hide();
});