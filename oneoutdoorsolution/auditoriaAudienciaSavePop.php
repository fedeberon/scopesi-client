<?php
session_start();
if(!isset($_SESSION['userName']))
	exit();
?>

<div id="overlay">
	<span id="titulacion">Guardar Plan</span>
	<form id="audienciaPlanPopForm" name="audienciaPlanPopForm" method="post">
		<table id="tableOver">
			<tbody>
				<tr>
					<td>Descrici&oacute;n</td>
					<td><input id="descripcionPlan" name="descripcionPlan" class="logInput" type="text"></td>				
				</tr>
			</tbody>
		</table>
		
		
		<a id="btnAudienciaGuardar" class="btnAcc btnAccB">Guardar</a>
		<a id="btnAudienciaSalir" class="btnAcc btnAccB">Cerrar</a>
		
	</form>
</div>

<script>
	$(document).ready(function() {
		$_initSavePlanAudienciaPop();
	});
</script>