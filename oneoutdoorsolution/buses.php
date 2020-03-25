<?php
/**
 *  Por Federico Pisarello - SisDev Software (c) 2012 - Buenos Aires, Argentina.
 * 	fpisarello@sisdevsoft.com
 */

require("includes/funciones.inc.php");

session_start();
if(!isset($_SESSION['userName']))
	exit();


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>--[:: SCOPESI.NET ::]--</title>
		<link href='http://fonts.googleapis.com/css?family=Numans' rel='stylesheet' type='text/css' />
		<link rel="stylesheet" type="text/css" href="css/css.css" />
		
		<!-- General Scripts -->
		<script type="text/javascript" src="js/jquery/jquery-1.7.1.min.js"></script>
		<script type="text/javascript" src="js/jquery/jquery-ui-1.8.16.custom.min.js"></script>
		<script type="text/javascript" src="js/jquery/jquery.blockUI-2.53.js"></script>
		<!-- Form Script -->
		<script src="js/datatables/js/jquery.dataTables.js" type="text/javascript" ></script>
		<script type="text/javascript" src="js/utils.js"></script>
	</head>
	<body>
	
		<div id="logoLog">
			<img src="images/logo_oos.png" height="92" width="342" />
		</div>
		
		<div id="cajaLog">
			<table>
				<tr>
					<td>Provincia</td>
					<td>
						<select class="logInput" id="provincia" name="provincia">
							<option value="buenos-aires">Buenos Aires</option>
							<option value="bahia-blanca">Bah&iacute;a Blanca</option>
							<option value="bariloche">Bariloche</option>
							<option value="cordoba">C&oacute;rdoba</option>
							<option value="la-plata">La Plata</option>
							<option value="mar-del-plata">Mar del Plata</option>
							<option value="mendoza">Mendoza</option>
							<option value="puerto-madryn">Puerto Madryn</option>
							<option value="rosario">Rosario</option>
						</select>
					</td>
				</tr>
				<tr>
					<td>Linea</td>
					<td><input class="logInput" type="text" name="nroLinea" id="nroLinea"></input></td>
				</tr>
				<tr>
					<td></td>
					<td>
						<button class="btnA" id="obtenerRecorrido">Obtener Recorrido</button>
						<img src="images/loading.gif" id="loading" style="display: none;" />
					</td>
				</tr>
			</table>
		</div>
		
		<!-- Global Message -->
		<div id="globalMess"> 
			<div class="img"></div>
			<div id="globalMessImgClose" class="imgClose"></div>
			<div id="tituloMess" class="msgTitulo">TITULO</div>
			<p id="textoMess" class="msgTexto">MENSAJE</p>
		</div>

		<script type='text/javascript'>
		$(document).ready(function() {
			$('#obtenerRecorrido').click(function(e){

				var nroLinea = $('#nroLinea').val(); 
				if(nroLinea == "") {
					$_showMessage('ERR', 'ERROR', 'Ingrese un Nro. de Linea V&aacute;lido');
				}
				else if(isNaN(parseFloat(nroLinea)) && !isFinite(nroLinea)) {
					$_showMessage('ERR', 'ERROR', 'Ingrese un Nro. de Linea V&aacute;lido');
				}
				else {

					$('#loading').show();
					$('#obtenerRecorrido').attr('disabled','disabled'); 
					
					$.ajax({
					    type: 'POST',
					    url: 'busesAction.php',
					    data: {actionOfForm: 'setDBBuses', linea: nroLinea, provincia: $('#provincia').val() },
					    dataType: 'json',
					    success: function(jsonObj) {
					    	if(jsonObj.status === 'OK')
                            	$_showMessage('OK', jsonObj.status, jsonObj.msg);
                			else
                				$_showMessage('ERR', jsonObj.status, jsonObj.msg);
					    	$('#loading').hide();
					    	$('#obtenerRecorrido').removeAttr('disabled');
					    }
					});
				}
			});
		});
		</script>
	</body>
</html>