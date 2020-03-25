<?php
session_start();
if(!isset($_SESSION['userName']))
	exit();
?>

<link rel="stylesheet" href="js/jquery/jquery.treeview/jquery.treeview.css" />
<script src="js/jquery/jquery.treeview/jquery.treeview.js" type="text/javascript"></script>

<div id="overlay">
	<!-- <span id="filtrosTitulo">Filtros</span> -->
	<ul id="Filtros" class="filetree">
<?php
require("includes/funciones.inc.php");

$DB = NewADOConnection('mysqli');
$DB->Connect();
$DB->Execute("SET NAMES utf8;");
//$DB->debug=true;

$joFilterUbicaciones = json_decode($_REQUEST['joFilterUbicaciones']);

if (array_key_exists('joElementosIds', $joFilterUbicaciones)) {
	if(count($joFilterUbicaciones->joElementosIds) > 0) {
		$strWhereFilterElementos = "idElemento IN (";
		foreach ($joFilterUbicaciones->joElementosIds as $value) {
			$strWhereFilterElementos .= $value. ",";
		}
		$strWhereFilterElementos = substr($strWhereFilterElementos, 0, -1).") ";

		$rsElementos = $DB->Execute("SELECT * FROM map_elementos WHERE ".$strWhereFilterElementos);

		echo "<li class='closed'><span class='folder'>Elementos</span>";
		echo "<ul>";
		while(!$rsElementos->EOF)
		{
			echo "<li><span class='file'>".$rsElementos->fields('descripcion')."</span></li>";
			$rsElementos->MoveNext();
		}
		echo "</ul>";
		echo "</li>";
	}
}

if (array_key_exists('joEVPIds', $joFilterUbicaciones)) {
	if(count($joFilterUbicaciones->joEVPIds) > 0) {
		$strWhereFilterEVPs = "idEmpresa IN (";
		foreach ($joFilterUbicaciones->joEVPIds as $value) {
			$strWhereFilterEVPs .= $value. ",";
		}
		$strWhereFilterEVPs = substr($strWhereFilterEVPs, 0, -1).") ";

		$rsEVPs = $DB->Execute("SELECT * FROM map_empresas WHERE ".$strWhereFilterEVPs);

		echo "<li class='closed'><span class='folder'>EVPs</span>";
		echo "<ul>";
		while(!$rsEVPs->EOF)
		{
			echo "<li><span class='file'>".$rsEVPs->fields('descripcion')."</span></li>";
			$rsEVPs->MoveNext();
		}
		echo "</ul>";
		echo "</li>";
	}
}

if (array_key_exists('joProvinciaIds', $joFilterUbicaciones)) {
	if(count($joFilterUbicaciones->joProvinciaIds) > 0) {
		$strWhereFilterProvincias = "idProvincia IN (";
		foreach ($joFilterUbicaciones->joProvinciaIds as $value) {
			$strWhereFilterProvincias .= $value. ",";
		}
		$strWhereFilterProvincias = substr($strWhereFilterProvincias, 0, -1).") ";


		$rsProvincias = $DB->Execute("SELECT * FROM map_provincias WHERE ".$strWhereFilterProvincias);

		echo "<li class='closed'><span class='folder'>Provincias</span>";
		echo "<ul>";
		while(!$rsProvincias->EOF)
		{
			echo "<li><span class='file'>".$rsProvincias->fields('descripcion')."</span></li>";
			$rsProvincias->MoveNext();
		}
		echo "</ul>";
		echo "</li>";
	}
}

if (array_key_exists('joLocalidadIds', $joFilterUbicaciones)) {
	if(count($joFilterUbicaciones->joLocalidadIds) > 0) {
		$strWhereFilterLocalidades = "idLocalidad IN (";
		foreach ($joFilterUbicaciones->joLocalidadIds as $value) {
			$strWhereFilterLocalidades .= $value. ",";
		}
		$strWhereFilterLocalidades = substr($strWhereFilterLocalidades, 0, -1).") ";

		$rsProvincias = $DB->Execute("SELECT * FROM map_localidades WHERE ".$strWhereFilterLocalidades);

		echo "<li class='closed'><span class='folder'>Localidades</span>";
		echo "<ul>";
		while(!$rsProvincias->EOF)
		{
			echo "<li><span class='file'>".$rsProvincias->fields('descripcion')."</span></li>";
			$rsProvincias->MoveNext();
		}
		echo "</ul>";
		echo "</li>";
	}
}
?>
	</ul>
	<a id="btnVerFiltrosSalir" class="btnAcc btnAccB">Cerrar</a>
	<a id="btnLimpiarFiltros" class="btnAcc btnAccB">Limpiar Filtros</a>
</div>

<!-- PopUp Limpiar Filtros Dialog -->
<div id="cleanFilterDialog" style="display: none;">
	<span id="titulacion">Realmente desea Limpiar los Filtros de Busqueda?</span>
	<a class="btnAcc btnAccB" id="btnConfirmCleanFiltro">S&iacute;</a>
	<a class="btnAcc btnAccB" id="btnExitCleanFiltro">No</a>
</div>

<script>
	$(document).ready(function() {
		$_initVerFiltros();
	});
</script>
