/*
 * Created by Federico Pisarello - SisDev Software (c) 2012 - fpisarello@sisdevsoft.com
 */

function focus_login() {
	var usuario = $('#txtUser').get(0);
	var pass = $('#txtPass').get(0);
  
	if (usuario && pass) {
		if (usuario.value != "" && pass.value == "") {
			pass.focus();
		} else if (usuario.value == "") {
			usuario.focus();
		}
	}
}

/** validador del formulario **/
function $_validaCamposMandatoriosForm()
{
	var res = true; // siempre optimistas! :)

	var usuario = $('#txtUser');
	var pass = $('#txtPass');
	
	if(usuario.val() === ''){ // el usuario es necesario
		usuario.effect("highlight", {color:'#F00'}, 1000);
	  	
	  	// le pongo el foco
	  	usuario.get(0).focus();
	  	
	  	// hay error
	  	res = false;
	}

	if(pass.val() === ''){ // la clave es necesaria
		pass.effect("highlight", {color:'#F00'}, 1000);
		
		if(res)
			pass.get(0).focus();
		
		// hay error
		res = false;
	}
	
	return res;
}