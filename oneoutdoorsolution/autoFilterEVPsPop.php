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
					<th>GeoPlanning</th>
					<th></th>
					<th width="4%"></th>
				</tr>
		    </thead>
		    <tbody>
		    	<tr>
		    		<td colspan="5" class="dataTables_empty">Cargando Datos...</td>
		    	</tr>
			</tbody>
		</table>
		<a id="btnAutoFiltroEVPsSalir" class="btnAcc btnAccB">Cerrar</a>
	</form>
</div>

<script>
	$(document).ready(function() {
		$_initAutoFiltroEVPsPop();
	});
</script>