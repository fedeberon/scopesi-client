<?php
/**
 *  Por Federico Pisarello - SisDev Software (c) 2012 - Buenos Aires, Argentina.
 * 	fpisarello@sisdevsoft.com
 */

require("includes/funciones.inc.php");

session_start();
if(!isset($_SESSION['userName']))
	exit();

function searchInversion()
{
	require("includes/constants.php");
	require("includes/datatables_db/Datatables.php");

	$datatables = new Datatables();

	$datatables->from('inversiones');

	$emptyFields = TRUE;
	$rubroOrder = FALSE;

	if(array_key_exists('chkSectores',$_REQUEST) && $_REQUEST['chkSectores'] == 'on') {
		$datatables->select('sectores.descripcion descSector');
		$datatables->join('sectores', 'sectores.idSector = inversiones.idSector', 'inner');
		$datatables->groupby('inversiones.idSector');
		if($_REQUEST['jsonDataSectores'] != "undefined" && $_REQUEST['jsonDataSectores'] != '[]')
			$datatables->where('sectores.idSector IN ('.substr(str_replace('"', "'", $_REQUEST['jsonDataSectores']), 1, -1).')');
		$emptyFields = FALSE;
		$rubroOrder = TRUE;
	}
	else {
		$datatables->select("'' blancoSectror");
	}

	if(array_key_exists('chkRubro',$_REQUEST) && $_REQUEST['chkRubro'] == 'on') {
		$datatables->select('rubros.descripcion');
		$datatables->join('rubros', 'rubros.idRubros = inversiones.idRubro', 'inner');
		$datatables->groupby('inversiones.idRubro');
		if($_REQUEST['jsonDataRubro'] != "undefined" && $_REQUEST['jsonDataRubro'] != '[]')
			$datatables->where('rubros.idRubros IN ('.substr(str_replace('"', "'", $_REQUEST['jsonDataRubro']), 1, -1).')');
		$emptyFields = FALSE;
		$rubroOrder = TRUE;
	}
	else {
		$datatables->select("'' blancoRubro");
	}

	if(array_key_exists('chkSegmento',$_REQUEST) && $_REQUEST['chkSegmento'] == 'on') {
		$datatables->select('segmentos.descripcion descSegmento');
		$datatables->join('segmentos', 'segmentos.idSegmento = inversiones.idSegmento', 'inner');
		$datatables->groupby('inversiones.idSegmento');
		if($_REQUEST['jsonDataSegmento'] != "undefined" && $_REQUEST['jsonDataSegmento'] != '[]')
			$datatables->where('segmentos.idSegmento IN ('.substr(str_replace('"', "'", $_REQUEST['jsonDataSegmento']), 1, -1).')');
		$emptyFields = FALSE;
	}
	else {
		$datatables->select("'' blancoSegmento");
	}

	if(array_key_exists('chkMesAnno',$_REQUEST) && $_REQUEST['chkMesAnno'] == 'on') {
		if($_REQUEST['chkMesAnnoApertura'] == 'on') {
			$datatables->select('inversiones.mes');
			$datatables->select('inversiones.anio');
			$datatables->groupby('inversiones.mes');
			$datatables->groupby('inversiones.anio');
		}
		else {
			$datatables->select("'' blancoMesAnio, '' blancoAnio");
		}

		if(array_key_exists('txtMesAnnoDesde',$_REQUEST) && $_REQUEST['txtMesAnnoDesde'] != "") {
			$fechaDesde = substr($_REQUEST['txtMesAnnoDesde'], -4).substr($_REQUEST['txtMesAnnoDesde'],0 ,2)."01";
			$datatables->where('inversiones.fecha >=\''.$fechaDesde.'\'');
		}
		if(array_key_exists('txtMesAnnoHasta',$_REQUEST) && $_REQUEST['txtMesAnnoHasta'] != "") {
			$fechaHasta = substr($_REQUEST['txtMesAnnoHasta'], -4).substr($_REQUEST['txtMesAnnoHasta'],0 ,2)."31";
			$datatables->where('inversiones.fecha <=\''.$fechaHasta.'\'');
		}
		$emptyFields = FALSE;
	}
	else {
		$datatables->select("'' blancoMes, '' blancoAnio");
	}

	if(array_key_exists('chkEVP',$_REQUEST) && $_REQUEST['chkEVP'] == 'on') {
		$datatables->select('empresas.descripcion descEmpresa');
		$datatables->join('empresas', 'empresas.idEmpresa = inversiones.idEmpresa', 'inner');
		$datatables->groupby('inversiones.idEmpresa');
		if($_REQUEST['jsonDataEVP'] != "undefined" && $_REQUEST['jsonDataEVP'] != '[]')
			$datatables->where('empresas.idEmpresa IN ('.substr(str_replace('"', "'", $_REQUEST['jsonDataEVP']), 1, -1).')');
		$emptyFields = FALSE;
	}
	else {
		$datatables->select("'' blancoEVP");
	}

	if(array_key_exists('chkAnunciante',$_REQUEST) && $_REQUEST['chkAnunciante'] == 'on') {
		$datatables->select('anunciantes.descripcion descAnunciante');
		$datatables->join('anunciantes', 'anunciantes.idAnunciante = inversiones.idAnunciante', 'inner');
		if($_REQUEST['jsonDataAnunciante'] != "undefined" && $_REQUEST['jsonDataAnunciante'] != '[]')
			$datatables->where('anunciantes.idAnunciante IN ('.substr(str_replace('"', "'", $_REQUEST['jsonDataAnunciante']), 1, -1).')');
		$datatables->groupby('inversiones.idAnunciante');
		$emptyFields = FALSE;
	}
	else {
		$datatables->select("'' blancoAnunciate");
	}

	if(array_key_exists('chkProducto',$_REQUEST) && $_REQUEST['chkProducto'] == 'on') {
		$datatables->select('productos.descripcion descProducto');
		$datatables->join('productos', 'productos.idProducto = inversiones.idProducto', 'inner');
		if($_REQUEST['jsonDataProducto'] != "undefined" && $_REQUEST['jsonDataProducto'] != '[]')
			$datatables->where('productos.idProducto IN ('.substr(str_replace('"', "'", $_REQUEST['jsonDataProducto']), 1, -1).')');
		$datatables->groupby('inversiones.idProducto');
		$emptyFields = FALSE;
	}
	else {
		$datatables->select("'' blancoProducto");
	}

	if(array_key_exists('chkPeriodo',$_REQUEST) && $_REQUEST['chkPeriodo'] == 'on') {
		$datatables->select('inversiones.Periodo');
		$datatables->groupby('inversiones.Periodo');

		$emptyFields = FALSE;
	}
	else {
		$datatables->select("'' blancoPeriodo");
	}

	if(array_key_exists('chkMedio',$_REQUEST) && $_REQUEST['chkMedio'] == 'on') {
		$datatables->select('medios.descripcion descMedio');
		$datatables->join('medios', 'medios.idmedio = inversiones.idMedio', 'inner');
		if($_REQUEST['jsonDataMedio'] != "undefined" && $_REQUEST['jsonDataMedio'] != '[]')
			$datatables->where('medios.idmedio IN ('.substr(str_replace('"', "'", $_REQUEST['jsonDataMedio']), 1, -1).')');
		$datatables->groupby('inversiones.idmedio');
		$emptyFields = FALSE;
	}
	else {
		$datatables->select("'' blancoMedio");
	}

	if(array_key_exists('chkTipoDispo',$_REQUEST) && $_REQUEST['chkTipoDispo'] == 'on') {
		$datatables->select('inversiones.Dispositivo');
		$datatables->groupby('inversiones.Dispositivo');

		$emptyFields = FALSE;
	}
	else {
		$datatables->select("'' blancoTipoDispo");
	}

	if($emptyFields || !$rubroOrder) {
		unset($_GET['iSortingCols']);
		unset($_POST['iSortingCols']);
	}

	if(array_key_exists('emptyTable',$_REQUEST) && $_REQUEST['emptyTable'] == "true")
		$datatables->where(' 1=0 ');
	else {
		$DB = NewADOConnection('mysqli');
		$DB->Connect();
		$DB->Execute("SET NAMES utf8;");
		//$DB->debug=true;

		//Procesamos el Contrato del Usuario
		$rsContrato = $DB->Execute("SELECT * FROM contratos_inversion WHERE idContrato = ".$_SESSION['idContratoInv']);
		while(!$rsContrato->EOF){
			$sqlWhereRubros .= " (inversiones.idRubro = '".$rsContrato->fields('idRubro')."' AND inversiones.fecha >= '".$rsContrato->fields('fechaDesde')."01' AND inversiones.fecha <= '".$rsContrato->fields('fechaHasta')."31') OR ";
			$rsContrato->MoveNext();
		}
		$datatables->where("(".substr($sqlWhereRubros,0,-3).")");

		$datatables->select('SUM(inversiones.importe)');
	}

	return $datatables->generate();
}

