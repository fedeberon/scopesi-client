/*
 * Created by Federico Pisarello - SisDev Software (c) 2012 - fpisarello@sisdevsoft.com
 */

var actionForm = 'inversionAction.php';

function $_initCreatividad()
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


	paramData = "actionOfForm=getRubrosHabCompleto";
	$.ajax({
		    type: 'POST',
		    url: actionForm,
		    data: paramData,
			dataType: 'json',
		    success: function(jsonObj)
		                        	{
								    	$('#cmbRubroCreatividad').append($('<option>').text('...').attr('value', 0));
								        $.each(jsonObj, function(i, value) {
								        	$('#cmbRubroCreatividad').append($('<option>').text(value.descripcion).attr('value', value.idRubros));
								        });
		                        	}
    });
	
	$('#btnFiltroCreatividad').click( function(e) {
		var res = true;
		var enfocar = undefined;
		
		//Valido Previamente los Campos
		if($('#txtMesAnnoCreatividad').val() === null || $('#txtMesAnnoCreatividad').val() === ''){
			res = false;
			
			$('#txtMesAnnoCreatividad').effect("highlight", {color:'#FF0000'}, 1000);
			if(!enfocar)
				enfocar = $('#txtMesAnnoCreatividad').get(0);
		}
		
		if($('#cmbRubroCreatividad').val() === null || $('#cmbRubroCreatividad').val() === '0'){
			res = false;
			
			$('#cmbRubroCreatividad').effect("highlight", {color:'#FF0000'}, 1000);
			if(!enfocar)
				enfocar = $('#cmbRubroCreatividad').get(0);
		}
		if(enfocar)
			enfocar.focus();

		if(res) {
			var paramData = "actionOfForm=creatividadesShow&" + $('#creatividadesPopForm').serialize();
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
	});
	
	$('#btnZip').click( function(e) {
		var res = true;
		var enfocar = undefined;
		
		//Valido Previamente los Campos
		if($('#txtMesAnnoCreatividad').val() === null || $('#txtMesAnnoCreatividad').val() === ''){
			res = false;
			
			$('#txtMesAnnoCreatividad').effect("highlight", {color:'#FF0000'}, 1000);
			if(!enfocar)
				enfocar = $('#txtMesAnnoCreatividad').get(0);
		}
		
		if($('#cmbRubroCreatividad').val() === null || $('#cmbRubroCreatividad').val() === '0'){
			res = false;
			
			$('#cmbRubroCreatividad').effect("highlight", {color:'#FF0000'}, 1000);
			if(!enfocar)
				enfocar = $('#cmbRubroCreatividad').get(0);
		}
		if(enfocar)
			enfocar.focus();

		if(res) {
			var paramData = "actionOfForm=getFotosInversionZip&" + $('#creatividadesPopForm').serialize();
			
			if(!$.browser.msie)
				location.href = actionForm + '?' + paramData;
			else
				window.open(actionForm + '?' + paramData);
		}
	});
}