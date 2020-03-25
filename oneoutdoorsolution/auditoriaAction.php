<?php
/**
 *  Por Federico Pisarello - SisDev Software (c) 2012 - Buenos Aires, Argentina.
 * 	fpisarello@sisdevsoft.com
 */

require("includes/funciones.inc.php");

session_start();
if(!isset($_SESSION['userName']))
	exit();

function searchCampanna()
{
	require("includes/constants.php");
	require("includes/datatables_db/Datatables.php");

	$datatables = new Datatables();
	$sqlWhereCampannas = '';

	$datatables
			->select('aud_campanias.idCampania, aud_campanias.descripcion descCampannas, aud_productos.descripcion descProducto, aud_agencias.descripcion descAgencia')
			->from('aud_campanias')
			->join('aud_productos', 'aud_productos.idProducto = aud_campanias.idProducto', 'inner')
			->join('aud_agencias', 'aud_agencias.idAgencia = aud_campanias.idAgencia', 'left')
			->add_column('detalleCampanna', '<a id="btnDetalle" href="javascript:;" class="btnAccGrid btnAccA" onclick="$_detalleCircuitoProxyShow($1)">Ingresar</a>', 'aud_campanias.idCampania')
			->add_column('mappingCampanna', '<input type="checkbox" id="$1" name="$1" value=$1 class="chkClassCampania" onclick="$_selectCampanna($1)">', 'aud_campanias.idCampania')
			->add_column('downloadCampanna', '<a id="btnDownload" href="javascript:;" class="btnAccGrid btnAccA" onclick="$_downloadCircuitoProxyShow($1)">ZIP</a>', 'aud_campanias.idCampania');

	if(array_key_exists('chkMesAnno', $_REQUEST) && $_REQUEST['chkMesAnno'] == 'on') {
		if($_REQUEST['txtMesAnnoDesde'] != "") {
			$mesAnnoDesde = substr($_REQUEST['txtMesAnnoDesde'], -4) . '-' . substr($_REQUEST['txtMesAnnoDesde'],0 ,2) . '-' . '01';
			$datatables->where('aud_campanias.fecha_Inicio >=\''.$mesAnnoDesde.'\'');
		}
		if(array_key_exists('txtMesAnnoHasta', $_REQUEST) && $_REQUEST['txtMesAnnoHasta'] != "") {
			$mesAnnoHasta = substr($_REQUEST['txtMesAnnoHasta'], -4) . '-' . substr($_REQUEST['txtMesAnnoHasta'],0 ,2) . '-' . '01';
			$datatables->where('aud_campanias.fecha_Inicio <=\''.$mesAnnoHasta.'\'');
		}
	}

	$DB = NewADOConnection('mysqli');
	$DB->Connect();
	$DB->Execute("SET NAMES utf8;");
	//$DB->debug=true;

	//Procesamos el Contrato del Usuario
	$rsContrato = $DB->Execute("SELECT * FROM contratos_auditoria WHERE idContrato = ".$_SESSION['idContratoAud']);
	while(!$rsContrato->EOF){
		$sqlWhereCampannas .= " (aud_campanias.idCampania = '".$rsContrato->fields('idCampanna')."') OR ";
		$rsContrato->MoveNext();
	}
	if(!empty($sqlWhereCampannas))
		$datatables->where("(".substr($sqlWhereCampannas,0,-3).")");
	else
		$datatables->where(' 1=0 ');

	if(array_key_exists('chkCampanna', $_REQUEST) && $_REQUEST['chkCampanna'] == 'on') {
		if($_REQUEST['jsonDataCampannas'] != "undefined" && $_REQUEST['jsonDataCampannas'] != '[]')
			$datatables->where('aud_campanias.idCampania IN ('.substr(str_replace('"', "'", $_REQUEST['jsonDataCampannas']), 1, -1).')');
	}

	return $datatables->generate();
}

function searchInforme()
{
	require("includes/constants.php");
	require("includes/datatables_db/Datatables.php");

	$emptyFields = false;

	$datatables = new Datatables();

	$datatables->from('aud_circuitos');
	$datatables->join('aud_circuitos_detalle', 'aud_circuitos_detalle.idCircuito = aud_circuitos.idCircuito', 'inner');

	if(array_key_exists('chkElementoInf', $_REQUEST) && $_REQUEST['chkElementoInf'] == 'on') {
		$datatables->select('aud_elementos.descripcion descElemento');
		$datatables->join('aud_elementos', 'aud_elementos.idElemento = aud_circuitos.idElemento', 'inner');
		$datatables->groupby('aud_circuitos.idElemento');
		if($_REQUEST['jsonDataElementos'] != "undefined" && $_REQUEST['jsonDataElementos'] != '[]')
			$datatables->where('aud_elementos.idElemento IN ('.substr(str_replace('"', "'", $_REQUEST['jsonDataElementos']), 1, -1).')');
		$emptyFields = FALSE;
	}
	else {
		$datatables->select("'' blancoElemento");
	}

	if(array_key_exists('chkEVPInf', $_REQUEST) && $_REQUEST['chkEVPInf'] == 'on') {
		$datatables->select('aud_empresas.descripcion descEVP');
		$datatables->join('aud_empresas', 'aud_empresas.idEmpresa = aud_circuitos.idEmpresa', 'inner');
		$datatables->groupby('aud_circuitos.idEmpresa');
		if($_REQUEST['jsonDataEVP'] != "undefined" && $_REQUEST['jsonDataEVP'] != '[]')
			$datatables->where('aud_circuitos.idEmpresa IN ('.substr(str_replace('"', "'", $_REQUEST['jsonDataEVP']), 1, -1).')');
		$emptyFields = FALSE;
	}
	else {
		$datatables->select("'' blancoEVP");
	}

	if(array_key_exists('chkProvinciaInf', $_REQUEST) && $_REQUEST['chkProvinciaInf'] == 'on') {
		$datatables->select('aud_provincias.descripcion descProvincia');
		$datatables->join('aud_provincias', 'aud_provincias.idProvincia = aud_circuitos.idProvincia', 'inner');
		$datatables->groupby('aud_provincias.idProvincia');
		if($_REQUEST['jsonDataProvincia'] != "undefined" && $_REQUEST['jsonDataProvincia'] != '[]')
			$datatables->where('aud_circuitos.idProvincia IN ('.substr(str_replace('"', "'", $_REQUEST['jsonDataProvincia']), 1, -1).')');
		$emptyFields = FALSE;
	}
	else {
		$datatables->select("'' blancoProvincia");
	}

	if(array_key_exists('chkLocalidadInf', $_REQUEST) && $_REQUEST['chkLocalidadInf'] == 'on') {
		$datatables->select('aud_localidades.descripcion descLocalidad');
		$datatables->join('aud_localidades', 'aud_localidades.idLocalidad = aud_circuitos.idLocalidad', 'inner');
		$datatables->groupby('aud_localidades.idLocalidad');
		if($_REQUEST['jsonDataLocalidad'] != "undefined" && $_REQUEST['jsonDataLocalidad'] != '[]')
			$datatables->where('aud_circuitos.idLocalidad IN ('.substr(str_replace('"', "'", $_REQUEST['jsonDataLocalidad']), 1, -1).')');
		$emptyFields = FALSE;
	}
	else {
		$datatables->select("'' blancoLocalidad");
	}

	if(array_key_exists('chkMesAnnoInf', $_REQUEST) && $_REQUEST['chkMesAnnoInf'] == 'on') {
		$datatables->select("aud_circuitos.fecha_inicio, aud_circuitos.fecha_fin, aud_circuitos.fecha_inicio_control");
		$datatables->edit_column('aud_circuitos.fecha_inicio','$1','formatoFecha(aud_circuitos.fecha_inicio)') // php functions
				   ->edit_column('aud_circuitos.fecha_fin','$1','formatoFecha(aud_circuitos.fecha_fin)') // php functions
				   ->edit_column('aud_circuitos.fecha_inicio_control','$1','formatoFecha(aud_circuitos.fecha_inicio_control)'); // php functions

		if(array_key_exists('txtMesAnnoDesdeInf', $_REQUEST) && $_REQUEST['txtMesAnnoDesdeInf'] != "") {
			$mesAnnoDesde = substr($_REQUEST['txtMesAnnoDesdeInf'], -4) . '-' . substr($_REQUEST['txtMesAnnoDesdeInf'],0 ,2) . '-' . '01';
			$datatables->where('aud_circuitos.fecha_inicio >=\''.$mesAnnoDesde.'\'');
		}
		if(array_key_exists('txtMesAnnoHastaInf', $_REQUEST) && $_REQUEST['txtMesAnnoHastaInf'] != "") {
			$mesAnnoHasta = substr($_REQUEST['txtMesAnnoHastaInf'], -4) . '-' . substr($_REQUEST['txtMesAnnoHastaInf'],0 ,2) . '-' . '31';
			$datatables->where('aud_circuitos.fecha_inicio <=\''.$mesAnnoHasta.'\'');
		}
		$emptyFields = FALSE;
	}
	else {
		$datatables->select("'' blancoFechaDesde, '' blancoFechaHasta, '' blancoFechaControl");
	}

	if(array_key_exists('chkFrecuenciaInf', $_REQUEST) && $_REQUEST['chkFrecuenciaInf'] == 'on') {
		if($_REQUEST['jsonDataFrecuencia'] != "undefined" && $_REQUEST['jsonDataFrecuencia'] != '[]') {
			if(in_array($primerControYControlUnico, json_decode($_REQUEST['jsonDataFrecuencia']))) {
				$datatables->select('aud_frecuenciacontroles.descripcion descFrecuencia');
				$datatables->join('aud_frecuenciacontroles', 'aud_frecuenciacontroles.idFrecuencia = '.$primerControYControlUnico, 'inner');
				$datatables->where('aud_circuitos.idFrecuencia IN ('.$controlUnico. ', '.$primerControl.')');
			}
			else {
				$datatables->select('aud_frecuenciacontroles.descripcion descFrecuencia');
				$datatables->join('aud_frecuenciacontroles', 'aud_frecuenciacontroles.idFrecuencia = aud_circuitos.idFrecuencia', 'inner');
				$datatables->where('aud_circuitos.idFrecuencia IN ('.substr(str_replace('"', "'", $_REQUEST['jsonDataFrecuencia']), 1, -1).')');
				$datatables->groupby('aud_frecuenciacontroles.idFrecuencia');
			}
		}
		$emptyFields = FALSE;
	}
	else {
		$datatables->select("'' blancoFrecuencia");
	}

	if($emptyFields) {
		unset($_GET['iSortingCols']);
		unset($_POST['iSortingCols']);
	}

	if($_REQUEST['emptyTable'])
		$datatables->where(' 1=0 ');
	else {
		$DB = NewADOConnection('mysqli');
		$DB->Connect();
		$DB->Execute("SET NAMES utf8;");
		//$DB->debug=true;

		//Procesamos el Contrato del Usuario
		$rsContrato = $DB->Execute("SELECT * FROM contratos_auditoria WHERE idContrato = ".$_SESSION['idContratoAud']);
		while(!$rsContrato->EOF){
			$sqlWhereCampannas .= " (aud_circuitos.idCampania = '".$rsContrato->fields('idCampanna')."') OR ";
			$rsContrato->MoveNext();
		}
		$datatables->where("(".substr($sqlWhereCampannas,0,-3).")");

		if($_REQUEST['chkCampannaInf'] == 'on') {
			if($_REQUEST['jsonDataCampannas'] != "undefined" && $_REQUEST['jsonDataCampannas'] != '[]')
				$datatables->where('aud_circuitos.idCampania IN ('.substr(str_replace('"', "'", $_REQUEST['jsonDataCampannas']), 1, -1).')');
		}

		$datatables->select('SUM(aud_circuitos_detalle.cantidad_BE)');
		$datatables->select('SUM(aud_circuitos_detalle.cantidad_SA)');
		$datatables->select('SUM(aud_circuitos_detalle.cantidad_CD)');
		$datatables->select('SUM(aud_circuitos_detalle.cantidad_BE) + SUM(aud_circuitos_detalle.cantidad_CD)');
	}

	return $datatables->generate();
}

