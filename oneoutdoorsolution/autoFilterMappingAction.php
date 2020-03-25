<?php
/**
 *  Por Federico Pisarello - SisDev Software (c) 2012 - Buenos Aires, Argentina.
 * 	fpisarello@sisdevsoft.com
 */

require("includes/funciones.inc.php");

session_start();
if(!isset($_SESSION['userName']))
	exit();

function searchAutoFilter()
{
	$sqlWhereEVP = '';

	require("includes/constants.php");
	require("includes/datatables_db/Datatables.php");

	$DB = NewADOConnection('mysqli');
	$DB->Connect();
	$DB->Execute("SET NAMES utf8;");
	//$DB->debug=true;

	$datatables = new Datatables();

	$table = $_REQUEST['table'];
	$fieldsOnly = $_REQUEST['fieldsOnly'];
	$idTable = $_REQUEST['idTable'];

	//$arrFields = explode(",", $fields);

	if($table == 'map_empresas')
		$datatables->from("(SELECT * FROM map_empresas ORDER BY orden) map_empresas");
	else
		$datatables->from($table);
	$datatables->select($idTable);
	$datatables->select($fieldsOnly);
	$datatables->edit_column($idTable, '<input type="checkbox" id="$1" name="$1" class="filterClass" onclick="$_addRemoveFilter(\'$1\')">', $idTable);

	if(!empty($_REQUEST['where'])) {
		$arrWhere = json_decode($_REQUEST['where']);
		if(!empty($arrWhere->dataWhereFilter)) {
			$datatables->where("$arrWhere->fieldFilter IN (".implode("," , $arrWhere->dataWhereFilter). ")");
		}
	}

	$joFilterUbicaciones = json_decode($_REQUEST['joFilterUbicaciones']);
	//var_dump($joFilterUbicaciones);
	$strWhereFilterUbicaciones = "";

	if($idTable != "idMedio" && array_key_exists('joMedioIds', $joFilterUbicaciones)) {
		if(count($joFilterUbicaciones->joMedioIds) > 0) {
			$strWhereFilterUbicaciones .= "idMedio IN (";
			foreach ($joFilterUbicaciones->joMedioIds as $value) {
				$strWhereFilterUbicaciones .= $value. ",";
			}
			$strWhereFilterUbicaciones = substr($strWhereFilterUbicaciones, 0, -1).") AND ";
		}
	}

	if($idTable != "idFormato" && array_key_exists('joFormatoIds', $joFilterUbicaciones)) {
		if(count($joFilterUbicaciones->joFormatoIds) > 0) {
			$strWhereFilterUbicaciones .= "idFormato IN (";
			foreach ($joFilterUbicaciones->joFormatoIds as $value) {
				$strWhereFilterUbicaciones .= $value. ",";
			}
			$strWhereFilterUbicaciones = substr($strWhereFilterUbicaciones, 0, -1).") AND ";
		}
	}

	if($idTable != "idElemento" && array_key_exists('joElementosIds', $joFilterUbicaciones)) {
		if(count($joFilterUbicaciones->joElementosIds) > 0) {
			$strWhereFilterUbicaciones .= "idElemento IN (";
			foreach ($joFilterUbicaciones->joElementosIds as $value) {
				$strWhereFilterUbicaciones .= $value. ",";
			}
			$strWhereFilterUbicaciones = substr($strWhereFilterUbicaciones, 0, -1).") AND ";
		}
	}

	if($idTable != "idEmpresa" && array_key_exists('joEVPIds', $joFilterUbicaciones)) {
		if(count($joFilterUbicaciones->joEVPIds) > 0) {
			$strWhereFilterUbicaciones .= "idEmpresa IN (";
			foreach ($joFilterUbicaciones->joEVPIds as $value) {
				$strWhereFilterUbicaciones .= $value. ",";
			}
			$strWhereFilterUbicaciones = substr($strWhereFilterUbicaciones, 0, -1).") AND ";
		}
	}

	if($idTable != "idProvincia" && array_key_exists('joProvinciaIds', $joFilterUbicaciones)) {
		if(count($joFilterUbicaciones->joProvinciaIds) > 0) {
			$strWhereFilterUbicaciones .= "idProvincia IN (";
			foreach ($joFilterUbicaciones->joProvinciaIds as $value) {
				$strWhereFilterUbicaciones .= $value. ",";
			}
			$strWhereFilterUbicaciones = substr($strWhereFilterUbicaciones, 0, -1).") AND ";
		}
	}

	if($idTable != "idLocalidad" && array_key_exists('joLocalidadIds', $joFilterUbicaciones)) {
		if(count($joFilterUbicaciones->joLocalidadIds) > 0) {
			$strWhereFilterUbicaciones .= "idLocalidad IN (";
			foreach ($joFilterUbicaciones->joLocalidadIds as $value) {
				$strWhereFilterUbicaciones .= $value. ",";
			}
			$strWhereFilterUbicaciones = substr($strWhereFilterUbicaciones, 0, -1).") AND ";
		}
	}

	if($strWhereFilterUbicaciones != "") {
		$strWhereFilterUbicaciones = substr($strWhereFilterUbicaciones, 0, -4);
		$datatables->where(" $idTable IN (SELECT $idTable FROM map_ubicaciones WHERE $strWhereFilterUbicaciones)");
	}


	//Filtro de Contrato
	if($table == 'map_empresas') {
		//Procesamos el Contrato del Usuario
		$rsContrato = $DB->Execute("SELECT * FROM contratos_mapping WHERE idContrato = ".$_SESSION['idContratoMap']);
		while(!$rsContrato->EOF){
			$sqlWhereEVP .= " (map_empresas.idEmpresa = '".$rsContrato->fields('idEVP')."') OR ";
			$rsContrato->MoveNext();
		}
		if(!empty($sqlWhereEVP))
			$datatables->where("(".substr($sqlWhereEVP,0,-3).")");
	}

	return $datatables->generate();
}

