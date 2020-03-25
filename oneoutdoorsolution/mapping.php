<?php
if (session_status() == PHP_SESSION_NONE) {
		session_start();
}
require("includes/constants.php");
?>

	<!-- Global site tag (gtag.js) - Google Analytics -->
	<script async src="https://www.googletagmanager.com/gtag/js?id=UA-116228558-1"></script>
	<script>
		window.dataLayer = window.dataLayer || [];
		function gtag(){dataLayer.push(arguments);}
		gtag('js', new Date());
		gtag('config', 'UA-116228558-1');
	</script>

<!-- File Upload Files -->
<link href="css/fileuploader.css" rel="stylesheet" type="text/css">
<script src="js/fileuploader.js" type="text/javascript"></script>
<script src="js/jquery/jquery.blockUI-2.53.js" type="text/javascript"></script>

<!-- Script Mapping Form -->
<script src="http://maps.google.com/maps/api/js?libraries=drawing,geometry&key=AIzaSyANGywbfxItEbdle738SiU-AVJGIjadVYM" type="text/javascript"></script>
<script src="js/jquery/jquery.gomap-1.3.2.js" type="text/javascript"></script>

<script src="js/mapping.js?<?=time();?>" type="text/javascript"></script>
<script src="js/autoFiltroMapping.js" type="text/javascript"></script>
<script src="js/autoFiltroEVPsMapping.js?<?=time();?>" type="text/javascript"></script>
<script src="js/autoFiltroPOIs.js" type="text/javascript"></script>
<script>
	$('#icon-mapping').addClass('active');
</script>


<div id="wrapper" class="d-flex flex-column flex-grow hidebottom hideaudiencia">
	<nav class="navbar padleft0 navbar-expand-lg navbar-light bg-light">
    <div class="tab">
      <a id="menu-toggle" href="#" class="menu-toggle glyphicon glyphicon-align-justify btn-menu toggle">
        <img id="arrow" src="images/arrow.png">
      </a>
    </div>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav mr-auto mt-0">
        <li id="filter" class="nav-item menu-toggle" title="Filtrar"></li>
      </ul>
      <ul id="toolbar" class="nav justify-content-end m-0 hidden">
				<li id="togglevista" class="nav-item toolsright gotoinicio" title="Alternar Vistas" style="display: none;">
					<a class="nav-link" href="#"><i class="fas fa-eye"></i></a>
				</li>
			  <li id="btnShareMap" class="nav-item toolsright" title="Compartir" style="display: none;">
        	<a class="nav-link" href="#"><i class="fas fa-share-alt-square" title="sss"></i></a>
        </li>
        <li id="btnAudiencia" class="nav-item toolsright" title="Evaluar" style="display: none;">
          <a class="nav-link" href="#" data-toggle="modal" data-target="#modalData"><i class="fas fa-chart-pie"></i></a>
        </li>
<?php if($_SESSION["userType"] != $idTypeConsult): ?>
        <li id="btnFavoritosGrid" class="nav-item toolsright desactivado" title="Favoritos" style="display: none;">
          <a class="nav-link" href="#"><i class="fas fa-star"></i></a>
        </li>
<?php endif; ?>
				<!--
        <li id="btnExportExcel" class="nav-item toolsright" title="Exportar a Excel" style="display: none;">
          <a class="nav-link" href="#"><i class="fas fa-file-excel"></i></a>
        </li>
				-->
      </ul>
    </div>
  </nav>
	<div id="sidebar-wrapper">
		<div id="titlemenu" class="col-md-12 mt-2 hidden">
      <div class="form-group">
        <input type="text" class="form-control" id="titleCampanna" placeholder="Campaña sin nombre" disabled>
				<a href="#" id="btnGuardarCampanna" title="Guardar campaña..."><i class="fas fa-save"></i></a>
				<a href="#" id="descartarCampanna" title="Descartar..."><i class="fas fa-times"></i></a>
      </div>
		</div>
		<div id="filtermenu" class="col-md-12 mt-2 hidden">
