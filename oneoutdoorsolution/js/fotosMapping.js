/*
 * Created by Federico Pisarello - SisDev Software (c) 2012 - fpisarello@sisdevsoft.com
 */

var actionForm = 'mappingAction.php';

function $_initFotosMapping()
{
	$('#galleria').galleria({
		height: 400,
		extend: function() {
            // $('#fullscreen').click(this.proxy(function(e) {
						// 		console.log('hi');
            //     e.preventDefault();
            //     this.enterFullscreen();
            // }));
        }
	});

	Galleria.ready(function() {

		var gallery = this; // galleria is ready and the gallery is assigned
			$('#fullscreen').click(function() {
				gallery.toggleFullscreen(); // toggles the fullscreen
			});

	    this.bind('thumbnail', function(e) {
	    	var desc; //Descripcion del Tooltip
	    	desc = $(e.thumbTarget).attr('src');
	    	desc = desc.substring(desc.lastIndexOf("/")+1);
	    	if (desc) {
	    		this.bindTooltip( e.thumbTarget, desc );
	    	}
	    });
	});

	Galleria.run('#galleria');

	var paramData = "actionOfForm=fotosMappingShow&idUbicacion=" + $('#idUbicacion').val();
	$.ajax({
		    type: 'POST',
		    url: actionForm,
		    data: paramData,
			dataType: 'json',
		    success: function(jsonObj)
		                        	{
		    							$('#galleria').data('galleria').load(jsonObj);
		                        	}
    });
}
