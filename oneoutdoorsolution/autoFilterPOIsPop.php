<?
session_start();
if(!isset($_SESSION['userName']))
	exit();
?>

<div id="overlay">
	<form id="autoFiltroPopForm" name="autoFiltroPopForm" method="post">
		<table id="dt_autofiltro" class="display">
			<thead>
				<tr>
					<th></th>
					<th>Id</th>
					<th>Descripci&oacute;n</th>
					<th width="4%"></th>
				</tr>
		    </thead>
		    <tbody>
		    	<tr>
		    		<td colspan="3" class="dataTables_empty">Cargando Datos...</td>
		    	</tr>
			</tbody>
		</table>
		<div id="polyCircle">
			<table>
				<tr>
					<td><input type="checkbox" id="circlePois" name="circlePois"></td>
					<td>Genera area de Influencia en POIS</td>
					<td>Mts. Radio</td>
					<td><input type="text" id="mtsRadioCircle" name="mtsRadioCircle" style="width: 80px"></td>
				</tr>
			</table>
		</div>
		<a id="btnAutoFiltroPOIsSalir" class="btnAcc btnAccB">Cerrar</a>
	</form>
</div>

<script>
	$(document).ready(function() {
		$_initAutoFiltroPOIsPop();
	});
</script>