function getCampannasHab()
{
	$DB = NewADOConnection('mysqli');
	$DB->Connect();
	$DB->Execute("SET NAMES utf8;");
	//$DB->debug=true;

	$strSQL = "SELECT * FROM aud_campanias WHERE idCampania IN (SELECT idCampanna FROM contratos_auditoria WHERE idContrato = ".$_SESSION['idContratoAud'].")";
	$rsCampannasHab = $DB->Execute($strSQL);

	$i=0;
	while(!$rsCampannasHab->EOF){
		$arrCampannasHab->data[$i++] = $rsCampannasHab->fields(0);
		$rsCampannasHab->MoveNext();
	}

	return json_encode($arrCampannasHab->data);
}

function searchCampannaCircuito()
{
	require("includes/constants.php");
	require("includes/datatables_db/Datatables.php");

	$datatables = new Datatables();

	$datatables
			->select('aud_campanias.descripcion descCampanna, aud_circuitos.fecha_inicio, aud_circuitos.fecha_fin, aud_empresas.descripcion descEmpresa, aud_elementos.descripcion descElemento, aud_circuitos.idCircuito, aud_provincias.descripcion descProvincia, aud_localidades.descripcion descLocalidad, cantidad_Pauta')
			->from('aud_circuitos')
			->join('aud_elementos', 'aud_elementos.idElemento = aud_circuitos.idElemento', 'inner')
			->join('aud_empresas', 'aud_empresas.idEmpresa = aud_circuitos.idEmpresa', 'inner')
			->join('aud_campanias', 'aud_campanias.idCampania = aud_circuitos.idCampania', 'inner')
			->join('aud_provincias', 'aud_provincias.idProvincia = aud_circuitos.idProvincia', 'inner')
			->join('aud_localidades', 'aud_localidades.idLocalidad = aud_circuitos.idLocalidad', 'inner')
			->edit_column('aud_circuitos.fecha_inicio','$1','formatoFecha(aud_circuitos.fecha_inicio)') // php functions
			->edit_column('aud_circuitos.fecha_fin','$1','formatoFecha(aud_circuitos.fecha_fin)') // php functions
			->where('aud_circuitos.idCampania = '.$_REQUEST['idCampanna'])
			->add_column('detalleCircuito', '<a id="btnDetalle" href="javascript:;" class="btnAccGrid btnAccA" onclick="$_detalleCircuitoDetalleProxyShow($1)">Ingresar</a>', 'aud_circuitos.idCircuito')
			->add_column('mappingCircuito', '<input type="checkbox" id="$1" name="$1" value=$1 class="chkClassCircuito" onclick="$_selectCircuito($1)">', 'aud_circuitos.idCircuito');

		$datatables->where("aud_circuitos.idCircuito IN (SELECT idCircuito FROM aud_circuitos_detalle)");
		$datatables->where("aud_circuitos.idFrecuencia IN ($primerControl, $controlUnico)");

	return $datatables->generate();
}

function searchCampannaCircuitoDetalle()
{
	require("includes/constants.php");
	require("includes/datatables_db/Datatables.php");

	$datatables = new Datatables();

	$datatables
			->select('aud_circuitos_detalle.idCircuito, aud_circuitos_detalle.Orden, aud_ubicaciones.direccion descDireccion, ae1.descripcion descBE, cantidad_BE, ae2.descripcion descCD, cantidad_CD, ae3.descripcion descSA, cantidad_SA')
			->from('aud_circuitos_detalle')
			->join('aud_ubicaciones', 'aud_ubicaciones.idUbicacion = aud_circuitos_detalle.idUbicacion', 'inner')
			->join('aud_estados ae1', 'ae1.idEstado = aud_circuitos_detalle.estado_BE', 'left')
			->join('aud_estados ae2', 'ae2.idEstado = aud_circuitos_detalle.estado_CD', 'left')
			->join('aud_estados ae3', 'ae3.idEstado = aud_circuitos_detalle.estado_SA', 'left')
			->where('aud_circuitos_detalle.idCircuito = '.$_REQUEST['idCircuito'])
			->add_column('imagen', '<a id="btnImagen" href="javascript:;" class="btnAccGrid btnAccA" onclick="$_imagenCircuitoProxyShow($1, $2)">Imagenes</a>', 'aud_circuitos_detalle.idCircuito,aud_circuitos_detalle.Orden');

	return $datatables->generate();
}


function auditoriaCampannaMarketsMap()
{
	require("includes/constants.php");

	try {

		$DB = NewADOConnection('mysqli');
		$DB->Connect();
		$DB->Execute("SET NAMES utf8;");
		//$DB->debug=true;

		$strSQL = "SELECT DISTINCT au.*, acd.idCircuito, acd.Orden, ac.idCampania, ae.descripcion AS descEmpresa, ael.descripcion AS descElemento, ap.descripcion AS descProvincia FROM aud_ubicaciones au
					INNER JOIN aud_circuitos_detalle acd ON au.idUbicacion = acd.idUbicacion
					INNER JOIN aud_circuitos ac ON acd.idCircuito = ac.idCircuito
					INNER JOIN aud_empresas ae ON ae.idEmpresa = ac.idEmpresa
					INNER JOIN aud_elementos ael ON ael.idElemento = ac.idElemento
					INNER JOIN aud_provincias ap ON ap.idProvincia = ac.idProvincia
					WHERE
						ac.idCampania IN (".substr(str_replace('"', "'", $_REQUEST['joCampannasSelect']), 1, -1).")
						AND (au.geo_latitud <> 0 AND au.geo_longitud <> 0)
					ORDER BY ac.idCampania";

		$rsUbicaciones = $DB->Execute($strSQL);

		$i=0;
		$idCampaniaActual="";
		$imgCampania = array();
		while(!$rsUbicaciones->EOF)
		{
			if($rsUbicaciones->fields("idCampania") != $idCampaniaActual) {
				$randoColorIcon = random_color();

				$url = 'http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=%E2%80%A2|'.$randoColorIcon;
				$imgIcon = 'images/icons/'.$randoColorIcon.'.png';
				file_put_contents($imgIcon, file_get_contents($url));

				array_push($imgCampania, array("id" => $rsUbicaciones->fields("idCampania"), "imagen" => $imgIcon));

				$idCampaniaActual = $rsUbicaciones->fields("idCampania");
			}

			//Busco si tiene Foto
			$camarita = "";
			$circuitoOrden = substr('0000'.$rsUbicaciones->fields("Orden"),-4);
			$dirImages = 'images/fotos_auditoria/'.$rsUbicaciones->fields("idCampania").'/'.$rsUbicaciones->fields("idCircuito");
			if(file_exists($dirImages)) {
				if ($filesFotosAuditoria = opendir($dirImages)) {
					while (false !== ($image = readdir($filesFotosAuditoria))) {
						if($image != "." && $image != "..") {
							$extn = explode('.', $image);
				 		    $extn = array_pop($extn);
							if(strpos($image, $circuitoOrden) !== false) {
								$camarita = '<br/><br/><img style="cursor:pointer;" src="images/camara.png" onclick="$_imagenCircuitoProxyShow(\''.$rsUbicaciones->fields("idCircuito").'\',\''.$rsUbicaciones->fields("Orden").'\')">';
								break;
							}
						}
			    	}
				}
			}

			$arrJSON->markers[$i]['latitude'] = floatval($rsUbicaciones->fields("geo_latitud"));
			$arrJSON->markers[$i]['longitude'] = floatval($rsUbicaciones->fields("geo_longitud"));
			$arrJSON->markers[$i]['html'] = '<h7>' . $rsUbicaciones->fields("direccion") . '</h7>' . '<br><h8>' . $rsUbicaciones->fields("descEmpresa") . '<br>' . $rsUbicaciones->fields("descElemento") . '<br>' . $rsUbicaciones->fields("descLocalidad") . '<br>' . $rsUbicaciones->fields("descProvincia") . '</h8>'.$camarita;
			$arrJSON->markers[$i]['icon'] = array("image" => $imgIcon, "id" => array($rsUbicaciones->fields("idCircuito"), $rsUbicaciones->fields("Orden")), "shadow" => false, "iconsize" => array(21,34), "iconanchor" => array(9,34));

			$rsUbicaciones->MoveNext();
			$i++;
		}

		$arrJSON->imagesMapa = $imgCampania;
		$arrJSON->status = "OK";

		return json_encode($arrJSON);

	} catch (Exception $e) {
		$arrJSON->status = "ERR";
		$arrJSON->msg = $e->getMessage();
		return json_encode($arrJSON);
	}
}

function auditoriaCampannaSemaforoMarketsMap()
{
	require("includes/constants.php");

	try {

		$DB = NewADOConnection('mysqli');
		$DB->Connect();
		$DB->Execute("SET NAMES utf8;");
		//$DB->debug=true;


		$strSQL = "SELECT DISTINCT adc.estado, au.direccion, au.geo_latitud, au.geo_longitud, ac.idCampania, adc.idCircuito, adc.Orden, ae.descripcion AS descEmpresa, ael.descripcion AS descElemento, al.descripcion AS descLocalidad, ap.descripcion AS descProvincia FROM aud_ubicaciones au
					INNER JOIN aud_detalle_circuitos adc ON au.idUbicacion = adc.idUbicacion
					INNER JOIN aud_circuitos ac ON adc.idCircuito = ac.idCircuito
					INNER JOIN aud_elementos ael ON ac.idElemento = ael.idElemento
					INNER JOIN aud_empresas ae ON ae.idEmpresa = ac.idEmpresa
					INNER JOIN aud_localidades al ON al.idLocalidad = ac.idLocalidad
					INNER JOIN aud_provincias ap ON ap.idProvincia = ac.idProvincia
					WHERE
					ac.idCircuito IN (".substr(str_replace('"', "'", $_REQUEST['joCircuitosSelect']), 1, -1).")
									AND (au.geo_latitud <> 0 AND au.geo_longitud <> 0)
									ORDER BY adc.estado";

		$rsUbicaciones = $DB->Execute($strSQL);

		$i=0;
		$idOrdenActual="";
		$imgOrden = array();
		while(!$rsUbicaciones->EOF)
		{
			if($rsUbicaciones->fields(0) != $idOrdenActual)
			{
				switch ($rsUbicaciones->fields(0)) {
					case $NoExhibe:
						$colorIcon = "FF0000";
						break;
					case $ConFallas:
						$colorIcon = "FFFF00";
						break;
					case $OK:
						$colorIcon = "008000";
						break;
				}

				$imgIcon = 'images/icons/'.$colorIcon.'.png';
				array_push($imgOrden, array("id" => $rsUbicaciones->fields(0), "imagen" => $imgIcon));
				$idOrdenActual = $rsUbicaciones->fields(0);
			}

			//Busco si tiene Foto
			$camarita = "";
			$circuitoOrden = substr('0000'.$rsUbicaciones->fields("Orden"),-4);
			$dirImages = 'images/fotos_auditoria/'.$rsUbicaciones->fields("idCampania").'/'.$rsUbicaciones->fields("idCircuito");
			if(file_exists($dirImages)) {
				if ($filesFotosAuditoria = opendir($dirImages)) {
					while (false !== ($image = readdir($filesFotosAuditoria))) {
						if($image != "." && $image != "..") {
							$extn = explode('.', $image);
							$extn = array_pop($extn);
							if(strpos($image, $circuitoOrden) !== false) {
								$camarita = '<br/><br/><img style="cursor:pointer;" src="images/camara.png" onclick="$_imagenCircuitoProxyShow(\''.$rsUbicaciones->fields("idCircuito").'\',\''.$rsUbicaciones->fields("Orden").'\')">';
								break;
							}
						}
					}
				}
			}

			$arrJSON->markers[$i]['latitude'] = floatval($rsUbicaciones->fields("geo_latitud"));
			$arrJSON->markers[$i]['longitude'] = floatval($rsUbicaciones->fields("geo_longitud"));
			$arrJSON->markers[$i]['html'] = '<h7>' . $rsUbicaciones->fields("direccion") . '</h7>' . '<br><h8>' . $rsUbicaciones->fields("descEmpresa") . '<br>' . $rsUbicaciones->fields("descElemento") . '<br>' . $rsUbicaciones->fields("descLocalidad") . '<br>' . $rsUbicaciones->fields("descProvincia") . '</h8>'.$camarita;
			$arrJSON->markers[$i]['icon'] = array("image" => $imgIcon, "id" => array($rsUbicaciones->fields("idCircuito"), $rsUbicaciones->fields("Orden")), "shadow" => false, "iconsize" => array(21,34), "iconanchor" => array(9,34));

			$rsUbicaciones->MoveNext();
			$i++;
		}

		$arrJSON->imagesMapa = $imgOrden;
		$arrJSON->status = "OK";

		return json_encode($arrJSON);

	} catch (Exception $e) {
		$arrJSON->status = "ERR";
		$arrJSON->msg = $e->getMessage();
		return json_encode($arrJSON);
	}
}