function searchEVPs()
{

	$sqlWhereEVP = '';

	require("includes/constants.php");
	require("includes/datatables_db/Datatables.php");

	$DB = NewADOConnection('mysqli');
	$DB->Connect();
	$DB->Execute("SET NAMES utf8;");
	//$DB->debug=true;

	$datatables = new Datatables();

	$table = $_REQUEST['table'];
	$fieldsOnly = $_REQUEST['fieldsOnly'];
	$idTable = $_REQUEST['idTable'];

	//$arrFields = explode(",", $fields);

	$datatables->from($table);
	$datatables->select($idTable);
	$datatables->select($idTable." empresaID");
	$datatables->select($fieldsOnly);
	$datatables->select("gpmas");
	$datatables->edit_column($idTable, '<input type="checkbox" id="$1" name="$1" class="filterClass" onclick="$_addRemoveFilter(\'$1\')">', $idTable);
	$datatables->edit_column('gpmas','$1','getGPImage(gpmas)');
	$datatables->add_column('edit', '<img src="images/details_open.png">');

	if($table == 'map_empresas')
		$datatables->select("orden");

	if(!empty($_REQUEST['where'])) {
		$arrWhere = json_decode($_REQUEST['where']);
		if(!empty($arrWhere->dataWhereFilter)) {
			$datatables->where("$arrWhere->fieldFilter IN (".implode("," , $arrWhere->dataWhereFilter). ")");
		}
	}

	$joFilterUbicaciones = json_decode($_REQUEST['joFilterUbicaciones']);
	$strWhereFilterUbicaciones = "";

	if($idTable != "idMedio" && array_key_exists('joMedioIds', $joFilterUbicaciones)) {
		if(count($joFilterUbicaciones->joMedioIds) > 0) {
			$strWhereFilterUbicaciones .= "idMedio IN (";
			foreach ($joFilterUbicaciones->joMedioIds as $value) {
				$strWhereFilterUbicaciones .= $value. ",";
			}
			$strWhereFilterUbicaciones = substr($strWhereFilterUbicaciones, 0, -1).") AND ";
		}
	}

	if($idTable != "idFormato" && array_key_exists('joFormatoIds', $joFilterUbicaciones)) {
		if(count($joFilterUbicaciones->joFormatoIds) > 0) {
			$strWhereFilterUbicaciones .= "idFormato IN (";
			foreach ($joFilterUbicaciones->joFormatoIds as $value) {
				$strWhereFilterUbicaciones .= $value. ",";
			}
			$strWhereFilterUbicaciones = substr($strWhereFilterUbicaciones, 0, -1).") AND ";
		}
	}

	if($idTable != "idElemento" && array_key_exists('joElementosIds', $joFilterUbicaciones)) {
		if(count($joFilterUbicaciones->joElementosIds) > 0) {
			$strWhereFilterUbicaciones .= "idElemento IN (";
			foreach ($joFilterUbicaciones->joElementosIds as $value) {
				$strWhereFilterUbicaciones .= $value. ",";
			}
			$strWhereFilterUbicaciones = substr($strWhereFilterUbicaciones, 0, -1).") AND ";
		}
	}

	if($idTable != "idEmpresa" && array_key_exists('joEVPIds', $joFilterUbicaciones)) {
		if(count($joFilterUbicaciones->joEVPIds) > 0) {
			$strWhereFilterUbicaciones .= "idEmpresa IN (";
			foreach ($joFilterUbicaciones->joEVPIds as $value) {
				$strWhereFilterUbicaciones .= $value. ",";
			}
			$strWhereFilterUbicaciones = substr($strWhereFilterUbicaciones, 0, -1).") AND ";
		}
	}

	if($idTable != "idProvincia" && array_key_exists('joProvinciaIds', $joFilterUbicaciones)) {
		if(count($joFilterUbicaciones->joProvinciaIds) > 0) {
			$strWhereFilterUbicaciones .= "idProvincia IN (";
			foreach ($joFilterUbicaciones->joProvinciaIds as $value) {
				$strWhereFilterUbicaciones .= $value. ",";
			}
			$strWhereFilterUbicaciones = substr($strWhereFilterUbicaciones, 0, -1).") AND ";
		}
	}

	if($idTable != "idLocalidad" && array_key_exists('joLocalidadIds', $joFilterUbicaciones)) {
		if(count($joFilterUbicaciones->joLocalidadIds) > 0) {
			$strWhereFilterUbicaciones .= "idLocalidad IN (";
			foreach ($joFilterUbicaciones->joLocalidadIds as $value) {
				$strWhereFilterUbicaciones .= $value. ",";
			}
			$strWhereFilterUbicaciones = substr($strWhereFilterUbicaciones, 0, -1).") AND ";
		}
	}

	if($strWhereFilterUbicaciones != "") {
		$strWhereFilterUbicaciones = substr($strWhereFilterUbicaciones, 0, -4);
		$datatables->where(" $idTable IN (SELECT $idTable FROM map_ubicaciones WHERE $strWhereFilterUbicaciones)");
	}


	//Filtro de Contrato
	if($table == 'map_empresas') {
		//Procesamos el Contrato del Usuario
		$rsContrato = $DB->Execute("SELECT * FROM contratos_mapping WHERE idContrato = ".$_SESSION['idContratoMap']);
		while(!$rsContrato->EOF){
			$sqlWhereEVP .= " (map_empresas.idEmpresa = '".$rsContrato->fields('idEVP')."') OR ";
			$rsContrato->MoveNext();
		}
		if(!empty($sqlWhereEVP))
			$datatables->where("(".substr($sqlWhereEVP,0,-3).")");
	}

	return $datatables->generate();
}

