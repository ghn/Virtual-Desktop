window.addEvent("domready", function() {
	
	// login set focus if exists
	if ($('vd_auth_login') != null) {
		$('vd_auth_login').focus();
	}
	
	// tool menu (show / hide)
	if ($('tools') != null) {
		var toolsEvent = $$('#tools')[0];
		var toolsList = $$('#tools nav')[0];
		
		toolsList.setStyle('display', 'none');
		
		toolsEvent.addEvent('mouseover', function(elem) {
			toolsList.setStyle('display', 'block');
		});
		
		toolsEvent.addEvent('mouseleave', function(elem) {
			toolsList.setStyle('display', 'none');
		});
	}
});