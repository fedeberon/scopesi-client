<?
session_start();
if(!isset($_SESSION['userName']))
	exit();
?>

<div id="overlay">
	<form id="mapCampannasFilePopForm" name="mapCampannasFilePopForm" method="post">
		<table id="tableOver">
			<tbody>
				<tr>
					<td>Crear Nueva Campa&ntilde;a</td>
					<td><input id="descripcionNewCampanna" name="descripcionNewCampanna" class="loginput" type="text"></td>
				</tr>
				<tr>
					<td>Guardar en Campa&ntilde;a</td>
					<td><select id="cmbCampannasGuardadas" name="cmbCampannasGuardadas" class="loginput"></select></td>
				</tr>
				<tr>
					<td>Nombre Circuito</td>
					<td><input id="archivoCampanna" name="archivoCampanna" class="loginput" type="text"></td>
				</tr>
			</tbody>
		</table>
		
		<a id="btnSalirCampannasFile" class="btnAcc btnAccB">Cerrar</a>
		<a id="btnGuardarCampannasFile" class="btnAcc btnAccB">Guardar</a>
		
	</form>
</div>

<script>
	$(document).ready(function() {
		$_initCampannasFilePop();
	});
</script>