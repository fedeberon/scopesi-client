<?
session_start();
if(!isset($_SESSION['userName']))
	exit();
?>

<div id="overlay">
	<form id="auditoriaCampannaCircuitoDetallePopForm" name="auditoriaCampannaCircuitoDetallePopForm" method="post">
		<table id="dt_auditoriasCampannasCircuitoDetalle" class="display">
			<thead>
				<tr>
					<th>Circuito</th>
					<th>Orden</th>
					<th>Direccion</th>
					<th>Estado BE</th>
					<th>Cantidad BE</th>
					<th>Estado CD</th>
					<th>Cantidad CD</th>
					<th>Estado SA</th>
					<th>Cantidad SA</th>
					<th>Imagenes</th>
				</tr>
		    </thead>
		    <tbody>
		    	<tr>
		    		<td colspan="10" class="dataTables_empty">Cargando Datos...</td>
		    	</tr>
			</tbody>
		</table>
		<a id="btnSalirCampCircuitoDetalle" class="btnAcc btnAccB">Cerrar</a>
	</form>
</div>

<script>
	$(document).ready(function() {
		$_initCampannasCircuitoDetallePop();
	});
</script>