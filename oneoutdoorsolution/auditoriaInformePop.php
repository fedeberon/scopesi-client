<?
session_start();
if(!isset($_SESSION['userName']))
	exit();
?>

<div id="overlay">
	<form id="auditoriaInformePopForm" name="auditoriaInformePopForm" method="post">
		<table id="tableOver">
			<tbody>
				<tr>
					<td><input type="checkbox" name="chkMesAnnoInf" id="chkMesAnnoInf" disabled="disabled"></td>
					<td>Mes/A&ntilde;o</td>
					<td><input type="text" name="txtMesAnnoDesdeInf" id="txtMesAnnoDesdeInf" style="width: 80px"></td>
					<td><input type="text" name="txtMesAnnoHastaInf" id="txtMesAnnoHastaInf" style="width: 80px"></td>
				</tr>
				<tr>
					<td><input type="checkbox" name="chkCampannaInf" id="chkCampannaInf"></td>
					<td>Campa&ntilde;a</td>
					<td><a id="btnCampannaInf" class="btnAccFilter btnAccB">Filtro</a></td>
					<td></td>
				</tr>
				<tr>
					<td><input type="checkbox" name="chkElementoInf" id="chkElementoInf"></td>
					<td>Elemento</td>
					<td><a id="btnElementoInf" class="btnAccFilter btnAccB">Filtro</a></td>
					<td></td>
				</tr>
				<tr>
					<td><input type="checkbox" name="chkEVPInf" id="chkEVPInf"></td>
					<td>EVP</td>
					<td><a id="btnEVPInf" class="btnAccFilter btnAccB">Filtro</a></td>
					<td></td>
				</tr>
				<tr>
					<td><input type="checkbox" name="chkProvinciaInf" id="chkProvinciaInf"></td>
					<td>Provincia</td>
					<td><a id="btnProvinciaInf" class="btnAccFilter btnAccB">Filtro</a></td>
					<td></td>
				</tr>
				<tr>
					<td><input type="checkbox" name="chkLocalidadInf" id="chkLocalidadInf"></td>
					<td>Localidad</td>
					<td><a id="btnLocalidadInf" class="btnAccFilter btnAccB">Filtro</a></td>
					<td></td>
				</tr>
				<tr>
					<td><input type="checkbox" name="chkFrecuenciaInf" id="chkFrecuenciaInf"></td>
					<td>Frecuencia</td>
					<td><a id="btnFrecuenciaInf" class="btnAccFilter btnAccB">Filtro</a></td>
					<td></td>
				</tr>
			</tbody>
		</table>
		<a id="btnSalirInf" class="btnAcc btnAccB">Cerrar</a>
		<a id="btnGenerarInformeInf" class="btnAcc btnAccB">Generar Informe</a>
	</form>
</div>

<!-- PopUp Autofilter Dialog -->
<div id="autoFilterDialog"></div>

<script>
	$(document).ready(function() {
		$_initInformesPop();
	});
</script>