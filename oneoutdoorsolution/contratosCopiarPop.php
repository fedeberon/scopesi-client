<?
session_start();
if(!isset($_SESSION['userName']))
	exit();
?>

<div id="overlay">
	<form id="contratosCopiarPopForm" name="contratosCopiarPopForm" method="post">
		<table id="tableOver">
			<tbody>
				<tr>
					<td>Copiar Contrado desde: </td>
				</tr>
				<tr>
					<td>
						<select id="cmbContratos" name="cmbContratos"></select>
					</td>
				</tr>
			</tbody>
		</table>
		<a id="btnSalirCopiar" class="btnAcc btnAccB">Cerrar</a>
		<a id="btnGuardarCopiar" class="btnAcc btnAccB">Guardar</a>
	</form>
</div>

<script>
	$(document).ready(function() {
		$_initCopiarPop();
	});
</script>