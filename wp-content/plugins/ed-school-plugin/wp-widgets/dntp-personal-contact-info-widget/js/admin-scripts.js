jQuery(document).ready(function($){

	var media_frame;
	var $accordion = $('.jsjr-pci-accordion');

	$('body').on('click', '.upload-button', function( event ){

		var $this = $(this);

		event.preventDefault();

		if ( media_frame ) {
			media_frame.open();
			return;
		}

		media_frame = wp.media.frames.media_frame = wp.media({
			title: 'Upload an Image, Or Select One From the Library',
			frame: 'select',
			button: {
				text: 'Select Image',
			},
            library: {
                type: 'image'
            },
			multiple: false
		});
		
		media_frame.on( 'select', function() {
			attachment = media_frame.state().get('selection').first().toJSON();
			$('#' + $this.attr('rel')).val( attachment.url );
		});

		media_frame.open();
	});
	
	$('body').on('click', '.jsjr-pci-toggle', function( event ){
		$(this).toggleClass('down');
		$(this).next().slideToggle();
	});
	
	$('body').on( 'mouseover', '.jsjr-pci-question', function( event ){
		if ($(this).tooltip() == null ) {
			$(this).tooltip();
		}
		$(this).tooltip( 'open');
	});

	$accordion.sortable({
		items: '.jsjr-pci-accordion-item',
	  	stop: function( event, ui ) {

	  		var res = '';
	  		var $parent = ui.item.parent();
	  		$parent.children('.jsjr-pci-accordion-item').each(function () {
	  			var $this = $(this);

	  			res += $this.data('id') + '|';
	  		});
	  		$parent.siblings('.item-order').val(res);
	  			
	  	}
	});
	
});