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

	$datatables

			->select('idRubros, idRubros idRubro, descripcion')
			->from('rubros')
			->join('contratos_inversion', 'rubros.idRubros = contratos_inversion.idRubro and contratos_inversion.idContrato = '.$_REQUEST['idContrato'], 'left')
			->select('fechaDesde, fechaHasta, creatividades')
			->edit_column('fechaDesde', '<input type="text" class="gridInputDateFrom">', 'fechaDesde')
			->edit_column('fechaHasta', '<input type="text" class="gridInputDateTo">', 'fechaHasta')
			->edit_column('idRubros', '<input type="checkbox" id="$1" name="$1" value=$2 class="chkClass">', 'idRubro, idRubros')
			->edit_column('creatividades', '<input type="checkbox" id="$1" name="$1" class="chkCreatividades">', 'idRubro, creatividades');

	return $datatables->generate();
}

function searchAuditoria()
{
	require("includes/constants.php");
	require("includes/datatables_db/Datatables.php");

	$datatables = new Datatables();

	$datatables
			->select('idCampania, idCampania idCampanna, descripcion, fecha_Inicio')
			->from('aud_campanias')
			->edit_column('idCampania', '<input type="checkbox" id="$1" name="$1" value=$1 class="chkClass">', 'idCampania');

	return $datatables->generate();
}

function searchMapping()
{
	require("includes/constants.php");
	require("includes/datatables_db/Datatables.php");

	$datatables = new Datatables();

	$datatables
	->select('idEmpresa, idEmpresa idEVP, descripcion')
	->from('map_empresas')
	->edit_column('idEmpresa', '<input type="checkbox" id="$1" name="$1" value=$1 class="chkClass">', 'idEmpresa');

	return $datatables->generate();
}

function searchABMcontratos()
{

	require("includes/constants.php");
	require("includes/datatables_db/Datatables.php");

	$datatables = new Datatables();

	$datatables
			->select('idContrato, descripcion, tipoContrato')
			->edit_column('tipoContrato', '$1', 'devuelveTipoContrato(tipoContrato)') // php functions
			->from('contratos')
			->where('estado <>', $stateErase)
			->add_column('contrato', '<a id="btnDetalle" href="javascript:;" class="btnAccGrid btnAccA" onclick="$_detalleProxyShow($1)">Detalle</a>', 'idContrato');

	return $datatables->generate();
}

function devuelveTipoContrato($tipoContrato)
{
	require("includes/constants.php");

	if($tipoContrato == $contratoAuditoria)
		return 'Auditoria';
	else if($tipoContrato == $contratoInversion)
		return 'Inversi&oacute;n';
	else if($tipoContrato == $contratoMapping)
		return 'Mapping';
}

function addOrEdit()
{
	require("includes/constants.php");

	$DB = NewADOConnection('mysqli');
	$DB->Connect();
	$DB->Execute("SET NAMES utf8;");
	//$DB->debug=true;

	$DB->StartTrans(); // start transaction

	if(!isset($_REQUEST["idContrato"])){
		$strSQL = "INSERT INTO contratos ( ";
		$strSQL .= "	descripcion, ";
		$strSQL .= "	estado, ";
		$strSQL .= "	tipoContrato, ";
		$strSQL .= "	observacion ";
		$strSQL .= "	) VALUES ( ";
		$strSQL .= "	'".mb_strtoupper($_REQUEST["descripcion"], "UTF-8")."', ";
		$strSQL .= "	'".$stateAdd."', ";
		$strSQL .= "	'".$_REQUEST["cmbTipo"]."', ";
		$strSQL .= "	'".$_REQUEST["observacion"]."' ";
		$strSQL .= "	)";

		$DB->Execute($strSQL);
	}
	else{
		$strSQL = "UPDATE contratos SET ";
		$strSQL .= "	descripcion = '".mb_strtoupper($_REQUEST["descripcion"], "UTF-8")."', ";
		$strSQL .= "	estado = '".$stateModify."',";
		$strSQL .= "	tipoContrato = '".$_REQUEST["cmbTipo"]."',";
		$strSQL .= "	observacion = '".$_REQUEST["cmbProducto"]."'";
		$strSQL .= " WHERE idContrato = ". $_REQUEST["idContrato"];

		$DB->Execute($strSQL);
	}

	if (!$DB->CompleteTrans())
	{
		$arrJSON->status = "ERROR";
		$arrJSON->msg = 'Ocurri&oacute; un error al grabar el archivo';
	}
	else
	{
		$arrJSON->status = "OK";
		$arrJSON->msg = 'El registro se guard&oacute; correctamente';
	}
	return json_encode($arrJSON);
}

