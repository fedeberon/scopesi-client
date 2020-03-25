<?
session_start();
if(!isset($_SESSION['userName']))
	exit();
?>

<div id="overlay">
	<form id="mapCampannasItemPopForm" name="mapCampannasItemPopForm" method="post">
		<table id="tableOver">
			<tbody>
				<tr>
					<td>Descripci&oacute;n</td>
					<td><input id="descripcion" name="descripcion" class="loginput" type="text"></td>
				</tr>
				<tr>
					<td>Detalle</td>
					<td><textarea class="textArea" id="detalle" name="detalle"></textarea></td>
				</tr>
			</tbody>
		</table>
		<a id="btnSalirCampannasItem" class="btnAcc btnAccB">Cerrar</a>
		<a id="btnGuardarCampannasItem" class="btnAcc btnAccB">Guardar</a>
	</form>
</div>

<script>
	$(document).ready(function() {
		$_initCampannasItemPop();
	});
</script>