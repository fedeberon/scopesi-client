/*
 * Created by Federico Pisarello - SisDev Software (c) 2012 - fpisarello@sisdevsoft.com
 */

var actionForm = 'auditoriaAction.php';

function $_initFotosAuditoria()
{	
	$('#galleria').galleria({
		extend: function() {
            $('#fullscreen').click(this.proxy(function(e) {
                e.preventDefault();
                this.enterFullscreen();
            }));
        }
	});
	Galleria.ready(function() {
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

	var paramData = "actionOfForm=fotosAuditoriaShow&idCircuito=" + $('#idCircuito').val() + "&orden=" + $('#orden').val();
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