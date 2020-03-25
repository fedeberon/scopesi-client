<?php
error_reporting(0);
/**
 *  Por Federico Pisarello - SisDev Software (c) 2012 - Buenos Aires, Argentina.
 * 	fpisarello@sisdevsoft.com
 */

require("includes/funciones.inc.php");

session_start();
if(!isset($_SESSION['userName']))
	exit();

$baseFotosMap = '../images/fotos_map/';

function exportXLS()
{
	require("includes/constants.php");

	$joFilterUbicaciones = json_decode($_REQUEST['joFilterUbicaciones']);
	$joMarkerVisibleIds = explode(",", $_REQUEST['joMarkerVisibleIds']);
	$joUbicacionesCantidad = json_decode($_REQUEST['joUbicacionesCantidad']);

	$DB = NewADOConnection('mysqli');
	$DB->Connect();
	$DB->Execute("SET NAMES utf8;");
	//$DB->debug=true;

	$strSQL = "SELECT DISTINCT map_ubicaciones.idUbicacion, map_empresas.descripcion descEmpresa, map_ubicaciones.direccion, map_provincias.descripcion descProvincia, map_localidades.descripcion descLocalidad, map_elementos.descripcion descElemento, map_ubicaciones.transito, map_ubicaciones.visibilidad, map_ubicaciones.medidas, map_ubicaciones.id_referencia, map_ubicaciones.nro_agip, map_ubicaciones.cantidad
				FROM map_ubicaciones
				INNER JOIN map_empresas ON map_ubicaciones.idEmpresa = map_empresas.idEmpresa
				INNER JOIN map_provincias ON map_ubicaciones.idProvincia = map_provincias.idProvincia
				INNER JOIN map_localidades ON map_ubicaciones.idLocalidad = map_localidades.idLocalidad
				INNER JOIN map_elementos ON map_ubicaciones.idElemento = map_elementos.idElemento
				WHERE
					map_ubicaciones.bajaLogica = 0 AND ";

	if(count($joFilterUbicaciones->joMedioIds) > 0) {
		$strWhereFilterUbicaciones .= "map_ubicaciones.idMedio IN (";
		foreach ($joFilterUbicaciones->joMedioIds as $value) {
			$strWhereFilterUbicaciones .= $value. ",";
		}
		$strWhereFilterUbicaciones = substr($strWhereFilterUbicaciones, 0, -1).") AND ";
	}

	if(count($joFilterUbicaciones->joFormatoIds) > 0) {
		$strWhereFilterUbicaciones .= "map_ubicaciones.idFormato IN (";
		foreach ($joFilterUbicaciones->joMedioIds as $value) {
			$strWhereFilterUbicaciones .= $value. ",";
		}
		$strWhereFilterUbicaciones = substr($strWhereFilterUbicaciones, 0, -1).") AND ";
	}

	if(count($joFilterUbicaciones->joElementosIds) > 0) {
		$strWhereFilterUbicaciones .= "map_ubicaciones.idElemento IN (";
		foreach ($joFilterUbicaciones->joElementosIds as $value) {
			$strWhereFilterUbicaciones .= $value. ",";
		}
		$strWhereFilterUbicaciones = substr($strWhereFilterUbicaciones, 0, -1).") AND ";
	}

	if(count($joFilterUbicaciones->joEVPIds) > 0) {
		$strWhereFilterUbicaciones .= "map_ubicaciones.idEmpresa IN (";
		foreach ($joFilterUbicaciones->joEVPIds as $value) {
			$strWhereFilterUbicaciones .= $value. ",";
		}
		$strWhereFilterUbicaciones = substr($strWhereFilterUbicaciones, 0, -1).") AND ";
	}

	if(count($joFilterUbicaciones->joProvinciaIds) > 0) {
		$strWhereFilterUbicaciones .= "map_ubicaciones.idProvincia IN (";
		foreach ($joFilterUbicaciones->joProvinciaIds as $value) {
			$strWhereFilterUbicaciones .= $value. ",";
		}
		$strWhereFilterUbicaciones = substr($strWhereFilterUbicaciones, 0, -1).") AND ";
	}

	if(count($joFilterUbicaciones->joLocalidadIds) > 0) {
		$strWhereFilterUbicaciones .= "map_ubicaciones.idLocalidad IN (";
		foreach ($joFilterUbicaciones->joLocalidadIds as $value) {
			$strWhereFilterUbicaciones .= $value. ",";
		}
		$strWhereFilterUbicaciones = substr($strWhereFilterUbicaciones, 0, -1).") AND ";
	}

	if(count($joMarkerVisibleIds) > 0) {
		$strWhereFilterUbicaciones .= "map_ubicaciones.idUbicacion IN (";
		foreach ($joMarkerVisibleIds as $value) {
			$strWhereFilterUbicaciones .= "'".$value. "',";
		}
		$strWhereFilterUbicaciones = substr($strWhereFilterUbicaciones, 0, -1).") AND ";
	}

	if($strWhereFilterUbicaciones != "") {
		$strWhereFilterUbicaciones = substr($strWhereFilterUbicaciones, 0, -4);
	}

	if($strWhereFilterUbicaciones != "")
	{
		$strSQL .= $strWhereFilterUbicaciones;

		//Procesamos el Contrato del Usuario
		$rsContrato = $DB->Execute("SELECT * FROM contratos_mapping WHERE idContrato = ".$_SESSION['idContratoMap']);
		while(!$rsContrato->EOF){
			$sqlWhereMapping .= " (map_ubicaciones.idEmpresa = '".$rsContrato->fields('idEVP')."') OR ";
			$rsContrato->MoveNext();
		}
		if(!empty($sqlWhereMapping))
			$sqlWhereEVP = " AND (".substr($sqlWhereMapping,0,-3).")";
		else
			$sqlWhereEVP = "";

		$strSQL .= $sqlWhereEVP;
	}

	$arrayTituloCampos = array("idUbicacion", "EVP", "Direcci�n", "Provincia", "Localidad", "Elemento", "Transito", "Visibilidad", "Medidas", "ID_Referencia", "Nro_AGIP", "Cantidad");
	$arrayValoresCampos = array("idUbicacion","descEmpresa", "direccion", "descProvincia", "descLocalidad", "descElemento", "transito", "visibilidad", "medidas", "id_referencia", "nro_agip", "cantidad");

	$nombreArchivo = "Mapping_";

	require_once("includes/excel/excel_write/class.writeexcel_workbook.inc.php");
	require_once("includes/excel/excel_write/class.writeexcel_worksheet.inc.php");

	$fname = tempnam("tmp", $nombreArchivo . date('Ymd') . ".xls");
	$workbook = new writeexcel_workbook($fname);
	$workbook->set_tempdir("tmp");
	$fecha = date('d')."-".date('m')."-".date('Y');
	$worksheet =& $workbook->addworksheet("Reporte");

	$rs = $DB->Execute($strSQL);

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
			if(count($joUbicacionesCantidad) > 0) {
				if($arrayTituloCampos[$ide] == "Cantidad"){
					$worksheet->write($i, $ide, getUbicacionCantidad($joUbicacionesCantidad, $rs->fields($arrayValoresCampos[0])));
				}
				else {
					$worksheet->write($i, $ide, trim($rs->fields($arrayValoresCampos[$ide])));
				}
			}
			else
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

function getUbicacionCantidad($arrUbicaciones, $id) {
	foreach($arrUbicaciones as $ubicacion) {
		if($ubicacion->idUbicacion == $id) return $ubicacion->cantidad;
	}
	return 0;
}

function searchFiltroComun()
{
	require("includes/constants.php");
	require("includes/datatables_db/Datatables.php");

	$sSQL = "SELECT evp.idEmpresa, evp.descripcion AS descEmpresa, pr.idProvincia, pr.descripcion AS descProvincia, me.idElemento, me.descripcion AS descElemento, COUNT(*) cantidad FROM map_ubicaciones mu
				INNER JOIN map_empresas evp ON evp.idEmpresa = mu.idEmpresa
				INNER JOIN map_provincias pr ON pr.idProvincia = mu.idProvincia
				INNER JOIN map_elementos me ON me.idElemento = mu.idElemento
			WHERE
				mu.bajaLogica = 0 AND
				me.evalua = 1 AND
				pr.evalua = 1 ";

				//localidad.evalua
				//0 no evalua
				//1 caba y gba
				//2 rosario

	if($_REQUEST['emptyTable'] == true) {
		$sSQL .= ' AND 1=0 ';
	}
	else {
		$strWhereFilterUbicaciones = "";
		$joFilterUbicaciones = json_decode($_REQUEST['joFilterUbicaciones']);

		if($_REQUEST['joUbicacionesCantidad'] == '"[]"')
			$joUbicacionesCantidad = array();
		else
			$joUbicacionesCantidad = json_decode($_REQUEST['joUbicacionesCantidad']);

		if(count($joUbicacionesCantidad) > 0) {
			$strWhereFilterUbicaciones .= " AND idUbicacion IN (";
			foreach ($joUbicacionesCantidad as $joUbicacion) {
				$strWhereFilterUbicaciones .= $joUbicacion->idUbicacion. ",";
			}
			$strWhereFilterUbicaciones = substr($strWhereFilterUbicaciones, 0, -1).") ";
		}

		if(count($joFilterUbicaciones->joFavoritosIds) > 0) {
			$strWhereFilterUbicaciones .= "AND mu.idUbicacion IN (";
			foreach ($joFilterUbicaciones->joFavoritosIds as $value) {
				$strWhereFilterUbicaciones .= $value. ",";
			}
			$strWhereFilterUbicaciones = substr($strWhereFilterUbicaciones, 0, -1).")";
		}

		if(count($joFilterUbicaciones->joPOISResultIds) > 0) {
			$strWhereFilterUbicaciones .= "AND mu.idUbicacion IN (";
			foreach ($joFilterUbicaciones->joPOISResultIds as $value) {
				$strWhereFilterUbicaciones .= $value. ",";
			}
			$strWhereFilterUbicaciones = substr($strWhereFilterUbicaciones, 0, -1).")";
		}

		if(count($joFilterUbicaciones->joMedioIds) > 0) {
			$strWhereFilterUbicaciones .= "AND mu.idMedio IN (";
			foreach ($joFilterUbicaciones->joMedioIds as $value) {
				$strWhereFilterUbicaciones .= $value. ",";
			}
			$strWhereFilterUbicaciones = substr($strWhereFilterUbicaciones, 0, -1).")";
		}

		if(count($joFilterUbicaciones->joFormatoIds) > 0) {
			$strWhereFilterUbicaciones .= " AND mu.idFormato IN (";
			foreach ($joFilterUbicaciones->joFormatoIds as $value) {
				$strWhereFilterUbicaciones .= $value. ",";
			}
			$strWhereFilterUbicaciones = substr($strWhereFilterUbicaciones, 0, -1).")";
		}

		if(count($joFilterUbicaciones->joElementosIds) > 0) {
			$strWhereFilterUbicaciones .= " AND mu.idElemento IN (";
			foreach ($joFilterUbicaciones->joElementosIds as $value) {
				$strWhereFilterUbicaciones .= $value. ",";
			}
			$strWhereFilterUbicaciones = substr($strWhereFilterUbicaciones, 0, -1).")";
		}

		if(count($joFilterUbicaciones->joEVPIds) > 0) {
			$strWhereFilterUbicaciones .= " AND mu.idEmpresa IN (";
			foreach ($joFilterUbicaciones->joEVPIds as $value) {
				$strWhereFilterUbicaciones .= $value. ",";
			}
			$strWhereFilterUbicaciones = substr($strWhereFilterUbicaciones, 0, -1).")";
		}

		if(count($joFilterUbicaciones->joProvinciaIds) > 0) {
			$strWhereFilterUbicaciones .= " AND mu.idProvincia IN (";
			foreach ($joFilterUbicaciones->joProvinciaIds as $value) {
				$strWhereFilterUbicaciones .= $value. ",";
			}
			$strWhereFilterUbicaciones = substr($strWhereFilterUbicaciones, 0, -1).")";
		}

		if(count($joFilterUbicaciones->joLocalidadIds) > 0) {
			$strWhereFilterUbicaciones .= " AND mu.idLocalidad IN (";
			foreach ($joFilterUbicaciones->joLocalidadIds as $value) {
				$strWhereFilterUbicaciones .= $value. ",";
			}
			$strWhereFilterUbicaciones = substr($strWhereFilterUbicaciones, 0, -1).")";
		}

		$sSQL .= $strWhereFilterUbicaciones;

		$DB = NewADOConnection('mysqli');
		$DB->Connect();
		$DB->Execute("SET NAMES utf8;");
		//$DB->debug=true;

		//Procesamos el Contrato del Usuario
		$rsContrato = $DB->Execute("SELECT * FROM contratos_mapping WHERE idContrato = ".$_SESSION['idContratoMap']);
		while(!$rsContrato->EOF){
			$sqlWhereMapping .= " (evp.idEmpresa = '".$rsContrato->fields('idEVP')."') OR ";
			$rsContrato->MoveNext();
		}
		if(!empty($sqlWhereMapping))
			$sSQL .= " AND (".substr($sqlWhereMapping,0,-3).")";
	}

	$sSQL .= " GROUP BY evp.idEmpresa, evp.descripcion, pr.idProvincia, pr.descripcion, me.idElemento, me.descripcion";

	$datatables = new Datatables();

	$datatables
			->select('descEmpresa, descProvincia, descElemento, cantidad')
			->from('('.$sSQL.') u');

	return $datatables->generate();
}


function searchFiltroBuses()
{
	require("includes/constants.php");
	require("includes/datatables_db/Datatables.php");

	$sSQL = "SELECT mu.idUbicacion, evp.descripcion AS descEmpresa, mu.direccion, me.idElemento, me.descripcion AS descElemento, mu.idMapBuses FROM map_ubicaciones mu
				INNER JOIN map_empresas evp ON evp.idEmpresa = mu.idEmpresa
				INNER JOIN map_provincias pr ON pr.idProvincia = mu.idProvincia
				INNER JOIN map_elementos me ON me.idElemento = mu.idElemento
			WHERE
				mu.bajaLogica = 0 AND
				me.evalua = 1 AND
				pr.evalua = 1 ";

	if($_REQUEST['emptyTable'] == true) {
		$sSQL .= ' AND 1=0 ';
	}
	else {
		$strWhereFilterUbicaciones = "";
		$joFilterUbicaciones = json_decode($_REQUEST['joFilterUbicaciones']);

		if($_REQUEST['joUbicacionesCantidad'] == '"[]"')
			$joUbicacionesCantidad = array();
		else
			$joUbicacionesCantidad = json_decode($_REQUEST['joUbicacionesCantidad']);

		if(count($joUbicacionesCantidad) > 0) {
			$strWhereFilterUbicaciones .= " AND idUbicacion IN (";
			foreach ($joUbicacionesCantidad as $joUbicacion) {
				$strWhereFilterUbicaciones .= $joUbicacion->idUbicacion. ",";
			}
			$strWhereFilterUbicaciones = substr($strWhereFilterUbicaciones, 0, -1).") ";
		}

		if(count($joFilterUbicaciones->joFavoritosIds) > 0) {
			$strWhereFilterUbicaciones .= "AND mu.idUbicacion IN (";
			foreach ($joFilterUbicaciones->joFavoritosIds as $value) {
				$strWhereFilterUbicaciones .= $value. ",";
			}
			$strWhereFilterUbicaciones = substr($strWhereFilterUbicaciones, 0, -1).")";
		}

		if(count($joFilterUbicaciones->joMedioIds) > 0) {
			$strWhereFilterUbicaciones .= "AND mu.idMedio IN (";
			foreach ($joFilterUbicaciones->joMedioIds as $value) {
				$strWhereFilterUbicaciones .= $value. ",";
			}
			$strWhereFilterUbicaciones = substr($strWhereFilterUbicaciones, 0, -1).")";
		}

		if(count($joFilterUbicaciones->joFormatoIds) > 0) {
			$strWhereFilterUbicaciones .= " AND mu.idFormato IN (";
			foreach ($joFilterUbicaciones->joFormatoIds as $value) {
				$strWhereFilterUbicaciones .= $value. ",";
			}
			$strWhereFilterUbicaciones = substr($strWhereFilterUbicaciones, 0, -1).")";
		}

		$esBuses = false;
		$arrBuses = array(102,144,153,223,224,225,226); //Elementos de Buses
		if(count($joFilterUbicaciones->joElementosIds) > 0) {
			$strWhereFilterUbicaciones .= " AND mu.idElemento IN (";
			foreach ($joFilterUbicaciones->joElementosIds as $value) {
				$strWhereFilterUbicaciones .= $value. ",";
				if(in_array($value, $arrBuses))
					$esBuses = true;
			}
			$strWhereFilterUbicaciones = substr($strWhereFilterUbicaciones, 0, -1).")";
		}

		if(count($joFilterUbicaciones->joEVPIds) > 0) {
			$strWhereFilterUbicaciones .= " AND mu.idEmpresa IN (";
			foreach ($joFilterUbicaciones->joEVPIds as $value) {
				$strWhereFilterUbicaciones .= $value. ",";
			}
			$strWhereFilterUbicaciones = substr($strWhereFilterUbicaciones, 0, -1).")";
		}

		if(count($joFilterUbicaciones->joProvinciaIds) > 0) {
			$strWhereFilterUbicaciones .= " AND mu.idProvincia IN (";
			foreach ($joFilterUbicaciones->joProvinciaIds as $value) {
				$strWhereFilterUbicaciones .= $value. ",";
			}
			$strWhereFilterUbicaciones = substr($strWhereFilterUbicaciones, 0, -1).")";
		}

		if(count($joFilterUbicaciones->joLocalidadIds) > 0) {
			$strWhereFilterUbicaciones .= " AND mu.idLocalidad IN (";
			foreach ($joFilterUbicaciones->joLocalidadIds as $value) {
				$strWhereFilterUbicaciones .= $value. ",";
			}
			$strWhereFilterUbicaciones = substr($strWhereFilterUbicaciones, 0, -1).")";
		}

		$sSQL .= $strWhereFilterUbicaciones;

		$DB = NewADOConnection('mysqli');
		$DB->Connect();
		$DB->Execute("SET NAMES utf8;");
		//$DB->debug=true;

		//Procesamos el Contrato del Usuario
		$rsContrato = $DB->Execute("SELECT * FROM contratos_mapping WHERE idContrato = ".$_SESSION['idContratoMap']);
		while(!$rsContrato->EOF){
			$sqlWhereMapping .= " (evp.idEmpresa = '".$rsContrato->fields('idEVP')."') OR ";
			$rsContrato->MoveNext();
		}
		if(!empty($sqlWhereMapping))
			$sSQL .= " AND (".substr($sqlWhereMapping,0,-3).")";
	}

	$datatables = new Datatables();

	$datatables
		->select('descEmpresa, direccion, descElemento, idUbicacion, idMapBuses, idElemento')
		->from('('.$sSQL.') u')
		->add_column('cantidad', '<input type="number" class="canAudBuses" min="1" max="999" id="cantidad_buses_$1_$2_$3" name="cantidad_buses_$1_$2_$3">', 'idUbicacion, idMapBuses, idElemento')
		->unset_column('idUbicacion')
		->unset_column('idMapBuses')
		->unset_column('idElemento');

		return $datatables->generate();
}

function searchMapping()
{
	require("includes/constants.php");
	require("includes/datatables_db/Datatables.php");

	$datatables = new Datatables();

	$datatables
			->select('map_empresas.idEmpresa, map_ubicaciones.idUbicacion, map_elementos.idElemento, map_ubicaciones.geo_latitud, map_ubicaciones.geo_longitud, map_empresas.descripcion AS descEmpresa, map_ubicaciones.direccion, map_provincias.descripcion AS descProvincia, map_localidades.descripcion AS descLocalidad, map_elementos.descripcion AS descElemento, map_ubicaciones.transito, map_ubicaciones.visibilidad, map_ubicaciones.medidas, map_ubicaciones.id_referencia, map_ubicaciones.nro_agip, map_ubicaciones.cantidad')
			->from('map_ubicaciones')
			->join('map_empresas', 'map_ubicaciones.idEmpresa = map_empresas.idEmpresa', 'inner')
			->join('map_provincias', 'map_ubicaciones.idProvincia = map_provincias.idProvincia', 'inner')
			->join('map_localidades', 'map_ubicaciones.idLocalidad = map_localidades.idLocalidad', 'inner')
			->join('map_elementos', 'map_ubicaciones.idElemento = map_elementos.idElemento', 'inner')
			->edit_column('map_empresas.idEmpresa, map_elementos.idElemento, map_ubicaciones.geo_latitud, map_ubicaciones.geo_longitud', '$1', 'imgReferenceId(map_empresas.idEmpresa, map_elementos.idElemento, map_ubicaciones.geo_latitud, map_ubicaciones.geo_longitud)')
			->select('map_ubicaciones.idUbicacion AS idFavorito, map_ubicaciones.idMapBuses')
			->edit_column('idFavorito', '$1', 'imgFavorito(idFavorito, map_ubicaciones.idMapBuses)')
			->edit_column('map_ubicaciones.cantidad', '<span id="cant_$1">$2</span>', 'map_ubicaciones.idUbicacion, map_ubicaciones.cantidad')
			->edit_column('descElemento', '<span id="descElemento_$1">$2</span>', 'map_ubicaciones.idUbicacion, descElemento')
			->edit_column('descEmpresa', '<span id="descEmpresa_$1">$2</span>', 'map_ubicaciones.idUbicacion, descEmpresa')
			->unset_column('map_ubicaciones.idUbicacion')
			->unset_column('map_elementos.idElemento')
			->unset_column('map_ubicaciones.idMapBuses')
			->unset_column('map_ubicaciones.geo_latitud')
			->unset_column('map_ubicaciones.geo_longitud')
			->where('map_ubicaciones.bajaLogica = 0');

	if($_REQUEST['emptyTable'] == true) {
		$datatables->where(' 1=0 ');
	}
	else {
		$joFilterUbicaciones = json_decode($_REQUEST['joFilterUbicaciones']);
		if($_REQUEST['joUbicacionesCantidad'] == '"[]"')
			$joUbicacionesCantidad = array();
		else
			$joUbicacionesCantidad = json_decode($_REQUEST['joUbicacionesCantidad']);
		$strWhereFilterUbicaciones = "";

		if(count($joFilterUbicaciones->joMedioIds) > 0) {
			$strWhereFilterUbicaciones .= "map_ubicaciones.idMedio IN (";
			foreach ($joFilterUbicaciones->joMedioIds as $value) {
				$strWhereFilterUbicaciones .= $value. ",";
			}
			$strWhereFilterUbicaciones = substr($strWhereFilterUbicaciones, 0, -1).") AND ";
		}

		if(count($joFilterUbicaciones->joFormatoIds) > 0) {
			$strWhereFilterUbicaciones .= "map_ubicaciones.idFormato IN (";
			foreach ($joFilterUbicaciones->joFormatoIds as $value) {
				$strWhereFilterUbicaciones .= $value. ",";
			}
			$strWhereFilterUbicaciones = substr($strWhereFilterUbicaciones, 0, -1).") AND ";
		}

		if(count($joFilterUbicaciones->joElementosIds) > 0) {
			$strWhereFilterUbicaciones .= "map_ubicaciones.idElemento IN (";
			foreach ($joFilterUbicaciones->joElementosIds as $value) {
				$strWhereFilterUbicaciones .= $value. ",";
			}
			$strWhereFilterUbicaciones = substr($strWhereFilterUbicaciones, 0, -1).") AND ";
		}

		if(count($joFilterUbicaciones->joEVPIds) > 0) {
			$strWhereFilterUbicaciones .= "map_ubicaciones.idEmpresa IN (";
			foreach ($joFilterUbicaciones->joEVPIds as $value) {
				$strWhereFilterUbicaciones .= $value. ",";
			}
			$strWhereFilterUbicaciones = substr($strWhereFilterUbicaciones, 0, -1).") AND ";
		}

		if(count($joFilterUbicaciones->joProvinciaIds) > 0) {
			$strWhereFilterUbicaciones .= "map_ubicaciones.idProvincia IN (";
			foreach ($joFilterUbicaciones->joProvinciaIds as $value) {
				$strWhereFilterUbicaciones .= $value. ",";
			}
			$strWhereFilterUbicaciones = substr($strWhereFilterUbicaciones, 0, -1).") AND ";
		}

		if(count($joFilterUbicaciones->joLocalidadIds) > 0) {
			$strWhereFilterUbicaciones .= "map_ubicaciones.idLocalidad IN (";
			foreach ($joFilterUbicaciones->joLocalidadIds as $value) {
				$strWhereFilterUbicaciones .= $value. ",";
			}
			$strWhereFilterUbicaciones = substr($strWhereFilterUbicaciones, 0, -1).") AND ";
		}

		if(count($joUbicacionesCantidad) > 0) {
			$strWhereFilterUbicaciones .= "map_ubicaciones.idUbicacion IN (";
			foreach ($joUbicacionesCantidad as $joUbicacion) {
				$strWhereFilterUbicaciones .= $joUbicacion->idUbicacion. ",";
			}
			$strWhereFilterUbicaciones = substr($strWhereFilterUbicaciones, 0, -1).") AND ";
		}

		if($strWhereFilterUbicaciones != "") {
			$strWhereFilterUbicaciones = substr($strWhereFilterUbicaciones, 0, -4);
			$datatables->where($strWhereFilterUbicaciones);
		}

		$DB = NewADOConnection('mysqli');
		$DB->Connect();
		$DB->Execute("SET NAMES utf8;");
		//$DB->debug=true;

		//Procesamos el Contrato del Usuario
		$rsContrato = $DB->Execute("SELECT * FROM contratos_mapping WHERE idContrato = ".$_SESSION['idContratoMap']);
		while(!$rsContrato->EOF){
			$sqlWhereMapping .= " (map_ubicaciones.idEmpresa = '".$rsContrato->fields('idEVP')."') OR ";
			$rsContrato->MoveNext();
		}
		if(!empty($sqlWhereMapping))
			$datatables->where("(".substr($sqlWhereMapping,0,-3).")");
		else
			$datatables->where(' 1=0 ');
	}

	return $datatables->generate();
}

function imgFavorito($idFavorito, $idMapBuses)
{
	$joFilterUbicaciones = json_decode($_REQUEST['joFilterUbicaciones']);
	$joFavoritos = $joFilterUbicaciones->joFavoritosIds;

	if(is_array($joFavoritos)) {
		if(in_array($idFavorito, $joFavoritos))
			return '<img style="cursor:pointer;" data-buses="'.$idMapBuses.'" src="images/fav_activado_tooltip.png" onclick="$_addMappingFavorito(this)" id="fav_grilla_'.$idFavorito.'">';
		else
			return '<img style="cursor:pointer;" data-buses="'.$idMapBuses.'" src="images/fav_desactivado_tooltip.png" onclick="$_addMappingFavorito(this)" id="fav_grilla_'.$idFavorito.'">';
	}
	else {
		return '<img style="cursor:pointer;" data-buses="'.$idMapBuses.'" src="images/fav_desactivado_tooltip.png" onclick="$_addMappingFavorito(this)" id="fav_grilla_'.$idFavorito.'">';
	}
}

function imgReferenceId($idEmpresa, $idElemento, $lat, $lng)
{
	$idImagen = 0;
	if($_REQUEST['tipoFiltro'] == 1) {
		$idImagen = $idEmpresa.$idElemento;
	}
	else if($_REQUEST['tipoFiltro'] == 2) {
		$idImagen = $idEmpresa;
	}
	else if($_REQUEST['tipoFiltro'] == 3) {
		$idImagen = $idElemento;
	}
	else {
		$idImagen = $idEmpresa.$idElemento;
	}

	return $idImagen."|".$lat."|".$lng;
}

function downloadArchivoCampanna()
{
	$mcrypt = new MCrypt();

	$DB = NewADOConnection('mysqli');
	$DB->Connect();
	$DB->Execute("SET NAMES utf8;");
	//$DB->debug=true;

	$rsArchCamp = $DB->Execute("SELECT * FROM map_campanias_archivos WHERE idCampaniaArchivos = ? ", array($_REQUEST['idArchivoCampanna']));

	$fileName = "filter_files/".$rsArchCamp->fields('nombreArchivo');
	$fileContent = $mcrypt->encrypt($rsArchCamp->fields('dataArchivo'));
	file_put_contents($fileName, $fileContent);

	if(isset($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'],'MSIE'))
	{
		header("Pragma: no-cache");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Expires: 0");
	}
	header("Content-Transfer-Encoding: binary");
	header("Content-Length: ".@filesize($fileName));
	header("Content-Type: plain/text; name=\"".$fileName."\"");
	header("Content-Disposition: attachment; filename=\"".$rsArchCamp->fields('nombreArchivo')."\"");
	$fh=fopen($fileName, "rb");
	fpassthru($fh);
	unlink($fileName);
}

function grabarFiltro()
{
	$mcrypt = new MCrypt();

	$fileName = "filter_files/MAPPING_FILTER_". date('Ymd') .".txt";
	$fileContent = $mcrypt->encrypt($_REQUEST['joFilterUbicaciones']."|".$_REQUEST['joMarkerNotVisibleIds']."|".$_REQUEST['joUbicacionesCantidad']);
	file_put_contents($fileName, $fileContent);

	if(isset($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'],'MSIE'))
	{
		header("Pragma: no-cache");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Expires: 0");
	}
	header("Content-Transfer-Encoding: binary");
	header("Content-Length: ".@filesize($fileName));
	header("Content-Type: plain/text; name=\"".$fileName."\"");
	header("Content-Disposition: attachment; filename=\""."MAPPING_FILTER_". date('Ymd') .".txt"."\"");
	$fh=fopen($fileName, "rb");
	fpassthru($fh);
	unlink($fileName);
}

function cargarFiltro()
{
	$DB = NewADOConnection('mysqli');
	$DB->Connect();
	$DB->Execute("SET NAMES utf8;");
	//$DB->debug=true;

	try
	{
		$mcrypt = new MCrypt();

		$fileName = "filter_files/".$_REQUEST['fileName'];

		$fContent = file_get_contents($fileName, FILE_USE_INCLUDE_PATH);

		$fileContent = $mcrypt->decrypt(trim($fContent));

		list($joFilterUbicaciones, $joMarkerNotVisibleIds, $joUbicacionesCantidad) = explode("|", $fileContent);

		if($_REQUEST['idCampanna'] != "undefined") {
			$DB->StartTrans(); // start transaction

			$sSQL = "INSERT INTO map_campanias_archivos (";
			$sSQL .= "	idCampania, ";
			$sSQL .= "	nombreArchivo, ";
			$sSQL .= "	dataArchivo ";
			$sSQL .= "	) VALUES ( ";
			$sSQL .= "	'".$_REQUEST["idCampanna"]."', ";
			$sSQL .= "	'".$_REQUEST["fileName"]."', ";
			$sSQL .= "	'".$fileContent."' ";
			$sSQL .= "	)";

			$DB->Execute($sSQL);

			$rsArchivos = $DB->Execute("SELECT * FROM map_campanias_archivos WHERE idCampania = ".$_REQUEST["idCampanna"]);

			$arrJsones = array();
			while(!$rsArchivos->EOF){
				array_push($arrJsones, $rsArchivos->fields('dataArchivo'));
				$rsArchivos->MoveNext();
			}

			$arrFiltro = rearmarFiltroCampanna($arrJsones);

			$filtroCampanna = json_encode($arrFiltro->Filtro)."|".json_encode($arrFiltro->NotVisible)."|".json_encode($arrFiltro->UbicacionesCantidad);

			$DB->Execute("UPDATE map_campanias SET filtro = '$filtroCampanna' WHERE idCampania=" . $_REQUEST["idCampanna"]);

			if (!$DB->CompleteTrans())
			{
				$arrJSON->status = "ERR";
				$arrJSON->msg = 'Se produjo un error al guardar el filtro en la Campa&ntilde;a';
			}
			else {
				$arrJSON->status = "OK";
				$arrJSON->msg = 'Se cargó el filtro del archivo en la Campa&ntilde;a';
				$arrJSON->joFilterUbicaciones = json_encode($arrFiltro->Filtro);
				$arrJSON->joMarkerNotVisibleIds = json_encode($arrFiltro->NotVisible);
				$arrJSON->joUbicacionesCantidad = json_encode($arrFiltro->UbicacionesCantidad);
			}
		}
		else {
			$arrJSON->status = "OK";
			$arrJSON->msg = 'Se cargó el filtro del archivo';
			$arrJSON->joFilterUbicaciones = $joFilterUbicaciones;
			$arrJSON->joMarkerNotVisibleIds = $joMarkerNotVisibleIds;
			$arrJSON->joUbicacionesCantidad = $joUbicacionesCantidad == null ? array() : $joUbicacionesCantidad;
		}
	}
	catch(Exception $e)
	{
		$arrJSON->status = "ERR";
		$arrJSON->msg = 'Se produjo un error al cargar el filtro del archivo';
	}

	return json_encode($arrJSON);
}

function actualizarXLSMapa()
{

	global $baseFotosMap;

	$DB = NewADOConnection('mysqli');
	$DB->Connect();
	$DB->Execute("SET NAMES utf8;");
	//$DB->debug=true;

	$sSQL = "SELECT DISTINCT mu.*, me.descripcion AS descEmpresa, mel.descripcion AS descElemento, mp.descripcion AS descProvincia  FROM map_ubicaciones mu
				INNER JOIN map_empresas me ON me.idEmpresa = mu.idEmpresa
				INNER JOIN map_elementos mel ON mel.idElemento = mu.idElemento
				INNER JOIN map_provincias mp ON mp.idProvincia = mu.idProvincia
				WHERE
					mu.bajaLogica = 0 ";

	$joUbicacionesCantidad = json_decode($_REQUEST['joUbicacionesCantidad']);
	$strWhereFilterUbicaciones = "";

	if(count($joUbicacionesCantidad) > 0) {
		$strWhereFilterUbicaciones .= " AND idUbicacion IN (";
		foreach ($joUbicacionesCantidad as $joUbicacion) {
			$strWhereFilterUbicaciones .= $joUbicacion->idUbicacion. ",";
		}
		$strWhereFilterUbicaciones = substr($strWhereFilterUbicaciones, 0, -1).") ";
	}

	$orderBy = " ORDER BY mu.idEmpresa, mu.idElemento";

	$rsUbicaciones = $DB->Execute($sSQL.$strWhereFilterUbicaciones.$orderBy);

	$i=0;
	$imgFiltro = array();
	$idTipoFiltroActual = "";
	while(!$rsUbicaciones->EOF)
	{
		if($rsUbicaciones->fields("idEmpresa").$rsUbicaciones->fields("idElemento") != $idTipoFiltroActual) {
			$imgIcon = random_pic();
			array_push($imgFiltro, array("id" => $rsUbicaciones->fields("idEmpresa").$rsUbicaciones->fields("idElemento"), "imagen" => $imgIcon));
			$idTipoFiltroActual = $rsUbicaciones->fields("idEmpresa").$rsUbicaciones->fields("idElemento");
		}

		//Busco si tiene Foto
		$camarita = "";
		if(file_exists($baseFotosMap.$rsUbicaciones->fields("idEmpresa").'/'.$rsUbicaciones->fields("idUbicacion").".jpg")) {
			//$camarita = '<br/><br/><img style="cursor:pointer;" src="images/camara.png" onclick="$_imagenMappingProxyShow(\''.$rsUbicaciones->fields("idUbicacion").'\')">';
			$camarita = '<br/><br/><i class="fas fa-camera fotoElemento" onclick="$_imagenMappingProxyShow(\''.$rsUbicaciones->fields("idUbicacion").'\')"></i>';
		}

		if(is_array($joFilterUbicaciones->joFavoritosIds)) {
			if(in_array($rsUbicaciones->fields("idUbicacion"), $joFilterUbicaciones->joFavoritosIds))
				$fav = '<img class="favorito" src="images/fav_activado_tooltip.png" onclick="$_addMappingFavorito(this)" id="fav_'.$rsUbicaciones->fields("idUbicacion").'">';
				else
					$fav = '<img class="favorito" src="images/fav_desactivado_tooltip.png" onclick="$_addMappingFavorito(this)" id="fav_'.$rsUbicaciones->fields("idUbicacion").'">';
		}
		else {
			$fav = '<img class="favorito" src="images/fav_desactivado_tooltip.png" onclick="$_addMappingFavorito(this)" id="fav_'.$rsUbicaciones->fields("idUbicacion").'">';
		}

		$arrJSON->markers[$i]['latitude'] = floatval($rsUbicaciones->fields("geo_latitud"));
		$arrJSON->markers[$i]['longitude'] = floatval($rsUbicaciones->fields("geo_longitud"));
		$arrJSON->markers[$i]['html'] = '<h7>' . $rsUbicaciones->fields("direccion") . '</h7>' . '<br><h8>' . $rsUbicaciones->fields("descEmpresa") . '<br>' . $rsUbicaciones->fields("descElemento") . '<br>' . $rsUbicaciones->fields("descProvincia") . '</h8>'.$camarita."&nbsp;&nbsp;".$fav;
		$arrJSON->markers[$i]['icon'] = $imgIcon;
		$arrJSON->markers[$i]['id'] = $rsUbicaciones->fields("idUbicacion");

		$rsUbicaciones->MoveNext();
		$i++;
	}

	$arrJSON->imagesMapa = $imgFiltro;
	$arrJSON->status = "OK";

	return json_encode($arrJSON);
}

function actualizarXLSMapaUpload()
{

	global $baseFotosMap;

	$DB = NewADOConnection('mysqli');
	$DB->Connect();
	$DB->Execute("SET NAMES utf8;");
	//$DB->debug=true;

	$sSQL = "SELECT DISTINCT mu.*, me.descripcion AS descEmpresa, mel.descripcion AS descElemento, mp.descripcion AS descProvincia  FROM map_ubicaciones mu
				INNER JOIN map_empresas me ON me.idEmpresa = mu.idEmpresa
				INNER JOIN map_elementos mel ON mel.idElemento = mu.idElemento
				INNER JOIN map_provincias mp ON mp.idProvincia = mu.idProvincia
				WHERE
					mu.bajaLogica = 0 ";

	$joUbicacionesCantidad = json_decode($_REQUEST['joUbicacionesCantidad']);
	$strWhereFilterUbicaciones = "";

	if(count($joUbicacionesCantidad) > 0) {
		$strWhereFilterUbicaciones .= " AND idUbicacion IN (";
		foreach ($joUbicacionesCantidad as $joUbicacion) {
			$strWhereFilterUbicaciones .= $joUbicacion->idUbicacion. ",";
		}
		$strWhereFilterUbicaciones = substr($strWhereFilterUbicaciones, 0, -1).") ";
	}

	$orderBy = " ORDER BY mu.idEmpresa, mu.idElemento";

	$rsUbicaciones = $DB->Execute($sSQL.$strWhereFilterUbicaciones.$orderBy);

	$i=0;
	$imgIcon = random_pic();
	$imgFiltro = array();

	while(!$rsUbicaciones->EOF)
	{
		array_push($imgFiltro, array("id" => $rsUbicaciones->fields("idEmpresa").$rsUbicaciones->fields("idElemento"), "imagen" => $imgIcon));

		//Busco si tiene Foto
		$camarita = "";
		if(file_exists($baseFotosMap.$rsUbicaciones->fields("idEmpresa").'/'.$rsUbicaciones->fields("idUbicacion").".jpg")) {
			//$camarita = '<br/><br/><img style="cursor:pointer;" src="images/camara.png" onclick="$_imagenMappingProxyShow(\''.$rsUbicaciones->fields("idUbicacion").'\')">';
			$camarita = '<br/><br/><i class="fas fa-camera fotoElemento" onclick="$_imagenMappingProxyShow(\''.$rsUbicaciones->fields("idUbicacion").'\')"></i>';
		}

		if(is_array($joFilterUbicaciones->joFavoritosIds)) {
			if(in_array($rsUbicaciones->fields("idUbicacion"), $joFilterUbicaciones->joFavoritosIds))
				$fav = '<img class="favorito" src="images/fav_activado_tooltip.png" onclick="$_addMappingFavorito(this)" id="fav_'.$rsUbicaciones->fields("idUbicacion").'">';
				else
					$fav = '<img class="favorito" src="images/fav_desactivado_tooltip.png" onclick="$_addMappingFavorito(this)" id="fav_'.$rsUbicaciones->fields("idUbicacion").'">';
		}
		else {
			$fav = '<img class="favorito" src="images/fav_desactivado_tooltip.png" onclick="$_addMappingFavorito(this)" id="fav_'.$rsUbicaciones->fields("idUbicacion").'">';
		}

		$arrJSON->markers[$i]['latitude'] = floatval($rsUbicaciones->fields("geo_latitud"));
		$arrJSON->markers[$i]['longitude'] = floatval($rsUbicaciones->fields("geo_longitud"));
		$arrJSON->markers[$i]['html'] = '<h7>' . $rsUbicaciones->fields("direccion") . '</h7>' . '<br><h8>' . $rsUbicaciones->fields("descEmpresa") . '<br>' . $rsUbicaciones->fields("descElemento") . '<br>' . $rsUbicaciones->fields("descProvincia") . '</h8>'.$camarita."&nbsp;&nbsp;".$fav;
		$arrJSON->markers[$i]['icon'] = $imgIcon;
		$arrJSON->markers[$i]['id'] = $rsUbicaciones->fields("idUbicacion");

		$idElemento = $rsUbicaciones->fields("idElemento");

		$rsUbicaciones->MoveNext();
		$i++;
	}

	$arrJSON->imagesMapa = $imgFiltro;
	$arrJSON->status = "OK";

	return json_encode($arrJSON);
}

function actualizarBusesMapa()
{
	$DB = NewADOConnection('mysqli');
	$DB->Connect();
	$DB->Execute("SET NAMES utf8;");
	//$DB->debug=true;

	$joFilterUbicaciones = json_decode($_REQUEST['joFilterUbicaciones']);
	$strWhereFilterUbicaciones = "";

	$strSQL = "SELECT DISTINCT mbr.*, mb.color FROM map_buses_recorridos mbr
				INNER JOIN map_buses mb ON mbr.idMapBuses = mb.idMapBuses
				INNER JOIN map_buses_elementos mbe ON mb.idMapBuses = mbe.idMapBuses
				INNER JOIN map_ubicaciones mu ON mbe.idElemento = mu.idElemento
				WHERE
					map_ubicaciones.bajaLogica = 0 AND ";

	if(count($joFilterUbicaciones->joMedioIds) > 0) {
		$strWhereFilterUbicaciones .= "map_ubicaciones.idMedio IN (";
		foreach ($joFilterUbicaciones->joMedioIds as $value) {
			$strWhereFilterUbicaciones .= $value. ",";
		}
		$strWhereFilterUbicaciones = substr($strWhereFilterUbicaciones, 0, -1).") AND ";
	}

	if(count($joFilterUbicaciones->joFormatoIds) > 0) {
		$strWhereFilterUbicaciones .= "map_ubicaciones.idFormato IN (";
		foreach ($joFilterUbicaciones->joFormatoIds as $value) {
			$strWhereFilterUbicaciones .= $value. ",";
		}
		$strWhereFilterUbicaciones = substr($strWhereFilterUbicaciones, 0, -1).") AND ";
	}

	if(count($joFilterUbicaciones->joElementosIds) > 0) {
		$strWhereFilterUbicaciones .= "map_ubicaciones.idElemento IN (";
		foreach ($joFilterUbicaciones->joElementosIds as $value) {
			$strWhereFilterUbicaciones .= $value. ",";
		}
		$strWhereFilterUbicaciones = substr($strWhereFilterUbicaciones, 0, -1).") AND ";
	}

	if(count($joFilterUbicaciones->joEVPIds) > 0) {
		$strWhereFilterUbicaciones .= "map_ubicaciones.idEmpresa IN (";
		foreach ($joFilterUbicaciones->joEVPIds as $value) {
			$strWhereFilterUbicaciones .= $value. ",";
		}
		$strWhereFilterUbicaciones = substr($strWhereFilterUbicaciones, 0, -1).") AND ";
	}

	if(count($joFilterUbicaciones->joProvinciaIds) > 0) {
		$strWhereFilterUbicaciones .= "map_ubicaciones.idProvincia IN (";
		foreach ($joFilterUbicaciones->joProvinciaIds as $value) {
			$strWhereFilterUbicaciones .= $value. ",";
		}
		$strWhereFilterUbicaciones = substr($strWhereFilterUbicaciones, 0, -1).") AND ";
	}

	if(count($joFilterUbicaciones->joLocalidadIds) > 0) {
		$strWhereFilterUbicaciones .= "map_ubicaciones.idLocalidad IN (";
		foreach ($joFilterUbicaciones->joLocalidadIds as $value) {
			$strWhereFilterUbicaciones .= $value. ",";
		}
		$strWhereFilterUbicaciones = substr($strWhereFilterUbicaciones, 0, -1).") AND ";
	}

	if(count($joMarkerVisibleIds) > 0) {
		$strWhereFilterUbicaciones .= "map_ubicaciones.idUbicacion IN (";
		foreach ($joMarkerVisibleIds as $value) {
			$strWhereFilterUbicaciones .= "'".$value. "',";
		}
		$strWhereFilterUbicaciones = substr($strWhereFilterUbicaciones, 0, -1).") AND ";
	}

	if($strWhereFilterUbicaciones != "") {
		$strWhereFilterUbicaciones = substr($strWhereFilterUbicaciones, 0, -4);
	}

	if($strWhereFilterUbicaciones == "")
		$strSQL .= " 1 = 0 ";
	else {
		$strSQL .= $strWhereFilterUbicaciones;

		//Procesamos el Contrato del Usuario
		$rsContrato = $DB->Execute("SELECT * FROM contratos_mapping WHERE idContrato = ".$_SESSION['idContratoMap']);
		while(!$rsContrato->EOF){
			$sqlWhereMapping .= " (map_ubicaciones.idEmpresa = '".$rsContrato->fields('idEVP')."') OR ";
			$rsContrato->MoveNext();
		}
		if(!empty($sqlWhereMapping))
			$sqlWhereEVP = " AND (".substr($sqlWhereMapping,0,-3).")";
		else
			$sqlWhereEVP = "";

		$strSQL .= $sqlWhereEVP;
	}

	$rsBuses = $DB->Execute($strSQL);

	$i=0;
	while(!$rsBuses->EOF)
	{
		$arrJSON->polyline[$i]['id'] = "buses_".$rsBuses->fields("idMapBuses").$rsBuses->fields("idRecorrido");
		$arrJSON->polyline[$i]['encodePath'] = addslashes($rsBuses->fields("recorrido"));
		$arrJSON->polyline[$i]['color'] = $rsBuses->fields("color");

		$rsBuses->MoveNext();
		$i++;
	}

	$arrJSON->status = "OK";

	return json_encode($arrJSON);
}

function actualizarPOIsMapa()
{
	$DB = NewADOConnection('mysqli');
	$DB->Connect();
	$DB->Execute("SET NAMES utf8;");
	//$DB->debug=true;

	$sSQL = "SELECT * FROM map_pois ";

	$joEntidadPOIs = json_decode($_REQUEST['joEntidadPOIs']);
	$strWhereFilterUbicaciones = "";


	if(count($joEntidadPOIs) > 0) {
		$strWhereFilterUbicaciones .= "idEntidad IN (";
		foreach ($joEntidadPOIs as $value) {
			$strWhereFilterUbicaciones .= $value. ",";
		}
		$strWhereFilterUbicaciones = substr($strWhereFilterUbicaciones, 0, -1).") AND ";
	}

	if($strWhereFilterUbicaciones != "") {
		$strWhereFilterUbicaciones = " WHERE ".substr($strWhereFilterUbicaciones, 0, -4);
	}

	$rsPOIs = $DB->Execute($sSQL.$strWhereFilterUbicaciones);

	$i=0;
	while(!$rsPOIs->EOF)
	{
		$imgIcon = 'images/icons_pois/'.$rsPOIs->fields("icono");

		$arrJSON->markers[$i]['latitude'] = floatval($rsPOIs->fields("geo_latitud"));
		$arrJSON->markers[$i]['longitude'] = floatval($rsPOIs->fields("geo_longitud"));
		$arrJSON->markers[$i]['html'] = '<h7>' . $rsPOIs->fields("descripcion") . '</h7>';
		$arrJSON->markers[$i]['icon'] = $imgIcon;

		$rsPOIs->MoveNext();
		$i++;
	}

	$arrJSON->status = "OK";

	return json_encode($arrJSON);
}

function actualizarMapa()
{

	global $baseFotosMap;

	$DB = NewADOConnection('mysqli');
	$DB->Connect();
	$DB->Execute("SET NAMES utf8;");
	//$DB->debug=true;

	$sSQL = "SELECT DISTINCT *, me.descripcion AS descEmpresa, mel.descripcion AS descElemento, ml.descripcion AS descLocalidad, mp.descripcion AS descProvincia  FROM map_ubicaciones mu
				INNER JOIN map_empresas me ON me.idEmpresa = mu.idEmpresa
				INNER JOIN map_elementos mel ON mel.idElemento = mu.idElemento
				INNER JOIN map_provincias mp ON mp.idProvincia = mu.idProvincia
				INNER JOIN map_localidades ml ON ml.idLocalidad = mu.idLocalidad
				WHERE
					mu.bajaLogica = 0 AND ";


	$joFilterUbicaciones = json_decode($_REQUEST['joFilterUbicaciones']);
	$joUbicacionesCantidad = json_decode($_REQUEST['joUbicacionesCantidad']);

	$strWhereFilterUbicaciones = "";

	if(count($joFilterUbicaciones->joMedioIds) > 0) {
		$strWhereFilterUbicaciones .= "mu.idMedio IN (";
		foreach ($joFilterUbicaciones->joMedioIds as $value) {
			$strWhereFilterUbicaciones .= $value. ",";
		}
		$strWhereFilterUbicaciones = substr($strWhereFilterUbicaciones, 0, -1).") AND ";
	}

	if(count($joFilterUbicaciones->joFormatoIds) > 0) {
		$strWhereFilterUbicaciones .= "mu.idFormato IN (";
		foreach ($joFilterUbicaciones->joFormatoIds as $value) {
			$strWhereFilterUbicaciones .= $value. ",";
		}
		$strWhereFilterUbicaciones = substr($strWhereFilterUbicaciones, 0, -1).") AND ";
	}

	if(count($joFilterUbicaciones->joElementosIds) > 0) {
		$strWhereFilterUbicaciones .= "mu.idElemento IN (";
		foreach ($joFilterUbicaciones->joElementosIds as $value) {
			$strWhereFilterUbicaciones .= $value. ",";
		}
		$strWhereFilterUbicaciones = substr($strWhereFilterUbicaciones, 0, -1).") AND ";
	}

	if(count($joFilterUbicaciones->joEVPIds) > 0) {
		$strWhereFilterUbicaciones .= "mu.idEmpresa IN (";
		foreach ($joFilterUbicaciones->joEVPIds as $value) {
			$strWhereFilterUbicaciones .= $value. ",";
		}
		$strWhereFilterUbicaciones = substr($strWhereFilterUbicaciones, 0, -1).") AND ";
	}

	if(count($joFilterUbicaciones->joProvinciaIds) > 0) {
		$strWhereFilterUbicaciones .= "mu.idProvincia IN (";
		foreach ($joFilterUbicaciones->joProvinciaIds as $value) {
			$strWhereFilterUbicaciones .= $value. ",";
		}
		$strWhereFilterUbicaciones = substr($strWhereFilterUbicaciones, 0, -1).") AND ";
	}

	if(count($joFilterUbicaciones->joLocalidadIds) > 0) {
		$strWhereFilterUbicaciones .= "mu.idLocalidad IN (";
		foreach ($joFilterUbicaciones->joLocalidadIds as $value) {
			$strWhereFilterUbicaciones .= $value. ",";
		}
		$strWhereFilterUbicaciones = substr($strWhereFilterUbicaciones, 0, -1).") AND ";
	}

	if(count($joUbicacionesCantidad) > 0) {
		$strWhereFilterUbicaciones .= "mu.idUbicacion IN (";
		foreach ($joUbicacionesCantidad as $ubicacion) {
			$strWhereFilterUbicaciones .= $ubicacion->idUbicacion. ",";
		}
		$strWhereFilterUbicaciones = substr($strWhereFilterUbicaciones, 0, -1).") AND ";
	}

	if($strWhereFilterUbicaciones != "") {
		$strWhereFilterUbicaciones = substr($strWhereFilterUbicaciones, 0, -4);
		$sSQL .= $strWhereFilterUbicaciones;
	}

	//Procesamos el Contrato del Usuario
	$rsContrato = $DB->Execute("SELECT * FROM contratos_mapping WHERE idContrato = ".$_SESSION['idContratoMap']);
	while(!$rsContrato->EOF){
		$sqlWhereMapping .= " (mu.idEmpresa = '".$rsContrato->fields('idEVP')."') OR ";
		$rsContrato->MoveNext();
	}

	if(!empty($sqlWhereMapping))
		if($strWhereFilterUbicaciones == "")
			$sqlWhereEVP = " (".substr($sqlWhereMapping,0,-3).")";
		else
			$sqlWhereEVP = " AND (".substr($sqlWhereMapping,0,-3).")";
	else {
		if($strWhereFilterUbicaciones == "")
			$sqlWhereEVP = " 1 = 1";
		else
			$sqlWhereEVP = " AND 1 = 1";
	}

	$sSQL .= $sqlWhereEVP;


	switch ($_REQUEST['tipoFiltro']) {
		case '1':
			$sSQL .= " ORDER BY mu.idEmpresa, mu.idElemento";
			break;
		case '2':
			$sSQL .= " ORDER BY mu.idEmpresa";
			break;
		case '3':
			$sSQL .= " ORDER BY mu.idElemento";
			break;
	}

	$rsUbicaciones = $DB->Execute($sSQL);

	$i=0;
	$idTipoFiltroActual = "";
	$imgFiltro = array();
	while(!$rsUbicaciones->EOF)
	{
		switch ($_REQUEST['tipoFiltro']) {
			case '1':
				if($rsUbicaciones->fields("idEmpresa").$rsUbicaciones->fields("idElemento") != $idTipoFiltroActual) {
					$imgIcon = random_pic();
					array_push($imgFiltro, array("id" => $rsUbicaciones->fields("idEmpresa").$rsUbicaciones->fields("idElemento"), "imagen" => $imgIcon));
					$idTipoFiltroActual = $rsUbicaciones->fields("idEmpresa").$rsUbicaciones->fields("idElemento");
				}
				break;

			case '2':
				if($rsUbicaciones->fields("idEmpresa") != $idTipoFiltroActual) {
					$imgIcon = random_pic();
					array_push($imgFiltro, array("id" => $rsUbicaciones->fields("idEmpresa"), "imagen" => $imgIcon));
					$idTipoFiltroActual = $rsUbicaciones->fields("idEmpresa");
				}
				break;

			case '3':
				if($rsUbicaciones->fields("idElemento") != $idTipoFiltroActual) {
					$imgIcon = random_pic();
					array_push($imgFiltro, array("id" => $rsUbicaciones->fields("idElemento"), "imagen" => $imgIcon));
					$idTipoFiltroActual = $rsUbicaciones->fields("idElemento");
				}
				break;

			default:
				$imgIcon = 'images/icons/009A1B.png';
				array_push($imgFiltro, array("id" => 0, "imagen" => $imgIcon));
				$idTipoFiltroActual = 0;
		}

		//Busco si tiene Foto
		$camarita = "";
		if(file_exists($baseFotosMap.$rsUbicaciones->fields("idEmpresa").'/'.$rsUbicaciones->fields("idUbicacion").".jpg")) {
			//$camarita = '<br/><br/><img style="cursor:pointer;" src="images/camara.png" onclick="$_imagenMappingProxyShow(\''.$rsUbicaciones->fields("idUbicacion").'\')">';
			$camarita = '<br/><br/><i class="fas fa-camera fotoElemento" onclick="$_imagenMappingProxyShow(\''.$rsUbicaciones->fields("idUbicacion").'\')"></i>';
		}

		if(is_array($joFilterUbicaciones->joFavoritosIds)) {
			if(in_array($rsUbicaciones->fields("idUbicacion"), $joFilterUbicaciones->joFavoritosIds))
				$fav = '<img class="favorito" src="images/fav_activado_tooltip.png" onclick="$_addMappingFavorito(this)" id="fav_'.$rsUbicaciones->fields("idUbicacion").'">';
			else
				$fav = '<img class="favorito" src="images/fav_desactivado_tooltip.png" onclick="$_addMappingFavorito(this)" id="fav_'.$rsUbicaciones->fields("idUbicacion").'">';
		}
		else {
			$fav = '<img class="favorito" src="images/fav_desactivado_tooltip.png" onclick="$_addMappingFavorito(this)" id="fav_'.$rsUbicaciones->fields("idUbicacion").'">';
		}

		$arrJSON->markers[$i]['latitude'] = floatval($rsUbicaciones->fields("geo_latitud"));
		$arrJSON->markers[$i]['longitude'] = floatval($rsUbicaciones->fields("geo_longitud"));
		$arrJSON->markers[$i]['html'] = '<h7>' . $rsUbicaciones->fields("direccion") . '</h7>' . '<br><h8>' . $rsUbicaciones->fields("descEmpresa") . '<br>' . $rsUbicaciones->fields("descElemento") . '<br>' . $rsUbicaciones->fields("descLocalidad") . '<br>' . $rsUbicaciones->fields("descProvincia") . '</h8>'.$camarita."&nbsp;&nbsp;".$fav;
		$arrJSON->markers[$i]['icon'] = $imgIcon;
		$arrJSON->markers[$i]['id'] = $rsUbicaciones->fields("idUbicacion");

		$rsUbicaciones->MoveNext();
		$i++;
	}

	//POIs
	if(count($joFilterUbicaciones->joEntidadPOIs) > 0) {
		$sSQL = "SELECT * FROM map_pois WHERE ";

		$joEntidadPOIs = json_decode($_REQUEST['joEntidadPOIs']);
		$strWhereFilterUbicaciones = "";

		$strWhereFilterUbicaciones .= "idEntidad IN (";
		foreach ($joFilterUbicaciones->joEntidadPOIs as $value) {
			$strWhereFilterUbicaciones .= $value. ",";
		}
		$strWhereFilterUbicaciones = substr($strWhereFilterUbicaciones, 0, -1).")";

		if(count($joFilterUbicaciones->joProvinciaIds) > 0) {
			$strWhereFilterUbicaciones .= " AND idProvincia IN (";
			foreach ($joFilterUbicaciones->joProvinciaIds as $value) {
				$strWhereFilterUbicaciones .= $value. ",";
			}
			$strWhereFilterUbicaciones = substr($strWhereFilterUbicaciones, 0, -1).")";
		}

		$rsPOIs = $DB->Execute($sSQL.$strWhereFilterUbicaciones);

		while(!$rsPOIs->EOF)
		{
			$imgIcon = 'images/icons_pois/'.$rsPOIs->fields("icono");

			$arrJSON->markers[$i]['latitude'] = floatval($rsPOIs->fields("geo_latitud"));
			$arrJSON->markers[$i]['longitude'] = floatval($rsPOIs->fields("geo_longitud"));
			$arrJSON->markers[$i]['html'] = '<h7>' . $rsPOIs->fields("descripcion") . '</h7>';
			$arrJSON->markers[$i]['icon'] = $imgIcon;
			$arrJSON->markers[$i]['id'] = "P".$rsPOIs->fields("idPoi");

			$rsPOIs->MoveNext();
			$i++;
		}
	}

	//Buses
	$strWhereFilterUbicaciones = "";
	$sSQL = "SELECT DISTINCT mbr.*, mb.color, mb.linea FROM map_buses_recorridos mbr
				INNER JOIN map_buses mb ON mbr.idMapBuses = mb.idMapBuses
				INNER JOIN map_buses_elementos mbe ON mb.idMapBuses = mbe.idMapBuses
				LEFT JOIN map_ubicaciones mu ON mbe.idElemento = mu.idElemento
			WHERE
				mu.bajaLogica = 0";

	if(count($joFilterUbicaciones->joMedioIds) > 0) {
		$strWhereFilterUbicaciones .= " AND mu.idMedio IN (";
		foreach ($joFilterUbicaciones->joMedioIds as $value) {
			$strWhereFilterUbicaciones .= $value. ",";
		}
		$strWhereFilterUbicaciones = substr($strWhereFilterUbicaciones, 0, -1).") ";
	}

	if(count($joFilterUbicaciones->joFormatoIds) > 0) {
		$strWhereFilterUbicaciones .= " AND mu.idFormato IN (";
		foreach ($joFilterUbicaciones->joFormatoIds as $value) {
			$strWhereFilterUbicaciones .= $value. ",";
		}
		$strWhereFilterUbicaciones = substr($strWhereFilterUbicaciones, 0, -1).") ";
	}


	if(count($joFilterUbicaciones->joElementosIds) > 0) {
		$strWhereFilterUbicaciones .= " AND mbe.idElemento IN (";
		foreach ($joFilterUbicaciones->joElementosIds as $value) {
			$strWhereFilterUbicaciones .= $value. ",";
		}
		$strWhereFilterUbicaciones = substr($strWhereFilterUbicaciones, 0, -1).") ";
	}

	if(count($joFilterUbicaciones->joEVPIds) > 0) {
		$strWhereFilterUbicaciones .= " AND mbe.idEmpresa IN (";
		foreach ($joFilterUbicaciones->joEVPIds as $value) {
			$strWhereFilterUbicaciones .= $value. ",";
		}
		$strWhereFilterUbicaciones = substr($strWhereFilterUbicaciones, 0, -1).") ";
	}

	//$strWhereFilterUbicaciones .= "mb.idMapbuses = 3 AND ";

	$rsBuses = $DB->Execute($sSQL.$strWhereFilterUbicaciones);

	$i=0;
	while(!$rsBuses->EOF)
	{
		$arrJSON->polyline[$i]['id'] = "buses_".$rsBuses->fields("idMapBuses").$rsBuses->fields("idRecorrido");
		$arrJSON->polyline[$i]['encodePath'] = $rsBuses->fields("recorrido");
		$arrJSON->polyline[$i]['color'] = $rsBuses->fields("color");
		$arrJSON->polyline[$i]['html'] = "Linea ".$rsBuses->fields("linea")." - Recorrido: ".$rsBuses->fields("idRecorrido");

		$rsBuses->MoveNext();
		$i++;
	}

	$arrJSON->imagesMapa = $imgFiltro;
	$arrJSON->status = "OK";

	return json_encode($arrJSON);
}

function fotosMappingShow()
{

	global $baseFotosMap;

	$DB = NewADOConnection('mysqli');
	$DB->Connect();
	$DB->Execute("SET NAMES utf8;");
	//$DB->debug=true;

	$strSQL = "SELECT * FROM map_ubicaciones WHERE idUbicacion = " . $_REQUEST['idUbicacion'];
	$rsCampanna = $DB->Execute($strSQL);

	$idEmpresa = $rsCampanna->fields('idEmpresa');
	$idUbicacion = $_REQUEST['idUbicacion'];

	$includedExtensions = array ('jpg', 'gif', 'png');

	$dirImages = $baseFotosMap.$idEmpresa;
	$i=0;
	if(file_exists($dirImages)) {
		if ($filesFotosMapping = opendir($dirImages)) {
			while (false !== ($image = readdir($filesFotosMapping))) {
				if($image != "." && $image != "..") {
					$extn = explode('.', $image);
		 		    $extn = array_pop($extn);
					if (in_array(strtolower($extn),$includedExtensions)) {
						if(strpos($image, $idUbicacion) !== false) {
							$arrImages[] = array("image" => $dirImages."/".$image);
							$i++;
						}
					}
				}
	    	}
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

function searchGlosario()
{
	require("includes/constants.php");
	require("includes/datatables_db/Datatables.php");

	$datatables = new Datatables();

	$datatables
				->select('idGlosario, descripcion, otrosNombres')
				->from('glosario')
				->add_column('edit', '<img src="images/details_open.png">');


	return $datatables->generate();
}

function searchGlosarioDetalle()
{

	require("includes/constants.php");

	$DB = NewADOConnection('mysqli');
	$DB->Connect();
	$DB->Execute("SET NAMES utf8;");
	//$DB->debug=true;

	$sSQL = "SELECT * FROM glosario WHERE idGlosario = ".$_REQUEST["idGlosario"];
	$rsDetails = $DB->Execute($sSQL);

	if($rsDetails->EOF)
		return "";

	$strOutput .= "<tr>
						<td colspan=2><img align='left' src='images/glosario/".$rsDetails->fields('archivo')."'></td>
				   </tr>
				   <tr>
						<td>Medidas</td>
						<td>".$rsDetails->fields('medidas')."</td>
					</tr>
					<tr>
						<td>Periodo</td>
						<td>".$rsDetails->fields("periodo")."</td>
					</tr>
					<tr>
						<td>Otros Nombres</td>
						<td>".$rsDetails->fields("otrosNombres")."</td>
					</tr>";

	return $strOutput;
}

function searchCampannas()
{

	require("includes/constants.php");
	require("includes/datatables_db/Datatables.php");

	$datatables = new Datatables();

	$datatables
				->select('idCampania, descripcion')
				->from('map_campanias')
				->add_column('edit', '<img src="images/details_open.png">')
				->where('estado <>', $stateErase)
				->where('idUsuario = ', $_SESSION['idUser']);
/*
	if($_SESSION['userType'] != $idTypeAdministrator){
		$datatables->where('idUsuario = ', $_SESSION['idUser']);
	}
	*/


	return $datatables->generate();
}

function addOrEditCampanna()
{
	require("includes/constants.php");

	$DB = NewADOConnection('mysqli');
	$DB->Connect();
	$DB->Execute("SET NAMES utf8;");
	//$DB->debug=true;

	$DB->StartTrans(); // start transaction

	if(!isset($_REQUEST["idCampanna"])){
		$strSQL = "INSERT INTO map_campanias ( ";
		$strSQL .= "	descripcion, ";
		$strSQL .= "	detalle, ";
		$strSQL .= "	filtro, ";
		$strSQL .= "	estado, ";
		$strSQL .= "	idUsuario ";
		$strSQL .= "	) VALUES ( ";
		$strSQL .= "	'".mb_strtoupper($_REQUEST["descripcion"], "UTF-8")."', ";
		$strSQL .= "	'".$_REQUEST["detalle"]."', ";
		$strSQL .= "	'', ";
		$strSQL .= "	'".$stateAdd."', ";
		$strSQL .= "	".$_SESSION['idUser'];
		$strSQL .= "	)";

		$DB->Execute($strSQL);

	}
	else{
		$strSQL = "UPDATE map_campanias SET ";
		$strSQL .= "	descripcion = '".mb_strtoupper($_REQUEST["descripcion"], "UTF-8")."', ";
		$strSQL .= "	detalle = '".$_REQUEST["detalle"]."', ";
		$strSQL .= "	estado = '".stateModify."' ";
		$strSQL .= " WHERE idCampania = ". $_REQUEST["idCampanna"];

		$DB->Execute($strSQL);

	}

	if (!$DB->CompleteTrans())
	{
		$arrJSON->status = "ERROR";
		$arrJSON->msg = 'Ocurri&oacute; un error al grabar la Campa&ntilde;a';
	}
	else
	{
		$arrJSON->status = "OK";
		$arrJSON->msg = 'La Campa&ntilde;a se guard&oacute; correctamente';
	}
	return json_encode($arrJSON);
}

function editCampanna()
{
	require("includes/constants.php");

	$DB = NewADOConnection('mysqli');
	$DB->Connect();
	$DB->Execute("SET NAMES utf8;");
	//$DB->debug=true;

	$rsCampanna = $DB->Execute("SELECT * FROM map_campanias WHERE estado <> '$stateErase' and idCampania=" . $_REQUEST["idCampanna"]);

	if(!$rsCampanna->EOF){
		$joCampanna->data['descripcion'] = $rsCampanna->fields("descripcion");
		$joCampanna->data['detalle'] = $rsCampanna->fields("detalle");

		return json_encode($joCampanna->data);
	}
	else
		return json_encode(array());
}

function del()
{
	require("includes/constants.php");

	$DB = NewADOConnection('mysqli');
	$DB->Connect();
	$DB->Execute("SET NAMES utf8;");
	//$DB->debug=true;

	$DB->StartTrans(); // start transaction
	$DB->Execute("UPDATE map_campanias SET estado='$stateErase' WHERE idCampania=" . $_REQUEST["idCampanna"]);

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

function cargarFiltroCampanna()
{
	require("includes/constants.php");

	try
	{
		$DB = NewADOConnection('mysqli');
		$DB->Connect();
		$DB->Execute("SET NAMES utf8;");
		//$DB->debug=true;

		$rsCampanna = $DB->Execute("SELECT * FROM map_campanias WHERE estado <> '$stateErase' and idCampania=" . $_REQUEST["idCampanna"]);

		if(!$rsCampanna->EOF) {
			list($joFilterUbicaciones, $joMarkerNotVisibleIds,  $joUbicacionesCantidad) = explode("|", $rsCampanna->fields('filtro'));

			$arrJSON->status = "OK";
			$arrJSON->msg = "Se cargaron los datos de la Campa&ntilde;a ".$rsCampanna->fields('descripcion');

			$joUbu->joMedioIds = array();
			$joUbu->joFormatoIds = array();
			$joUbu->joElementosIds = array();
			$joUbu->joEVPIds = array();
			$joUbu->joProvinciaIds = array();
			$joUbu->joLocalidadIds = array();
			$joUbu->joEntidadPOIs = array();
			$joUbu->joFavoritosIds = array();

			$arrJSON->joFilterUbicaciones = $joUbu;
			$arrJSON->joMarkerNotVisibleIds = $joMarkerNotVisibleIds;
			$arrJSON->joUbicacionesCantidad = $joUbicacionesCantidad;
			$arrJSON->titleCampanna = $rsCampanna->fields('descripcion');
		}
		else {
			$arrJSON->status = "ERR";
			$arrJSON->titleCampanna = "";
			$arrJSON->msg = 'Se produjo un error al cargar los datos de la Campa&ntilde;a';
		}
	}
	catch(Exception $e)
	{
		$arrJSON->status = "ERR";
		$arrJSON->msg = 'Se produjo un error al cargar los datos de la Campa&ntilde;a';
	}

	return json_encode($arrJSON);
}

function rearmarFiltroCampanna($arrJsones)
{
	require("includes/constants.php");

	try
	{
		$arrFiltro = array();
		$arrNotVisible = array();
		$arrUbicacionesCantidad = array();

		foreach ($arrJsones as $jsonObj)
		{
			list($joFilterUbicaciones, $joMarkerNotVisibleIds, $joUbicacionesCantidad) = explode("|", $jsonObj);

			$arrFiltroArchivoUbicaciones = json_decode($joFilterUbicaciones);
			$arrMarkerNotVisibleIds = json_decode($joMarkerNotVisibleIds, true);
			$arrUbicacionesCantidadObj = json_decode($joUbicacionesCantidad);

			if(isset($arrFiltro->joMedioIds))
				$arrFiltro->joMedioIds = array_unique(array_merge($arrFiltro->joMedioIds, $arrFiltroArchivoUbicaciones->joMedioIds));
			else
				$arrFiltro->joMedioIds = $arrFiltroArchivoUbicaciones->joMedioIds != null ? $arrFiltroArchivoUbicaciones->joMedioIds : array();

			if(isset($arrFiltro->joFormatoIds))
				$arrFiltro->joFormatoIds = array_unique(array_merge($arrFiltro->joFormatoIds, $arrFiltroArchivoUbicaciones->joFormatoIds));
			else
				$arrFiltro->joFormatoIds = $arrFiltroArchivoUbicaciones->joFormatoIds != null ? $arrFiltroArchivoUbicaciones->joFormatoIds : array();

			if(isset($arrFiltro->joEVPIds))
				$arrFiltro->joEVPIds = array_unique(array_merge($arrFiltro->joEVPIds, $arrFiltroArchivoUbicaciones->joEVPIds));
			else
				$arrFiltro->joEVPIds = $arrFiltroArchivoUbicaciones->joEVPIds != null ? $arrFiltroArchivoUbicaciones->joEVPIds : array();

			if(isset($arrFiltro->joProvinciaIds))
				$arrFiltro->joProvinciaIds = array_unique(array_merge($arrFiltro->joProvinciaIds, $arrFiltroArchivoUbicaciones->joProvinciaIds));
			else
				$arrFiltro->joProvinciaIds = $arrFiltroArchivoUbicaciones->joProvinciaIds != null ? $arrFiltroArchivoUbicaciones->joProvinciaIds : array();

			if(isset($arrFiltro->joLocalidadIds))
				$arrFiltro->joLocalidadIds = array_unique(array_merge($arrFiltro->joLocalidadIds, $arrFiltroArchivoUbicaciones->joLocalidadIds));
			else
				$arrFiltro->joLocalidadIds = $arrFiltroArchivoUbicaciones->joLocalidadIds != null ? $arrFiltroArchivoUbicaciones->joLocalidadIds : array();

			if(isset($arrNotVisible->joMarkerNotVisibleIds))
				$arrNotVisible = array_unique(array_merge($arrFiltro->joMarkerNotVisibleIds, $arrMarkerNotVisibleIds));

			if(isset($arrUbicacionesCantidadObj))
				$arrUbicacionesCantidad = array_merge($arrUbicacionesCantidadObj, $arrUbicacionesCantidad);
		}

		$temp_uids = array();
		$unique_UbicacionesCantidad = array();
		foreach($arrUbicacionesCantidad as $resultUbicacion){
			if(!in_array($resultUbicacion->idUbicacion, $temp_uids)){
				$temp_uids[]=$resultUbicacion->idUbicacion;
				$unique_UbicacionesCantidad[]=$resultUbicacion;
			}
		}

		$arrFinal->Filtro = $arrFiltro;
		$arrFinal->NotVisible = $arrNotVisible;
		$arrFinal->UbicacionesCantidad = $unique_UbicacionesCantidad;

		return $arrFinal;
	}
	catch(Exception $e)
	{
		return array();
	}
}

function searchCampannaDetalle()
{
	require("includes/constants.php");

	$DB = NewADOConnection('mysqli');
	$DB->Connect();
	$DB->Execute("SET NAMES utf8;");
	//$DB->debug=true;

	$rsArchivos = $DB->Execute("SELECT * FROM map_campanias_archivos WHERE idCampania = ".$_REQUEST['idCampanna']);

	while(!$rsArchivos->EOF){
		$strOutput .= '
			<tr>
				<td style="font-size: 12px;">'.$rsArchivos->fields("nombreArchivo").'</td>
				<td>
					<a href="#" title="Borrar Archivo de Campa&ntilde;a"  class="delArchivo" onclick="delArchivoCampanna('.$rsArchivos->fields("idCampaniaArchivos").','.$rsArchivos->fields("idCampania").')"></a>
					<a href="#" title="Descargar Archivo de Campa&ntilde;a" class="downloadArchivo" onclick="downloadArchivoCampanna('.$rsArchivos->fields("idCampaniaArchivos").','.$rsArchivos->fields("idCampania").')"></a>
				</td>
			</tr>';
		$rsArchivos->MoveNext();
	}
	return $strOutput;
}

function deleteArchivo()
{
	require("includes/constants.php");

	$DB = NewADOConnection('mysqli');
	$DB->Connect();
	$DB->Execute("SET NAMES utf8;");
	//$DB->debug=true;

	$DB->StartTrans(); // start transaction

	//Borro el Archivo y Actualizo la Campanna OJO que es sin transaccion.
	$DB->Execute("DELETE FROM map_campanias_archivos WHERE idCampaniaArchivos = ".$_REQUEST['idArchivoCampanna']);

	$rsArchivos = $DB->Execute("SELECT * FROM map_campanias_archivos WHERE idCampania = ".$_REQUEST["idCampanna"]);

	$arrJsones = array();
	while(!$rsArchivos->EOF){
		array_push($arrJsones, $rsArchivos->fields('dataArchivo'));
		$rsArchivos->MoveNext();
	}

	$arrFiltro = rearmarFiltroCampanna($arrJsones);

	$filtroCampanna = json_encode($arrFiltro->Filtro)."|".json_encode($arrFiltro->NotVisible)."|".json_encode($arrFiltro->UbicacionesCantidad);

	$strSQL = "UPDATE map_campanias SET ";
	$strSQL .= "	filtro = '".$filtroCampanna."' ";
	$strSQL .= " WHERE idCampania = ". $_REQUEST["idCampanna"];

	$DB->Execute($strSQL);

	if (!$DB->CompleteTrans()) {
		$arrJSON->msg = 'Se Produjo un Error al Eliminar el Archivo';
		$arrJSON->status = "ERR";
	}
	else {
		$arrJSON->status = "OK";
	}

	return json_encode($arrJSON);
}

function getEVPs()
{
	require("includes/constants.php");

	$DB = NewADOConnection('mysqli');
	$DB->Connect();
	$DB->Execute("SET NAMES utf8;");
	//$DB->debug=true;

	$rsEVP = $DB->Execute("SELECT * FROM map_empresas WHERE idEmpresa IN (SELECT idEVP FROM contratos_mapping WHERE idContrato = ".$_SESSION['idContratoMap'].") ORDER BY descripcion");

	$i=0;
	while(!$rsEVP->EOF){
		$arrEVP->data[$i]['idEmpresa'] = $rsEVP->fields('idEmpresa');
		$arrEVP->data[$i++]['descripcion'] = $rsEVP->fields('descripcion');
		$rsEVP->MoveNext();
	}

	return json_encode($arrEVP->data);
}

function getGeoplanningPlusEVP()
{
	require("includes/constants.php");

	$DB = NewADOConnection('mysqli');
	$DB->Connect();
	$DB->Execute("SET NAMES utf8;");
	//$DB->debug=true;

	$emp = json_decode($_REQUEST['joEVPMap']) ;

	if($emp == "") {
		$arrJSON->status = "EMPTY";
		return json_encode($arrJSON);
	}

	$rsEVP = $DB->Execute("SELECT * FROM map_empresas WHERE idEmpresa IN (".implode(",", $emp).") AND gpmas IN (0, 1)"); //Empresas que no son Geoplanning+

	$i=0;
	$arrEmpresas = array();
	while(!$rsEVP->EOF){
		$objEmp = "";
		$objEmp->descripcion = $rsEVP->fields('descripcion');
		$arrEmpresas[] = $objEmp;
		$rsEVP->MoveNext();
	}

	if(empty($arrEmpresas)) {
		$arrJSON->status = "EMPTY";
	}
	else {
		$arrJSON->status = "OK";
		$arrJSON->empresas = $arrEmpresas;
	}

	return json_encode($arrJSON);
}

function getElementos()
{
	require("includes/constants.php");

	$DB = NewADOConnection('mysqli');
	$DB->Connect();
	$DB->Execute("SET NAMES utf8;");
	//$DB->debug=true;

	$rsElemento = $DB->Execute("SELECT * FROM map_elementos ORDER BY descripcion");

	$i=0;
	while(!$rsElemento->EOF){
		$arrElemento->data[$i]['idElemento'] = $rsElemento->fields('idElemento');
		$arrElemento->data[$i++]['descripcion'] = $rsElemento->fields('descripcion');
		$rsElemento->MoveNext();
	}

	return json_encode($arrElemento->data);
}

function cargarExcel()
{
	require('includes/excel/excel_read/reader.php');
	require("includes/constants.php");

	try
	{
		$DB = NewADOConnection('mysqli');
		$DB->Connect();
		$DB->Execute("SET NAMES utf8;");
		//$DB->debug=true;

		if($_REQUEST['idEvp'] == "") {
			$arrJSON->status = "ERR";
			$arrJSON->msg = 'Seleccione una Empresa de V&iacute;a P&uacute;blica para la Grilla';
			$arrJSON->joUbicacionesCantidad = array();

		}
		else if($_REQUEST['idEvp2'] == "") {
			$arrJSON->status = "ERR";
			$arrJSON->msg = 'Seleccione una Empresa de V&iacute;a P&uacute;blica';
			$arrJSON->joUbicacionesCantidad = array();

		}
		else if($_REQUEST['idElemento'] == "") {
			$arrJSON->status = "ERR";
			$arrJSON->msg = 'Seleccione un Elemento';
			$arrJSON->joUbicacionesCantidad = array();
		}
		else {
			$data = new Spreadsheet_Excel_Reader();
			$data->setOutputEncoding('CP1251');

			$data->read("filter_files/".$_REQUEST['fileName']);

			$arrUbicaciones = array();
			$arrUbicacionesNotFound = array();

			$elementos = json_decode($_REQUEST['elementos']);
			if(empty($elementos))
				$elementos = array();
			$elementos[] = $_REQUEST['idElemento'];

			for ($i = 2; $i <= $data->sheets[0]['numRows']; $i++) {

				$idRef = $data->sheets[0]['cells'][$i][1];
				$dir =  str_replace("'", "''", $data->sheets[0]['cells'][$i][2]);

				$idEVP = $_REQUEST['idEvp'];
				$idEVP2 = $_REQUEST['idEvp2'] == "" ? "0" : $_REQUEST['idEvp2'];

				$sSQL = "SELECT * FROM map_ubicaciones
							WHERE
								bajaLogica = 0 AND
								id_referencia = '$idRef' AND
								idEmpresa IN ($idEVP, $idEVP2) AND
								idElemento IN (".implode(",", $elementos).")";

				$rsUbicacion = $DB->Execute($sSQL);

				//Busco por ID Referencia
				if(!$rsUbicacion->EOF)
				{
					$idUbicacion = $rsUbicacion->fields('idUbicacion');
					$encArray = false;

					foreach($arrUbicaciones as $rowUbicacion) {
						if($rowUbicacion->idUbicacion == $idUbicacion)
						{
							$rowUbicacion->cantidad += $data->sheets[0]['cells'][$i][4];
							$encArray = true;
						}
					}

					if(!$encArray)
					{
						$objUbicacion = "";
						$objUbicacion->idUbicacion = $idUbicacion;
						$objUbicacion->cantidad = $data->sheets[0]['cells'][$i][4];

						array_push($arrUbicaciones, $objUbicacion);
					}
				}
				else {

					$sSQL = "SELECT * FROM map_ubicaciones
								WHERE
									bajaLogica = 0 AND
									direccion like '%$dir%' AND
									idEmpresa IN ($idEVP, $idEVP2) AND
									idElemento IN (".implode(",", $elementos).")";

					$rsUbicacion = $DB->Execute($sSQL);

					//Busco por Direccion
					if(!$rsUbicacion->EOF)
					{
						$idUbicacion = $rsUbicacion->fields('idUbicacion');
						$encArray = false;

						foreach($arrUbicaciones as $rowUbicacion) {
							if($rowUbicacion->idUbicacion == $idUbicacion)
							{
								$rowUbicacion->cantidad += $data->sheets[0]['cells'][$i][4];
								$encArray = true;
							}
						}

						if(!$encArray)
						{
							$objUbicacion = "";
							$objUbicacion->idUbicacion = $idUbicacion;
							$objUbicacion->cantidad = $data->sheets[0]['cells'][$i][4];

							array_push($arrUbicaciones, $objUbicacion);
						}
					}
					else
						array_push($arrUbicacionesNotFound, $data->sheets[0]['cells'][$i][1]);
				}
			}

			if(!empty($arrUbicacionesNotFound)) {

				$strNotFound = "<ul><li>" . implode("</li><li>", $arrUbicacionesNotFound) . "</li></ul>";

				$arrJSON->status = "ERR";
				$arrJSON->msg = 'Las siguientes ubicaciones no se encuentran en la Base de Datos: <br/>'.$strNotFound;
				$arrJSON->joUbicacionesCantidad = $arrUbicaciones;
			}
			else {
				$arrJSON->status = "OK";
				$arrJSON->msg = 'Se cargó el archivo Excel correctamente';
				$arrJSON->joUbicacionesCantidad = $arrUbicaciones;
			}
		}
	}
	catch(Exception $e)
	{
		$arrJSON->status = "ERR";
		$arrJSON->msg = 'Se produjo un error al cargar el archivo Excel';
		$arrJSON->joUbicacionesCantidad = array();
	}

	return json_encode($arrJSON);
}

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

	$joUbicaciones = json_decode($_REQUEST["joUbiAudiencia"]);
	$joUbiBusesCantidad = json_decode($_REQUEST["joUbiBusesCantidad"]);
	$esBuses = $_REQUEST["esBuses"];
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
	$strSQL .= "	'".($esBuses == "true" ? "VPM" : "MAP")."' ";
	$strSQL .= "	) ";

	$DB->Execute($strSQL);
	$insert_ID = $DB->Insert_ID();

	if($esBuses == "true") {
		foreach ($joUbiBusesCantidad as $ubi)
		{
			$strSQL = "INSERT INTO map_procesos_detalle ( ";
			$strSQL .= "	ID, ";
			$strSQL .= "	idUbicacion, ";
			$strSQL .= "	idElemento, ";
			$strSQL .= "	idMapBuses, ";
			$strSQL .= "	idRecorrido, ";
			$strSQL .= "	Coeficiente, ";
			$strSQL .= "	Cantidad_Buses ";
			$strSQL .= "	) VALUES ( ";
			$strSQL .= "	'".$insert_ID."', ";
			$strSQL .= "	'".$ubi->idUbicacion."', ";
			$strSQL .= "	'".$ubi->idElemento."', ";
			$strSQL .= "	'".$ubi->idMapBuses."', ";
			$strSQL .= "	'"."1"."', ";
			$strSQL .= "	'"."0.99"."', ";
			$strSQL .= "	'".$ubi->cantidad."' ";
			$strSQL .= "	) ";

			$DB->Execute($strSQL);
		}
	}
	else {
		foreach ($joUbicaciones as $ubi)
		{
			$strSQL = "INSERT INTO map_procesos_detalle ( ";
			$strSQL .= "	ID, ";
			$strSQL .= "	idUbicacion ";
			$strSQL .= "	) VALUES ( ";
			$strSQL .= "	'".$insert_ID."', ";
			$strSQL .= "	'".$ubi."' ";
			$strSQL .= "	) ";

			$DB->Execute($strSQL);
		}
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

		number_format($n�mero, 2, ',', ' ');

	$objGeneral = "";
	$objGeneral->Target = "Edad: ".$Edad.", Sexo: ".$Genero.", NSE: ".$NSE;
	$objGeneral->Universo = number_format($rsResult->fields('Total_Personas_Universo'), 2, ',', '.');
	$objGeneral->CantUbicaciones = $rsCant->fields('canUbi');
	$objGeneral->CoberturaNeta = number_format($rsResult->fields('Cobertura'), 2, ',', '.');
	$objGeneral->Frecuencia = $rsResult->fields('Tasa_Repeticion');
	$objGeneral->Impactos = number_format($rsResult->fields('Total_Personas_Muestra'), 2, ',', '.');
	$objGeneral->Cobertura_Porc = $rsResult->fields('Cobertura_Porc');
	$objGeneral->PBR = round($rsResult->fields('Pbr'),2);
	$objGeneral->CPR = 0;
	$objGeneral->CPM = 0;
	$objGeneral->PersonasCoberturaxSexo = $rsResult->fields('PersonasCoberturaxSexo');
	$objGeneral->PersonasCoberturaxNSE = $rsResult->fields('PersonasCoberturaxNSE');
	$objGeneral->PersonasCoberturaxEDAD = $rsResult->fields('PersonasCoberturaxEDAD');

	$objGeneral->Detallada = array();
	$objGeneral->PorEmpresa = array();
	$objGeneral->PorElemento = array();
	$objGeneral->PorCircuito = array();

	//Detallada
	$sSQL = "SELECT mp.descripcion AS descEmpresa, mu.direccion, me.descripcion AS descElemento, ml.descripcion AS descLocalidad, mr.descripcion AS descProvincia, mpd.* FROM map_procesos_detalle mpd
					INNER JOIN map_ubicaciones mu ON mpd.idUbicacion = mu.idUbicacion
					INNER JOIN map_elementos me ON mu.idElemento = me.idElemento
					INNER JOIN map_empresas mp ON mu.idEmpresa = mp.idEmpresa
					INNER JOIN map_localidades ml ON mu.idLocalidad = ml.idLocalidad
					INNER JOIN map_provincias mr ON mu.idProvincia = mr.idProvincia
				WHERE mpd.ID = $insert_ID ";

	if($_SESSION['userType'] == $idTypeUser || $_SESSION['userType'] == $idTypeConsult)
		$sSQL .= "AND me.acumulaDatos = 0";

	$rsDetalle = $DB->Execute($sSQL);

	while(!$rsDetalle->EOF)
	{
		$objDetalle = "";
		// $objDetalle[] = "";
		$objDetalle[]= $rsDetalle->fields('descEmpresa');
		$objDetalle[]= $rsDetalle->fields('direccion');
		$objDetalle[]= $rsDetalle->fields('descLocalidad');
		$objDetalle[]= $rsDetalle->fields('descProvincia');
		$objDetalle[]= $rsDetalle->fields('descElemento');
		$objDetalle[]= $rsDetalle->fields('Cobertura') == 0 ? "*" : number_format($rsDetalle->fields('Cobertura'), 2, ',', '.');
		$objDetalle[]= $rsDetalle->fields('Tasa_Repeticion') == 0 ? "*" : number_format($rsDetalle->fields('Tasa_Repeticion'), 2, ',', '.');
		$objDetalle[]= $rsDetalle->fields('Cobertura_Porc') == 0 ? "*" : $rsDetalle->fields('Cobertura_Porc');
		$objDetalle[]= $rsDetalle->fields('Impactos') == "0" ? "*" : number_format($rsDetalle->fields('Impactos'), 2, ',', '.');
		$objDetalle[]= $rsDetalle->fields('Pbr') == 0 ? "*" : round($rsDetalle->fields('Pbr'),2);

		$objDetalle[]= 0;
		$objDetalle[]= 0;
		$objDetalle[]= "Edad: ".$Edad.", Sexo: ".$Genero.", NSE: ".$NSE;

		array_push($objGeneral->Detallada, $objDetalle);

		$rsDetalle->MoveNext();
	}

	//Empresa
	$rsDetalle = $DB->Execute("SELECT mp.descripcion AS descEmpresa, COUNT(mu.idUbicacion) AS canUbi, SUM(mpd.Impactos) AS Impactos, SUM(mpd.Contactos) AS Contactos, SUM(mpd.Pbr) AS Pbr FROM map_procesos_detalle mpd
									INNER JOIN map_ubicaciones mu ON mpd.idUbicacion = mu.idUbicacion
									INNER JOIN map_elementos me ON mu.idElemento = me.idElemento
									INNER JOIN map_empresas mp ON mu.idEmpresa = mp.idEmpresa
								WHERE mpd.ID = $insert_ID
									GROUP BY mp.idEmpresa");

	while(!$rsDetalle->EOF)
	{
		$objEmpresa = "";
		// $objEmpresa[]= "";
		$objEmpresa[]= $rsDetalle->fields('descEmpresa');
		$objEmpresa[]= $rsDetalle->fields('canUbi');
		$objEmpresa[]= 0;
		$objEmpresa[]= number_format($rsDetalle->fields('Impactos'), 2, ',', '.');
		$objEmpresa[]= number_format($rsDetalle->fields('Pbr'), 2, ',', '.');
		$objEmpresa[]= 0;
		$objEmpresa[]= 0;
		$objEmpresa[]= "Edad: ".$Edad.", Sexo: ".$Genero.", NSE: ".$NSE;

		array_push($objGeneral->PorEmpresa, $objEmpresa);

		$rsDetalle->MoveNext();
	}

	//Elemento
	$rsDetalle = $DB->Execute("SELECT me.descripcion AS descElemento, COUNT(mu.idUbicacion) AS canUbi, SUM(mpd.Impactos) AS Impactos, SUM(mpd.Contactos) AS Contactos, SUM(mpd.Pbr) AS Pbr FROM map_procesos_detalle mpd
									INNER JOIN map_ubicaciones mu ON mpd.idUbicacion = mu.idUbicacion
									INNER JOIN map_elementos me ON mu.idElemento = me.idElemento
								WHERE mpd.ID = $insert_ID
									GROUP BY me.idElemento");

	while(!$rsDetalle->EOF)
	{
		$objElemento = "";
		// $objElemento[]= "";
		$objElemento[]= $insert_ID;
		$objElemento[]= $rsDetalle->fields('descElemento');
		$objElemento[]= $rsDetalle->fields('canUbi');
		$objElemento[]= 0;
		$objElemento[]= number_format($rsDetalle->fields('Impactos'), 2, ',', '.');
		$objElemento[]= number_format($rsDetalle->fields('Pbr'), 2, ',', '.');
		$objElemento[]= 0;
		$objElemento[]= 0;
		$objElemento[]= "Edad: ".$Edad.", Sexo: ".$Genero.", NSE: ".$NSE;

		array_push($objGeneral->PorElemento, $objElemento);

		$rsDetalle->MoveNext();
	}

	//Circuito
	$rsDetalle = $DB->Execute("SELECT mp.descripcion AS descEmpresa, me.descripcion AS descElemento, ml.descripcion AS descLocalidad, COUNT(mu.idUbicacion) AS canUbi, SUM(mpd.Impactos) AS Impactos, SUM(mpd.Contactos) AS Contactos, SUM(mpd.Pbr) AS Pbr, map.descripcion AS descPlan FROM map_procesos_detalle mpd
			INNER JOIN map_ubicaciones mu ON mpd.idUbicacion = mu.idUbicacion
			INNER JOIN map_elementos me ON mu.idElemento = me.idElemento
			INNER JOIN map_empresas mp ON mu.idEmpresa = mp.idEmpresa
			INNER JOIN map_localidades ml ON ml.idLocalidad = mu.idLocalidad
			LEFT JOIN map_audiencia_planes map ON mpd.ID = map.idMapAudiencia
			WHERE mpd.ID = $insert_ID
			GROUP BY mu.idEmpresa, mu.idElemento, mu.idLocalidad");

	while(!$rsDetalle->EOF)
	{
		$objCircuito = "";
		$objCircuito[]= $rsDetalle->fields('descEmpresa');
		$objCircuito[]= $rsDetalle->fields('descElemento');
		$objCircuito[]= $rsDetalle->fields('descLocalidad');
		$objCircuito[]= $rsDetalle->fields('canUbi');
		$objCircuito[]= 0;
		$objCircuito[]= number_format($rsDetalle->fields('Impactos'), 2, ',', '.');
		$objCircuito[]= number_format($rsDetalle->fields('Pbr'), 2, ',', '.');
		$objCircuito[]= 0;
		$objCircuito[]= 0;
		$objCircuito[]= "Edad: ".$Edad.", Sexo: ".$Genero.", NSE: ".$NSE;

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

	//Header
	$worksheet->write(0, 0, html_entity_decode("Descripci�n", ENT_HTML5, 'ISO-8859-1'), $header);
	$worksheet->write(0, 1, html_entity_decode("Valor", ENT_HTML5, 'ISO-8859-1'), $header);

	$row = 0;
	while(!$rsResult->EOF)
	{
		$rsCant = $DB->Execute("SELECT COUNT(*) AS canUbi FROM map_procesos_detalle WHERE ID = ".$rsResult->fields('ID'));

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
	$sSQL = "SELECT mp.descripcion AS descEmpresa, mu.direccion, me.descripcion AS descElemento, ml.descripcion AS descLocalidad, mr.descripcion AS descProvincia, mpd.*, map.descripcion AS descPlan FROM map_procesos_detalle mpd
			INNER JOIN map_ubicaciones mu ON mpd.idUbicacion = mu.idUbicacion
			INNER JOIN map_elementos me ON mu.idElemento = me.idElemento
			INNER JOIN map_empresas mp ON mu.idEmpresa = mp.idEmpresa
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
	$worksheet->write(0, 13, html_entity_decode("Target", ENT_HTML5, 'ISO-8859-1'), $header);

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
		$worksheet->write($row, 12, 0);
		$worksheet->write($row++, 13, "Edad: ".$Edad.", Sexo: ".$Genero.", NSE: ".$NSE);

		$rsDetalle->MoveNext();
	}
	/////////////////////
	//Empresa
	////////////////////
	$worksheet =& $workbook->addworksheet("Empresa");
	$rsDetalle = $DB->Execute("SELECT mp.descripcion AS descEmpresa, COUNT(mu.idUbicacion) AS canUbi, SUM(mpd.Impactos) AS Impactos, SUM(mpd.Contactos) AS Contactos, SUM(mpd.Pbr) AS Pbr, map.descripcion AS descPlan FROM map_procesos_detalle mpd
			INNER JOIN map_ubicaciones mu ON mpd.idUbicacion = mu.idUbicacion
			INNER JOIN map_elementos me ON mu.idElemento = me.idElemento
			INNER JOIN map_empresas mp ON mu.idEmpresa = mp.idEmpresa
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
	$worksheet->write(0, 8, html_entity_decode("Target", ENT_HTML5, 'ISO-8859-1'), $header);

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
		$worksheet->write($row, 7, 0);
		$worksheet->write($row++, 8, "Edad: ".$Edad.", Sexo: ".$Genero.", NSE: ".$NSE);

		$rsDetalle->MoveNext();
	}
	///////////////////////
	//Elemento
	//////////////////////
	$worksheet =& $workbook->addworksheet("Elemento");
	$rsDetalle = $DB->Execute("SELECT me.descripcion AS descElemento, COUNT(mu.idUbicacion) AS canUbi, SUM(mpd.Impactos) AS Impactos, SUM(mpd.Contactos) AS Contactos, SUM(mpd.Pbr) AS Pbr, map.descripcion AS descPlan FROM map_procesos_detalle mpd
			INNER JOIN map_ubicaciones mu ON mpd.idUbicacion = mu.idUbicacion
			INNER JOIN map_elementos me ON mu.idElemento = me.idElemento
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
	$worksheet->write(0, 8, html_entity_decode("Target", ENT_HTML5, 'ISO-8859-1'), $header);

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
		$worksheet->write($row, 7, 0);
		$worksheet->write($row++, 8, "Edad: ".$Edad.", Sexo: ".$Genero.", NSE: ".$NSE);

		$rsDetalle->MoveNext();
	}

	///////////////////////
	//Circuito
	//////////////////////
	$worksheet =& $workbook->addworksheet("Circuito");
	$rsDetalle = $DB->Execute("SELECT mp.descripcion AS descEmpresa, me.descripcion AS descElemento, ml.descripcion AS descLocalidad, COUNT(mu.idUbicacion) AS canUbi, SUM(mpd.Impactos) AS Impactos, SUM(mpd.Contactos) AS Contactos, SUM(mpd.Pbr) AS Pbr, map.descripcion AS descPlan FROM map_procesos_detalle mpd
			INNER JOIN map_ubicaciones mu ON mpd.idUbicacion = mu.idUbicacion
			INNER JOIN map_elementos me ON mu.idElemento = me.idElemento
			INNER JOIN map_empresas mp ON mu.idEmpresa = mp.idEmpresa
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
	$worksheet->write(0, 9, html_entity_decode("Target", ENT_HTML5, 'ISO-8859-1'), $header);

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
		$worksheet->write($row, 8, 0);
		$worksheet->write($row++, 9, "Edad: ".$Edad.", Sexo: ".$Genero.", NSE: ".$NSE);

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

	$rsAudGuardadas = $DB->Execute("SELECT * FROM map_audiencia_planes WHERE idUsuario = ".$_SESSION['idUser']);

	if($rsAudGuardadas->EOF)
		return json_encode(array());

	$i=0;
	while(!$rsAudGuardadas->EOF){
		$arrAudGuardadas->data[$i]['idMapAudiencia'] = $rsAudGuardadas->fields('idMapAudiencia');
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

	$DB->Execute("DELETE FROM map_audiencia_planes WHERE idMapAudiencia = ".$_REQUEST['idMapAudiencia']);

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
	$objGeneral->PorCircuito = array();

	//Detallada
	$sSQL = "SELECT mp.descripcion AS descEmpresa, mu.direccion, me.descripcion AS descElemento, ml.descripcion AS descLocalidad, mr.descripcion AS descProvincia, mpd.*, map.descripcion AS descPlan FROM map_procesos_detalle mpd
			INNER JOIN map_ubicaciones mu ON mpd.idUbicacion = mu.idUbicacion
			INNER JOIN map_elementos me ON mu.idElemento = me.idElemento
			INNER JOIN map_empresas mp ON mu.idEmpresa = mp.idEmpresa
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
		$objDetalle[] = $rsDetalle->fields('descPlan');
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
		$objDetalle[]= "Edad: ".$Edad.", Sexo: ".$Genero.", NSE: ".$NSE;

		array_push($objGeneral->Detallada, $objDetalle);

		$rsDetalle->MoveNext();
	}

	//Empresa
	$rsDetalle = $DB->Execute("SELECT mp.descripcion AS descEmpresa, COUNT(mu.idUbicacion) AS canUbi, SUM(mpd.Impactos) AS Impactos, SUM(mpd.Contactos) AS Contactos, SUM(mpd.Pbr) AS Pbr, map.descripcion AS descPlan FROM map_procesos_detalle mpd
			INNER JOIN map_ubicaciones mu ON mpd.idUbicacion = mu.idUbicacion
			INNER JOIN map_elementos me ON mu.idElemento = me.idElemento
			INNER JOIN map_empresas mp ON mu.idEmpresa = mp.idEmpresa
			LEFT JOIN map_audiencia_planes map ON mpd.ID = map.idMapAudiencia
			WHERE mpd.ID = $idAud
			GROUP BY mp.idEmpresa");

	while(!$rsDetalle->EOF)
	{
		$objEmpresa = "";
		$objEmpresa[] = $rsDetalle->fields('descPlan');
		$objEmpresa[]= $rsDetalle->fields('descEmpresa');
		$objEmpresa[]= $rsDetalle->fields('canUbi');
		$objEmpresa[]= 0;
		$objEmpresa[]= $rsDetalle->fields('Impactos');
		$objEmpresa[]= round($rsDetalle->fields('Pbr'),2);
		$objEmpresa[]= 0;
		$objEmpresa[]= 0;
		$objEmpresa[]= "Edad: ".$Edad.", Sexo: ".$Genero.", NSE: ".$NSE;

		array_push($objGeneral->PorEmpresa, $objEmpresa);

		$rsDetalle->MoveNext();
	}

	//Elemento
	$rsDetalle = $DB->Execute("SELECT me.descripcion AS descElemento, COUNT(mu.idUbicacion) AS canUbi, SUM(mpd.Impactos) AS Impactos, SUM(mpd.Contactos) AS Contactos, SUM(mpd.Pbr) AS Pbr, map.descripcion AS descPlan FROM map_procesos_detalle mpd
			INNER JOIN map_ubicaciones mu ON mpd.idUbicacion = mu.idUbicacion
			INNER JOIN map_elementos me ON mu.idElemento = me.idElemento
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
		$objElemento[]= "Edad: ".$Edad.", Sexo: ".$Genero.", NSE: ".$NSE;

		array_push($objGeneral->PorElemento, $objElemento);

		$rsDetalle->MoveNext();
	}

	//Circuito
	$rsDetalle = $DB->Execute("SELECT mp.descripcion AS descEmpresa, me.descripcion AS descElemento, ml.descripcion AS descLocalidad, COUNT(mu.idUbicacion) AS canUbi, SUM(mpd.Impactos) AS Impactos, SUM(mpd.Contactos) AS Contactos, SUM(mpd.Pbr) AS Pbr, map.descripcion AS descPlan FROM map_procesos_detalle mpd
			INNER JOIN map_ubicaciones mu ON mpd.idUbicacion = mu.idUbicacion
			INNER JOIN map_elementos me ON mu.idElemento = me.idElemento
			INNER JOIN map_empresas mp ON mu.idEmpresa = mp.idEmpresa
			INNER JOIN map_localidades ml ON ml.idLocalidad = mu.idLocalidad
			LEFT JOIN map_audiencia_planes map ON mpd.ID = map.idMapAudiencia
			WHERE mpd.ID = $idAud
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
		$objCircuito[]= "Edad: ".$Edad.", Sexo: ".$Genero.", NSE: ".$NSE;

		array_push($objGeneral->PorCircuito, $objCircuito);

		$rsDetalle->MoveNext();
	}

	$objGeneral->ID = $idAud;
	$objGeneral->status = "OK";

	return json_encode($objGeneral);
}

function getElementosExcel()
{
	require("includes/constants.php");
	require("includes/datatables_db/Datatables.php");

	$datatables = new Datatables();

	$table = $_REQUEST['table'];
	$fieldsOnly = $_REQUEST['fieldsOnly'];
	$idTable = $_REQUEST['idTable'];

	$arrFields = explode(",", $fields);

	$datatables->select('idElemento, descripcion');
	$datatables->from('map_elementos');
	$datatables->edit_column('idElemento', '<input type="checkbox" id="$1" name="$1" class="filterClassXLS" onclick="$_addRemoveElementoExcel(\'$1\')">', 'idElemento');

	if(isset($_REQUEST['idEVP2']) && $_REQUEST['idEVP2'] != "")
		$datatables->where("idElemento IN (SELECT idElemento FROM map_ubicaciones WHERE idEmpresa = " . $_REQUEST['idEVP2'] . ")");

	if(isset($_REQUEST['idEVP']) && $_REQUEST['idEVP'] != "")
		$datatables->where("idElemento IN (SELECT idElemento FROM map_ubicaciones WHERE idEmpresa = " . $_REQUEST['idEVP'] . ")");

	return $datatables->generate();
}

function addOrEditAudienciaPlan()
{
	require("includes/constants.php");

	$DB = NewADOConnection('mysqli');
	$DB->Connect();
	$DB->Execute("SET NAMES utf8;");
	//$DB->debug=true;

	$DB->StartTrans(); // start transaction

	$strSQL = "INSERT INTO map_audiencia_planes ( ";
	$strSQL .= "	idMapAudiencia, ";
	$strSQL .= "	descripcion, ";
	$strSQL .= "	idUsuario ";
	$strSQL .= "	) VALUES ( ";
	$strSQL .= "	'".$_REQUEST["idMapAudiencia"]."', ";
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

function getCampannasGuardadas()
{
	require("includes/constants.php");

	$DB = NewADOConnection('mysqli');
	$DB->Connect();
	$DB->Execute("SET NAMES utf8;");
	//$DB->debug=true;

	$rsCampannas = $DB->Execute("SELECT * FROM map_campanias WHERE estado <> '$stateErase' AND idUsuario = ".$_SESSION['idUser']);

	$i=0;
	while(!$rsCampannas->EOF){
		$arrCampannas->data[$i]['idCampania'] = $rsCampannas->fields('idCampania');
		$arrCampannas->data[$i++]['descripcion'] = $rsCampannas->fields('descripcion');
		$rsCampannas->MoveNext();
	}

	return json_encode($arrCampannas->data);
}

function grabarCampannaFile()
{
	$DB = NewADOConnection('mysqli');
	$DB->Connect();
	$DB->Execute("SET NAMES utf8;");
	//$DB->debug=true;

	$fileName = strtoupper($_REQUEST['archivoCampanna']).".txt";

	try
	{
		if($_REQUEST['idCampaniaGuardada'] == "0")
		{
			$DB->StartTrans(); // start transaction

			$strSQL = "INSERT INTO map_campanias ( ";
			$strSQL .= "	descripcion, ";
			$strSQL .= "	detalle, ";
			$strSQL .= "	filtro, ";
			$strSQL .= "	estado, ";
			$strSQL .= "	idUsuario ";
			$strSQL .= "	) VALUES ( ";
			$strSQL .= "	'".mb_strtoupper($_REQUEST["descripcionCampaniaNew"], "UTF-8")."', ";
			$strSQL .= "	'', ";
			$strSQL .= "	'', ";
			$strSQL .= "	'".$stateAdd."', ";
			$strSQL .= "	".$_SESSION['idUser'];
			$strSQL .= "	)";

			$DB->Execute($strSQL);

			$campanna_ID = $DB->Insert_ID();

			$fileContent = $_REQUEST['joFilterUbicaciones']."|".$_REQUEST['joMarkerNotVisibleIds']."|".$_REQUEST['joUbicacionesCantidad'];

			$sSQL = "INSERT INTO map_campanias_archivos (";
			$sSQL .= "	idCampania, ";
			$sSQL .= "	nombreArchivo, ";
			$sSQL .= "	dataArchivo ";
			$sSQL .= "	) VALUES ( ";
			$sSQL .= "	'".$campanna_ID."', ";
			$sSQL .= "	'".$fileName."', ";
			$sSQL .= "	'".$fileContent."' ";
			$sSQL .= "	)";

			$DB->Execute($sSQL);

			$rsArchivos = $DB->Execute("SELECT * FROM map_campanias_archivos WHERE idCampania = ".$campanna_ID);

			$arrJsones = array();
			while(!$rsArchivos->EOF){
				array_push($arrJsones, $rsArchivos->fields('dataArchivo'));
				$rsArchivos->MoveNext();
			}

			$arrFiltro = rearmarFiltroCampanna($arrJsones);

			$filtroCampanna = json_encode($arrFiltro->Filtro)."|".json_encode($arrFiltro->NotVisible)."|".json_encode($arrFiltro->UbicacionesCantidad);

			$DB->Execute("UPDATE map_campanias SET filtro = '$filtroCampanna' WHERE idCampania=" . $campanna_ID);

			if (!$DB->CompleteTrans())
			{
				$arrJSON->status = "ERR";
				$arrJSON->msg = 'Se Produjo un Error al Guardar el Filtro en la Campa&ntilde;a';
			}
			else {
				$arrJSON->status = "OK";
				$arrJSON->msg = 'Se cargó el filtro del archivo en la Campa&ntilde;a';
			}
		}
		else {

			$DB->StartTrans(); // start transaction

			$fileContent = $_REQUEST['joFilterUbicaciones']."|".$_REQUEST['joMarkerNotVisibleIds']."|".$_REQUEST['joUbicacionesCantidad'];

			$sSQL = "INSERT INTO map_campanias_archivos (";
			$sSQL .= "	idCampania, ";
			$sSQL .= "	nombreArchivo, ";
			$sSQL .= "	dataArchivo ";
			$sSQL .= "	) VALUES ( ";
			$sSQL .= "	'".$_REQUEST['idCampaniaGuardada']."', ";
			$sSQL .= "	'".$fileName."', ";
			$sSQL .= "	'".$fileContent."' ";
			$sSQL .= "	)";

			$DB->Execute($sSQL);

			$rsArchivos = $DB->Execute("SELECT * FROM map_campanias_archivos WHERE idCampania = ".$_REQUEST['idCampaniaGuardada']);

			$arrJsones = array();
			while(!$rsArchivos->EOF){
				array_push($arrJsones, $rsArchivos->fields('dataArchivo'));
				$rsArchivos->MoveNext();
			}

			$arrFiltro = rearmarFiltroCampanna($arrJsones);

			$filtroCampanna = json_encode($arrFiltro->Filtro)."|".json_encode($arrFiltro->NotVisible)."|".json_encode($arrFiltro->UbicacionesCantidad);

			$DB->Execute("UPDATE map_campanias SET filtro = '$filtroCampanna' WHERE idCampania=" . $_REQUEST['idCampaniaGuardada']);

			if (!$DB->CompleteTrans())
			{
				$arrJSON->status = "ERR";
				$arrJSON->msg = 'Se produjo un error al guardar el filtro en la Campa&ntilde;a';
			}
			else {
				$arrJSON->status = "OK";
				$arrJSON->msg = 'Se cargó el filtro del archivo en la Campa&ntilde;a';
			}
		}
	}
	catch(Exception $e)
	{
		$arrJSON->status = "ERR";
		$arrJSON->msg = 'Se produjo un error al cargar el filtro del Archivo';
	}

	return json_encode($arrJSON);
}

function grabarShareMap()
{
	$DB = NewADOConnection('mysqli');
	$DB->Connect();
	$DB->Execute("SET NAMES utf8;");
	//$DB->debug=true;

	$uniqueID = uniqid($_SESSION['idUser'].date('YmdHis'), true);
	$fileContent = $_REQUEST['joFilterUbicaciones']."|".$_REQUEST['joMarkerNotVisibleIds']."|".$_REQUEST['joUbicacionesCantidad'];

	try
	{
		$DB->StartTrans(); // start transaction

		$strSQL = "INSERT INTO map_compartir ( ";
		$strSQL .= "	idUsuario, ";
		$strSQL .= "	hash, ";
		$strSQL .= "	fecha, ";
		$strSQL .= "	vto, ";
		$strSQL .= "	filtro ";
		$strSQL .= "	) VALUES ( ";
		$strSQL .= "	'".$_SESSION['idUser']."', ";
		$strSQL .= "	'".$uniqueID."', ";
		$strSQL .= "	'".date('Y-m-d H:i:s')."', ";
		$strSQL .= "	'".date('Y-m-d H:i:s', dateAdd('d', 30, date('Y-m-d H:i:s')))."', ";
		$strSQL .= "	'".$fileContent."' ";
		$strSQL .= "	)";

		$DB->Execute($strSQL);

		if (!$DB->CompleteTrans())
		{
			$arrJSON->status = "ERR";
			$arrJSON->msg = 'Se produjo un error al compartir la Campa&ntilde;a';
		}
		else {
			$arrJSON->status = "OK";
			$arrJSON->msg = 'Se cargó la Campa&ntilde;a en el portapapeles';
			$arrJSON->uniqueID = $uniqueID;
		}
	}
	catch(Exception $e)
	{
		$arrJSON->status = "ERR";
		$arrJSON->msg = 'Se produjo un error al compartir la Campa&ntilde;a';
	}

	return json_encode($arrJSON);
}

function cargarShareMap()
{
	$DB = NewADOConnection('mysqli');
	$DB->Connect();
	$DB->Execute("SET NAMES utf8;");
	//$DB->debug=true;

	try
	{
		$rsArchivos = $DB->Execute("SELECT * FROM map_compartir WHERE hash = ?", array($_REQUEST["uniqueID"]));

		if($rsArchivos->EOF)
		{
			$arrJSON->status = "ERR";
			$arrJSON->msg = 'No existe el filtro de compartir Campa&ntilde;a';
		}
		else if(datediff("d", date("Y-m-d H:i:s"), $rsArchivos->fields('vto')) > 30)
		{
			$arrJSON->status = "ERR";
			$arrJSON->msg = 'El filtro del compartir Campa&ntilde;a está vencido';
		}
		else
		{
			list($joFilterUbicaciones, $joMarkerNotVisibleIds, $joUbicacionesCantidad) = explode("|", $rsArchivos->fields('filtro'));

			$arrJSON->status = "OK";
			$arrJSON->msg = 'Se cargó el filtro de compartir Campa&ntilde;a';
			$arrJSON->joFilterUbicaciones = $joFilterUbicaciones;
			$arrJSON->joMarkerNotVisibleIds = $joMarkerNotVisibleIds;
			$arrJSON->joUbicacionesCantidad = $joUbicacionesCantidad;
		}
	}
	catch(Exception $e)
	{
		$arrJSON->status = "ERR";
		$arrJSON->msg = 'Se produjo un error al cargar Compartir Campa&ntilde;a';
	}

	return json_encode($arrJSON);
}

switch($_REQUEST['actionOfForm'])
{
	case "searchMapping":
		echo searchMapping();
		break;

	case "actualizarMapa":
		echo actualizarMapa();
		break;

	case "actualizarXLSMapa":
		if($_REQUEST['fromXLS'] == 'S')
			echo actualizarXLSMapaUpload();
		else
			echo actualizarXLSMapa();
		break;

	case "cargarFiltro":
		echo cargarFiltro();
		break;

	case "grabarFiltro":
		echo grabarFiltro();
		break;

	case "fotosMappingShow":
		echo fotosMappingShow();
		break;

	case "exportXLS":
		echo exportXLS();
		break;

	case "actualizarPOIsMapa":
		echo actualizarPOIsMapa();
		break;

	case "actualizarBusesMapa":
		echo actualizarBusesMapa();
		break;

	case "searchGlosario":
		echo searchGlosario();
		break;

	case "searchGlosarioDetalle":
		echo searchGlosarioDetalle();
		break;

	case "searchCampannas":
		echo searchCampannas();
		break;

	case "addOrEditCampanna":
		echo addOrEditCampanna();
		break;

	case "EDIT":
		echo editCampanna();
		break;

	case "DELETE":
		echo del();
		break;

	case "cargarFiltroCampanna":
		echo cargarFiltroCampanna();
		break;

	case "searchCampannaDetalle":
		echo searchCampannaDetalle();
		break;

	case "deleteArchivo":
		echo deleteArchivo();
		break;

	case "getElementos":
		echo getElementos();
		break;

	case "getEVPs":
		echo getEVPs();
		break;

	case "getGeoplanningPlusEVP":
		echo getGeoplanningPlusEVP();
		break;

	case "cargarExcel":
		echo cargarExcel();
		break;

	case "getElementosExcel":
		echo getElementosExcel();
		break;

	//Audiencia
	case "searchFiltroBuses":
		echo searchFiltroBuses();
		break;

	case "searchFiltroComun":
		echo searchFiltroComun();
		break;

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

	case "getCampannasGuardadas":
		echo getCampannasGuardadas();
		break;

	case "grabarCampannaFile":
		echo grabarCampannaFile();
		break;

	case "downloadArchivoCampanna":
		echo downloadArchivoCampanna();
		break;

	case "grabarShareMap":
		echo grabarShareMap();
		break;

	case "cargarShareMap":
		echo cargarShareMap();
		break;

}
?>
