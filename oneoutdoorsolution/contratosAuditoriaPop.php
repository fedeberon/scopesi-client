<?
session_start();
if(!isset($_SESSION['userName']))
	exit();
?>

<div id="overlay">
	<form id="contratosPopForm" name="contratosPopForm" method="post">
		<img align="left" src="images/auditorias.png">
		<span id="titulacion">Modulo de Auditoria</span>
		<table id="dt_contratosAuditoria" class="display" style="width: 676px;">
			<thead>
				<tr>
					<th>Habilitado</th>
					<th>Id</th>
					<th>Campa&ntilde;a</th>
					<th>Fecha</th>
				</tr>
		    </thead>
		    <tbody>
		    	<tr>
		    		<td colspan="4" class="dataTables_empty">Cargando Datos...</td>
		    	</tr>
			</tbody>
		</table>
		<a id="btnSalirAuditoria" class="btnAcc btnAccB">Cerrar</a>
		<a id="btnGuardarAuditoria" class="btnAcc btnAccB">Guardar</a>
	</form>
</div>

<script>
	$(document).ready(function() {
		$_initPopAuditoria();
	});
</script>