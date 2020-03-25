<!-- Script User Form -->
<script src="js/usuarios.js" type="text/javascript"></script>

<div id="modCenter">
	<a class="btnAcc btnAccC" id="btnEliminar" href="javascript:;">Eliminar Usuario</a>
	<a class="btnAcc btnAccB" id="btnEditar" href="javascript:;">Editar Usuario</a>
	<a class="btnAcc btnAccA" id="btnAgregar" href="javascript:;">Ingresar Usuario</a>
	<img align="left" src="images/usuarios.png">
	<span id="titulacion">Usuarios</span>
	<table id="dt_usuarios" class="display">
		<thead>
			<tr>
				<th>Id</th>
				<th>Usuario</th>
				<th>Nombre Completo</th>
				<th>Telefono</th>
				<th>EMail</th>
				<th>Tipo de Usuario</th>
				<th>Cargo</th>
			</tr>
	    </thead>
	    <tbody>
	    	<tr>
	    		<td colspan="6" class="dataTables_empty">Cargando Datos...</td>
	    	</tr>
		</tbody>
	</table>
</div>

<!-- PopUp Users Dialog -->
<div id="usuariosDialog"></div>

<!-- PopUp Delete Users Dialog -->
<div id="deleteUsuariosDialog" style="display: none;">
	<span id="titulacion">Realmente desea eliminar este registro?</span>	
	<a class="btnAcc btnAccB" id="btnConfirmDelete">S&iacute;</a>
	<a class="btnAcc btnAccB" id="btnExitDelete">No</a>		
</div>

<script>
	$_init();
</script>