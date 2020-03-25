<?
session_start();
if(!isset($_SESSION['userName']))
	exit();
?>

<div id="overlay">
	<table id="tableOver">
		<tbody>
			<tr>
				<td>Empresa V&iacute;a P&uacute;blica</td>
				<td><select id="cmbExcelEVP" name="cmbExcelEVP" class="loginput"></select></td>
			</tr>
			<tr>
				<td>EVP 2</td>
				<td><select id="cmbExcelEVP2" name="cmbExcelEVP2" class="loginput"></select></td>
			</tr>
			<tr>
				<td>Elemento</td>
				<td><select id="cmbExcelElemento" name="cmbExcelElemento" class="loginput"></select></td>
			</tr>
			<tr>
				<td colspan="2">
					<table id="dt_elementosExcel" class="display">
						<thead>
							<tr>
								<th class="selectAllXLS"></th>
								<th>Descripci&oacute;n</th>
							</tr>
					    </thead>
					    <tbody>
					    	<tr>
					    		<td colspan="2" class="dataTables_empty">Cargando Datos...</td>
					    	</tr>
						</tbody>
					</table>
				</td>
			<!-- <tr>
				<td colspan="2">
					<div id="btnFileUploadExcel" class="btnAcc btnAccB">Subir XLS</div>
				</td>
			</tr> -->
		</tbody>
	</table>

	<a id="btnSalirUploadExcel" class="btnAcc btnAccB">Cerrar</a>
	<div id="btnFileUploadExcel" class="btnAcc btnAccB">Subir XLS</div>

	<div class="loaderXLS" style="display: none;">
		<img src="images/loading.gif">
	</div>
</div>

<script>
	$(document).ready(function() {
		$_initPopExcel();
	});
</script>
