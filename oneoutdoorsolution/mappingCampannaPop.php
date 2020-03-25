<?
session_start();
if(!isset($_SESSION['userName']))
	exit();
?>

<div id="overlay">
	<a class="btnAcc btnAccB" id="btnEliminarCampanna" href="javascript:;">Eliminar</a>
	<a class="btnAcc btnAccB" id="btnEditarCampanna" href="javascript:;">Editar</a>
	<a class="btnAcc btnAccB" id="btnAgregarCampanna" href="javascript:;">Agregar</a>

	<!--<span id="titulacion">Mantenimiento de Campa&ntilde;as</span>-->
	<table id="dt_campannas" class="display">
		<thead>
			<tr>
				<th>Id</th>
				<th>Descripci&oacute;n</th>
				<th>Archivos</th>
			</tr>
	    </thead>
	    <tbody>
	    	<tr>
	    		<td colspan="3" class="dataTables_empty">Cargando Datos...</td>
	    	</tr>
		</tbody>
	</table>
	<a id="btnSalirCampannas" class="btnAcc btnAccB">Cerrar</a>

</div>

<!-- PopUp Item Dialog -->
<div id="campannasItemDialog"></div>

<!-- PopUp Delete Campanna Dialog -->
<div id="deleteCampannaDialog" style="display: none;">
	<span id="titulacion">Realmente desea eliminar este registro?</span>
	<a class="btnAcc btnAccB" id="btnConfirmDelete">S&iacute;</a>
	<a class="btnAcc btnAccB" id="btnExitDelete">No</a>
</div>

<script>
	$(document).ready(function() {
		$_initPopCampannas();
	});
</script>