function getInversionCombos()
{
	$DB = NewADOConnection('mysqli');
	$DB->Connect();
	$DB->Execute("SET NAMES utf8;");
	//$DB->debug=true;

	$rsPeriodos = $DB->Execute("SELECT DISTINCT Periodo FROM inversiones ORDER BY Periodo");

	if($rsPeriodos->EOF)
		$arrInversionCombos->periodo = "";
	else
	{
		$i=0;
		while(!$rsPeriodos->EOF){
			$arrInversionCombos->periodo[$i]['id'] = $i;
			$arrInversionCombos->periodo[$i++]['periodo'] = $rsPeriodos->fields(0);
			$rsPeriodos->MoveNext();
		}
	}

	$rsDispositivo = $DB->Execute("SELECT DISTINCT Dispositivo FROM inversiones ORDER BY Dispositivo");

	if($rsDispositivo->EOF)
		$arrInversionCombos->tipoDispo = "";
	else
	{
		$i=0;
		while(!$rsDispositivo->EOF){
			$arrInversionCombos->tipoDispo[$i]['id'] = $i;
			$arrInversionCombos->tipoDispo[$i++]['dispositivo'] = $rsDispositivo->fields(0);
			$rsDispositivo->MoveNext();
		}
	}

	return json_encode($arrInversionCombos);
}

