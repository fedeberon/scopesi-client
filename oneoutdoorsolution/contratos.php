<!-- Script Contratos Form -->
<script src="js/contratos.js" type="text/javascript"></script>
<script>
	$('#icon-contratos').addClass('active');
</script>


<div id="modCenter">
	<a class="btnAcc btnAccA" id="btnCopiar" href="javascript:;">Copiar Contrato</a>
	<a class="btnAcc btnAccC" id="btnEliminar" href="javascript:;">Eliminar Contrato</a>
	<a class="btnAcc btnAccB" id="btnEditar" href="javascript:;">Editar Contrato</a>
	<a class="btnAcc btnAccA" id="btnAgregar" href="javascript:;">Ingresar Contrato</a>
	<img align="left" src="images/contratos.png">
	<span id="titulacion">Contratos</span>
	<table id="dt_contratos" class="display">
		<thead>
			<tr>
				<th>Id</th>
				<th>Descripci&oacute;n</th>
				<th>Tipo</th>
				<th>Contrato</th>
			</tr>
	    </thead>
	    <tbody>
	    	<tr>
	    		<td colspan="4" class="dataTables_empty">Cargando Datos...</td>
	    	</tr>
		</tbody>
	</table>
</div>

<!-- PopUp Contratos Dialog -->
<div id="contratosDialog"></div>
<div id="contratosCopiarDialog"></div>
<div id="contratosInversionDialog"></div>
<div id="contratosAuditoriaDialog"></div>
<div id="contratosMappingDialog"></div>

<!-- PopUp Delete Contratos Dialog -->
<div id="deleteContratosDialog" style="display: none;">
	<span id="titulacion">Realmente desea eliminar este registro?</span>	
	<a class="btnAcc btnAccB" id="btnConfirmDelete">S&iacute;</a>
	<a class="btnAcc btnAccB" id="btnExitDelete">No</a>		
</div>

<script>
	$_init();
</script>