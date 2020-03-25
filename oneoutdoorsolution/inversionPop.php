<?
session_start();
if(!isset($_SESSION['userName']))
	exit();
?>

<div id="overlay">
	<form id="inversionPopForm" name="inversionPopForm" method="post">
		<table id="tableOver">
			<tbody>
				<tr>
					<td><input type="checkbox" name="chkMesAnno" id="chkMesAnno"></td>
					<td>Mes/A&ntilde;o</td>
					<td><input type="text" name="txtMesAnnoDesde" id="txtMesAnnoDesde" style="width: 80px"></td>
					<td><input type="text" name="txtMesAnnoHasta" id="txtMesAnnoHasta" style="width: 80px"></td>
				</tr>
				<tr>
					<td><input type="checkbox" name="chkMesAnnoApertura" id="chkMesAnnoApertura"></td>
					<td>Apertura Mensual</td>
					<td></td>
					<td></td>
				</tr>
				<tr>
					<td><input type="checkbox" name="chkSectores" id="chkSectores"></td>
					<td>Sectores</td>
					<td><a id="btnSectores" class="btnAccFilter btnAccB">Filtro</a></td>
					<td></td>
				</tr>
				<tr>
					<td><input type="checkbox" name="chkRubro" id="chkRubro"></td>
					<td>Rubro</td>
					<td><a id="btnRubro" class="btnAccFilter btnAccB">Filtro</a></td>
					<td></td>
				</tr>
				<tr>
					<td><input type="checkbox" name="chkSegmento" id="chkSegmento"></td>
					<td>Segmento</td>
					<td><a id="btnSegmento" class="btnAccFilter btnAccB">Filtro</a></td>
					<td></td>
				</tr>
				<tr>
					<td><input type="checkbox" name="chkEVP" id="chkEVP"></td>
					<td>EVP</td>
					<td><a id="btnEVP" class="btnAccFilter btnAccB">Filtro</a></td>
					<td></td>
				</tr>
				<tr>
					<td><input type="checkbox" name="chkAnunciante" id="chkAnunciante"></td>
					<td>Anunciante</td>
					<td><a id="btnAnunciante" class="btnAccFilter btnAccB">Filtro</a></td>
					<td></td>
				</tr>
				<tr>
					<td><input type="checkbox" name="chkProducto" id="chkProducto"></td>
					<td>Producto</td>
					<td><a id="btnProducto" class="btnAccFilter btnAccB">Filtro</a></td>
					<td></td>
				</tr>
				<tr>
					<td><input type="checkbox" name="chkMedio" id="chkMedio"></td>
					<td>Medio</td>
					<td><a id="btnMedio" class="btnAccFilter btnAccB">Filtro</a></td>
					<td></td>
				</tr>
				<tr>
					<td><input type="checkbox" name="chkPeriodo" id="chkPeriodo"></td>
					<td>Periodo</td>
					<td></td>
					<td></td>
				</tr>
				<tr>
					<td><input type="checkbox" name="chkTipoDispo" id="chkTipoDispo"></td>
					<td>Tipo Dispositivo</td>
					<td></td>
					<td></td>
				</tr>
			</tbody>
		</table>
		<a id="btnSalir" class="btnAcc btnAccB">Cerrar</a>
		<a id="btnGenerarInforme" class="btnAcc btnAccB">Generar Informe</a>
	</form>
</div>

<!-- PopUp Autofilter Dialog -->
<div id="autoFilterDialog"></div>

<script>
	$(document).ready(function() {
		$_initPop();
	});
</script>