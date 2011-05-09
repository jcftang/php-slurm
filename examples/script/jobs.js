function closeMe($link) {
	$selector = '#' + $link;
	$($selector).animate({
		opacity: 0.0,
		height: 'toggle',
		padding: '0px',
		margin: '0px'
	}, 500, function() {
		$($selector).remove();	
	});
}