function auditoriaCampannaDetalleMarketsMap()
{
	require("includes/constants.php");

	try {

		$DB = NewADOConnection('mysqli');
		$DB->Connect();
		$DB->Execute("SET NAMES utf8;");
		//$DB->debug=true;

		$orderConsulta = 'ac.idEmpresa';
		if(array_key_exists('tipoFiltro', $_REQUEST) && $_REQUEST['tipoFiltro'] == $porElemento) {
			$orderConsulta = 'ac.idElemento';
		}

		if(array_key_exists('tipoFiltro', $_REQUEST) && $_REQUEST['tipoFiltro'] == $porCircuito) {
			$orderConsulta = 'CONCAT(ac.idEmpresa, ac.idElemento, ac.idCircuito)';
		}

		$strSQL = "SELECT DISTINCT $orderConsulta, au.direccion, au.geo_latitud, au.geo_longitud, ac.idCampania, acd.idCircuito, acd.Orden, ae.descripcion AS descEmpresa, ael.descripcion AS descElemento, al.descripcion AS descLocalidad, ap.descripcion AS descProvincia FROM aud_ubicaciones au
					INNER JOIN aud_circuitos_detalle acd ON au.idUbicacion = acd.idUbicacion
					INNER JOIN aud_circuitos ac ON acd.idCircuito = ac.idCircuito
					INNER JOIN aud_elementos ael ON ac.idElemento = ael.idElemento
					INNER JOIN aud_empresas ae ON ae.idEmpresa = ac.idEmpresa
					INNER JOIN aud_localidades al ON al.idLocalidad = ac.idLocalidad
					INNER JOIN aud_provincias ap ON ap.idProvincia = ac.idProvincia
					WHERE
						ac.idCircuito IN (".substr(str_replace('"', "'", $_REQUEST['joCircuitosSelect']), 1, -1).")
						AND (au.geo_latitud <> 0 AND au.geo_longitud <> 0)
						ORDER BY 1";

		$rsUbicaciones = $DB->Execute($strSQL);

		$i=0;
		$idOrdenActual="";
		$imgOrden = array();
		while(!$rsUbicaciones->EOF)
		{
			if($rsUbicaciones->fields(0) != $idOrdenActual) {
				$randoColorIcon = random_color();

				if(!file_exists('images/icons/'.$randoColorIcon.'.png')) {
					$url = 'http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=%E2%80%A2|'.$randoColorIcon;
					$imgIcon = 'images/icons/'.$randoColorIcon.'.png';
					file_put_contents($imgIcon, file_get_contents($url));
				}
				else
					$imgIcon = 'images/icons/'.$randoColorIcon.'.png';

				array_push($imgOrden, array("id" => $rsUbicaciones->fields(0), "imagen" => $imgIcon));

				$idOrdenActual = $rsUbicaciones->fields(0);
			}

			//Busco si tiene Foto
			$camarita = "";
			$circuitoOrden = substr('0000'.$rsUbicaciones->fields("Orden"),-4);
			$dirImages = 'images/fotos_auditoria/'.$rsUbicaciones->fields("idCampania").'/'.$rsUbicaciones->fields("idCircuito");
			if(file_exists($dirImages)) {
				if ($filesFotosAuditoria = opendir($dirImages)) {
					while (false !== ($image = readdir($filesFotosAuditoria))) {
						if($image != "." && $image != "..") {
							$extn = explode('.', $image);
				 		    $extn = array_pop($extn);
							if(strpos($image, $circuitoOrden) !== false) {
								$camarita = '<br/><br/><img style="cursor:pointer;" src="images/camara.png" onclick="$_imagenCircuitoProxyShow(\''.$rsUbicaciones->fields("idCircuito").'\',\''.$rsUbicaciones->fields("Orden").'\')">';
								break;
							}
						}
			    	}
				}
			}

			$arrJSON->markers[$i]['latitude'] = floatval($rsUbicaciones->fields("geo_latitud"));
			$arrJSON->markers[$i]['longitude'] = floatval($rsUbicaciones->fields("geo_longitud"));
			$arrJSON->markers[$i]['html'] = '<h7>' . $rsUbicaciones->fields("direccion") . '</h7>' . '<br><h8>' . $rsUbicaciones->fields("descEmpresa") . '<br>' . $rsUbicaciones->fields("descElemento") . '<br>' . $rsUbicaciones->fields("descLocalidad") . '<br>' . $rsUbicaciones->fields("descProvincia") . '</h8>'.$camarita;
			$arrJSON->markers[$i]['icon'] = array("image" => $imgIcon, "id" => array($rsUbicaciones->fields("idCircuito"), $rsUbicaciones->fields("Orden")), "shadow" => false, "iconsize" => array(21,34), "iconanchor" => array(9,34));

			$rsUbicaciones->MoveNext();
			$i++;
		}

		$arrJSON->imagesMapa = $imgOrden;
		$arrJSON->status = "OK";

		return json_encode($arrJSON);

	} catch (Exception $e) {
		$arrJSON->status = "ERR";
		$arrJSON->msg = $e->getMessage();
		return json_encode($arrJSON);
	}
}