function del()
{
	require("includes/constants.php");

	$DB = NewADOConnection('mysqli');
	$DB->Connect();
	$DB->Execute("SET NAMES utf8;");
	//$DB->debug=true;

	$DB->StartTrans(); // start transaction
	$DB->Execute("UPDATE contratos SET estado='$stateErase' where idContrato=" . $_REQUEST["idContrato"]); // execute query

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

function editContrato()
{
	require("includes/constants.php");

	$DB = NewADOConnection('mysqli');
	$DB->Connect();
	$DB->Execute("SET NAMES utf8;");
	//$DB->debug=true;

	$rsContrato = $DB->Execute("SELECT * FROM contratos WHERE estado <> '$stateErase' and idContrato=" . $_REQUEST["idContrato"]); // execute query

	if(!$rsContrato->EOF){
		$joContrato->data['descripcion'] = $rsContrato->fields("descripcion");
		$joContrato->data['tipoContrato'] = $rsContrato->fields("tipoContrato");
		$joContrato->data['observacion'] = $rsContrato->fields("observacion");

		return json_encode($joContrato->data);
	}
	else
		return json_encode(array());
}

function saveContratosItemsInversion()
{
	$DB = NewADOConnection('mysqli');
	$DB->Connect();
	$DB->Execute("SET NAMES utf8;");
	//$DB->debug=true;

	$idContrato = $_REQUEST["idContrato"];
	$jaItems = json_decode($_REQUEST["joDataContratosItems"], true);

	$DB->StartTrans(); // start transaction

	$DB->Execute("DELETE FROM contratos_inversion WHERE idContrato = $idContrato");

	foreach ($jaItems as $obj) {
		$strSQL = "INSERT INTO contratos_inversion ( ";
		$strSQL .= "	idContrato, ";
		$strSQL .= "	idRubro, ";
		$strSQL .= "	fechaDesde, ";
		$strSQL .= "	fechaHasta, ";
		$strSQL .= "	creatividades ";
		$strSQL .= "	) VALUES ( ";
		$strSQL .= "	".$idContrato.", ";
		$strSQL .= "	'".$obj['idRubro']."', ";
		$strSQL .= "	'".substr($obj['fechaDesde'], -4).substr($obj['fechaDesde'], 0, 2)."', ";
		$strSQL .= "	'".substr($obj['fechaHasta'], -4).substr($obj['fechaHasta'], 0, 2)."', ";
		if($obj['creatividades'])
			$strSQL .= "	'S' ";
		else
			$strSQL .= "	'N' ";
		$strSQL .= "	)";

		$DB->Execute($strSQL);
	}

	if (!$DB->CompleteTrans())
	{
		$arrJSON->status = "ERROR";
		$arrJSON->msg = 'Ocurri&oacute; un error al grabar Los Rubros del Contrato';
	}
	else
	{
		$arrJSON->status = "OK";
		$arrJSON->msg = 'Los Rubros del Contrato se guard&oacute; correctamente';
	}
	return json_encode($arrJSON);
}

function saveContratosItemsAuditoria()
{
	$DB = NewADOConnection('mysqli');
	$DB->Connect();
	$DB->Execute("SET NAMES utf8;");
	//$DB->debug=true;

	$idContrato = $_REQUEST["idContrato"];
	$jaItems = json_decode($_REQUEST["joDataContratosItems"], true);

	$DB->StartTrans(); // start transaction

	$DB->Execute("DELETE FROM contratos_auditoria WHERE idContrato = $idContrato");

	foreach ($jaItems as $obj) {
		$strSQL = "INSERT INTO contratos_auditoria ( ";
		$strSQL .= "	idContrato, ";
		$strSQL .= "	idCampanna ";
		$strSQL .= "	) VALUES ( ";
		$strSQL .= "	".$idContrato.", ";
		$strSQL .= "	'".$obj['idCampanna']."' ";
		$strSQL .= "	)";

		$DB->Execute($strSQL);
	}

	if (!$DB->CompleteTrans())
	{
		$arrJSON->status = "ERROR";
		$arrJSON->msg = 'Ocurri&oacute; un error al grabar Las Campa&ntilde;a del Contrato';
	}
	else
	{
		$arrJSON->status = "OK";
		$arrJSON->msg = 'Las Campa&ntilde;a del Contrato se guard&oacute; correctamente';
	}
	return json_encode($arrJSON);
}

function saveContratosItemsMapping()
{
	$DB = NewADOConnection('mysqli');
	$DB->Connect();
	$DB->Execute("SET NAMES utf8;");
	//$DB->debug=true;

	$idContrato = $_REQUEST["idContrato"];
	$jaItems = json_decode($_REQUEST["joDataContratosItems"], true);

	$DB->StartTrans(); // start transaction

	$DB->Execute("DELETE FROM contratos_mapping WHERE idContrato = $idContrato");

	foreach ($jaItems as $obj) {
		$strSQL = "INSERT INTO contratos_mapping ( ";
		$strSQL .= "	idContrato, ";
		$strSQL .= "	idEVP ";
		$strSQL .= "	) VALUES ( ";
		$strSQL .= "	".$idContrato.", ";
		$strSQL .= "	'".$obj['idEVP']."' ";
		$strSQL .= "	)";

		$DB->Execute($strSQL);
	}

	if (!$DB->CompleteTrans())
	{
		$arrJSON->status = "ERROR";
		$arrJSON->msg = 'Ocurri&oacute; un error al grabar Las EVP del Contrato';
	}
	else
	{
		$arrJSON->status = "OK";
		$arrJSON->msg = 'Las EVP del Contrato se guard&oacute; correctamente';
	}
	return json_encode($arrJSON);
}

function getContratoInversion()
{
	$DB = NewADOConnection('mysqli');
	$DB->Connect();
	$DB->Execute("SET NAMES utf8;");
	//$DB->debug=true;

	$idContrato = $_REQUEST["idContrato"];
	$rsContratoInversion = $DB->Execute("SELECT * FROM contratos_inversion WHERE idContrato = $idContrato");

	$i=0;
	while(!$rsContratoInversion->EOF){
		$arrContratoInversion->data[$i]['idRubro'] = $rsContratoInversion->fields('idRubro');
		$arrContratoInversion->data[$i]['fechaDesde'] = substr($rsContratoInversion->fields('fechaDesde'), -2)."/".substr($rsContratoInversion->fields('fechaDesde'), 0, 4);
		$arrContratoInversion->data[$i]['fechaHasta'] = substr($rsContratoInversion->fields('fechaHasta'), -2)."/".substr($rsContratoInversion->fields('fechaHasta'), 0, 4);
		$arrContratoInversion->data[$i++]['creatividades'] = $rsContratoInversion->fields('creatividades') == 'S' ? true : false;
		$rsContratoInversion->MoveNext();
	}

	return json_encode($arrContratoInversion->data);
}

function getContratoAuditoria()
{
	$DB = NewADOConnection('mysqli');
	$DB->Connect();
	$DB->Execute("SET NAMES utf8;");
	//$DB->debug=true;

	$idContrato = $_REQUEST["idContrato"];
	$rsContratoAuditoria = $DB->Execute("SELECT * FROM contratos_auditoria WHERE idContrato = $idContrato");

	$i=0;
	while(!$rsContratoAuditoria->EOF){
		$arrContratoAuditoria->data[$i]['idContrato'] = $rsContratoAuditoria->fields('idContrato');
		$arrContratoAuditoria->data[$i++]['idCampanna'] = $rsContratoAuditoria->fields('idCampanna');
		$rsContratoAuditoria->MoveNext();
	}

	return json_encode($arrContratoAuditoria->data);
}

function getContratoMapping()
{
	$DB = NewADOConnection('mysqli');
	$DB->Connect();
	$DB->Execute("SET NAMES utf8;");
	//$DB->debug=true;

	$idContrato = $_REQUEST["idContrato"];
	$rsContratoMapping = $DB->Execute("SELECT * FROM contratos_mapping WHERE idContrato = $idContrato");

	$i=0;
	while(!$rsContratoMapping->EOF){
		$arrContratoMapping->data[$i]['idContrato'] = $rsContratoMapping->fields('idContrato');
		$arrContratoMapping->data[$i++]['idEVP'] = $rsContratoMapping->fields('idEVP');
		$rsContratoMapping->MoveNext();
	}

	return json_encode($arrContratoMapping->data);
}

function getTipoContrato()
{
	$DB = NewADOConnection('mysqli');
	$DB->Connect();
	$DB->Execute("SET NAMES utf8;");
	//$DB->debug=true;

	$arrJSON = new stdClass;

	$rsContratos = $DB->Execute("SELECT * FROM contratos WHERE idContrato = ".$_REQUEST["idContrato"]);
	if(!$rsContratos->EOF)
		$arrJSON->tipo = $rsContratos->fields('tipoContrato');
	else
		$arrJSON->tipo = '0';

	return json_encode($arrJSON);

}

function getContratos()
{
	$DB = NewADOConnection('mysqli');
	$DB->Connect();
	$DB->Execute("SET NAMES utf8;");
	//$DB->debug=true;

	$strSQL = "SELECT * FROM contratos
				WHERE tipoContrato =
					(SELECT tipoContrato FROM contratos WHERE idContrato = ".$_REQUEST["idContrato"]. ")
					AND idContrato <> ".$_REQUEST["idContrato"];

	$rsContratos = $DB->Execute($strSQL);

	$i=0;
	while(!$rsContratos->EOF){
		$arrContratos->data[$i]['idContrato'] = $rsContratos->fields('idContrato');
		$arrContratos->data[$i++]['descripcion'] = $rsContratos->fields('descripcion');
		$rsContratos->MoveNext();
	}

	return json_encode($arrContratos->data);
}

function copiarContrato()
{
	require("includes/constants.php");

	$DB = NewADOConnection('mysqli');
	$DB->Connect();
	$DB->Execute("SET NAMES utf8;");
	//$DB->debug=true;

	$idContratoDesde = $_REQUEST['idContratoDesde'];
	$idContrato = $_REQUEST['idContrato'];

	$DB->StartTrans(); // start transaction

	$rsContratos = $DB->Execute("SELECT * FROM contratos WHERE idContrato = ".$_REQUEST["idContrato"]);
	if($rsContratos->EOF) {
		$arrJSON->status = "ERROR";
		$arrJSON->msg = 'Ocurri&oacute; un error al grabar Las Campa&ntilde;a del Contrato';
		return json_encode($arrJSON);
	}
	else {
		switch($rsContratos->fields('tipoContrato'))
		{
			case $contratoInversion:
				$DB->Execute("DELETE FROM contratos_inversion WHERE idContrato = $idContrato");
				$strSQL = "INSERT INTO contratos_inversion (idContrato, idRubro, fechaDesde, fechaHasta, creatividades) SELECT $idContrato, idRubro, fechaDesde, fechaHasta, creatividades FROM contratos_inversion WHERE idContrato = $idContratoDesde";
				break;
			case $contratoAuditoria:
				$DB->Execute("DELETE FROM contratos_auditoria WHERE idContrato = $idContrato");
				$strSQL = "INSERT INTO contratos_auditoria (idContrato, idCampanna) SELECT $idContrato, idCampanna FROM contratos_auditoria WHERE idContrato = $idContratoDesde";
				break;
			case $contratoMapping:
				$arrJSON->status = "ERROR";
				$arrJSON->msg = 'Funcionalidad no Implementada';
				return json_encode($arrJSON);
				break;
		}

		$DB->Execute($strSQL);

		if (!$DB->CompleteTrans())
		{
			$arrJSON->status = "ERROR";
			$arrJSON->msg = 'Ocurri&oacute; un error al grabar Las Campa&ntilde;a del Contrato';
		}
		else
		{
			$arrJSON->status = "OK";
			$arrJSON->msg = 'Las Campa&ntilde;a del Contrato se guard&oacute; correctamente';
		}

		return json_encode($arrJSON);
	}
}

switch($_REQUEST['actionOfForm'])
{
	case "EDIT":
		echo editContrato();
		break;
	case "DELETE":
		echo del();
		break;
	case "search":
		echo searchABMcontratos();
		break;
	case "addOrEdit":
		echo addOrEdit();
		break;
	case "searchInversion":
		echo searchInversion();
		break;
	case "searchAuditoria":
		echo searchAuditoria();
		break;
	case "searchMapping":
		echo searchMapping();
		break;
	case "saveContratosItemsInversion":
		echo saveContratosItemsInversion();
		break;
	case "saveContratosItemsAuditoria":
		echo saveContratosItemsAuditoria();
		break;
	case "saveContratosItemsMapping":
		echo saveContratosItemsMapping();
		break;
	case "getContratoInversion":
		echo getContratoInversion();
		break;
	case "getContratoAuditoria":
		echo getContratoAuditoria();
		break;
	case "getContratoMapping":
		echo getContratoMapping();
		break;
	case "getTipoContrato":
		echo getTipoContrato();
		break;
	case "getContratos":
		echo getContratos();
		break;
	case "copiarContrato":
		echo copiarContrato();
		break;
}
?>
