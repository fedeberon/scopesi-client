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
					<th>Descripci&oacute;n</th>
				</tr>
		    </thead>
		    <tbody>
		    	<tr>
		    		<td colspan="2" class="dataTables_empty">Cargando Datos...</td>
		    	</tr>
			</tbody>
		</table>
		<a id="btnAutoFiltroSalir" class="btnAcc btnAccB">Cerrar</a>
	</form>
</div>

<script>
	$(document).ready(function() {
		$_initAutoFiltroPop();
	});
</script>