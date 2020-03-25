<?
session_start();
if(!isset($_SESSION['userName']))
	exit();
?>

<div id="overlay">
	<form id="contratosPopForm" name="contratosPopForm" method="post">
		<img align="left" src="images/inversion.png">
		<span id="titulacion">Modulo de Inversi&oacute;n</span>
		<table id="dt_contratosInversion" class="display" style="width: 676px;">
			<thead>
				<tr>
					<th>Habilitado</th>
					<th>Id</th>
					<th>Rubro</th>
					<th>Fecha Desde</th>
					<th>Fecha Hasta</th>
					<th>Creatividades</th>
				</tr>
		    </thead>
		    <tbody>
		    	<tr>
		    		<td colspan="2" class="dataTables_empty">Cargando Datos...</td>
		    	</tr>
			</tbody>
		</table>
		<a id="btnSalirInversion" class="btnAcc btnAccB">Cerrar</a>
		<a id="btnGuardarInversion" class="btnAcc btnAccB">Guardar</a>
	</form>
</div>

<script>
	$(document).ready(function() {
		$_initPopInversion();
	});
</script>