function exportXLS()
{
	require("includes/constants.php");

	$DB = NewADOConnection('mysqli');
	$DB->Connect();
	$DB->Execute("SET NAMES utf8;");
	//$DB->debug=true;

	$nombreArchivo = "Auditoria_";
	$innerJoins = "";
	$fieldsQuery = "  ";
	$groupbyQuery = "  ";
	$where = " 1=1 ";


	$arrayTituloCampos = array();
	$arrayValoresCampos = array();
	$arrayTipoDatosCampos = array();

	if(array_key_exists('chkElementoInf', $_REQUEST) && $_REQUEST['chkElementoInf'] == 'on') {
		array_push($arrayTituloCampos, "Elementos");
		array_push($arrayValoresCampos, "descElementos");
		array_push($arrayTipoDatosCampos, "string");

		$fieldsQuery .= " aud_elementos.descripcion descElementos, ";
		$innerJoins .= " INNER JOIN aud_elementos ON aud_elementos.idElemento = aud_circuitos.idElemento";
		if($_REQUEST['jsonDataElementos'] != "undefined" && $_REQUEST['jsonDataElementos'] != '[]')
			$where .= " AND aud_elementos.idElemento IN (".substr(str_replace('"', "'", $_REQUEST['jsonDataElementos']), 1, -1).")";
		$groupbyQuery .= " aud_circuitos.idElemento, ";
	}

	if(array_key_exists('chkEVPInf', $_REQUEST) && $_REQUEST['chkEVPInf'] == 'on') {
		array_push($arrayTituloCampos, "EVP");
		array_push($arrayValoresCampos, "descEVP");
		array_push($arrayTipoDatosCampos, "string");

		$fieldsQuery .= " aud_empresas.descripcion descEVP, ";
		$innerJoins .= " INNER JOIN aud_empresas ON aud_empresas.idEmpresa = aud_circuitos.idEmpresa";
		if($_REQUEST['jsonDataEVP'] != "undefined" && $_REQUEST['jsonDataEVP'] != '[]')
			$where .= " AND aud_empresas.idEmpresa IN (".substr(str_replace('"', "'", $_REQUEST['jsonDataEVP']), 1, -1).")";
		$groupbyQuery .= " aud_circuitos.idEmpresa, ";
	}

	if(array_key_exists('chkProvinciaInf', $_REQUEST) && $_REQUEST['chkProvinciaInf'] == 'on') {
		array_push($arrayTituloCampos, "Provincia");
		array_push($arrayValoresCampos, "descProvincia");
		array_push($arrayTipoDatosCampos, "string");

		$fieldsQuery .= " aud_provincias.descripcion descProvincia, ";
		$innerJoins .= " INNER JOIN aud_provincias ON aud_provincias.idProvincia = aud_circuitos.idProvincia";
		if($_REQUEST['jsonDataProvincia'] != "undefined" && $_REQUEST['jsonDataProvincia'] != '[]')
			$where .= " AND aud_provincias.idProvincia IN (".substr(str_replace('"', "'", $_REQUEST['jsonDataProvincia']), 1, -1).")";
		$groupbyQuery .= " aud_circuitos.idProvincia, ";
	}

	if(array_key_exists('chkLocalidadInf', $_REQUEST) && $_REQUEST['chkLocalidadInf'] == 'on') {
		array_push($arrayTituloCampos, "Localidad");
		array_push($arrayValoresCampos, "descLocalidad");
		array_push($arrayTipoDatosCampos, "string");

		$fieldsQuery .= " aud_localidades.descripcion descLocalidad, ";
		$innerJoins .= " INNER JOIN aud_localidades ON aud_localidades.idLocalidad = aud_circuitos.idLocalidad";
		if($_REQUEST['jsonDataLocalidad'] != "undefined" && $_REQUEST['jsonDataLocalidad'] != '[]')
			$where .= " AND aud_localidades.idLocalidad IN (".substr(str_replace('"', "'", $_REQUEST['jsonDataLocalidad']), 1, -1).")";
		$groupbyQuery .= " aud_circuitos.idLocalidad, ";
	}

	if(array_key_exists('chkFrecuenciaInf', $_REQUEST) && $_REQUEST['chkFrecuenciaInf'] == 'on') {
		array_push($arrayTituloCampos, "Frecuencia");
		array_push($arrayValoresCampos, "descFrecuencia");
		array_push($arrayTipoDatosCampos, "string");
		if(in_array($primerControYControlUnico, json_decode($_REQUEST['jsonDataFrecuencia']))) {
			$fieldsQuery .= " aud_frecuenciacontroles.descripcion descFrecuencia, ";
			$innerJoins .= " INNER JOIN aud_frecuenciacontroles ON aud_frecuenciacontroles.idFrecuencia = ". $primerControYControlUnico;
			$where .= " AND aud_circuitos.idFrecuencia IN (".$controlUnico. ", ".$primerControl.")";
		}
		else {
			$fieldsQuery .= " aud_frecuenciacontroles.descripcion descFrecuencia, ";
			$innerJoins .= " INNER JOIN aud_frecuenciacontroles ON aud_frecuenciacontroles.idFrecuencia = aud_circuitos.idFrecuencia";
			if($_REQUEST['jsonDataFrecuencia'] != "undefined" && $_REQUEST['jsonDataFrecuencia'] != '[]')
				$where .= " AND aud_frecuenciacontroles.idFrecuencia IN (".substr(str_replace('"', "'", $_REQUEST['jsonDataFrecuencia']), 1, -1).")";
			$groupbyQuery .= " aud_circuitos.idFrecuencia, ";
		}
	}

	if(array_key_exists('chkMesAnnoInf', $_REQUEST) && $_REQUEST['chkMesAnnoInf'] == 'on') {
		array_push($arrayTituloCampos, "Fecha Inicio", "Fecha Fin", "Fecha Control");
		array_push($arrayValoresCampos, "fecha_inicio", "fecha_fin", "fecha_inicio_control");
		array_push($arrayTipoDatosCampos, "string", "string", "string");
		$fieldsQuery .= " aud_circuitos.fecha_inicio, aud_circuitos.fecha_fin, aud_circuitos.fecha_inicio_control, ";
		$groupbyQuery .= "  aud_circuitos.fecha_inicio, aud_circuitos.fecha_fin, aud_circuitos.fecha_inicio_control, ";

		if(array_key_exists('txtMesAnnoDesdeInf', $_REQUEST) && $_REQUEST['txtMesAnnoDesdeInf'] != "") {
			$mesAnnoDesde = substr($_REQUEST['txtMesAnnoDesdeInf'], -4) . '-' . substr($_REQUEST['txtMesAnnoDesdeInf'],0 ,2) . '-' . '01';
			$where .= ' AND aud_circuitos.fecha_inicio >=\''.$mesAnnoDesde.'\'';
		}

		if(array_key_exists('txtMesAnnoHasta', $_REQUEST) && $_REQUEST['txtMesAnnoHasta'] != "") {
			$mesAnnoHasta = substr($_REQUEST['txtMesAnnoHastaInf'], -4) . '-' . substr($_REQUEST['txtMesAnnoHastaInf'],0 ,2) . '-' . '01';
			$where .= ' AND aud_circuitos.fecha_inicio <=\''.$mesAnnoHasta.'\'';
		}
	}

	array_push($arrayTituloCampos, "Cantidad OK");
	array_push($arrayValoresCampos, "sumaBE");
	array_push($arrayTipoDatosCampos, "string");
	$fieldsQuery .= "SUM(aud_circuitos_detalle.cantidad_BE) sumaBE,  ";

	array_push($arrayTituloCampos, "Cantidad SA");
	array_push($arrayValoresCampos, "sumaSA");
	array_push($arrayTipoDatosCampos, "string");
	$fieldsQuery .= "SUM(aud_circuitos_detalle.cantidad_SA) sumaSA,  ";

	array_push($arrayTituloCampos, "Cantidad CD");
	array_push($arrayValoresCampos, "sumaCD");
	array_push($arrayTipoDatosCampos, "string");
	$fieldsQuery .= "SUM(aud_circuitos_detalle.cantidad_CD) sumaCD,  ";

	array_push($arrayTituloCampos, "Total");
	array_push($arrayValoresCampos, "sumaTotal");
	array_push($arrayTipoDatosCampos, "string");
	$fieldsQuery .= "SUM(aud_circuitos_detalle.cantidad_BE) + SUM(aud_circuitos_detalle.cantidad_CD) sumaTotal  ";

	$fieldsQuery = substr($fieldsQuery, 0, -2);
	$groupbyQuery = substr($groupbyQuery, 0, -2);

	//Procesamos el Contrato del Usuario
	$rsContrato = $DB->Execute("SELECT * FROM contratos_auditoria WHERE idContrato = ".$_SESSION['idContratoAud']);
	if(!$rsContrato->EOF) {
		while(!$rsContrato->EOF){
			$sqlWhereRubros .= " (aud_circuitos.idCampania = ".$rsContrato->fields('idCampanna').") OR ";
			$rsContrato->MoveNext();
		}
		$sqlWhereRubros = " AND (".substr($sqlWhereRubros,0,-3).")";

		if($_REQUEST['jsonDataCampannas'] != "undefined" && $_REQUEST['jsonDataCampannas'] != '[]')
			$where .= ' AND aud_circuitos.idCampania IN ('.substr(str_replace('"', "'", $_REQUEST['jsonDataCampannas']), 1, -1).')';

	}
	else {
		$sqlWhereRubros = "";
	}

	$query = "SELECT $fieldsQuery FROM aud_circuitos
				INNER JOIN aud_circuitos_detalle ON aud_circuitos.idCircuito = aud_circuitos_detalle.idCircuito
				$innerJoins WHERE $where $sqlWhereRubros";
	if($groupbyQuery)
		$query .= "GROUP BY $groupbyQuery";

	require_once("includes/excel/excel_write/class.writeexcel_workbook.inc.php");
	require_once("includes/excel/excel_write/class.writeexcel_worksheet.inc.php");

	$fname = tempnam("tmp", $nombreArchivo . date('Ymd') . ".xls");
	$workbook = new writeexcel_workbook($fname);
	$workbook->set_tempdir("tmp");
	$fecha = date('d')."-".date('m')."-".date('Y');
	$worksheet =& $workbook->addworksheet("Reporte");

	$rs = $DB->Execute($query);

	$header =& $workbook->addformat();
	$header->set_bold();
	$header->set_size(10);
	$header->set_bg_color('silver');

	for($id = 0; $id < count($arrayTituloCampos) ; $id++) {
		$worksheet->write(0, $id, $arrayTituloCampos[$id], $header);
	}

	$i=1;

	while (!$rs->EOF)
	{
		for($ide=0 ; $ide < count($arrayValoresCampos); $ide++) {
			$worksheet->write($i, $ide, trim($rs->fields($arrayValoresCampos[$ide])));
		}

		$i++;
		$rs->MoveNext();
	}

	$workbook->close();

	if(isset($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'],'MSIE'))
	{
		header("Pragma: public");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	}
	header("Content-Length: ".@filesize($fname));
	header("Content-Type: application/x-msexcel; name=\"".$nombreArchivo."" . date('Ymd') . ".xls\"");
	header("Content-Disposition: inline; filename=\"".$nombreArchivo."" . date('Ymd') . ".xls\"");
	$fh=fopen($fname, "rb");
	fpassthru($fh);
	unlink($fname);
}

function marcarCircuitos()
{

	require("includes/constants.php");

	$DB = NewADOConnection('mysqli');
	$DB->Connect();
	$DB->Execute("SET NAMES utf8;");
	//$DB->debug=true;

	$strSQL = "SELECT * FROM aud_circuitos WHERE idCampania = " . $_REQUEST['idCampanna']."
			 		AND idCircuito IN (SELECT idCircuito FROM aud_circuitos_detalle)
			 		AND idFrecuencia IN ($primerControl, $controlUnico)";

	$rsCircuitos = $DB->Execute($strSQL);

	$arrJSON = array();
	$arrCircuitos = json_decode($_REQUEST['joCircuitosSelect']);

	while(!$rsCircuitos->EOF)
	{
		if(!in_array($rsCircuitos->fields("idCircuito"), $arrCircuitos))
			array_push($arrJSON, $rsCircuitos->fields("idCircuito"));
		$rsCircuitos->MoveNext();
	}

	return json_encode($arrJSON);
}

function fotosAuditoriaShow()
{
	$DB = NewADOConnection('mysqli');
	$DB->Connect();
	$DB->Execute("SET NAMES utf8;");
	//$DB->debug=true;

	$strSQL = "SELECT * FROM aud_circuitos WHERE idCircuito = " . $_REQUEST['idCircuito'];
	$rsCampanna = $DB->Execute($strSQL);

	$idCampanna = $rsCampanna->fields('idCampania');
	$idCircuito = $_REQUEST['idCircuito'];
	$circuitoOrden = substr('0000'.$_REQUEST['orden'],-4);

	$includedExtensions = array ('jpg', 'gif', 'png');

	$dirImages = 'images/fotos_auditoria/'.$idCampanna.'/'.$idCircuito;
	if(file_exists($dirImages)) {
		if ($filesFotosAuditoria = opendir($dirImages)) {
			while (false !== ($image = readdir($filesFotosAuditoria))) {
				if($image != "." && $image != "..") {
					$extn = explode('.', $image);
		 		    $extn = array_pop($extn);
					if (in_array(strtolower($extn),$includedExtensions)) {
						if(strpos($image, $circuitoOrden) !== false) {
							$arrImages[] = array("image" => $dirImages."/".$image);
							$i++;
						}
					}
				}
	    	}
			//usort($arrImages, "cmpFileNameImages");
			return json_encode($arrImages);
		}
		else {
			return json_encode(array());
		}
	}
	else {
		return json_encode(array());
	}
}

function getFotosAuditoriaCampannaZip()
{
	$includedExtensions = array ('jpg', 'gif', 'png');

	$campanna = $_REQUEST['idCampanna'];
	$dirImages = 'images/fotos_auditoria/'.$campanna;
	$fileName = $campanna .'.zip';

	$result = Zip($dirImages, $dirImages."/".$fileName);

	if(isset($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'],'MSIE'))
	{
		header("Pragma: no-cache");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Expires: 0");
	}
	header("Content-Transfer-Encoding: binary");
	header("Content-Length: ".@filesize($dirImages .'/'. $fileName));
	header("Content-Type: plain/text; name=\"".$fileName."\"");
	header("Content-Disposition: attachment; filename=\"".$fileName."\"");
	$fh=fopen($dirImages .'/'. $fileName, "rb");
	fpassthru($fh);
	unlink($dirImages .'/'. $fileName);
}

////////////////////
//Audiencia
///////////////////
function searchFiltroEdad()
{
	require("includes/constants.php");
	require("includes/datatables_db/Datatables.php");

	$datatables = new Datatables();

	$datatables
	->select('idEdades, descripcion, valor')
	->from('map_edades')
	->edit_column('idEdades', '<input checked="checked" type="checkbox" onclick="$_addRemoveFilterAudiencia(\'$2\',\'edad\')">', 'idEdades, valor')
	->unset_column('valor');

	return $datatables->generate();
}

function searchFiltroSexo()
{
	require("includes/constants.php");
	require("includes/datatables_db/Datatables.php");

	$datatables = new Datatables();

	$datatables
	->select('idSexo, descripcion, valor')
	->from('map_sexo')
	->edit_column('idSexo', '<input checked="checked" type="checkbox" onclick="$_addRemoveFilterAudiencia(\'$2\',\'sexo\')">', 'idSexo, valor')
	->unset_column('valor');

	return $datatables->generate();
}

function searchFiltroNSE()
{
	require("includes/constants.php");
	require("includes/datatables_db/Datatables.php");

	$datatables = new Datatables();

	$datatables
	->select('idNiveles, descripcion, valor')
	->from('map_niveles')
	->edit_column('idNiveles', '<input checked="checked" type="checkbox" onclick="$_addRemoveFilterAudiencia(\'$2\',\'nse\')">', 'idNiveles, valor')
	->unset_column('valor');

	return $datatables->generate();
}

function searchFiltroPeriodo()
{
	require("includes/constants.php");
	require("includes/datatables_db/Datatables.php");

	$datatables = new Datatables();

	$datatables
	->select('idPeriodo, descripcion, valor')
	->from('map_periodo')
	->edit_column('idPeriodo', '<input checked="checked" type="checkbox" onclick="$_addRemoveFilterAudiencia(\'$2\',\'periodo\')">', 'idPeriodo, valor')
	->unset_column('valor');

	return $datatables->generate();
}

function evaluarAudiencia()
{
	require("includes/constants.php");

	$DB = NewADOConnection('mysqli');
	$DB->Connect();
	$DB->Execute("SET NAMES utf8;");
	//$DB->debug=true;

	$DB->StartTrans(); // start transaction

	$joCircuitos = json_decode($_REQUEST["joCircuitos"]);
	$arrFiltros = json_decode($_REQUEST["joAudiencia"]);

	foreach ($arrFiltros as $clave => $filtro){

		switch ($clave) {
			case 'jaEdades':
				$minEdad = 99;
				$maxEdad = -99;
				foreach ($filtro as $edad) {
					list($desde, $hasta) = explode("-", $edad);
					if($minEdad > $desde)
						$minEdad = $desde;
						if($maxEdad < $hasta)
							$maxEdad = $hasta;
				}
				break;
			case 'jaMSE':
				$nseBajo = "";
				$nseMedio = "";
				$nseAlto = "";
				foreach ($filtro as $mse) {
					switch ($mse) {
						case 'B':
							$nseBajo = $mse;
							break;
						case 'M':
							$nseMedio = $mse;
							break;
						case 'A':
							$nseAlto = $mse;
							break;
					}
				}
				if($nseBajo != "") {
					$maxNse = $nseBajo;
					if($nseAlto != "")
						$minNse = $nseAlto;
						else
							$minNse = $nseMedio;
				}
				else if($nseMedio != "") {
					$maxNse = $nseMedio;
					if($nseAlto != "")
						$minNse = $nseAlto;
						else
							$minNse = $nseMedio;
				}
				else {
					$maxNse = $nseAlto;
					$minNse = $nseAlto;
				}
				break;
			case 'jaPeriodo':
				list($lun, $mar, $mie, $jue, $vie, $sab, $dom) = array(0,0,0,0,0,0,0);
				foreach ($filtro as $dia) {
					switch ($dia) {
						case 'L':
							$lun = 1;
							break;
						case 'M':
							$mar = 1;
							break;
						case 'I':
							$mie = 1;
							break;
						case 'J':
							$jue = 1;
							break;
						case 'V':
							$vie = 1;
							break;
						case 'S':
							$sab = 1;
							break;
						case 'D':
							$dom = 1;
							break;
					}
				}
				break;
			case 'jaSexo':
				$sexoMasc = 0;
				$sexoFem = 0;
				foreach ($filtro as $sexo) {
					if($sexo == 'M')
						$sexoMasc = 1;
						if($sexo == 'F')
							$sexoFem = 1;
				}
				break;
		}
	}

	//INSERT EN PROCESOS
	$strSQL = "INSERT INTO map_procesos ( ";
	$strSQL .= "	Descripcion, ";
	$strSQL .= "	IdUsuario, ";
	$strSQL .= "	Target_Edad_Desde, ";
	$strSQL .= "	Target_Edad_Hasta, ";
	$strSQL .= "	Target_Sexo_Femenino, ";
	$strSQL .= "	Target_Sexo_Masculino, ";
	$strSQL .= "	Target_NSE_Desde, ";
	$strSQL .= "	Target_NSE_Hasta, ";
	$strSQL .= "	Lunes, ";
	$strSQL .= "	Martes, ";
	$strSQL .= "	Miercoles, ";
	$strSQL .= "	Jueves, ";
	$strSQL .= "	Viernes, ";
	$strSQL .= "	Sabado, ";
	$strSQL .= "	Domingo, ";
	$strSQL .= "	Tipo_Muestra ";
	$strSQL .= "	) VALUES ( ";
	$strSQL .= "	'AUDIENCIA DEL USUARIO ".$_SESSION['userName']."', ";
	$strSQL .= "	'".$_SESSION['idUser']."', ";
	$strSQL .= "	'".$minEdad."', ";
	$strSQL .= "	'".$maxEdad."', ";
	$strSQL .= "	'".$sexoFem."', ";
	$strSQL .= "	'".$sexoMasc."', ";
	$strSQL .= "	'".$minNse."', ";
	$strSQL .= "	'".$maxNse."', ";
	$strSQL .= "	'".$lun."', ";
	$strSQL .= "	'".$mar."', ";
	$strSQL .= "	'".$mie."', ";
	$strSQL .= "	'".$jue."', ";
	$strSQL .= "	'".$vie."', ";
	$strSQL .= "	'".$sab."', ";
	$strSQL .= "	'".$dom."', ";
	$strSQL .= "	'AUD' ";
	$strSQL .= "	) ";

	$DB->Execute($strSQL);
	$insert_ID = $DB->Insert_ID();

	$sSQL = "SELECT DISTINCT acd.idUbicacion, acd.idCircuito, IFNULL(me.Coeficiente,1) Coeficiente FROM aud_circuitos_detalle acd
					INNER JOIN aud_ubicaciones au ON acd.idUbicacion = au.idUbicacion
					INNER JOIN map_elementos me ON me.idElemento = au.idElemento
				WHERE
					idCircuito IN (".implode(",", $joCircuitos).")";

	$rsCirucitos = $DB->Execute($sSQL);

	while(!$rsCirucitos->EOF)
	{
		$strSQL = "INSERT INTO map_procesos_detalle ( ";
		$strSQL .= "	ID, ";
		$strSQL .= "	idUbicacion, ";
		$strSQL .= "	idCircuito, ";
		$strSQL .= "	Coeficiente ";
		$strSQL .= "	) VALUES ( ";
		$strSQL .= "	'".$insert_ID."', ";
		$strSQL .= "	'".$rsCirucitos->fields('idUbicacion')."', ";
		$strSQL .= "	'".$rsCirucitos->fields('idCircuito')."', ";
		$strSQL .= "	'".$rsCirucitos->fields('Coeficiente')."' ";
		$strSQL .= "	) ";

		$DB->Execute($strSQL);

		$rsCirucitos->MoveNext();
	}

	if (!$DB->CompleteTrans())
	{
		$arrJSON->status = "ERROR";
		$arrJSON->msg = 'Ocurri&oacute; un error al Evaluar la Informaci&oacute;n';

		return json_encode($arrJSON);
	}

	//Ejecuto el SP para Evaluar la Informacion de Ubicaciones
	$DB->Execute("CALL map_procesar_plan($insert_ID, @Desc_Error);");

	$rsResult = $DB->Execute("SELECT * FROM map_procesos WHERE ID = $insert_ID");
	if($rsResult->fields('Estado') != "F") {
		$arrJSON->status = "ERROR";
		$arrJSON->msg = 'Ocurri&oacute; un error al Evaluar la Informaci&oacute;n';

		return json_encode($arrJSON);
	}

	//Armo los JSON para el RESPONSE
	$rsCant = $DB->Execute("SELECT COUNT(*) AS canUbi FROM map_procesos_detalle WHERE ID = $insert_ID");

	$Edad = $rsResult->fields('Target_Edad_Desde')."-".$rsResult->fields('Target_Edad_Hasta');
	$Genero = ($rsResult->fields('Target_Sexo_Femenino') == 1 ? "F" : "").($rsResult->fields('Target_Sexo_Masculino') == 1 ? "M" : "");
	$NSE = $rsResult->fields('Target_NSE_Desde').$rsResult->fields('Target_NSE_Hasta');
	if($NSE == "AB")
		$NSE = "AMB";

	$objGeneral = "";
	$objGeneral->Target = "Edad: ".$Edad.", Sexo: ".$Genero.", NSE: ".$NSE;
	$objGeneral->Universo = $rsResult->fields('Total_Personas_Universo');
	$objGeneral->CantUbicaciones = $rsCant->fields('canUbi');
	$objGeneral->CoberturaNeta = $rsResult->fields('Cobertura');
	$objGeneral->Frecuencia = $rsResult->fields('Tasa_Repeticion');
	$objGeneral->Impactos = $rsResult->fields('Total_Personas_Muestra');
	$objGeneral->Cobertura_Porc = $rsResult->fields('Cobertura_Porc');
	$objGeneral->PBR = round($rsResult->fields('Pbr'),2);
	$objGeneral->CPR = 0;
	$objGeneral->CPM = 0;

	$objGeneral->Detallada = array();
	$objGeneral->PorEmpresa = array();
	$objGeneral->PorElemento = array();
	$objGeneral->PorCircuito = array();

	//Detallada
	$sSQL = "SELECT DISTINCT mp.descripcion AS descEmpresa, mu.direccion, me.descripcion AS descElemento, ml.descripcion AS descLocalidad, mr.descripcion AS descProvincia, mpd.* FROM map_procesos_detalle mpd
					INNER JOIN aud_ubicaciones mu ON mpd.idUbicacion = mu.idUbicacion
					INNER JOIN aud_circuitos ac ON mpd.idCircuito = ac.idCircuito
					INNER JOIN map_elementos me ON ac.idElemento = me.idElemento
					INNER JOIN map_empresas mp ON ac.idEmpresa = mp.idEmpresa
					INNER JOIN map_localidades ml ON mu.idLocalidad = ml.idLocalidad
					INNER JOIN map_provincias mr ON mu.idProvincia = mr.idProvincia
				WHERE mpd.ID = $insert_ID ";

	if($_SESSION['userType'] == $idTypeUser || $_SESSION['userType'] == $idTypeConsult)
		$sSQL .= "AND me.acumulaDatos = 0";

	$rsDetalle = $DB->Execute($sSQL);

	while(!$rsDetalle->EOF)
	{
		$objDetalle = "";
		$objDetalle[]= "";
		$objDetalle[]= $rsDetalle->fields('descEmpresa');
		$objDetalle[]= $rsDetalle->fields('direccion');
		$objDetalle[]= $rsDetalle->fields('descLocalidad');
		$objDetalle[]= $rsDetalle->fields('descProvincia');
		$objDetalle[]= $rsDetalle->fields('descElemento');
		$objDetalle[]= $rsDetalle->fields('Cobertura') == 0 ? "*" : $rsDetalle->fields('Cobertura');
		$objDetalle[]= $rsDetalle->fields('Tasa_Repeticion') == 0 ? "*" : round($rsDetalle->fields('Tasa_Repeticion'),2);
		$objDetalle[]= $rsDetalle->fields('Cobertura_Porc') == 0 ? "*" : $rsDetalle->fields('Cobertura_Porc');
		$objDetalle[]= $rsDetalle->fields('Impactos') == "0" ? "*" : $rsDetalle->fields('Impactos');
		$objDetalle[]= $rsDetalle->fields('Pbr') == 0 ? "*" : round($rsDetalle->fields('Pbr'),2);
		$objDetalle[]= 0;
		$objDetalle[]= 0;

		array_push($objGeneral->Detallada, $objDetalle);

		$rsDetalle->MoveNext();
	}

	//Empresa
	$rsDetalle = $DB->Execute("SELECT DISTINCT mp.descripcion AS descEmpresa, COUNT(mu.idUbicacion) AS canUbi, SUM(mpd.Impactos) AS Impactos, SUM(mpd.Contactos) AS Contactos, SUM(mpd.Pbr) AS Pbr FROM map_procesos_detalle mpd
									INNER JOIN aud_ubicaciones mu ON mpd.idUbicacion = mu.idUbicacion
									INNER JOIN aud_circuitos ac ON mpd.idCircuito = ac.idCircuito
									INNER JOIN map_elementos me ON ac.idElemento = me.idElemento
									INNER JOIN map_empresas mp ON ac.idEmpresa = mp.idEmpresa
								WHERE mpd.ID = $insert_ID
								GROUP BY mp.idEmpresa");

	while(!$rsDetalle->EOF)
	{
		$objEmpresa = "";
		$objEmpresa[]= "";
		$objEmpresa[]= $rsDetalle->fields('descEmpresa');
		$objEmpresa[]= $rsDetalle->fields('canUbi');
		$objEmpresa[]= 0;
		$objEmpresa[]= $rsDetalle->fields('Impactos');
		$objEmpresa[]= round($rsDetalle->fields('Pbr'),2);
		$objEmpresa[]= 0;
		$objEmpresa[]= 0;

		array_push($objGeneral->PorEmpresa, $objEmpresa);

		$rsDetalle->MoveNext();
	}

	//Elemento
	$rsDetalle = $DB->Execute("SELECT DISTINCT me.descripcion AS descElemento, COUNT(mu.idUbicacion) AS canUbi, SUM(mpd.Impactos) AS Impactos, SUM(mpd.Contactos) AS Contactos, SUM(mpd.Pbr) AS Pbr FROM map_procesos_detalle mpd
									INNER JOIN aud_ubicaciones mu ON mpd.idUbicacion = mu.idUbicacion
									INNER JOIN aud_circuitos ac ON mpd.idCircuito = ac.idCircuito
									INNER JOIN map_elementos me ON ac.idElemento = me.idElemento
									WHERE
										mpd.ID = $insert_ID
										GROUP BY me.idElemento");

	while(!$rsDetalle->EOF)
	{
		$objElemento = "";
		$objElemento[]= "";
		$objElemento[]= $insert_ID;
		$objElemento[]= $rsDetalle->fields('descElemento');
		$objElemento[]= $rsDetalle->fields('canUbi');
		$objElemento[]= 0;
		$objElemento[]= $rsDetalle->fields('Impactos');
		$objElemento[]= round($rsDetalle->fields('Pbr'),2);
		$objElemento[]= 0;
		$objElemento[]= 0;

		array_push($objGeneral->PorElemento, $objElemento);

		$rsDetalle->MoveNext();
	}

	//Circuito
	$rsDetalle = $DB->Execute("SELECT DISTINCT mp.descripcion AS descEmpresa, me.descripcion AS descElemento, ml.descripcion AS descLocalidad, COUNT(mu.idUbicacion) AS canUbi, SUM(mpd.Impactos) AS Impactos, SUM(mpd.Contactos) AS Contactos, SUM(mpd.Pbr) AS Pbr, map.descripcion AS descPlan FROM map_procesos_detalle mpd
									INNER JOIN aud_ubicaciones mu ON mpd.idUbicacion = mu.idUbicacion
									INNER JOIN aud_circuitos ac ON mpd.idCircuito = ac.idCircuito
									INNER JOIN map_elementos me ON ac.idElemento = me.idElemento
									INNER JOIN map_empresas mp ON ac.idEmpresa = mp.idEmpresa
									INNER JOIN map_localidades ml ON ml.idLocalidad = mu.idLocalidad
									LEFT JOIN map_audiencia_planes map ON mpd.ID = map.idMapAudiencia
								WHERE
									mpd.ID = $insert_ID
									GROUP BY mu.idEmpresa, mu.idElemento, mu.idLocalidad");

	while(!$rsDetalle->EOF)
	{
		$objCircuito = "";
		$objCircuito[]= $rsDetalle->fields('descEmpresa');
		$objCircuito[]= $rsDetalle->fields('descElemento');
		$objCircuito[]= $rsDetalle->fields('descLocalidad');
		$objCircuito[]= $rsDetalle->fields('canUbi');
		$objCircuito[]= 0;
		$objCircuito[]= $rsDetalle->fields('Impactos');
		$objCircuito[]= round($rsDetalle->fields('Pbr'),2);
		$objCircuito[]= 0;
		$objCircuito[]= 0;

		array_push($objGeneral->PorCircuito, $objCircuito);

		$rsDetalle->MoveNext();
	}

	$objGeneral->ID = $insert_ID;
	$objGeneral->status = "OK";

	return json_encode($objGeneral);
}

function getAudienciaExcel()
{
	require("includes/constants.php");
	require_once("includes/excel/excel_write/class.writeexcel_workbook.inc.php");
	require_once("includes/excel/excel_write/class.writeexcel_worksheet.inc.php");

	$DB = NewADOConnection('mysqli');
	$DB->Connect();
	$DB->Execute("SET NAMES utf8;");
	//$DB->debug=true;

	$id = $_REQUEST['idMapProcesos'];
	$joCircuitos = json_decode($_REQUEST["joCircuitos"]);
	$nombreArchivo = "Audiencia_".date('Ymd');

	$fname = tempnam("tmp", $nombreArchivo . ".xls");
	$workbook = new writeexcel_workbook($fname);
	$workbook->set_tempdir("tmp");

	$header =& $workbook->addformat();
	$header->set_bold();
	$header->set_size(10);
	$header->set_bg_color('silver');

	$ids = implode(",", json_decode($_REQUEST['idMapProcesos']));

	////////////////
	//General
	///////////////
	$worksheet =& $workbook->addworksheet("General");
	$rsResult = $DB->Execute("SELECT * FROM map_procesos WHERE ID IN ($ids)");
	$rsCant = $DB->Execute("SELECT COUNT(*) AS canUbi FROM map_procesos_detalle WHERE ID IN ($ids)");

	//Header
	$worksheet->write(0, 0, html_entity_decode("Descripci�n", ENT_HTML5, 'ISO-8859-1'), $header);
	$worksheet->write(0, 1, html_entity_decode("Valor", ENT_HTML5, 'ISO-8859-1'), $header);

	$row = 0;
	while(!$rsResult->EOF)
	{
		$Edad = $rsResult->fields('Target_Edad_Desde')."-".$rsResult->fields('Target_Edad_Hasta');
		$Genero = ($rsResult->fields('Target_Sexo_Femenino') == 1 ? "F" : "").($rsResult->fields('Target_Sexo_Masculino') == 1 ? "M" : "");
		$NSE = $rsResult->fields('Target_NSE_Desde').$rsResult->fields('Target_NSE_Hasta');
		if($NSE == "AB")
			$NSE = "AMB";

			//Datos
			$worksheet->write($row+1, 0, "Target");
			$worksheet->write($row+1, 1, "Edad: ".$Edad.", Sexo: ".$Genero.", NSE: ".$NSE);
			$worksheet->write($row+2, 0, "Universo");
			$worksheet->write($row+2, 1, $rsResult->fields('Total_Personas_Universo'));
			$worksheet->write($row+3, 0, "Cantidad de Ubicaciones");
			$worksheet->write($row+3, 1, $rsCant->fields('canUbi'));
			$worksheet->write($row+4, 0, "Cobertura Neta");
			$worksheet->write($row+4, 1, $rsResult->fields('Cobertura'));
			$worksheet->write($row+5, 0, "Frecuencia");
			$worksheet->write($row+5, 1, $rsResult->fields('Tasa_Repeticion'));
			$worksheet->write($row+6, 0, "Impactos");
			$worksheet->write($row+6, 1, $rsResult->fields('Total_Personas_Muestra'));
			$worksheet->write($row+7, 0, "Cobertura %");
			$worksheet->write($row+7, 1, $rsResult->fields('Cobertura_Porc'));
			$worksheet->write($row+8, 0, "PBR");
			$worksheet->write($row+8, 1, round($rsResult->fields('Pbr'),2));
			$worksheet->write($row+9, 0, "CPR");
			$worksheet->write($row+9, 1, 0);
			$worksheet->write($row+10, 0, "CPM");
			$worksheet->write($row+10, 1, 0);

			$row = $row+10;

			$rsResult->MoveNext();
	}

	/////////////////
	//Detallada
	////////////////
	$worksheet =& $workbook->addworksheet("Detallada");
	$sSQL = "SELECT DISTINCT mp.descripcion AS descEmpresa, mu.direccion, me.descripcion AS descElemento, ml.descripcion AS descLocalidad, mr.descripcion AS descProvincia, mpd.*, map.descripcion AS descPlan FROM map_procesos_detalle mpd
					INNER JOIN aud_ubicaciones mu ON mpd.idUbicacion = mu.idUbicacion
					INNER JOIN aud_circuitos ac ON mpd.idCircuito = ac.idCircuito
					INNER JOIN map_elementos me ON ac.idElemento = me.idElemento
					INNER JOIN map_empresas mp ON ac.idEmpresa = mp.idEmpresa
					INNER JOIN map_localidades ml ON mu.idLocalidad = ml.idLocalidad
					INNER JOIN map_provincias mr ON mu.idProvincia = mr.idProvincia
					LEFT JOIN map_audiencia_planes map ON mpd.ID = map.idMapAudiencia
				WHERE
				mpd.ID IN ($ids) ";

	if($_SESSION['userType'] == $idTypeUser || $_SESSION['userType'] == $idTypeConsult)
		$sSQL .= "AND me.acumulaDatos = 0";

	$rsDetalle = $DB->Execute($sSQL);

	//Header
	$worksheet->write(0, 0, html_entity_decode("Descripci�n Plan", ENT_HTML5, 'ISO-8859-1'), $header);
	$worksheet->write(0, 1, html_entity_decode("Empresa", ENT_HTML5, 'ISO-8859-1'), $header);
	$worksheet->write(0, 2, html_entity_decode("Direcci�n", ENT_HTML5, 'ISO-8859-1'), $header);
	$worksheet->write(0, 3, html_entity_decode("Localidad", ENT_HTML5, 'ISO-8859-1'), $header);
	$worksheet->write(0, 4, html_entity_decode("Provincia", ENT_HTML5, 'ISO-8859-1'), $header);
	$worksheet->write(0, 5, html_entity_decode("Elemento", ENT_HTML5, 'ISO-8859-1'), $header);
	$worksheet->write(0, 6, html_entity_decode("Cobertura Neta", ENT_HTML5, 'ISO-8859-1'), $header);
	$worksheet->write(0, 7, html_entity_decode("Frecuencia", ENT_HTML5, 'ISO-8859-1'), $header);
	$worksheet->write(0, 8, html_entity_decode("Cobertura %", ENT_HTML5, 'ISO-8859-1'), $header);
	$worksheet->write(0, 9, html_entity_decode("Impactos Totales", ENT_HTML5, 'ISO-8859-1'), $header);
	$worksheet->write(0, 10, html_entity_decode("PBR", ENT_HTML5, 'ISO-8859-1'), $header);
	$worksheet->write(0, 11, html_entity_decode("CPR", ENT_HTML5, 'ISO-8859-1'), $header);
	$worksheet->write(0, 12, html_entity_decode("CPM", ENT_HTML5, 'ISO-8859-1'), $header);

	$row = 1;
	while(!$rsDetalle->EOF)
	{
		$worksheet->write($row, 0, $rsDetalle->fields('descPlan'));
		$worksheet->write($row, 1, $rsDetalle->fields('descEmpresa'));
		$worksheet->write($row, 2, $rsDetalle->fields('direccion'));
		$worksheet->write($row, 3, $rsDetalle->fields('descLocalidad'));
		$worksheet->write($row, 4, $rsDetalle->fields('descProvincia'));
		$worksheet->write($row, 5, $rsDetalle->fields('descElemento'));
		$worksheet->write($row, 6, $rsDetalle->fields('Cobertura'));
		$worksheet->write($row, 7, round($rsDetalle->fields('Tasa_Repeticion'),2));
		$worksheet->write($row, 8, $rsDetalle->fields('Cobertura_Porc'));
		$worksheet->write($row, 9, $rsDetalle->fields('Impactos'));
		$worksheet->write($row, 10, round($rsDetalle->fields('Pbr'),2));
		$worksheet->write($row, 11, 0);
		$worksheet->write($row++, 12, 0);

		$rsDetalle->MoveNext();
	}
	/////////////////////
	//Empresa
	////////////////////
	$worksheet =& $workbook->addworksheet("Empresa");
	$rsDetalle = $DB->Execute("SELECT DISTINCT mp.descripcion AS descEmpresa, COUNT(mu.idUbicacion) AS canUbi, SUM(mpd.Impactos) AS Impactos, SUM(mpd.Contactos) AS Contactos, SUM(mpd.Pbr) AS Pbr, map.descripcion AS descPlan FROM map_procesos_detalle mpd
					INNER JOIN aud_ubicaciones mu ON mpd.idUbicacion = mu.idUbicacion
					INNER JOIN aud_circuitos ac ON mpd.idCircuito = ac.idCircuito
					INNER JOIN map_elementos me ON ac.idElemento = me.idElemento
					INNER JOIN map_empresas mp ON ac.idEmpresa = mp.idEmpresa
					LEFT JOIN map_audiencia_planes map ON mpd.ID = map.idMapAudiencia
					WHERE mpd.ID IN ($ids)
						GROUP BY mpd.ID, mp.idEmpresa");

	//Header
	$worksheet->write(0, 0, html_entity_decode("Descripci�n", ENT_HTML5, 'ISO-8859-1'), $header);
	$worksheet->write(0, 1, html_entity_decode("Empresa", ENT_HTML5, 'ISO-8859-1'), $header);
	$worksheet->write(0, 2, html_entity_decode("Cantidad de Ubicaciones", ENT_HTML5, 'ISO-8859-1'), $header);
	$worksheet->write(0, 3, html_entity_decode("Inversi&oacute;n", ENT_HTML5, 'ISO-8859-1'), $header);
	$worksheet->write(0, 4, html_entity_decode("Impactos", ENT_HTML5, 'ISO-8859-1'), $header);
	$worksheet->write(0, 5, html_entity_decode("PBR", ENT_HTML5, 'ISO-8859-1'), $header);
	$worksheet->write(0, 6, html_entity_decode("CPR", ENT_HTML5, 'ISO-8859-1'), $header);
	$worksheet->write(0, 7, html_entity_decode("CPM", ENT_HTML5, 'ISO-8859-1'), $header);

	$row = 1;
	while(!$rsDetalle->EOF)
	{
		$worksheet->write($row, 0, $rsDetalle->fields('descPlan'));
		$worksheet->write($row, 1, $rsDetalle->fields('descEmpresa'));
		$worksheet->write($row, 2, $rsDetalle->fields('canUbi'));
		$worksheet->write($row, 3, 0);
		$worksheet->write($row, 4, $rsDetalle->fields('Impactos'));
		$worksheet->write($row, 5, round($rsDetalle->fields('Pbr'),2));
		$worksheet->write($row, 6, 0);
		$worksheet->write($row++, 7, 0);

		$rsDetalle->MoveNext();
	}
	///////////////////////
	//Elemento
	//////////////////////
	$worksheet =& $workbook->addworksheet("Elemento");
	$rsDetalle = $DB->Execute("SELECT DISTINCT me.descripcion AS descElemento, COUNT(mu.idUbicacion) AS canUbi, SUM(mpd.Impactos) AS Impactos, SUM(mpd.Contactos) AS Contactos, SUM(mpd.Pbr) AS Pbr, map.descripcion AS descPlan FROM map_procesos_detalle mpd
									INNER JOIN aud_ubicaciones mu ON mpd.idUbicacion = mu.idUbicacion
									INNER JOIN aud_circuitos ac ON mpd.idCircuito = ac.idCircuito
									INNER JOIN map_elementos me ON ac.idElemento = me.idElemento
									LEFT JOIN map_audiencia_planes map ON mpd.ID = map.idMapAudiencia
								WHERE mpd.ID IN ($ids)
									GROUP BY mpd.ID, me.idElemento");

	//Header
	$worksheet->write(0, 0, html_entity_decode("Descripci�n", ENT_HTML5, 'ISO-8859-1'), $header);
	$worksheet->write(0, 1, html_entity_decode("Elemento", ENT_HTML5, 'ISO-8859-1'), $header);
	$worksheet->write(0, 2, html_entity_decode("Cantidad de Ubicaciones", ENT_HTML5, 'ISO-8859-1'), $header);
	$worksheet->write(0, 3, html_entity_decode("Inversi&oacute;n", ENT_HTML5, 'ISO-8859-1'), $header);
	$worksheet->write(0, 4, html_entity_decode("Impactos", ENT_HTML5, 'ISO-8859-1'), $header);
	$worksheet->write(0, 5, html_entity_decode("PBR", ENT_HTML5, 'ISO-8859-1'), $header);
	$worksheet->write(0, 6, html_entity_decode("CPR", ENT_HTML5, 'ISO-8859-1'), $header);
	$worksheet->write(0, 7, html_entity_decode("CPM", ENT_HTML5, 'ISO-8859-1'), $header);

	$row = 1;
	while(!$rsDetalle->EOF)
	{
		$worksheet->write($row, 0, $rsDetalle->fields('descPlan'));
		$worksheet->write($row, 1, $rsDetalle->fields('descElemento'));
		$worksheet->write($row, 2, $rsDetalle->fields('canUbi'));
		$worksheet->write($row, 3, 0);
		$worksheet->write($row, 4, $rsDetalle->fields('Impactos'));
		$worksheet->write($row, 5, round($rsDetalle->fields('Pbr'),2));
		$worksheet->write($row, 6, 0);
		$worksheet->write($row++, 7, 0);

		$rsDetalle->MoveNext();
	}
	///////////////////////
	//Circuito
	//////////////////////
	$worksheet =& $workbook->addworksheet("Circuito");
	$rsDetalle = $DB->Execute("SELECT DISTINCT mp.descripcion AS descEmpresa, me.descripcion AS descElemento, ml.descripcion AS descLocalidad, COUNT(mu.idUbicacion) AS canUbi, SUM(mpd.Impactos) AS Impactos, SUM(mpd.Contactos) AS Contactos, SUM(mpd.Pbr) AS Pbr, map.descripcion AS descPlan FROM map_procesos_detalle mpd
								INNER JOIN aud_ubicaciones mu ON mpd.idUbicacion = mu.idUbicacion
								INNER JOIN aud_circuitos ac ON mpd.idCircuito = ac.idCircuito
								INNER JOIN map_elementos me ON ac.idElemento = me.idElemento
								INNER JOIN map_empresas mp ON ac.idEmpresa = mp.idEmpresa
								INNER JOIN map_localidades ml ON ml.idLocalidad = mu.idLocalidad
								LEFT JOIN map_audiencia_planes map ON mpd.ID = map.idMapAudiencia
									WHERE mpd.ID IN ($ids)
									GROUP BY mu.idEmpresa, mu.idElemento, mu.idLocalidad");

	//Header
	$worksheet->write(0, 0, html_entity_decode("Empresa", ENT_HTML5, 'ISO-8859-1'), $header);
	$worksheet->write(0, 1, html_entity_decode("Elemento", ENT_HTML5, 'ISO-8859-1'), $header);
	$worksheet->write(0, 2, html_entity_decode("Localidad", ENT_HTML5, 'ISO-8859-1'), $header);
	$worksheet->write(0, 3, html_entity_decode("Cantidad de Ubicaciones", ENT_HTML5, 'ISO-8859-1'), $header);
	$worksheet->write(0, 4, html_entity_decode("Inversi&oacute;n", ENT_HTML5, 'ISO-8859-1'), $header);
	$worksheet->write(0, 5, html_entity_decode("Impactos", ENT_HTML5, 'ISO-8859-1'), $header);
	$worksheet->write(0, 6, html_entity_decode("PBR", ENT_HTML5, 'ISO-8859-1'), $header);
	$worksheet->write(0, 7, html_entity_decode("CPR", ENT_HTML5, 'ISO-8859-1'), $header);
	$worksheet->write(0, 8, html_entity_decode("CPM", ENT_HTML5, 'ISO-8859-1'), $header);

	$row = 1;
	while(!$rsDetalle->EOF)
	{
		$worksheet->write($row, 0, $rsDetalle->fields('descEmpresa'));
		$worksheet->write($row, 1, $rsDetalle->fields('descElemento'));
		$worksheet->write($row, 2, $rsDetalle->fields('descLocalidad'));
		$worksheet->write($row, 3, $rsDetalle->fields('canUbi'));
		$worksheet->write($row, 4, 0);
		$worksheet->write($row, 5, $rsDetalle->fields('Impactos'));
		$worksheet->write($row, 6, round($rsDetalle->fields('Pbr'),2));
		$worksheet->write($row, 7, 0);
		$worksheet->write($row++, 8, 0);

		$rsDetalle->MoveNext();
	}

	$workbook->close();

	if(isset($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'],'MSIE'))
	{
		header("Pragma: public");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	}
	header("Content-Length: ".@filesize($fname));
	header("Content-Type: application/x-msexcel; name=\"".$nombreArchivo. ".xls\"");
	header("Content-Disposition: inline; filename=\"".$nombreArchivo. ".xls\"");
	$fh=fopen($fname, "rb");
	fpassthru($fh);
	unlink($fname);
}

function getAudienciasGuardadas()
{
	require("includes/constants.php");

	$DB = NewADOConnection('mysqli');
	$DB->Connect();
	$DB->Execute("SET NAMES utf8;");
	//$DB->debug=true;

	$rsAudGuardadas = $DB->Execute("SELECT * FROM aud_audiencia_planes WHERE idUsuario = ".$_SESSION['idUser']);

	if($rsAudGuardadas->EOF)
		return json_encode(array());

		$i=0;
		while(!$rsAudGuardadas->EOF){
			$arrAudGuardadas->data[$i]['idAudAudiencia'] = $rsAudGuardadas->fields('idAudAudiencia');
			$arrAudGuardadas->data[$i++]['descripcion'] = $rsAudGuardadas->fields('descripcion');
			$rsAudGuardadas->MoveNext();
		}

		return json_encode($arrAudGuardadas->data);
}

function deleteAudienciasGuardadas()
{
	require("includes/constants.php");

	$DB = NewADOConnection('mysqli');
	$DB->Connect();
	$DB->Execute("SET NAMES utf8;");
	//$DB->debug=true;

	$DB->StartTrans(); // start transaction

	$DB->Execute("DELETE FROM aud_audiencia_planes WHERE idAudAudiencia = ".$_REQUEST['idAudAudiencia']);

	if (!$DB->CompleteTrans())
	{
		$arrJSON->status = "ERROR";
		$arrJSON->msg = 'Ocurri&oacute; un error al eliminar';
	}
	else
	{
		$arrJSON->status = "OK";
		$arrJSON->msg = 'El registro se elimin&oacute; correctamente';
	}
	return json_encode($arrJSON);
}

function getDatosAudienciaGuardada()
{
	require("includes/constants.php");

	$DB = NewADOConnection('mysqli');
	$DB->Connect();
	$DB->Execute("SET NAMES utf8;");
	//$DB->debug=true;

	$idAud = $_REQUEST['idMapProcesos'];
	$joCircuitos = json_decode($_REQUEST["joCircuitos"]);

	$rsResult = $DB->Execute("SELECT * FROM map_procesos WHERE ID = $idAud");
	if($rsResult->fields('Estado') != "F") {
		$arrJSON->status = "ERROR";
		$arrJSON->msg = 'Ocurri&oacute; un error en la Evaluanci&oacute;n de la Informaci&oacute;n';

		return json_encode($arrJSON);
	}

	//Armo los JSON para el RESPONSE
	$rsCant = $DB->Execute("SELECT COUNT(*) AS canUbi FROM map_procesos_detalle WHERE ID = $idAud");

	$Edad = $rsResult->fields('Target_Edad_Desde')."-".$rsResult->fields('Target_Edad_Hasta');
	$Genero = ($rsResult->fields('Target_Sexo_Femenino') == 1 ? "F" : "").($rsResult->fields('Target_Sexo_Masculino') == 1 ? "M" : "");
	$NSE = $rsResult->fields('Target_NSE_Desde').$rsResult->fields('Target_NSE_Hasta');
	if($NSE == "AB")
		$NSE = "AMB";

	$objGeneral = "";
	$objGeneral->Target = "Edad: ".$Edad.", Sexo: ".$Genero.", NSE: ".$NSE;
	$objGeneral->Universo = $rsResult->fields('Total_Personas_Universo');
	$objGeneral->CantUbicaciones = $rsCant->fields('canUbi');
	$objGeneral->CoberturaNeta = $rsResult->fields('Cobertura');
	$objGeneral->Frecuencia = $rsResult->fields('Tasa_Repeticion');
	$objGeneral->Impactos = $rsResult->fields('Total_Personas_Muestra');
	$objGeneral->Cobertura_Porc = $rsResult->fields('Cobertura_Porc');
	$objGeneral->PBR = round($rsResult->fields('Pbr'),2);
	$objGeneral->CPR = 0;
	$objGeneral->CPM = 0;

	$objGeneral->Detallada = array();
	$objGeneral->PorEmpresa = array();
	$objGeneral->PorElemento = array();

	//Detallada
	$sSQL = "SELECT mp.descripcion AS descEmpresa, mu.direccion, me.descripcion AS descElemento, ml.descripcion AS descLocalidad, mr.descripcion AS descProvincia, mpd.*, map.descripcion AS descPlan FROM map_procesos_detalle mpd
						INNER JOIN aud_ubicaciones mu ON mpd.idUbicacion = mu.idUbicacion
						INNER JOIN aud_circuitos ac ON mpd.idCircuito = ac.idCircuito
						INNER JOIN map_elementos me ON ac.idElemento = me.idElemento
						INNER JOIN map_empresas mp ON ac.idEmpresa = mp.idEmpresa
						INNER JOIN map_localidades ml ON mu.idLocalidad = ml.idLocalidad
						INNER JOIN map_provincias mr ON mu.idProvincia = mr.idProvincia
						LEFT JOIN map_audiencia_planes map ON mpd.ID = map.idMapAudiencia
					WHERE mpd.ID = $idAud ";

	if($_SESSION['userType'] == $idTypeUser || $_SESSION['userType'] == $idTypeConsult)
		$sSQL .= "AND me.acumulaDatos = 0";

	$rsDetalle = $DB->Execute($sSQL);

	while(!$rsDetalle->EOF)
	{
		$objDetalle = "";
		$objDetalle[]= $rsDetalle->fields('descPlan');
		$objDetalle[]= $rsDetalle->fields('descEmpresa');
		$objDetalle[]= $rsDetalle->fields('direccion');
		$objDetalle[]= $rsDetalle->fields('descLocalidad');
		$objDetalle[]= $rsDetalle->fields('descProvincia');
		$objDetalle[]= $rsDetalle->fields('descElemento');
		$objDetalle[]= $rsDetalle->fields('Cobertura') == 0 ? "*" : $rsDetalle->fields('Cobertura');
		$objDetalle[]= $rsDetalle->fields('Tasa_Repeticion') == 0 ? "*" : round($rsDetalle->fields('Tasa_Repeticion'),2);
		$objDetalle[]= $rsDetalle->fields('Cobertura_Porc') == 0 ? "*" : $rsDetalle->fields('Cobertura_Porc');
		$objDetalle[]= $rsDetalle->fields('Impactos') == "0" ? "*" : $rsDetalle->fields('Impactos');
		$objDetalle[]= $rsDetalle->fields('Pbr') == 0 ? "*" : round($rsDetalle->fields('Pbr'),2);
		$objDetalle[]= 0;
		$objDetalle[]= 0;

		array_push($objGeneral->Detallada, $objDetalle);

		$rsDetalle->MoveNext();
	}

	//Empresa
	$rsDetalle = $DB->Execute("SELECT mp.descripcion AS descEmpresa, COUNT(mu.idUbicacion) AS canUbi, SUM(mpd.Impactos) AS Impactos, SUM(mpd.Contactos) AS Contactos, SUM(mpd.Pbr) AS Pbr, map.descripcion AS descPlan FROM map_procesos_detalle mpd
			INNER JOIN aud_ubicaciones mu ON mpd.idUbicacion = mu.idUbicacion
			INNER JOIN aud_circuitos ac ON mpd.idCircuito = ac.idCircuito
			INNER JOIN map_elementos me ON ac.idElemento = me.idElemento
			INNER JOIN map_empresas mp ON ac.idEmpresa = mp.idEmpresa
			LEFT JOIN map_audiencia_planes map ON mpd.ID = map.idMapAudiencia
			WHERE mpd.ID = $idAud
			GROUP BY mp.idEmpresa");

	while(!$rsDetalle->EOF)
	{
		$objEmpresa = "";
		$objEmpresa[]= $rsDetalle->fields('descPlan');
		$objEmpresa[]= $rsDetalle->fields('descEmpresa');
		$objEmpresa[]= $rsDetalle->fields('canUbi');
		$objEmpresa[]= 0;
		$objEmpresa[]= $rsDetalle->fields('Impactos');
		$objEmpresa[]= round($rsDetalle->fields('Pbr'),2);
		$objEmpresa[]= 0;
		$objEmpresa[]= 0;

		array_push($objGeneral->PorEmpresa, $objEmpresa);

		$rsDetalle->MoveNext();
	}

	//Elemento
	$rsDetalle = $DB->Execute("SELECT me.descripcion AS descElemento, COUNT(mu.idUbicacion) AS canUbi, SUM(mpd.Impactos) AS Impactos, SUM(mpd.Contactos) AS Contactos, SUM(mpd.Pbr) AS Pbr, map.descripcion AS descPlan FROM map_procesos_detalle mpd
								INNER JOIN aud_ubicaciones mu ON mpd.idUbicacion = mu.idUbicacion
								INNER JOIN aud_circuitos ac ON mpd.idCircuito = ac.idCircuito
								INNER JOIN map_elementos me ON ac.idElemento = me.idElemento
								LEFT JOIN map_audiencia_planes map ON mpd.ID = map.idMapAudiencia
								WHERE mpd.ID = $idAud
								GROUP BY me.idElemento");

	while(!$rsDetalle->EOF)
	{
		$objElemento = "";
		$objElemento[]= $rsDetalle->fields('descPlan');
		$objElemento[]= $idAud;
		$objElemento[]= $rsDetalle->fields('descElemento');
		$objElemento[]= $rsDetalle->fields('canUbi');
		$objElemento[]= 0;
		$objElemento[]= $rsDetalle->fields('Impactos');
		$objElemento[]= round($rsDetalle->fields('Pbr'),2);
		$objElemento[]= 0;
		$objElemento[]= 0;

		array_push($objGeneral->PorElemento, $objElemento);

		$rsDetalle->MoveNext();
	}

	//Circuito
	$rsDetalle = $DB->Execute("SELECT mp.descripcion AS descEmpresa, me.descripcion AS descElemento, ml.descripcion AS descLocalidad, COUNT(mu.idUbicacion) AS canUbi, SUM(mpd.Impactos) AS Impactos, SUM(mpd.Contactos) AS Contactos, SUM(mpd.Pbr) AS Pbr, map.descripcion AS descPlan FROM map_procesos_detalle mpd
			INNER JOIN aud_ubicaciones mu ON mpd.idUbicacion = mu.idUbicacion
			INNER JOIN aud_circuitos ac ON mpd.idCircuito = ac.idCircuito
			INNER JOIN map_elementos me ON ac.idElemento = me.idElemento
			INNER JOIN map_empresas mp ON ac.idEmpresa = mp.idEmpresa
			INNER JOIN map_localidades ml ON ml.idLocalidad = mu.idLocalidad
			LEFT JOIN map_audiencia_planes map ON mpd.ID = map.idMapAudiencia
			WHERE mpd.ID = $idAud
			GROUP BY mu.idEmpresa, mu.idElemento, mu.idLocalidad");

	while(!$rsDetalle->EOF)
	{
		$objCircuito = "";
		$objCircuito[] = $rsDetalle->fields('descEmpresa');
		$objCircuito[]= $rsDetalle->fields('descElemento');
		$objCircuito[]= $rsDetalle->fields('descLocalidad');
		$objCircuito[]= $rsDetalle->fields('canUbi');
		$objCircuito[]= 0;
		$objCircuito[]= $rsDetalle->fields('Impactos');
		$objCircuito[]= round($rsDetalle->fields('Pbr'),2);
		$objCircuito[]= 0;
		$objCircuito[]= 0;

		array_push($objGeneral->PorCircuito, $objCircuito);

		$rsDetalle->MoveNext();
	}

	$objGeneral->ID = $idAud;
	$objGeneral->status = "OK";

	return json_encode($objGeneral);
}

function addOrEditAudienciaPlan()
{
	require("includes/constants.php");

	$DB = NewADOConnection('mysqli');
	$DB->Connect();
	$DB->Execute("SET NAMES utf8;");
	//$DB->debug=true;

	$DB->StartTrans(); // start transaction

	$strSQL = "INSERT INTO aud_audiencia_planes ( ";
	$strSQL .= "	idAudAudiencia, ";
	$strSQL .= "	descripcion, ";
	$strSQL .= "	idUsuario ";
	$strSQL .= "	) VALUES ( ";
	$strSQL .= "	'".$_REQUEST["idAudAudiencia"]."', ";
	$strSQL .= "	'".mb_strtoupper($_REQUEST["descripcionPlan"], "UTF-8")."', ";
	$strSQL .= "	'".$_SESSION["idUser"]."' ";
	$strSQL .= "	)";

	$DB->Execute($strSQL);


	if (!$DB->CompleteTrans())
	{
		$arrJSON->status = "ERROR";
		$arrJSON->msg = 'Ocurri&oacute; un error al grabar el Plan';
	}
	else
	{
		$arrJSON->status = "OK";
		$arrJSON->msg = 'El Plan se guard&oacute; correctamente';
	}
	return json_encode($arrJSON);
}

switch($_REQUEST['actionOfForm'])
{
	case "searchCampanna":
		echo searchCampanna();
		break;

	case "searchInforme":
		echo searchInforme();
		break;

	case "searchCampannaCircuito":
		echo searchCampannaCircuito();
		break;

	case "searchCampannaCircuitoDetalle":
		echo searchCampannaCircuitoDetalle();
		break;

	case "getCampannasHab":
		echo getCampannasHab();
		break;

	case "auditoriaCampannaMarketsMap":
		echo auditoriaCampannaMarketsMap();
		break;

	case "auditoriaCampannaDetalleMarketsMap":
		echo auditoriaCampannaDetalleMarketsMap();
		break;

	case "auditoriaCampannaSemaforoMarketsMap":
		echo auditoriaCampannaSemaforoMarketsMap();
		break;

	case "marcarCircuitos":
		echo marcarCircuitos();
		break;

	case "fotosAuditoriaShow":
		echo fotosAuditoriaShow();
		break;

	case "exportXLS":
		echo exportXLS();
		break;

	case "getFotosAuditoriaCampannaZip":
		echo getFotosAuditoriaCampannaZip();
		break;

	//Audiencia
	case "searchFiltroEdad":
		echo searchFiltroEdad();
		break;

	case "searchFiltroSexo":
		echo searchFiltroSexo();
		break;

	case "searchFiltroNSE":
		echo searchFiltroNSE();
		break;

	case "searchFiltroPeriodo":
		echo searchFiltroPeriodo();
		break;

	case "evaluarAudiencia":
		echo evaluarAudiencia();
		break;

	case "getAudienciaExcel":
		echo getAudienciaExcel();
		break;

	case "getAudienciasGuardadas":
		echo getAudienciasGuardadas();
		break;

	case "deleteAudienciasGuardadas":
		echo deleteAudienciasGuardadas();
		break;

	case "getDatosAudienciaGuardada":
		echo getDatosAudienciaGuardada();
		break;

	case "addOrEditAudienciaPlan":
		echo addOrEditAudienciaPlan();
		break;
}
?>
