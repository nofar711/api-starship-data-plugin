$(document).ready(function () {
	// badges loop
	var numberOfImages = 8;
	for (var i = 0; i < numberOfImages; i++) {
		$('.badges').append('<img src="badges/box-' + i + '.svg"/>');
	}

	var width = $(window).width();
	var size = getSize(width);

	$(window).resize(function () {
		width = $(window).width();
		size = getSize(width);
    $('.bubbels-left').attr('src', 'bg-images/' +size + '-left.png');
    $('.bubbels-right').attr('src', 'bg-images/' +size + '-right.png');
	});

	function getSize(width) {
		if (width >= 1920) {
			return 'xl';
		} else if ((width < 1919) & (width >= 1023)) {
			return 'lg';
		} else if ((width < 1024) & (width >= 768)) {
			return 'md';
		} else if (width < 767) {
			return 'sm';
		}
	}

});


