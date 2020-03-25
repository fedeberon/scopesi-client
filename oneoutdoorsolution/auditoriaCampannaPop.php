<?
session_start();
if(!isset($_SESSION['userName']))
	exit();
?>

<div id="overlay">
	<form id="auditoriaCampannaPopForm" name="auditoriaCampannaPopForm" method="post">
		<table id="tableOver">
			<tbody>
				<tr>
					<td><input type="checkbox" name="chkMesAnno" id="chkMesAnno"></td>
					<td>Mes/A&ntilde;o</td>
					<td><input type="text" name="txtMesAnnoDesde" id="txtMesAnnoDesde" style="width: 80px"></td>
					<td><input type="text" name="txtMesAnnoHasta" id="txtMesAnnoHasta" style="width: 80px"></td>
				</tr>
				<tr>
					<td><input type="checkbox" name="chkCampanna" id="chkCampanna"></td>
					<td>Campa&ntilde;a</td>
					<td><a id="btnCampanna" class="btnAccFilter btnAccB">Filtro</a></td>
					<td></td>
				</tr>
			</tbody>
		</table>
		<a id="btnSalirCamp" class="btnAcc btnAccB">Cerrar</a>
		<a id="btnGenerarInformeCamp" class="btnAcc btnAccB">Generar Informe</a>
	</form>
</div>

<!-- PopUp Autofilter Dialog -->
<div id="autoFilterDialog"></div>

<script>
	$(document).ready(function() {
		$_initCampannasPop();
	});
</script>