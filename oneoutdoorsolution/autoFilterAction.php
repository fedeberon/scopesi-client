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
	require("includes/constants.php");
	require("includes/datatables_db/Datatables.php");
	
	$DB = NewADOConnection('mysqlt');
	$DB->Connect();
	$DB->Execute("SET NAMES utf8;");
	//$DB->debug=true;
	
	$datatables = new Datatables();
	
	$table = $_REQUEST['table'];
	$fieldsOnly = $_REQUEST['fieldsOnly'];
	$idTable = $_REQUEST['idTable'];
	
	$arrFields = explode(",", $fields);
	
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
	
	//Filtro de Contrato y Fecha para Campannas
	if($table == 'aud_campanias') {
		//Procesamos el Contrato del Usuario
		$rsContrato = $DB->Execute("SELECT * FROM contratos_auditoria WHERE idContrato = ".$_SESSION['idContratoAud']);
		while(!$rsContrato->EOF){
			$sqlWhereCampannas .= " (aud_campanias.idCampania = '".$rsContrato->fields('idCampanna')."') OR ";
			$rsContrato->MoveNext();
		}
		if(!empty($sqlWhereCampannas))
			$datatables->where("(".substr($sqlWhereCampannas,0,-3).")");
	}
	
	if(!empty($_REQUEST['fecha'])) {
		$arrFecha = json_decode($_REQUEST['fecha']);
		if(!empty($arrFecha->fechaDesde)) {
			$mesAnnoDesde = substr($arrFecha->fechaDesde, -4) . '-' . substr($arrFecha->fechaDesde,0 ,2) . '-' . '01';
			$datatables->where('aud_campanias.fecha_Inicio >=\''.$mesAnnoDesde.'\'');
		}
		if(!empty($arrFecha->fechaHasta)) {
			$mesAnnoHasta = substr($arrFecha->fechaHasta, -4) . '-' . substr($arrFecha->fechaHasta,0 ,2) . '-' . '01';
			$datatables->where('aud_campanias.fecha_Inicio <=\''.$mesAnnoHasta.'\'');
		}
	}
			
	return $datatables->generate();
}

switch($_REQUEST['actionOfForm'])
{
	case "search":
		echo searchAutoFilter();
		break;
}
?>
