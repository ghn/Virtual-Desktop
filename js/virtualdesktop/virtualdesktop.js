window.addEvent("domready", function() {
	
	$('vd_auth_login').focus();
	
	// login set focus if exists
	if ($chk('vd_auth_login')) {
		$('vd_auth_login').focus();
	}
	
	// tool menu (show / hide)
	if ($chk('div#tools div.box-content')[0]) {
		var toolsEvent = $$('div#tools div.box')[0];
		var tools = $$('div#tools div.box-content')[0];
		
		tools.setStyle('display', 'none');
		
		toolsEvent.addEvent('mouseover', function(elem) {
			tools.setStyle('display', 'block');
		});
		
		toolsEvent.addEvent('mouseleave', function(elem) {
			tools.setStyle('display', 'none');
		});
	}
});