function getGPImage($idGPMas)
{
	switch ($idGPMas) {
		case 0:
			return '&nbsp;';
			break;
		case 1:
			return '<img src="images/GP1.png">';
			break;
		case 2:
			return '<img src="images/GP2.png">';
			break;
		default:
			return '&nbsp;';
			break;
	}
}

function searchEVPsDetalle()
{
	require("includes/constants.php");

	$DB = NewADOConnection('mysqli');
	$DB->Connect();
	$DB->Execute("SET NAMES utf8;");
	//$DB->debug=true;

	$strSQL = "SELECT html FROM map_empresas WHERE idEmpresa = '".$_REQUEST['idEmpresa']."' ";
	$rsEVPs = $DB->Execute($strSQL);

	if($rsEVPs->EOF || !$rsEVPs->fields('html'))
		$strOutput = "";
	else
		$strOutput = file_get_contents($rsEVPs->fields('html'));

	return $strOutput;
}


function searchPOIs()
{
	require("includes/constants.php");
	require("includes/datatables_db/Datatables.php");

	$datatables = new Datatables();

	$datatables->select('idSectorPoi, idSectorPoi sectorPoiId, descripcion')
			   ->from('map_pois_sector')
			   ->edit_column('idSectorPoi', '<input type="checkbox" id="$1" name="$1" class="filterClass">', 'idSectorPoi')
			   ->add_column('edit', '<img src="images/details_open.png">');

	return $datatables->generate();
}

function searchPOIsDetalle()
{
	require("includes/constants.php");

	$DB = NewADOConnection('mysqli');
	$DB->Connect();
	$DB->Execute("SET NAMES utf8;");
	//$DB->debug=true;

	$joEntidadPOIIds = explode(",", $_REQUEST["joEntidadPOIIds"]);

	$strSQL = "SELECT * FROM map_pois_entidad
					WHERE idSector = '".$_REQUEST['idSector']."' ";

	if($_REQUEST['joProvincias'] != "undefined" && $_REQUEST['joProvincias'] != "") {
		$strSQL .= " AND idEntidad IN (SELECT idEntidad FROM map_pois WHERE idProvincia IN (" . $_REQUEST['joProvincias'] . "))";
	}

	$rsPOIs = $DB->Execute($strSQL);

	$strOutput = '';

	while(!$rsPOIs->EOF){
		if(in_array($rsPOIs->fields("idEntidad"), $joEntidadPOIIds))
			$checked = "checked";
		else
			$checked = "";
		$strOutput .= '<tr><td><input onclick="$_addRemoveFilter(\''.$rsPOIs->fields("idEntidad").'\')" type="checkbox" id="'.$rsPOIs->fields("idEntidad").'" name="'.$rsPOIs->fields("idSector").'" '.$checked.'></td><td style="font-size: 12px;">'.$rsPOIs->fields("descripcion").'</td></tr>';
		$rsPOIs->MoveNext();
	}
	return $strOutput;
}

switch($_REQUEST['actionOfForm'])
{
	case "searchPOIsDetalle":
		echo searchPOIsDetalle();
		break;

	case "searchPOIs":
		echo searchPOIs();
		break;

	case "searchEVPs":
		echo searchEVPs();
		break;

	case "searchEVPsDetalle":
		echo searchEVPsDetalle();
		break;

	case "search":
		echo searchAutoFilter();
		break;
}
?>
