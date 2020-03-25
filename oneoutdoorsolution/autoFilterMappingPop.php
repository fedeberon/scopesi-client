<?php
if (session_status() == PHP_SESSION_NONE) {
		session_start();
}
if(!isset($_SESSION['userName']))
	exit();
?>

<div id="overlay">
	<form id="autoFiltroPopForm" name="autoFiltroPopForm" method="post">
		<table id="dt_autofiltro" class="display">
			<thead>
				<tr>
					<th id="selectAllHeader"></th>
					<th>Descripci&oacute;n</th>
				</tr>
		    </thead>
		    <tbody>
		    	<tr>
		    		<td colspan="2" class="dataTables_empty">Cargando Datos...</td>
		    	</tr>
			</tbody>
		</table>
		<div id="polyElementosCircle" style="display: none;">
			<table>
				<tr>
					<td><input type="checkbox" id="circleElementos" name="circleElementos"></td>
					<td>Genera area de Influencia en Elementos</td>
					<td>Mts. Radio</td>
					<td><input type="text" id="mtsRadioCircleElementos" name="mtsRadioCircleElementos" style="width: 80px"></td>
				</tr>
			</table>
		</div>
		<div id="polyEVPCircle" style="display: none;">
			<table>
				<tr>
					<td><input type="checkbox" id="circleEVP" name="circleEVP"></td>
					<td>Genera area de Influencia en EVP</td>
					<td>Mts. Radio</td>
					<td><input type="text" id="mtsRadioCircleEVP" name="mtsRadioCircleEVP" style="width: 80px"></td>
				</tr>
			</table>
		</div>
		<a id="btnAutoFiltroSalir" class="btnAcc btnAccB">Cerrar</a>
	</form>
</div>

<script>
	$(document).ready(function() {
		$_initAutoFiltroPop();
	});
</script>
