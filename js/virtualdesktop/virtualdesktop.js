window.addEvent("domready", function() {
	
	var togEvent = $$('div#tools div.box')[0];
	var togable = $$('div#tools div.box-content')[0];
	
	togable.setStyle('display', 'none');
	
	togEvent.addEvent('mouseover', function(elem) {
		togable.setStyle('display', 'block');
	});
	
	togEvent.addEvent('mouseleave', function(elem) {
		togable.setStyle('display', 'none');
	});
});