<?php if($_SESSION["userType"] != $idTypeConsult) { ?>
			<div id="botonesMapping" class="text-center">
				<table id="tableOver">
					<tr>
						<!-- <td colspan="2">Medio</td> -->
						<!-- <td colspan="3"><a id="btnMedio" class="btnAccFilter btnAccB btn-secondary">Medio</a></td> -->
						<td colspan="3"><button id="btnMedio" class="btn btn-secondary btn-sm btn-block"><small>MEDIO</small></button></td>
					</tr>
					<tr>
						<!-- <td colspan="2">Formato</td> -->
						<!-- <td colspan="3"><a id="btnFormato" class="btnAccFilter btnAccB btn-secondary">Formato</a></td> -->
						<td colspan="3"><button id="btnFormato" class="btn btn-secondary btn-sm btn-block"><small>FORMATO</small></button></td>
					</tr>
					<tr>
						<!-- <td colspan="2">Elemento</td> -->
						<!-- <td colspan="3"><a id="btnElemento" class="btnAccFilter btnAccB btn-secondary">Elemento</a></td> -->
						<td colspan="3"><button id="btnElemento" class="btn btn-secondary btn-sm btn-block"><small>ELEMENTO</small></button></td>
					</tr>
					<tr>
						<!-- <td colspan="2">EVP</td> -->
						<!-- <td colspan="3"><a id="btnEVP" class="btnAccFilter btnAccB btn-secondary">EVP</a></td> -->
						<td colspan="3"><button id="btnEVP" class="btn btn-secondary btn-sm btn-block"><small>EVP</small></button></td>
					</tr>
					<tr>
						<!-- <td colspan="2">Provincia</td> -->
						<!-- <td colspan="3"><a id="btnProvincia" class="btnAccFilter btnAccB btn-secondary">Provincia</a></td> -->
						<td colspan="3"><button id="btnProvincia" class="btn btn-secondary btn-sm btn-block"><small>PROVINCIA</small></button></td>
					</tr>
					<tr>
						<!-- <td colspan="2">Localidad</td> -->
						<!-- <td colspan="3"><a id="btnLocalidad" class="btnAccFilter btnAccB btn-secondary">Localidad</a></td> -->
						<td colspan="3"><button id="btnLocalidad" class="btn btn-secondary btn-sm btn-block"><small>LOCALIDAD</small></button></td>
					</tr>
					<tr>
						<!-- <td colspan="2">POIs</td> -->
						<!-- <td colspan="3"><a id="btnPOIs" class="btnAccFilter btnAccB btn-secondary">POIs</a></td> -->
						<td colspan="3"><button id="btnPOIs" class="btn btn-secondary btn-sm btn-block"><small>POIs</small></button></td>
					</tr>
					<tr>
						<td colspan="3">
							<a class="dropdown-item" id="btnCargarFiltro" href="#"></a>
							<a class="dropdown-item" id="btnGrabarFiltro" href="#">Grabar Filtro</a>
							<a class="dropdown-item" id="btnMostrarFiltros" href="#">Ver Filtros</a>
						</td>
						</tr>
						<tr>
							<td colspan="3">
									<!--<a id="btnActulizarMapa_" style="font-size: 9px;" class="btnAccFilter btnAccB btn-secondary">Actualizar Mapa</a>-->
										<div class="form-group mb-2">
					            <button type="button" class="defaultfocus btn btn-primary btn-sm" id="btnActulizarMapa"><i class="fas fa-running"></i> Ejecutar</button>
					          </div>
										<div class="form-group mb-0">
					            <button type="button" class="btn btn-secondary btn-sm btn-block" id="btnReiniciar"><i class="fas fa-undo-alt"></i> <small>RESET</small></button>
					          </div>
									</td>
								</tr>
								<tr><td colspan="3">&nbsp;</td></tr>
								<tr>
									<td colspan="2">Referencia del ícono</td>
									<td>
										<select name="tipoFiltro" id="tipoFiltro">
											<option value="1">EVP/Elemento</option>
											<option value="2">EVP</option>
											<option value="3">Elemento</option>
										</select>
									</td>
								</tr>
								<tr><td colspan="3">&nbsp;</td></tr>
								<tr><td colspan="3"><a href="#" id="test">test</a></td></tr>
								<tr><td colspan="3"><input type="number" step="50" min="0" value="0" id="dynamicradio"></tr>
								<!--
								<tr>
									<td><div id="btnCargarFiltro"></div></td>
									<td><div id="btnGrabarFiltro" class="btnAccFilter btnAccB btn-secondary">Grabar Filtro</div></td>
									<td><a id="btnMostrarFiltros" class="btnAccFilter btnAccB btn-secondary">Ver Filtros</a></td>
								</tr>
								-->
								<!--
								<tr>
									<td></td>
									<td colspan="2"><div id="btnGrabarMapa" class="btnAccFilter btnAccB btn-secondary width90">Grabar Mapa</div></td>
									<td colspan="2" class="text-right"><button id="btnGrabarMapa" type="button" class="btn btn-secondary btn-sm btn-block"><small>GRABAR MAPA</small></button></td>
								</tr>
								-->
								<!--
								<tr>
									<td></td>
									<td colspan="2"><div id="btnUploadExcel" class="btnAccFilter btnAccB btn-secondary width90">Upload Excel</div></td>
									<td colspan="2" class="text-right"><button id="btnUploadExcel" type="button" class="btn btn-secondary btn-sm btn-block"><small>UPLOAD EXCEL</small></button></td>
								</tr>
								-->
								<!--
								<tr>
									<td></td>
									<td colspan="2"><div id="btnGlosario" class="btnAccFilter btnAccB btn-secondary width90">Glosario</div></td>
									<td colspan="2" class="text-right"><button id="btnGlosario" type="button" class="btn btn-secondary btn-sm btn-block"><small>GLOSARIO</small></button></td>
								</tr>
								-->
							</table>
						</div>
					<?php } else {?>
						<div id="botonesMapping">
							<table id="tableOver">
								<tr>
									<td colspan="2">Elemento</td>
									<td><a href="javascript:;" class="btnAccFilter btnAccB btn-secondary">Filtro</a></td>
								</tr>
								<tr>
									<td colspan="2">EVP</td>
									<td><a href="javascript:;" class="btnAccFilter btnAccB btn-secondary">Filtro</a></td>
								</tr>
								<tr>
									<td colspan="2">Provincia</td>
									<td><a href="javascript:;" class="btnAccFilter btnAccB btn-secondary">Filtro</a></td>
								</tr>
								<tr>
									<td colspan="2">Localidad</td>
									<td><a href="javascript:;" class="btnAccFilter btnAccB btn-secondary">Filtro</a></td>
								</tr>
								<tr>
									<td colspan="2">POIs</td>
									<td><a href="javascript:;" class="btnAccFilter btnAccB btn-secondary">Filtro</a></td>
								</tr>

								<tr>
									<!--<td colspan="2"><a href="javascript:;" style="font-size: 9px;" class="btnAccFilter btnAccB btn-secondary">Actualizar Mapa</a></td>-->
									<td colspan="3">
										<div class="form-group">
					            <button type="button" class="defaultfocus btn btn-primary btn-sm"><i class="fas fa-running"></i> Ejecutar</button>
					          </div>
									</td>
								</tr>

								<tr>
									<td colspan="3"><a class="dropdown-item" id="btnCargarFiltro" href="#"></a></td>
								</tr>
								<tr>
									<td colspan="3"><a href="javascript:;" class="btnAccFilter btnAccB btn-secondary">Grabar Mapa</a></td>
								</tr>
							</table>
							<input type="hidden" name="tipoFiltro" id="tipoFiltro" value="1">
						</div>
					<?php } ?>

    </div>

		<div id="mainmenu" class="col-md-12 mt-3">
			<button id="btnAbrir" type="button" class="btn btn-secondary btn-sm btn-block"><small>ABRIR</small></button>
			<button id="btnNueva" type="button" class="btn btn-secondary btn-sm btn-block"><small>NUEVA</small></button>
			<button id="btnUploadExcel" type="button" class="btn btn-secondary btn-sm btn-block"><small>UPLOAD EXCEL</small></button>
		</div>

			<!--
			<div class="col-md-12 mt-2">
				<table class="table90">
					<tr>
						<td></td>
						<td></td>
						<td></td>
					</tr>
					<tr>
						<td></td>
						<td></td>
						<td></td>
					</tr>
				</table>
				<div id="modIzqMapping">
					<div id="botonesAcciones">
						<div id="btnCampanna" class="btnAccFilter btnAccB">Campa&ntilde;as</div>
					</div>
				<div id="referenceTable" class="showTable">
					<div id="btnVerGrilla" class="btnAccFilter btnAccB btn-secondary">Ver Grilla</div>
				</div>
			</div>
		</div>
		-->

	</div>
	<!-- Botbar -->
	<div id="botbar-wrapper">
		<div id="botbar-content">
			<div class="gripcontainer"> <div id="griplines" class="text-center"><i class="fas fa-grip-lines"></i></div></div>
			<div class="row">
				<div class="col align-self-start ml-2 small mb-2"><button id="btnExportExcel" type="button" class="btn btn-secondary btn-sm" title="Exportar a Excel"><i class="fas fa-file-excel"></i> Exportar Excel</button></div>
				<div class="col mt-2 ml-2 small mb-2 text-center">Cantidad de Ubicaciones: <span id="countUbiGrid"></span></div>
				<div class="col align-self-end small mb-2 mr-2 text-right">Selección aleatoria de <input id="randomUbi" name="randomUbi" type="number" class=""> Ubicaciones</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<div id="tablaReferencias">
					<!--
					<div>
						<table id="tableRandom">
							<tr>
								<td>Selecci&oacute;n Aleatoria</td>
								<td><input id="randomUbi_" name="randomUbi" type="text" style="width: 100px"></td>
								<td>Ubicaciones</td>
							</tr>
							<tr>
								<td colspan="2">Cantidad de Ubicaciones:</td>
								<td><span id="countUbiGrid_"></span></td>
							</tr>
						</table>
					</div>
					-->

					<!--
					<a id="btnMaxMin" class="btnMaximizarGrid" title="Maximizar/Minimizar Grilla" style="margin: 5px;"></a>
					<a id="btnExportExcel_" class="btnExcelGrid" title="Exportar a Excel" style="margin: 5px;"></a>
					<?php //if($_SESSION["userType"] != $idTypeConsult) { ?>
						<a id="btnGuardarCampanna_" class="btnGuardarCampanna" title="Guardar Plan en Campa&ntilde;a" style="margin: 5px;"></a>
						<a id="btnFavoritosGrid_" class="desactivado" title="Seleccionar Favoritos" style="margin: 5px;"></a>
					<?php //}?>
					<a id="btnAudiencia_" class="btnAudiencia" title="Evaluar Plan" style="margin: 5px;"></a>
					<a id="btnShareMap_" class="btnShareMap" title="Compartir Plan" style="margin: 5px;"></a>
					-->

					<table id="dt_referencias" class="display">
						<thead>
							<tr>
								<th>&nbsp;</th>
								<th>EVP</th>
								<th>Direcci&oacute;n</th>
								<th>Provincia</th>
								<th>Localidad</th>
								<th>Elemento</th>
								<th>Tránsito</th>
								<th>Visibilidad</th>
								<th>Medidas</th>
								<th>ID Ref.</th>
								<th>AGIP</th>
								<th>Cantidad</th>
								<th class="text-center">
								<div class="custom-control custom-checkbox">
								  <input type="checkbox" class="custom-control-input" id="bulkfav">
								  <label class="custom-control-label" for="bulkfav"></label>
								</div>
								</th>
							</tr>
					    </thead>
					    <tbody>
					    	<tr>
					    		<td colspan="13" class="dataTables_empty">Cargando Datos...</td>
					    	</tr>
							</tbody>
						</table>
					<div>
					</div>
				</div>
			</div>
			</div>
		</div>
	</div>
	<!-- Fin Botbar -->
	<!-- INICIO Audiencias -->
	<div id="audiencias-wrapper" data-init="false">

	</div>
	<!-- FIN Audiencias -->
	<div id="outer" class="d-flex flex-column flex-grow">
		<div id="modDerMapping">
			<!-- <span id="titleCampanna" style="display: none;">Campa&ntilde;a: </span> -->
			<span id="titlePolyEnc" style="display: none;"></span>
		</div>
		<div id="mapping" class="h-100 flex-grow"></div>
	</div>
