window.addEvent('domready', function() { // wait for the content
	
	if ($('upload-form')) {
		// our uploader instance 
		
		var up = new FancyUpload2($('upload-status'), $('upload-list'), { // options object
			// we console.log infos, remove that in production!!
			verbose: true,
			
			// url is read from the form, so you just have to change one place
			url: $('upload-form').action,
			
			// path to the SWF file
			path: 'lib/modules/drive/js/Swiff.Uploader.swf',
			
			// remove that line to select all files, or edit it, add more items
			typeFilter: {
				'Images (*.jpg, *.jpeg, *.gif, *.png)': '*.jpg; *.jpeg; *.gif; *.png'
			},
			
			// this is our browse button, *target* is overlayed with the Flash movie
			target: 'upload-browse',
			
			// graceful degradation, onLoad is only called if all went well with Flash
			onLoad: function() {
				$('upload-status').removeClass('hide'); // we show the actual UI
				
				// We relay the interactions with the overlayed flash to the link
				this.target.addEvents({
					click: function() {
						return false;
					},
					mouseenter: function() {
						this.addClass('hover');
					},
					mouseleave: function() {
						this.removeClass('hover');
						this.blur();
					},
					mousedown: function() {
						this.focus();
					}
				});

				// Interactions for the 2 other buttons
				
				$('upload-clear').addEvent('click', function() {
					up.remove(); // remove all files
					return false;
				});

				$('upload-upload').addEvent('click', function() {
					up.start(); // start upload
					return false;
				});
			},
			
			// Edit the following lines, it is your custom event handling
			
			/**
			 * Is called when files were not added, "files" is an array of invalid File classes.
			 * 
			 * This example creates a list of error elements directly in the file list, which
			 * hide on click.
			 */ 
			onSelectFail: function(files) {
				files.each(function(file) {
					new Element('li', {
						'class': 'validation-error',
						html: file.validationErrorMessage || file.validationError,
						title: MooTools.lang.get('FancyUpload', 'removeTitle'),
						events: {
							click: function() {
								this.destroy();
							}
						}
					}).inject(this.list, 'top');
				}, this);
			},
			
			/**
			 * This one was directly in FancyUpload2 before, the event makes it
			 * easier for you, to add your own response handling (you probably want
			 * to send something else than JSON or different items).
			 */
			onFileSuccess: function(file, response) {
				var json = new Hash(JSON.decode(response, true) || {});
				
				if (json.get('status') == '1') {
					file.element.addClass('file-success');
					file.info.set('html', '<strong>Image was uploaded:</strong> ' + json.get('width') + ' x ' + json.get('height') + 'px, <em>' + json.get('mime') + '</em>)');
				} else {
					file.element.addClass('file-failed');
					file.info.set('html', '<strong>An error occured:</strong> ' + (json.get('error') ? (json.get('error') + ' #' + json.get('code')) : response));
				}
			},
			
			/**
			 * onFail is called when the Flash movie got bashed by some browser plugin
			 * like Adblock or Flashblock.
			 */
			onFail: function(error) {
				switch (error) {
					case 'hidden': // works after enabling the movie and clicking refresh
						alert('To enable the embedded uploader, unblock it in your browser and refresh (see Adblock).');
						break;
					case 'blocked': // This no *full* fail, it works after the user clicks the button
						alert('To enable the embedded uploader, enable the blocked Flash movie (see Flashblock).');
						break;
					case 'empty': // Oh oh, wrong path
						alert('A required file was not found, please be patient and we fix this.');
						break;
					case 'flash': // no flash 9+ :(
						alert('To enable the embedded uploader, install the latest Adobe Flash plugin.')
				}
			}
			
		});
	}
});