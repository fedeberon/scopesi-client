<?
session_start();
if(!isset($_SESSION['userName']))
	exit();
?>

<div id="overlay">
	<form id="usuariosPopForm" name="usuariosPopForm" method="post">
		<table id="tableOver">
			<tbody>
				<tr>
					<td>Usuario</td>
					<td><input id="usuario" name="usuario" class="logInput" type="text"></td>
				</tr>
				<tr>
					<td>Clave</td>
					<td><input id="password" name="password" class="logInput" type="password"></td>
				</tr>
				<tr>
					<td>Nombre Completo</td>
					<td><input id="nombreCompleto" name="nombreCompleto" class="logInput" type="text"></td>
				</tr>
				<tr>
					<td>EMail</td>
					<td><input id="eMail" name="eMail" class="logInput normalCase" type="text"></td>
				</tr>
				<tr>
					<td>Tel&eacute;fono</td>
					<td><input id="telefono" name="telefono" class="logInput" type="text"></td>
				</tr>
				<tr>
					<td>Cuenta</td>
					<td><select id="cmbCuenta" name="cmbCuenta" class="logInput"></select></td>
				</tr>
				<tr>
					<td>Cargo</td>
					<td><input id="cargo" name="cargo" class="logInput" type="text"></td>
				</tr>
				<tr>
					<td>Producto</td>
					<td><select id="cmbProducto" name="cmbProducto" class="logInput"></select></td>
				</tr>
				<tr>
					<td>Tipo de Usuario</td>
					<td><select id="cmbTipoUsuario" name="cmbTipoUsuario" class="logInput"></select></td>
				</tr>
				<tr>
					<td>Contrato Inversi&oacute;n</td>
					<td><select id="cmbContratoInv" name="cmbContratoInv" class="logInput"></select></td>
				</tr>
				<tr>
					<td>Contrato Auditoria</td>
					<td><select id="cmbContratoAud" name="cmbContratoAud" class="logInput"></select></td>
				</tr>
				<tr>
					<td>Contrato Mapping</td>
					<td><select id="cmbContratoMap" name="cmbContratoMap" class="logInput"></select></td>
				</tr>
				<tr>
					<td>Modulos</td>
					<td><div id="sistemaModulos"></div></td>
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