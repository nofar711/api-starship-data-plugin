$(document).ready(function () {

  // badges loop
  var numberOfImages = 8;
  for (var i = 0; i < numberOfImages; i++) {
    $('.badges').append('<img src="badges/box-' + i + '.svg"/>');
  }

});