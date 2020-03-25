<?php
/**
 *  Por Federico Pisarello - SisDev Software (c) 2012 - Buenos Aires, Argentina.
 * 	fpisarello@sisdevsoft.com
 */

require("includes/funciones.inc.php");

session_start();

function searchABMUsuarios()
{

	require("includes/constants.php");
	require("includes/datatables_db/Datatables.php");

	$datatables = new Datatables();
	
	$datatables
			->select('idUsuario, usuario, nombreCompleto, telefono, eMail')
			->from('usuarios')
			->where('estado <>', $stateErase)
			->join('tipo_usuarios', 'usuarios.idTipoUsuario = tipo_usuarios.idTipoUsuario', 'inner')
			->select('descripcion, cargo');

	return $datatables->generate();
}

function addOrEdit()
{
	require("includes/constants.php");

	$DB = NewADOConnection('mysqli');
	$DB->Connect();
	$DB->Execute("SET NAMES utf8;");
	//$DB->debug=true;

	$DB->StartTrans(); // start transaction

	if(!isset($_REQUEST["idUsuario"])){
		$strSQL = "SELECT usuario FROM usuarios WHERE usuario = '".$_REQUEST["usuario"]."' AND estado <> '$stateErase'";
		$rsUsers = $DB->Execute($strSQL);

		if(!$rsUsers->EOF)
		{
			$arrJSON->status = "ERROR";
			$arrJSON->msg = 'El registro ingresado ya existe';
			return json_encode($arrJSON);
		}

		$strSQL = "INSERT INTO usuarios ( ";
		$strSQL .= "	usuario, ";
		$strSQL .= "	password, ";
		$strSQL .= "	nombreCompleto, ";
		$strSQL .= "	eMail, ";
		$strSQL .= "	telefono, ";
		$strSQL .= "	estado, ";
		$strSQL .= "	cargo, ";
		$strSQL .= "	idTipoUsuario, ";
		$strSQL .= "	idAnunciante, ";
		$strSQL .= "	idProducto, ";
		$strSQL .= "	idContratoInv, ";
		$strSQL .= "	idContratoAud, ";
		$strSQL .= "	idContratoMap ";
		$strSQL .= "	) VALUES ( ";
		$strSQL .= "	'".mb_strtoupper($_REQUEST["usuario"], "UTF-8")."', ";
		$strSQL .= "	'".fnEncrypt($_REQUEST["password"])."', ";
		$strSQL .= "	'".mb_strtoupper($_REQUEST["nombreCompleto"], "UTF-8")."', ";
		$strSQL .= "	'".$_REQUEST["eMail"]."', ";
		$strSQL .= "	'".mb_strtoupper($_REQUEST["telefono"], "UTF-8")."', ";
		$strSQL .= "	'".$stateAdd."', ";
		$strSQL .= "	'".mb_strtoupper($_REQUEST["cargo"], "UTF-8")."', ";
		$strSQL .= "	".$_REQUEST["cmbTipoUsuario"].", ";
		$strSQL .= "	".$_REQUEST["cmbCuenta"].", ";
		$strSQL .= "	".$_REQUEST["cmbProducto"].", ";
		$strSQL .= "	".$_REQUEST["cmbContratoInv"].", ";
		$strSQL .= "	".$_REQUEST["cmbContratoAud"].", ";
		$strSQL .= "	".$_REQUEST["cmbContratoMap"];
		$strSQL .= "	)";

		$DB->Execute($strSQL);

		$insert_ID = $DB->Insert_ID();

		if($_REQUEST["cmbTipoUsuario"] == $idTypeConsult) {
			$strSQL = "INSERT INTO usuarios_menu ( ";
			$strSQL .= "	idUsuario, ";
			$strSQL .= "	idMenu ";
			$strSQL .= "	) VALUES ( ";
			$strSQL .= "	$insert_ID, ";
			$strSQL .= "	".$idMenuMapping;
			$strSQL .= "	)";

			$DB->Execute($strSQL);
		}
		else {
			$rsMenu = $DB->Execute("SELECT * FROM menu");

			while(!$rsMenu->EOF){
				if(isset($_REQUEST['modulo'.$rsMenu->fields('idMenu')]))
				{
					$strSQL = "INSERT INTO usuarios_menu ( ";
					$strSQL .= "	idUsuario, ";
					$strSQL .= "	idMenu ";
					$strSQL .= "	) VALUES ( ";
					$strSQL .= "	$insert_ID, ";
					$strSQL .= "	".$rsMenu->fields('idMenu');
					$strSQL .= "	)";

					$DB->Execute($strSQL);
				}

				$rsMenu->MoveNext();
			}
		}
	}
	else{
		$strSQL = "UPDATE usuarios SET ";
		$strSQL .= "	usuario = '".mb_strtoupper($_REQUEST["usuario"], "UTF-8")."', ";
		$strSQL .= "	password = '".fnEncrypt($_REQUEST["password"])."', ";
		$strSQL .= "	nombreCompleto = '".mb_strtoupper($_REQUEST["nombreCompleto"], "UTF-8")."', ";
		$strSQL .= "	eMail = '".$_REQUEST["eMail"]."', ";
		$strSQL .= "	telefono = '".mb_strtoupper($_REQUEST["telefono"], "UTF-8")."', ";
		$strSQL .= "	estado = '".$stateModify."',";
		$strSQL .= "	cargo = '".mb_strtoupper($_REQUEST["cargo"], "UTF-8")."', ";
		$strSQL .= "	idTipoUsuario = ".$_REQUEST["cmbTipoUsuario"].", ";
		$strSQL .= "	idAnunciante = ".$_REQUEST["cmbCuenta"].", ";
		$strSQL .= "	idProducto = ".$_REQUEST["cmbProducto"].", ";
		$strSQL .= "	idContratoInv = ".$_REQUEST["cmbContratoInv"].", ";
		$strSQL .= "	idContratoAud = ".$_REQUEST["cmbContratoAud"].", ";
		$strSQL .= "	idContratoMap = ".$_REQUEST["cmbContratoMap"];
		$strSQL .= " WHERE idUsuario = ". $_REQUEST["idUsuario"];

		$DB->Execute($strSQL);

		//DELETE ALL MENU ASSIGNED
		$DB->Execute("DELETE FROM usuarios_menu WHERE idUsuario = ".$_REQUEST["idUsuario"]);

		if($_REQUEST["cmbTipoUsuario"] == $idTypeConsult) {
			$strSQL = "INSERT INTO usuarios_menu ( ";
			$strSQL .= "	idUsuario, ";
			$strSQL .= "	idMenu ";
			$strSQL .= "	) VALUES ( ";
			$strSQL .= "	".$_REQUEST["idUsuario"].", ";
			$strSQL .= "	".$idMenuMapping;
			$strSQL .= "	)";

			$DB->Execute($strSQL);
		}
		else {
			$rsMenu = $DB->Execute("SELECT * FROM menu");

			while(!$rsMenu->EOF){
				if(isset($_REQUEST['modulo'.$rsMenu->fields('idMenu')]))
				{
					$strSQL = "INSERT INTO usuarios_menu ( ";
					$strSQL .= "	idUsuario, ";
					$strSQL .= "	idMenu ";
					$strSQL .= "	) VALUES ( ";
					$strSQL .= "	".$_REQUEST["idUsuario"].", ";
					$strSQL .= "	".$rsMenu->fields('idMenu');
					$strSQL .= "	)";

					$DB->Execute($strSQL);
				}

				$rsMenu->MoveNext();
			}
		}
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
	$DB->Execute("UPDATE usuarios SET estado='$stateErase' where idUsuario=" . $_REQUEST["idUsuario"]); // execute query

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

function getTiposUsuario()
{
	$DB = NewADOConnection('mysqli');
	$DB->Connect();
	$DB->Execute("SET NAMES utf8;");
	//$DB->debug=true;

	$rsTypeUsers = $DB->Execute("SELECT * FROM tipo_usuarios");

	$i=0;
	while(!$rsTypeUsers->EOF){
		$arrTypeUsers->data[$i]['idTipoUsuario'] = $rsTypeUsers->fields(0);
		$arrTypeUsers->data[$i++]['descripcion'] = $rsTypeUsers->fields(1);
		$rsTypeUsers->MoveNext();
	}

	return json_encode($arrTypeUsers->data);
}

function getCuentas()
{
	$DB = NewADOConnection('mysqli');
	$DB->Connect();
	$DB->Execute("SET NAMES utf8;");
	//$DB->debug=true;

	$rsAnunciantes = $DB->Execute("SELECT * FROM anunciantes");

	$i=0;
	while(!$rsAnunciantes->EOF){
		$arrAnunciantes->data[$i]['idAnunciante'] = $rsAnunciantes->fields(0);
		$arrAnunciantes->data[$i++]['descripcion'] = $rsAnunciantes->fields(1);
		$rsAnunciantes->MoveNext();
	}

	return json_encode($arrAnunciantes->data);
}

function getProductos()
{
	$DB = NewADOConnection('mysqli');
	$DB->Connect();
	$DB->Execute("SET NAMES utf8;");
	//$DB->debug=true;

	$rsProductos = $DB->Execute("SELECT * FROM productos");

	$i=0;
	while(!$rsProductos->EOF){
		$arrProductos->data[$i]['idProducto'] = $rsProductos->fields(0);
		$arrProductos->data[$i++]['descripcion'] = $rsProductos->fields(1);
		$rsProductos->MoveNext();
	}

	return json_encode($arrProductos->data);
}

function getContratos()
{
	require("includes/constants.php");

	$DB = NewADOConnection('mysqli');
	$DB->Connect();
	$DB->Execute("SET NAMES utf8;");
	//$DB->debug=true;

	$tipoContrato = $_REQUEST['tipoContrato'];
	$rsContratos = $DB->Execute("SELECT * FROM contratos WHERE estado <> '$stateErase' AND tipoContrato = '$tipoContrato'");

	$i=0;
	if($rsContratos->EOF) {
		return json_encode(array());
	}
	else {
		while(!$rsContratos->EOF){
			$arrContratos->data[$i]['idContrato'] = $rsContratos->fields(0);
			$arrContratos->data[$i++]['descripcion'] = $rsContratos->fields(1);
			$rsContratos->MoveNext();
		}
	}
	return json_encode($arrContratos->data);
}

function editUsuario()
{
	require("includes/constants.php");

	$DB = NewADOConnection('mysqli');
	$DB->Connect();
	$DB->Execute("SET NAMES utf8;");
	//$DB->debug=true;

	$rsUsers = $DB->Execute("SELECT * FROM usuarios WHERE estado <> '$stateErase' and idUsuario=" . $_REQUEST["idUsuario"]); // execute query

	if(!$rsUsers->EOF){
		$joUser->data['usuario'] = $rsUsers->fields("usuario");
		$joUser->data['password'] = fnDecrypt($rsUsers->fields("password"));
		$joUser->data['nombreCompleto'] = $rsUsers->fields("nombreCompleto");
		$joUser->data['eMail'] = $rsUsers->fields("eMail");
		$joUser->data['telefono'] = $rsUsers->fields("telefono");
		$joUser->data['cargo'] = $rsUsers->fields("cargo");
		$joUser->data['idTipoUsuario'] = $rsUsers->fields("idTipoUsuario");
		$joUser->data['idAnunciante'] = $rsUsers->fields("idAnunciante");
		$joUser->data['idProducto'] = $rsUsers->fields("idProducto");
		$joUser->data['idContratoInv'] = $rsUsers->fields("idContratoInv");
		$joUser->data['idContratoAud'] = $rsUsers->fields("idContratoAud");
		$joUser->data['idContratoMap'] = $rsUsers->fields("idContratoMap");

		return json_encode($joUser->data);
	}
	else
		return json_encode('');
}

function getSistemaModulos()
{
	require("includes/constants.php");

	try
	{
		$DB = NewADOConnection('mysqli');
		$DB->Connect();
		$DB->Execute("SET NAMES utf8;");
		//$DB->debug=true;

		if(isset($_REQUEST["idUsuario"]))
		{
			$strSQL = "SELECT m.idMenu, m.descripcion, um.idMenu idMenuAsignado FROM menu m".
				  	  "	LEFT JOIN usuarios_menu um ON m.idMenu = um.idMenu AND um.idUsuario = ".$_REQUEST["idUsuario"];
		}
		else {
			$strSQL = "SELECT idMenu, descripcion, NULL FROM menu";
		}
		$rsMenu = $DB->Execute($strSQL);

		if(!$rsMenu->EOF)
		{
			while (!$rsMenu->EOF)
			{
				$arrMenu->items[$i]['idMenu'] = $rsMenu->fields(0);
				$arrMenu->items[$i]['descripcion'] = $rsMenu->fields(1);
				$arrMenu->items[$i++]['selectedMenu'] = $rsMenu->fields(2) == NULL ? "" : "checked";

				$rsMenu->MoveNext();
			}
			$arrMenu->status = "OK";
		}
		else {
			$arrMenu->status = "EMPTY";
		}

		return json_encode($arrMenu);
	}
	catch(Exception $e)
	{
		$arrTables->status = "ERROR";
		return json_encode($arrTables);
	}
}

function checkAvailableDB()
{
	try
	{
		$DB = NewADOConnection('mysqli');
		$DB->Connect();
		//$DB->Execute("SET NAMES utf8;");
		//$DB->debug=true;

		$strSQL = "SELECT s.idBD_Status, s.Descripcion, s.Mensaje FROM BD_current_Status cs
						INNER JOIN BD_Status s ON cs.idBD_current_Status = s.idBD_Status";
		$rsDB = $DB->Execute($strSQL);

		$arrJSON->status = $rsDB->fields('idBD_Status');
		$arrJSON->title = $rsDB->fields('Descripcion');
		$arrJSON->msg = $rsDB->fields('Mensaje');

		return json_encode($arrJSON);
	}
	catch(Exception $e)
	{
		$arrJSON->status = 5;
		$arrJSON->title = "BD no disponible";
		$arrJSON->msg = "El Sistema no esta disponible en este Momento, Disculpe las molestias.";
	}
}

switch($_REQUEST['actionOfForm'])
{
	case "EDIT":
		echo editUsuario();
		break;
	case "DELETE":
		echo del();
		break;
	case "search":
		echo searchABMUsuarios();
		break;
	case "addOrEdit":
		echo addOrEdit();
		break;
	case "getTiposUsuario":
		echo getTiposUsuario();
		break;
	case "getCuentas":
		echo getCuentas();
		break;
	case "getProductos":
		echo getProductos();
		break;
	case "getSistemaModulos":
		echo getSistemaModulos();
		break;
	case "getContratos":
		echo getContratos();
		break;
	case "checkAvailableDB":
		echo checkAvailableDB();
		break;
}
?>
