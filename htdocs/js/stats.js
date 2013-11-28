$(function() {
	$('th a').click(function() {
		$('th a.active').removeClass('active');
		$(this).addClass('active');
	});
});