<?
session_start();
if(!isset($_SESSION['userName']))
	exit();
?>

<div id="overlay">
	<form id="contratosPopForm" name="contratosPopForm" method="post">
		<table id="tableOver">
			<tbody>
				<tr>
					<td>Descripci&oacute;n</td>
					<td><input id="descripcion" name="descripcion" class="loginput" type="text"></td>
				</tr>
				<tr>
				<td>Tipo</td>
					<td>
						<select id="cmbTipo" name="cmbTipo" class="loginput">
							<option value="I">Inversi&oacute;n</option>
							<option value="A">Auditoria</option>
							<option value="M">Mapping</option>
						</select>
					</td>
				</tr>
				<tr>
					<td>Observaci&oacute;n</td>
					<td><textarea class="textArea" id="observacion" name="observacion"></textarea></td>
				</tr>
			</tbody>
		</table>
		<a id="btnSalir" class="btnAcc btnAccB">Cerrar</a>
		<a id="btnGuardar" class="btnAcc btnAccB">Guardar</a>
	</form>
</div>

<script>
	$(document).ready(function() {
		$_initPop();
	});
</script>