function exportXLS()
{
	$DB = NewADOConnection('mysqli');
	$DB->Connect();
	$DB->Execute("SET NAMES utf8;");
	//$DB->debug=true;

	$nombreArchivo = "Inversion_";
	$innerJoins = "";
	$fieldsQuery = "  ";
	$groupbyQuery = "  ";
	$where = " 1=1 ";


	$arrayTituloCampos = array();
	$arrayValoresCampos = array();
	$arrayTipoDatosCampos = array();

	if(array_key_exists('chkSectores',$_REQUEST) && $_REQUEST['chkSectores'] == 'on') {
		array_push($arrayTituloCampos, "Sectores");
		array_push($arrayValoresCampos, "descSector");
		array_push($arrayTipoDatosCampos, "string");

		$fieldsQuery .= " sectores.descripcion descSector, ";
		$innerJoins .= " INNER JOIN sectores ON sectores.idSector = inversiones.idSector";
		if($_REQUEST['jsonDataSectores'] != "undefined" && $_REQUEST['jsonDataSectores'] != '[]')
			$where .= " AND sectores.idSector	 IN (".substr(str_replace('"', "'", $_REQUEST['jsonDataSectores']), 1, -1).")";
		$groupbyQuery .= " inversiones.idSector, ";
	}

	if(array_key_exists('chkRubro',$_REQUEST) && $_REQUEST['chkRubro'] == 'on') {
		array_push($arrayTituloCampos, "Rubro");
		array_push($arrayValoresCampos, "descRubro");
		array_push($arrayTipoDatosCampos, "string");

		$fieldsQuery .= " rubros.descripcion descRubro, ";
		$innerJoins .= " INNER JOIN rubros ON rubros.idRubros = inversiones.idRubro";
		if($_REQUEST['jsonDataRubro'] != "undefined" && $_REQUEST['jsonDataRubro'] != '[]')
			$where .= " AND rubros.idRubros IN (".substr(str_replace('"', "'", $_REQUEST['jsonDataRubro']), 1, -1).")";
		$groupbyQuery .= " inversiones.idRubro, ";
	}

	if(array_key_exists('chkSegmento',$_REQUEST) && $_REQUEST['chkSegmento'] == 'on') {
		array_push($arrayTituloCampos, "Segmento");
		array_push($arrayValoresCampos, "descSegmento");
		array_push($arrayTipoDatosCampos, "string");

		$fieldsQuery .= " segmentos.descripcion descSegmento, ";
		$innerJoins .= " INNER JOIN segmentos ON segmentos.idSegmento = inversiones.idSegmento";
		if($_REQUEST['jsonDataSegmento'] != "undefined" && $_REQUEST['jsonDataSegmento'] != '[]')
			$where .= " AND segmentos.idSegmento IN (".substr(str_replace('"', "'", $_REQUEST['jsonDataSegmento']), 1, -1).")";
		$groupbyQuery .= " inversiones.idSegmento, ";
	}

	if(array_key_exists('chkMesAnno',$_REQUEST) && $_REQUEST['chkMesAnno'] == 'on') {
		if(array_key_exists('chkMesAnnoApertura',$_REQUEST) && $_REQUEST['chkMesAnnoApertura'] == 'on') {
			array_push($arrayTituloCampos, "Mes", "A�o");
			array_push($arrayValoresCampos, "mes", "anio");
			array_push($arrayTipoDatosCampos, "string", "string");
			$fieldsQuery .= " inversiones.mes, inversiones.anio, ";
			$groupbyQuery .= " inversiones.mes, inversiones.anio, ";
		}

		if(array_key_exists('txtMesAnnoDesde',$_REQUEST) && $_REQUEST['txtMesAnnoDesde'] != "") {
			$where .= ' AND CONCAT(inversiones.anio, inversiones.mes) >=\''.substr($_REQUEST['txtMesAnnoDesde'], -4).substr($_REQUEST['txtMesAnnoDesde'],0 ,2).'\'';
		}

		if(array_key_exists('txtMesAnnoHasta',$_REQUEST) && $_REQUEST['txtMesAnnoHasta'] != "") {
			$where .= ' AND CONCAT(inversiones.anio, inversiones.mes) <=\''.substr($_REQUEST['txtMesAnnoHasta'], -4).substr($_REQUEST['txtMesAnnoHasta'],0 ,2).'\'';
		}
	}

	if(array_key_exists('chkEVP',$_REQUEST) && $_REQUEST['chkEVP'] == 'on') {
		array_push($arrayTituloCampos, "EVP");
		array_push($arrayValoresCampos, "descEmpresa");
		array_push($arrayTipoDatosCampos, "string");

		$fieldsQuery .= " empresas.descripcion descEmpresa, ";
		$innerJoins .= " INNER JOIN empresas ON empresas.idEmpresa = inversiones.idEmpresa";
		if($_REQUEST['jsonDataEVP'] != "undefined" && $_REQUEST['jsonDataEVP'] != '[]')
			$where .= " AND empresas.idEmpresa IN (".substr(str_replace('"', "'", $_REQUEST['jsonDataEVP']), 1, -1).")";
		$groupbyQuery .= " inversiones.idEmpresa, ";
	}

	if(array_key_exists('chkAnunciante',$_REQUEST) && $_REQUEST['chkAnunciante'] == 'on') {
		array_push($arrayTituloCampos, "Anunciante");
		array_push($arrayValoresCampos, "descAnunciante");
		array_push($arrayTipoDatosCampos, "string");

		$fieldsQuery .= " anunciantes.descripcion descAnunciante, ";
		$innerJoins .= " INNER JOIN anunciantes ON anunciantes.idAnunciante = inversiones.idAnunciante";
		if($_REQUEST['jsonDataAnunciante'] != "undefined" && $_REQUEST['jsonDataAnunciante'] != '[]')
			$where .= " AND anunciantes.idAnunciante IN (".substr(str_replace('"', "'", $_REQUEST['jsonDataAnunciante']), 1, -1).")";
		$groupbyQuery .= " inversiones.idAnunciante, ";
	}

	if(array_key_exists('chkProducto',$_REQUEST) && $_REQUEST['chkProducto'] == 'on') {
		array_push($arrayTituloCampos, "Productos");
		array_push($arrayValoresCampos, "descProducto");
		array_push($arrayTipoDatosCampos, "string");

		$fieldsQuery .= " productos.descripcion descProducto, ";
		$innerJoins .= " INNER JOIN productos ON productos.idProducto = inversiones.idProducto";
		if($_REQUEST['jsonDataProducto'] != "undefined" && $_REQUEST['jsonDataProducto'] != '[]')
			$where .= " AND productos.idProducto IN (".substr(str_replace('"', "'", $_REQUEST['jsonDataProducto']), 1, -1).")";
		$groupbyQuery .= " inversiones.idProducto, ";
	}

	if(array_key_exists('chkPeriodo',$_REQUEST) && $_REQUEST['chkPeriodo'] == 'on') {
		array_push($arrayTituloCampos, "Periodo");
		array_push($arrayValoresCampos, "Periodo");
		array_push($arrayTipoDatosCampos, "string");

		$fieldsQuery .= " inversiones.Periodo, ";
		$groupbyQuery .= " inversiones.Periodo, ";
	}

	if(array_key_exists('chkMedio',$_REQUEST) && $_REQUEST['chkMedio'] == 'on') {
		array_push($arrayTituloCampos, "Medios");
		array_push($arrayValoresCampos, "descMedio");
		array_push($arrayTipoDatosCampos, "string");

		$fieldsQuery .= " medios.descripcion descMedio, ";
		$innerJoins .= " INNER JOIN medios ON medios.idmedio = inversiones.idMedio";
		if($_REQUEST['jsonDataMedio'] != "undefined" && $_REQUEST['jsonDataMedio'] != '[]')
			$where .= " AND medios.idmedio IN (".substr(str_replace('"', "'", $_REQUEST['jsonDataMedio']), 1, -1).")";
		$groupbyQuery .= " inversiones.idMedio, ";
	}

	if(array_key_exists('chkTipoDispo',$_REQUEST) && $_REQUEST['chkTipoDispo'] == 'on') {
		array_push($arrayTituloCampos, "Dispositivo");
		array_push($arrayValoresCampos, "Dispositivo");
		array_push($arrayTipoDatosCampos, "string");

		$fieldsQuery .= " inversiones.Dispositivo, ";
		$groupbyQuery .= " inversiones.Dispositivo, ";
	}

	array_push($arrayTituloCampos, "Importe");
	array_push($arrayValoresCampos, "SumaImporte");
	array_push($arrayTipoDatosCampos, "string");
	$fieldsQuery .= "SUM(inversiones.importe) SumaImporte  ";

	$fieldsQuery = substr($fieldsQuery, 0, -2);
	$groupbyQuery = substr($groupbyQuery, 0, -2);

	//Procesamos el Contrato del Usuario
	$rsContrato = $DB->Execute("SELECT * FROM contratos_inversion WHERE idContrato = ".$_SESSION['idContratoInv']);
	if(!$rsContrato->EOF) {
		while(!$rsContrato->EOF){
			$sqlWhereRubros .= " (inversiones.idRubro = '".$rsContrato->fields('idRubro')."' AND LEFT(inversiones.fecha,6) >= '".$rsContrato->fields('fechaDesde')."' AND LEFT(inversiones.fecha,6) <= '".$rsContrato->fields('fechaHasta')."') OR ";
			$rsContrato->MoveNext();
		}
		$sqlWhereRubros = " AND (".substr($sqlWhereRubros,0,-3).")";
	}
	else {
		$sqlWhereRubros = "";
	}

	$query = "SELECT $fieldsQuery FROM inversiones $innerJoins WHERE $where $sqlWhereRubros";
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

function exportXLSAdmin()
{
	if($_SESSION['userType'] != 1)
		die();

	$DB = NewADOConnection('mysqli');
	$DB->Connect();
	$DB->Execute("SET NAMES utf8;");
	//$DB->debug=true;

	$nombreArchivo = "Inversion_";
	$innerJoins = "";
	$fieldsQuery = "  ";
	$groupbyQuery = "  ";
	$where = " 1=1 ";


	$arrayTituloCampos = array();
	$arrayValoresCampos = array();
	$arrayTipoDatosCampos = array();

	if(array_key_exists('chkSectores',$_REQUEST) && $_REQUEST['chkSectores'] == 'on') {
		array_push($arrayTituloCampos, "ID Sector", "Sectores");
		array_push($arrayValoresCampos, "idSector", "descSector");
		array_push($arrayTipoDatosCampos, "string", "string");

		$fieldsQuery .= " sectores.idSector, sectores.descripcion descSector, ";
		$innerJoins .= " INNER JOIN sectores ON sectores.idSector = inversiones.idSector";
		if($_REQUEST['jsonDataSectores'] != "undefined" && $_REQUEST['jsonDataSectores'] != '[]')
			$where .= " AND sectores.idSector	 IN (".substr(str_replace('"', "'", $_REQUEST['jsonDataSectores']), 1, -1).")";
			$groupbyQuery .= " inversiones.idSector, ";
	}

	if(array_key_exists('chkRubro',$_REQUEST) && $_REQUEST['chkRubro'] == 'on') {
		array_push($arrayTituloCampos, "ID Rubro", "Rubro");
		array_push($arrayValoresCampos, "idRubros", "descRubro");
		array_push($arrayTipoDatosCampos, "string", "string");

		$fieldsQuery .= " rubros.idRubros, rubros.descripcion descRubro, ";
		$innerJoins .= " INNER JOIN rubros ON rubros.idRubros = inversiones.idRubro";
		if($_REQUEST['jsonDataRubro'] != "undefined" && $_REQUEST['jsonDataRubro'] != '[]')
			$where .= " AND rubros.idRubros IN (".substr(str_replace('"', "'", $_REQUEST['jsonDataRubro']), 1, -1).")";
			$groupbyQuery .= " inversiones.idRubro, ";
	}

	if(array_key_exists('chkSegmento',$_REQUEST) && $_REQUEST['chkSegmento'] == 'on') {
		array_push($arrayTituloCampos, "ID Segmento", "Segmento");
		array_push($arrayValoresCampos, "idSegmento", "descSegmento");
		array_push($arrayTipoDatosCampos, "string");

		$fieldsQuery .= " segmentos.idSegmento, segmentos.descripcion descSegmento, ";
		$innerJoins .= " INNER JOIN segmentos ON segmentos.idSegmento = inversiones.idSegmento";
		if($_REQUEST['jsonDataSegmento'] != "undefined" && $_REQUEST['jsonDataSegmento'] != '[]')
			$where .= " AND segmentos.idSegmento IN (".substr(str_replace('"', "'", $_REQUEST['jsonDataSegmento']), 1, -1).")";
			$groupbyQuery .= " inversiones.idSegmento, ";
	}

	if(array_key_exists('chkMesAnno',$_REQUEST) && $_REQUEST['chkMesAnno'] == 'on') {
		if(array_key_exists('chkMesAnnoApertura',$_REQUEST) && $_REQUEST['chkMesAnnoApertura'] == 'on') {
			array_push($arrayTituloCampos, "Mes", "A�o");
			array_push($arrayValoresCampos, "mes", "anio");
			array_push($arrayTipoDatosCampos, "string", "string");
			$fieldsQuery .= " inversiones.mes, inversiones.anio, ";
			$groupbyQuery .= " inversiones.mes, inversiones.anio, ";
		}

		if(array_key_exists('txtMesAnnoDesde',$_REQUEST) && $_REQUEST['txtMesAnnoDesde'] != "") {
			$where .= ' AND CONCAT(inversiones.anio, inversiones.mes) >=\''.substr($_REQUEST['txtMesAnnoDesde'], -4).substr($_REQUEST['txtMesAnnoDesde'],0 ,2).'\'';
		}

		if(array_key_exists('txtMesAnnoHasta',$_REQUEST) && $_REQUEST['txtMesAnnoHasta'] != "") {
			$where .= ' AND CONCAT(inversiones.anio, inversiones.mes) <=\''.substr($_REQUEST['txtMesAnnoHasta'], -4).substr($_REQUEST['txtMesAnnoHasta'],0 ,2).'\'';
		}
	}

	if(array_key_exists('chkEVP',$_REQUEST) && $_REQUEST['chkEVP'] == 'on') {
		array_push($arrayTituloCampos, "ID EVP", "EVP");
		array_push($arrayValoresCampos, "idEmpresa", "descEmpresa");
		array_push($arrayTipoDatosCampos, "string", "string");

		$fieldsQuery .= " empresas.idEmpresa, empresas.descripcion descEmpresa, ";
		$innerJoins .= " INNER JOIN empresas ON empresas.idEmpresa = inversiones.idEmpresa";
		if(array_key_exists('jsonDataEVP',$_REQUEST) && $_REQUEST['jsonDataEVP'] != "undefined" && $_REQUEST['jsonDataEVP'] != '[]')
			$where .= " AND empresas.idEmpresa IN (".substr(str_replace('"', "'", $_REQUEST['jsonDataEVP']), 1, -1).")";
			$groupbyQuery .= " inversiones.idEmpresa, ";
	}

	if(array_key_exists('chkAnunciante',$_REQUEST) && $_REQUEST['chkAnunciante'] == 'on') {
		array_push($arrayTituloCampos, "ID Anunciante", "Anunciante");
		array_push($arrayValoresCampos, "idAnunciante", "descAnunciante");
		array_push($arrayTipoDatosCampos, "string", "string");

		$fieldsQuery .= " anunciantes.idAnunciante, anunciantes.descripcion descAnunciante, ";
		$innerJoins .= " INNER JOIN anunciantes ON anunciantes.idAnunciante = inversiones.idAnunciante";
		if($_REQUEST['jsonDataAnunciante'] != "undefined" && $_REQUEST['jsonDataAnunciante'] != '[]')
			$where .= " AND anunciantes.idAnunciante IN (".substr(str_replace('"', "'", $_REQUEST['jsonDataAnunciante']), 1, -1).")";
			$groupbyQuery .= " inversiones.idAnunciante, ";
	}

	if(array_key_exists('chkProducto',$_REQUEST) && $_REQUEST['chkProducto'] == 'on') {
		array_push($arrayTituloCampos, "ID Producto", "Productos");
		array_push($arrayValoresCampos, "idProducto", "descProducto");
		array_push($arrayTipoDatosCampos, "string", "string");

		$fieldsQuery .= " productos.idProducto, productos.descripcion descProducto, ";
		$innerJoins .= " INNER JOIN productos ON productos.idProducto = inversiones.idProducto";
		if($_REQUEST['jsonDataProducto'] != "undefined" && $_REQUEST['jsonDataProducto'] != '[]')
			$where .= " AND productos.idProducto IN (".substr(str_replace('"', "'", $_REQUEST['jsonDataProducto']), 1, -1).")";
			$groupbyQuery .= " inversiones.idProducto, ";
	}

	if(array_key_exists('chkPeriodo',$_REQUEST) && $_REQUEST['chkPeriodo'] == 'on') {
		array_push($arrayTituloCampos, "Periodo");
		array_push($arrayValoresCampos, "Periodo");
		array_push($arrayTipoDatosCampos, "string");

		$fieldsQuery .= " inversiones.Periodo, ";
		$groupbyQuery .= " inversiones.Periodo, ";
	}

	if(array_key_exists('chkMedio',$_REQUEST) && $_REQUEST['chkMedio'] == 'on') {
		array_push($arrayTituloCampos, "ID Medio", "Medios");
		array_push($arrayValoresCampos, "idmedio", "descMedio");
		array_push($arrayTipoDatosCampos, "string", "string");

		$fieldsQuery .= " medios.idmedio, medios.descripcion descMedio, ";
		$innerJoins .= " INNER JOIN medios ON medios.idmedio = inversiones.idMedio";
		if($_REQUEST['jsonDataMedio'] != "undefined" && $_REQUEST['jsonDataMedio'] != '[]')
			$where .= " AND medios.idmedio IN (".substr(str_replace('"', "'", $_REQUEST['jsonDataMedio']), 1, -1).")";
			$groupbyQuery .= " inversiones.idMedio, ";
	}

	if(array_key_exists('chkTipoDispo',$_REQUEST) && $_REQUEST['chkTipoDispo'] == 'on') {
		array_push($arrayTituloCampos, "Dispositivo");
		array_push($arrayValoresCampos, "Dispositivo");
		array_push($arrayTipoDatosCampos, "string");

		$fieldsQuery .= " inversiones.Dispositivo, ";
		$groupbyQuery .= " inversiones.Dispositivo, ";
	}

	array_push($arrayTituloCampos, "Importe");
	array_push($arrayValoresCampos, "SumaImporte");
	array_push($arrayTipoDatosCampos, "string");
	$fieldsQuery .= "SUM(inversiones.importe) SumaImporte  ";

	$fieldsQuery = substr($fieldsQuery, 0, -2);
	$groupbyQuery = substr($groupbyQuery, 0, -2);

	//Procesamos el Contrato del Usuario
	$rsContrato = $DB->Execute("SELECT * FROM contratos_inversion WHERE idContrato = ".$_SESSION['idContratoInv']);
	if(!$rsContrato->EOF) {
		while(!$rsContrato->EOF){
			$sqlWhereRubros .= " (inversiones.idRubro = '".$rsContrato->fields('idRubro')."' AND LEFT(inversiones.fecha,6) >= '".$rsContrato->fields('fechaDesde')."' AND LEFT(inversiones.fecha,6) <= '".$rsContrato->fields('fechaHasta')."') OR ";
			$rsContrato->MoveNext();
		}
		$sqlWhereRubros = " AND (".substr($sqlWhereRubros,0,-3).")";
	}
	else {
		$sqlWhereRubros = "";
	}

	$query = "SELECT $fieldsQuery FROM inversiones $innerJoins WHERE $where $sqlWhereRubros";
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

function creatividadesShow()
{
	$includedExtensions = array ('jpg', 'gif', 'png');

	$anno = trim(substr($_REQUEST['txtMesAnnoCreatividad'], -4));
	$mes = trim(substr($_REQUEST['txtMesAnnoCreatividad'], 0, 2));
	$rubro = trim($_REQUEST['cmbRubroCreatividad']);

	//Verifico el Contrato
	$DB = NewADOConnection('mysqli');
	$DB->Connect();
	$DB->Execute("SET NAMES utf8;");
	//$DB->debug=true;

	$strSQL = "SELECT idRubro FROM contratos_inversion WHERE idContrato = ".$_SESSION['idContratoInv']." AND idRubro = '$rubro' AND fechaDesde <= '$anno$mes' AND fechaHasta >= '$anno$mes'";
	$rsRubrosHab = $DB->Execute($strSQL);

	if($rsRubrosHab->EOF)
		return json_encode(array());

	$dirImages = 'images/rubros/'.$anno.'/'.$mes.'/'.$rubro;
	if(file_exists($dirImages)) {
		if ($filesImgRubros = opendir($dirImages)) {
			while (false !== ($image = readdir($filesImgRubros))) {
				if($image != "." && $image != "..") {
					$extn = explode('.', $image);
		 		    $extn = array_pop($extn);
					if (in_array(strtolower($extn),$includedExtensions)) {
						$arrImages[] = array("image" => $dirImages."/".$image);
						$i++;
					}
				}
	    	}
			usort($arrImages, "cmpFileNameImages");
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

function getFotosInversionZip()
{
$includedExtensions = array ('jpg', 'gif', 'png');

	$anno = trim(substr($_REQUEST['txtMesAnnoCreatividad'], -4));
	$mes = trim(substr($_REQUEST['txtMesAnnoCreatividad'], 0, 2));
	$rubro = trim($_REQUEST['cmbRubroCreatividad']);

	//Verifico el Contrato
	$DB = NewADOConnection('mysqli');
	$DB->Connect();
	$DB->Execute("SET NAMES utf8;");
	//$DB->debug=true;

	$strSQL = "SELECT idRubro FROM contratos_inversion WHERE idContrato = ".$_SESSION['idContratoInv']." AND idRubro = '$rubro' AND fechaDesde <= '$anno$mes' AND fechaHasta >= '$anno$mes'";
	$rsRubrosHab = $DB->Execute($strSQL);

	if($rsRubrosHab->EOF)
		return json_encode(array());

	$dirImages = 'images/rubros/'.$anno.'/'.$mes.'/'.$rubro;
	if(file_exists($dirImages)) {
		if ($filesImgRubros = opendir($dirImages)) {
			while (false !== ($image = readdir($filesImgRubros))) {
				if($image != "." && $image != "..") {
					$extn = explode('.', $image);
		 		    $extn = array_pop($extn);
					if (in_array(strtolower($extn),$includedExtensions)) {
						$arrImages[] = array($dirImages."/".$image);
						$i++;
					}
				}
	    	}
		}
	}


	$fileName = $anno.'-'.$mes.'-'.$rubro .'.zip';

	$result = create_zip($arrImages, $dirImages . "/" . $fileName, true);

	if(!$result)
		die();

	//$fileName = "MAPPING_FILTER_". date('Ymd') .".txt";
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

function cmpFileNameImages($fileNameImages1, $fileNameImages2)
{
     if($fileNameImages1["image"]==$fileNameImages2["image"])
          return 0;
     if($fileNameImages1["image"]<$fileNameImages2["image"])
          return -1;
     return 1;
}


function getRubrosHab()
{
	$DB = NewADOConnection('mysqli');
	$DB->Connect();
	$DB->Execute("SET NAMES utf8;");
	//$DB->debug=true;

	$strSQL = "SELECT * FROM rubros WHERE idRubros IN (SELECT idRubro FROM contratos_inversion WHERE idContrato = ".$_SESSION['idContratoInv'].")";
	$rsRubrosHab = $DB->Execute($strSQL);

	$i=0;
	while(!$rsRubrosHab->EOF){
		$arrRubrosHab->data[$i++] = $rsRubrosHab->fields(0);
		$rsRubrosHab->MoveNext();
	}

	return json_encode($arrRubrosHab->data);
}

function getRubrosHabCompleto()
{
	$DB = NewADOConnection('mysqli');
	$DB->Connect();
	$DB->Execute("SET NAMES utf8;");
	//$DB->debug=true;

	$strSQL = "SELECT DISTINCT r.* FROM rubros r
				INNER JOIN contratos_inversion ci ON r.idRubros = ci.idRubro
				WHERE creatividades = 'S' AND
					  idRubros IN
					    (
					  		SELECT idRubro FROM contratos_inversion
					  			WHERE idContrato = ".$_SESSION['idContratoInv']."
						)";

	$rsRubrosHab = $DB->Execute($strSQL);

	$i=0;
	while(!$rsRubrosHab->EOF){
		$arrRubrosHab->data[$i]['idRubros'] = $rsRubrosHab->fields(0);
		$arrRubrosHab->data[$i++]['descripcion'] = $rsRubrosHab->fields(1);
		$rsRubrosHab->MoveNext();
	}

	return json_encode($arrRubrosHab->data);
}

function grabarFiltro()
{
	$mcrypt = new MCrypt();

	$fileName = "filter_files/INVERSION_FILTER_". date('Ymd') .".txt";
	file_put_contents($fileName, $mcrypt->encrypt($_REQUEST['joFilterInversion']));

	//$fileName = "MAPPING_FILTER_". date('Ymd') .".txt";
	if(isset($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'],'MSIE'))
	{
		header("Pragma: no-cache");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Expires: 0");
	}
	header("Content-Transfer-Encoding: binary");
	header("Content-Length: ".@filesize($fileName));
	header("Content-Type: plain/text; name=\"".$fileName."\"");
	header("Content-Disposition: attachment; filename=\""."INVERSION_FILTER_". date('Ymd') .".txt"."\"");
	$fh=fopen($fileName, "rb");
	fpassthru($fh);
	unlink($fileName);
}

function cargarFiltro()
{
	try
	{
		$mcrypt = new MCrypt();

		$fileName = "filter_files/".$_REQUEST['fileName'];
		$joFilterInversion= $mcrypt->decrypt(file_get_contents($fileName, FILE_USE_INCLUDE_PATH));

		$arrJSON->status = "OK";
		$arrJSON->msg = 'Se Cargo el Filtro del Archivo';
		$arrJSON->joFilterInversion = $joFilterInversion;
	}
	catch(Exception $e)
	{
		$arrJSON->status = "ERR";
		$arrJSON->msg = 'Se Produjo un Error al cargar el Filtro del Archivo';
	}

	return json_encode($arrJSON);
}


switch($_REQUEST['actionOfForm'])
{
	case "search":
		echo searchInversion();
		break;

	case "exportXLS":
		echo exportXLS();
		break;

	case "exportXLSAdmin":
		echo exportXLSAdmin();
		break;

	case "creatividadesShow":
		echo creatividadesShow();
		break;

	case "getFotosInversionZip":
		echo getFotosInversionZip();
		break;

	case "getInversionCombos":
		echo getInversionCombos();
		break;

	case "getRubrosHab":
		echo getRubrosHab();
		break;

	case "getRubrosHabCompleto":
		echo getRubrosHabCompleto();
		break;

	case "cargarFiltro":
		echo cargarFiltro();
		break;

	case "grabarFiltro":
		echo grabarFiltro();
		break;
}
?>
