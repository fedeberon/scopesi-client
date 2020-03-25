<?
session_start();
if(!isset($_SESSION['userName']))
	exit();
?>

<div id="overlay">
	<form id="glosarioPopForm" name="glosarioPopForm" method="post">
		<table id="dt_glosario" class="display">
			<thead>
				<tr>
					<th>Id</th>
					<th>Descripci&oacute;n</th>
					<th>Otros Nombres</th>
					<th width="4%"></th>
				</tr>
		    </thead>
		    <tbody>
		    	<tr>
		    		<td colspan="3" class="dataTables_empty">Cargando Datos...</td>
		    	</tr>
			</tbody>
		</table>
		<a id="btnGlosarioSalir" class="btnAcc btnAccB">Cerrar</a>
	</form>
</div>

<script>
	$(document).ready(function() {
		$_initGlosarioPop();
	});
</script>