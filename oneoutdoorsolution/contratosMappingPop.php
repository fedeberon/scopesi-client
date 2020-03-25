<?
session_start();
if(!isset($_SESSION['userName']))
	exit();
?>

<div id="overlay">
	<form id="contratosPopForm" name="contratosPopForm" method="post">
		<img align="left" src="images/mapping.png">
		<span id="titulacion">Modulo de Mapping</span>
		<table id="dt_contratosMapping" class="display" style="width: 676px;">
			<thead>
				<tr>
					<th>Habilitado</th>
					<th>Id</th>
					<th>EVP</th>
				</tr>
		    </thead>
		    <tbody>
		    	<tr>
		    		<td colspan="3" class="dataTables_empty">Cargando Datos...</td>
		    	</tr>
			</tbody>
		</table>
		<a id="btnSalirMapping" class="btnAcc btnAccB">Cerrar</a>
		<a id="btnGuardarMapping" class="btnAcc btnAccB">Guardar</a>
	</form>
</div>

<script>
	$(document).ready(function() {
		$_initPopMapping();
	});
</script>