<?php
require("includes/funciones.inc.php");
require("includes/constants.php");

	$DB = NewADOConnection('mysqli');
	$DB->Connect();
	$DB->Execute("SET NAMES utf8;");
	//$DB->debug=true;

	$strDivsSelect = '';

	if($_REQUEST['tipoMapa'] == $mapaCampania)
	{
		$strSQL = "SELECT * FROM aud_campanias WHERE idCampania IN (".substr(str_replace('"', "'", $_REQUEST['joCampannasSelect']), 1, -1).")";
		$rsCampannas = $DB->Execute($strSQL);

		while(!$rsCampannas->EOF){
				$strDivsSelect .= '<div id="'.$rsCampannas->fields('idCampania').'" class="campItm"><div class="iconMap" id="img'.$rsCampannas->fields('idCampania').'"></div><div style="width: 250px"><h8>'. $rsCampannas->fields('descripcion').'</h8></div></div>';
				$rsCampannas->MoveNext();
		}
	}

	if($_REQUEST['tipoMapa'] == $mapaCircuito)
	{
		if($_REQUEST['tipoFiltro'] == $porEmpresa) {
			$strSQL = "SELECT DISTINCT ae.idEmpresa, ae.descripcion FROM aud_empresas ae
						INNER JOIN aud_circuitos ac ON ac.idEmpresa = ae.idEmpresa
						INNER JOIN aud_localidades al ON al.idLocalidad = ac.idLocalidad
						INNER JOIN aud_circuitos_detalle acd ON ac.idCircuito = acd.idCircuito
						INNER JOIN aud_ubicaciones au ON au.idUbicacion = acd.idUbicacion
						WHERE
						(au.geo_latitud <> 0 AND au.geo_longitud <> 0) AND
						ac.idCircuito IN (".substr(str_replace('"', "'", $_REQUEST['joCircuitosSelect']), 1, -1).")";
		}

		if($_REQUEST['tipoFiltro'] == $porElemento) {
			$strSQL = "SELECT DISTINCT ae.idElemento, ae.descripcion FROM aud_elementos ae
						INNER JOIN aud_circuitos ac ON ac.idElemento = ae.idElemento
						INNER JOIN aud_circuitos_detalle acd ON ac.idCircuito = acd.idCircuito
						INNER JOIN aud_ubicaciones au ON au.idUbicacion = acd.idUbicacion
						WHERE
						(au.geo_latitud <> 0 AND au.geo_longitud <> 0) AND
						ac.idCircuito IN (".substr(str_replace('"', "'", $_REQUEST['joCircuitosSelect']), 1, -1).")";
		}

		if($_REQUEST['tipoFiltro'] == $porCircuito) {
			$strSQL = "SELECT DISTINCT CONCAT(ae.idEmpresa, ael.idElemento, ac.idCircuito) AS idCircuito, CONCAT(ae.descripcion,' - ', ael.descripcion, ' - ', al.descripcion) AS descripcion FROM aud_empresas ae
						INNER JOIN aud_circuitos ac ON ac.idEmpresa = ae.idEmpresa
						INNER JOIN aud_localidades al ON al.idLocalidad = ac.idLocalidad
						INNER JOIN aud_elementos ael ON ac.idElemento = ael.idElemento
						INNER JOIN aud_circuitos_detalle acd ON ac.idCircuito = acd.idCircuito
						INNER JOIN aud_ubicaciones au ON au.idUbicacion = acd.idUbicacion
						WHERE
						(au.geo_latitud <> 0 AND au.geo_longitud <> 0) AND
						ac.idCircuito IN (".substr(str_replace('"', "'", $_REQUEST['joCircuitosSelect']), 1, -1).")";
		}

		if($_REQUEST['tipoFiltro'] == $porSemaforo) {
			$strSQL = "SELECT DISTINCT estado, descEstado FROM aud_detalle_circuitos adc
						INNER JOIN aud_ubicaciones au ON au.idUbicacion = adc.idUbicacion
						WHERE
						(au.geo_latitud <> 0 AND au.geo_longitud <> 0) AND
						adc.idCircuito IN (".substr(str_replace('"', "'", $_REQUEST['joCircuitosSelect']), 1, -1).")";
		}

		$rsCircuitos = $DB->Execute($strSQL);

		while(!$rsCircuitos->EOF){
				$strDivsSelect .= '<div id="'.$rsCircuitos->fields(0).'" class="campItm"><div class="iconMap" id="img'.$rsCircuitos->fields(0).'"></div><div style="width: 250px"><h8>'. $rsCircuitos->fields(1).'</h8></div></div>';
				$rsCircuitos->MoveNext();
		}
	}
?>

<div id="overlay">
	<div id="modIzq">
		<div id="titMapCamp">Campa&ntilde;as</div>
		<?php echo $strDivsSelect; ?>
		<a id="btnSalirMapa" class="btnAcc btnAccB">Cerrar</a>
		<a id="btnAudiencia" class="btnAcc btnAccB">Audiencia</a>
	</div>
	<div id="modDer">
		<div id="mapa"></div>
	</div>
</div>

<script>
	$(document).ready(function() {
		$_initAuditoriaMapaPop();
	});
</script>