</div>

<!-- <img class="loader" src="images/ajax-loader-7.gif"> -->
<div class="loader">
	<!-- <div id="loaderCircle"></div> -->
</div>

<div id="mappingFotosDialog"></div>

<!-- PopUp Autofilter Dialog -->
<div id="autoFilterDialog" title=""></div>
<div id="glosarioDialog"></div>

<!-- PopUp Mapping Dialog -->
<div id="mappingExcelDialog" title="Upload Excel"></div>
<div id="mappingCampannaDialog" title="Mantenimiento de Campañas"></div>
<div id="mappingVerFiltros" title="Filtros"></div>
<div id="mappingAudienciaDialog" title="Evaluar Audiencias"></div>
<div id="mappingCampannasFileDialog" title="Guardar Campaña"></div>

<div id="mappingShareMapDialog" title="Compartir">
	<div id="overlay">
		<table id="tableOver">
			<tbody>
				<tr>
					<td>URL</td>
					<td>
						<textarea id="shareMapURL" name="shareMapURL" class="textArea"></textarea>
					</td>
				</tr>
			</tbody>
		</table>
		<a id="btnShareMapSalir" class="btnAcc btnAccB btn-secondary">Cerrar</a>
	</div>
</div>
<div id="globalMess" class="alert" role="alert">
	<!-- <div class="img"></div>-->
	<!-- <div id="globalMessImgClose" class="imgClose"></div> -->
	<!-- <div id="tituloMess" class="msgTitulo">TITULO</div> -->
	<span id="textoMess" class="msgTexto">MENSAJE</span>
	<button id="globalMessImgClose" type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
</div>
<script type="text/javascript" >
	var strMenssageErrorMandatoryFields = 'Faltan ingresar campos obligatorios';
	var strMenssageSelectionInGrid = 'Seleccione un Item en la Grilla';
</script>
<script>
	<?php
	$libPoly = '';
	if (isset($_SESSION["userType"])){
		$libPoly = $_SESSION["userType"] != $idTypeConsult ? 'drawing_lib' : '';
	};
	?>
	var libPoly = '<?php echo $libPoly; ?>';
	$_init();
</script>
<script src="js/nuevogp.js" type="text/javascript"></script>
<script src="js/chart.js" type="text/javascript"></script>
<script src="js/chartutils.js" type="text/javascript"></script>
