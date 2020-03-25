/*
 * Created by Federico Pisarello - SisDev Software (c) 2012 - fpisarello@gmails.com
 */

var actionForm = 'mappingAction.php';
var popAutoFilter = "autoFilterMappingPop.php";
var popGlosario = "glosarioPop.php";
var popAutoFilterPOIs = "autoFilterPOIsPop.php";
var popAutoFilterEVPs = "autoFilterEVPsPop.php";
var popCampannas = "mappingCampannaPop.php";
var popUploadExcel = "mappingExcelPop.php";
var popCampannasItem = "mappingCampannaItemPop.php";
var popAudiencia = "mappingAudienciaPop.php";
var popAudienciaSaveForm = "mappingAudienciaSavePop.php";
var popCampannasFile = "mappingCampannaFilePop.php";

//Variables de Filtro Mapping
var joMedioMap = undefined;
var joFormatoMap = undefined;
var joElementosMap = undefined;
var joEVPMap = undefined;
var joProvinciaMap = undefined;
var joLocalidadMap = undefined;
var joEntidadPoisMap = undefined;
var joFavoritosMap = undefined;
var joPOISResultMap = undefined;
var joUbicacionesCantidad = [];
var joMarkerNotVisibleIds = [];
var joOverlayNotVisibleIds = [];
var arrDataPOIs = [];
var joImagenesMapa;
var joDataElementosExcel;
var bPopLoaded = false;
var bLoadExcel = false;

//Totales Grilla
var totUbicaciones = 0;
var totUbiVisibles = 0;

//Audiencia
var jsonAudiencia = undefined;
var idAudiencia = undefined;
var oTableFiltroPeriodo;
var oTableFiltroNSE;
var oTableFiltroSexo;
var oTableFiltroEdad;

var oTableAudGeneral;
var oTableAudDetallada;
var oTableAudEmpresa;
var oTableAudElemento;

// var oTableFiltroComun;

var jaDataPolygon = [
                     	{circlePois: false, mtsRadio: 0, allMarkers: false},
                     	{circleElementos: false, mtsRadio: 0},
                     	{circleEVPs: false, mtsRadio: 0}
                     ];

var joFilterUbicaciones = undefined;
var oTableMAP;
var oTableCampannas;
var joDataCampanna;
var emptyTable = true;
var bolPrintMarca = false;

var actionPKCampanna = undefined;
var action = undefined;

var iFilterMapping = new fn_colFilterMapping();

var currMapJson;
var joUbiAudiencia;
var markersJson = JSON.parse('{}');

function fn_colFilterMapping()
{
	this.MEDIO = 0;
	this.FORMATO = 1;
	this.ELEMENTO = 2;
	this.EVP = 3;
	this.PROVINCIA = 4;
	this.LOCALIDAD = 5;
	this.POIS = 6;
}

function MapScreenShot(controlDiv, map) {

        // Set CSS for the control border.
        var controlUI = document.createElement('div');
        //controlUI.id = 'btnGrabarMapa';
        controlUI.style.backgroundColor = '#fff';
        controlUI.style.border = '2px solid #fff';
        controlUI.style.borderRadius = '2px';
        controlUI.style.boxShadow = '0 2px 6px rgba(0,0,0,.3)';
        controlUI.style.cursor = 'pointer';
        controlUI.style.marginTop = '5px';
        controlUI.style.height = '24px';
        controlUI.style.textAlign = 'center';
        controlUI.title = 'Descargar mapa como imagen';
        controlDiv.appendChild(controlUI);

        // Set CSS for the control interior.
        var controlText = document.createElement('div');
        controlText.style.color = 'rgb(25,25,25)';
        controlText.style.fontFamily = 'Roboto,Arial,sans-serif';
        controlText.style.fontSize = '16px';
        controlText.style.paddingTop = '2px';
        controlText.style.paddingLeft = '5px';
        controlText.style.paddingRight = '5px';
        controlText.innerHTML = '<i class="fas fa-camera"></i>';
        controlUI.appendChild(controlText);

        // Setup the click event listeners: simply set the map to Chicago.

        controlUI.addEventListener('click', function() {
          //map.setCenter(chicago);
          //console.log('screenshot!');
          grabarMapa();
        });


      }

function $_init()
{

  currMapJson = '';

	joFavoritosMap = [];
  joPOISResultMap = [];
	joUbicacionesCantidad = [];

	joFilterUbicaciones = {
							joMedioIds : joMedioMap,
							joFormatoIds : joFormatoMap,
							joElementosIds : joElementosMap,
							joEVPIds: joEVPMap,
							joProvinciaIds : joProvinciaMap,
							joLocalidadIds: joLocalidadMap,
							joEntidadPOIs: joEntidadPoisMap,
							joFavoritosIds: joFavoritosMap,
              joPOISResultIds: joPOISResultMap
						};

  $('#filter').tooltip({trigger : 'hover'});
  $('#vistagraph').tooltip({trigger : 'hover'});
  $('#vistamap').tooltip({trigger : 'hover'});
	$('#btnShareMap').tooltip({container: '#wrapper', trigger : 'hover'});
	$('#btnAudiencia').tooltip({container: '#wrapper', trigger : 'hover'});
	$('#btnFavoritosGrid').tooltip({container: '#wrapper', trigger : 'hover'});
  $('#togglevista').tooltip({container: '#wrapper', trigger : 'hover'});
	$('#btnGuardarCampanna').tooltip({trigger : 'hover'});
  $('#descartarCampanna').tooltip({trigger : 'hover'});
	$('#btnExportExcel').tooltip({container: '#wrapper', trigger : 'hover'});
	$('#btnMaxMin').tooltip({trigger : 'hover'});

	$('.btnNav').each(function (i){
		$(this).removeClass($(this).attr('id')+'Act');
		$(this).addClass($(this).attr('id'));
	});
	$('#btnMapp').addClass('btnMappAct');

	$('#mapping').goMap({
		address: 'Argentina',
		zoom: 5,
		disableDefaultUI: true,
    zoomControl: true,
    mapTypeControl: false,
    scaleControl: true,
    streetViewControl: true,
    rotateControl: true,
    fullscreenControl: false,
		maptype: 'ROADMAP',
		addMarker: libPoly
	});

  var map = $.goMap.map;
  var mapScreenShotDiv = document.createElement('div');
  var mapScreenShot = new MapScreenShot(mapScreenShotDiv, map);

  mapScreenShotDiv.index = 1;
  map.controls[google.maps.ControlPosition.TOP_CENTER].push(mapScreenShotDiv);


	$('#titleCampanna').dblclick(function() {
		actionPKCampanna = undefined;
		$_limpiarFiltrosCampanna();
		$(this).val('');
	});

  $('#descartarCampanna').click(function() {

		actionPKCampanna = undefined;
		$_limpiarFiltrosCampanna();
    setMenuState('reset');
    if (!$("#wrapper").hasClass("hidebottom")) $("#wrapper").toggleClass("hidebottom");
    resetBotBar();

	});

  $('#btnReiniciar').click(function() {
		  $('#descartarCampanna').trigger('click');
	});

  $('#test').click(function() {
		  dynamicPOIs();
	});

  $('#dynamicradio').change(function() {
		  adjustPOIsRadio();
	});

	$('#btnGlosario').click(function() {
		$('#glosarioDialog').load(popGlosario).dialog('open');
	});

	$('#btnExportExcel').click( function(e) {
		var paramData = 'actionOfForm=exportXLS&joFilterUbicaciones=' + encodeURIComponent(JSON.stringify(joFilterUbicaciones)) + '&joMarkerVisibleIds=' +  encodeURIComponent($.goMap.getMarkers('Ids')) + '&joUbicacionesCantidad=' + encodeURIComponent(JSON.stringify(joUbicacionesCantidad));;
		window.open(actionForm + '?' + paramData, '_blank');
	});

  $('#bulkfav').click( function(e) {
		$_addMappingFavoritoBulk();
	});

	$('#btnGuardarCampanna').click( function(e) {
		$('#mappingCampannasFileDialog').load(popCampannasFile).dialog('open');
	});

	$('#randomUbi').blur(function(){
		var rowRandom = $(this).val();
		if(!isNaN(parseFloat(rowRandom)) && isFinite(rowRandom))
		{
			var uniqueRandoms = [];

			//Vacio los Favoritos
			oTableMAP.$('tr').each(function() {
				var aData = oTableMAP.fnGetData(this);
				var aPos = oTableMAP.fnGetPosition(this);
				var idFavorito = $(aData[12]).attr('id');
				var oFavorito = $('#'+idFavorito).attr('src');
				$('#'+idFavorito).attr('src', 'images/fav_desactivado_tooltip.png');
        $('#'+idFavorito).css('padding-bottom', '9px');

			    if($(this).is(":visible"))
			    	uniqueRandoms.push(aPos);
			});
			joFavoritosMap = [];

			function makeUniqueRandom() {
			    var index = Math.floor(Math.random() * uniqueRandoms.length);
			    var val = uniqueRandoms[index];

			    uniqueRandoms.splice(index, 1);

			    return val;
			}

			for (i = 0; i < rowRandom; i++) {
				var randomRow = makeUniqueRandom();
				var aData = oTableMAP.fnGetData(randomRow);
				var idFavorito = $(aData[12]).attr('id');
				$('#'+idFavorito).trigger('click');
			}
		}
		else
			$(this).val('');
	});

	$('#btnFavoritosGrid').click( function(e) {

    if (!$("#wrapper").hasClass("hideaudiencia")) return;

		totUbicaciones = 0; totUbiVisibles = 0;

		if($(this).hasClass('desactivado')) {
			oTableMAP.$('tr').each(function() {
			    var aData = oTableMAP.fnGetData(this);
			    var idFavorito = $(aData[12]).attr('id');
			    var oFavorito = $('#'+idFavorito).attr('src');
			    var idMarker = $('#'+idFavorito).attr('id').split("_")[2];
			    var idFabBuses = $('#'+idFavorito).data('buses');

			    totUbicaciones += 1;

			    if(oFavorito.indexOf('desactivado') != -1)
			    {
				    if(idFabBuses != "") {
				    	$_setVisibleOverlay(idFabBuses, false);
				    }

			    	$_setVisibleMarker(idMarker, false)
			    	$(this).hide();
			    }
			    else
			    	totUbiVisibles += 1;

			    $('#countUbiGrid').text(totUbiVisibles + ' de ' + totUbicaciones);
			});
			$(this).removeClass('desactivado');
			$(this).addClass('activado');
		}
		else {
			oTableMAP.$('tr').each(function() {
				var aData = oTableMAP.fnGetData(this);
			    var idFavorito = $(aData[12]).attr('id');
			    var oFavorito = $('#'+idFavorito).attr('src');
			    var idMarker = $('#'+idFavorito).attr('id').split("_")[2];
			    var idFabBuses = $('#'+idFavorito).data('buses');

			    totUbicaciones += 1;

			    if(idFabBuses != "") {
			    	$_setVisibleOverlay(idFabBuses, true);
			    }

			    totUbiVisibles += 1;

				$_setVisibleMarker(idMarker, true)
				$(this).show();
			});
			$(this).removeClass('activado');
			$(this).addClass('desactivado');

			$('#countUbiGrid').text(totUbiVisibles + ' de ' + totUbicaciones);
		}
	});

	$('#btnAudiencia').click(function(){

    // console.log(joMarkerNotVisibleIds);

    //La función que abre y cierra está en nuevogp.js
		//$('#mappingAudienciaDialog').load(popAudiencia).dialog('open');

    //todo:
    // si está desplegado el menú izquierdo, colapsar
    // toggle del botón Evaluar

    //if (!$("#wrapper").hasClass("hideaudiencia")) return;

    //console.log($('#audiencias-wrapper').data('init'));

    // if  ($('#audiencias-wrapper').attr('data-init') == 'true') return;

    // $_initAudienciaPop();

		$.ajax({
		    url: actionForm,
		    type:'POST',
		    data: 'actionOfForm=getGeoplanningPlusEVP&joEVPMap=' + encodeURIComponent(JSON.stringify(joEVPMap)),
		    dataType: 'json',
		    success: function(json) {
		    	if(json.status == "OK") {
		    		var strEmpresas = "<ul>";
		    		$.each(json.empresas, function(i, item) {
		    			strEmpresas += "<li>" + item.descripcion + "</li>";
				    });

		    		$_showMessage('ALERT', 'ALERTA', "Las siguientes empresas no forman parte del GeoPlanning MAS, por favor desestime las mismas del plan para poder evaluar:<br/>" + strEmpresas, 30000);
		    	}
		    }
		});

    // $('#audiencias-wrapper').attr('data-init','true');

	});

	$('#btnShareMap').click(function(e){

    if (!$("#wrapper").hasClass("hideaudiencia")) return;

		var jaUbicacionesCantidad = [];

		oTableMAP.$('tr').each(function() {
		    var aData = oTableMAP.fnGetData(this);
		    var id = $(aData[12]).attr('id');

		    if($(this).is(":visible")){
		    	var idUbicacion = $('#'+id).attr('id').split("_")[2];
		    	var cantidad = $('#cant_'+idUbicacion).text();

			    var joUbiCantidad = {
			    		"idUbicacion": idUbicacion,
			    		"cantidad": cantidad
			    };

			    jaUbicacionesCantidad.push(joUbiCantidad);
		    }
		});

		var paramData = 'actionOfForm=grabarShareMap' +
							'&joFilterUbicaciones=[]' +
							'&joMarkerNotVisibleIds=[]'+
							'&joUbicacionesCantidad=' + encodeURIComponent(JSON.stringify(jaUbicacionesCantidad));

		$.ajax({
	        type: 'POST',
	        url: actionForm,
	        data: paramData,
			dataType: 'json',
	        success: function(jsonObj){
	            if(jsonObj.status === 'OK'){
					$_showMessage('OK', jsonObj.status, jsonObj.msg);

					var $urlCopy = window.location.href.split('#')[0] + "&uniqueID=" + jsonObj.uniqueID;

					$('#shareMapURL').text($urlCopy);
					$('#mappingShareMapDialog').dialog('open');
				}
				else
					$_showMessage('ERR', jsonObj.status, jsonObj.msg);
	        }
	    });
	});

	//$('#btnCampanna').click(function(){

  $('#btnNueva').click(function(){
		$("#mainmenu").toggleClass("hidden");
    $("#filtermenu").toggleClass("hidden");
    $("#titlemenu").toggleClass("hidden");
	});

  $('#btnAbrir').click(function(){
		$('#mappingCampannaDialog').load(popCampannas).dialog('open');
	});

	$('#btnUploadExcel').click(function(){
		$('#mappingExcelDialog').load(popUploadExcel).dialog('open');
	});

	$('#btnGrabarFiltro').click( function(e) {
		$_grabarFiltro();
	});

	$('#btnActulizarMapa').click( function(e) {
		joMarkerNotVisibleIds = [];
		joOverlayNotVisibleIds = [];
		$_actulizarMapa();
	});

	$('#btnMostrarFiltros').click( function(e) {
		$('#mappingVerFiltros').load('mappingVerFiltrosPop.php?joFilterUbicaciones=' + encodeURIComponent(JSON.stringify(joFilterUbicaciones))).dialog('open');
	});

	$('#btnMaxMin').click( function(e) {
		if($(this).attr('class') == 'btnMaximizarGrid'){
			$('#tablaReferencias').css({top : '220px', height: '640px'});
			//$('div.dataTables_scrollBody').height('520px');
			$(this).attr("class", "btnMinimizarGrid");
		}
		else {
			$('#tablaReferencias').css({top : '620px', height: '280px'});
			//$('div.dataTables_scrollBody').height('120px');
			$(this).attr("class", "btnMaximizarGrid");
		}
	});

	$('#btnVerGrilla').click( function(e) {

    toggleBotBar();

    /*
		if($(this).parent().attr('class') == "showTable") {
			$(this).parent().removeClass('showTable');
			$(this).parent().addClass('hideTable');
			$('#tablaReferencias').show("slide", { direction: "left" }, 1000);
		}
		else {
			$(this).parent().removeClass('hideTable');
			$(this).parent().addClass('showTable');
			$('#tablaReferencias').hide("slide", { direction: "left" }, 1000);
		}
    */
	});

	oTableMAP = $('#dt_referencias').DataTable( {
					//"bJQueryUI": true,
          "searching" : false,
					"pagingType": "simple",
          //"sPaginationType": "two_button",
					"bInfo": false,
					"bAutoWidth": false,
			        //"sScrollY" : "120px",
			        "bScrollInfinite": true,
			        "bScrollCollapse" : false,
					"bLengthChange": false,
					"bProcessing": true,
					"bServerSide": true,
					"iDisplayLength": -1,
					"oLanguage": {
						"sUrl": "js/datatables/js/dataTables.es.txt"
					},
					"sAjaxSource": actionForm + "?actionOfForm=searchMapping&emptyTable=true",
					"fnServerParams": function ( aoData ) {
						aoData.push(
										{"name": "tipoFiltro", "value": $('#tipoFiltro').val()},
										{"name": "joFilterUbicaciones", "value": JSON.stringify(joFilterUbicaciones)},
										{"name": "joUbicacionesCantidad", "value": JSON.stringify(joUbicacionesCantidad)}
									);
					},
					"fnDrawCallback": function(oSettings, json) {
						var canNotVisible = 0;
						totUbicaciones = 0; totUbiVisibles = 0;

						oTableMAP.$('tr').each(function() {
						    var aData = oTableMAP.fnGetData(this);
						    var aPos = oTableMAP.fnGetPosition(this);
						    var idUbicacion = ($(aData[12]).attr('id')).replace('fav_grilla_', '');

						    totUbicaciones += 1;
						    if($.inArray(idUbicacion, joMarkerNotVisibleIds) > -1) {
						    	$(this).hide();
						    	canNotVisible += 1;
						    } else {
                  //la pusheo!
                  // console.log('pushing into joPOISResultMap!');
                  if (!joPOISResultMap.includes(idUbicacion)) joPOISResultMap.push(idUbicacion);
                }

						    if(!$.isEmptyObject(joUbicacionesCantidad) && bLoadExcel) {
							    $.each(joUbicacionesCantidad, function(i, item) {
							    	if(item.idUbicacion == idUbicacion) {
							    		$('#cant_'+idUbicacion).text(item.cantidad);
							    		$('#descElemento_'+idUbicacion).text($("#cmbExcelElemento option:selected").text());
							    		$('#descEmpresa_'+idUbicacion).text($("#cmbExcelEVP option:selected").text());
							    		return false;
							    	}
							    });
						    }
						});

						totUbiVisibles = totUbicaciones - canNotVisible;
						$('#countUbiGrid').text(totUbiVisibles + ' de ' + totUbicaciones);
					},
					"aoColumnDefs": [{
						"aTargets": [0],
						"fnCreatedCell": function (nTd, sData, oData, iRow, iCol) {
								var valRef = oData[0].split("|");
								$.each(joImagenesMapa, function(i, val){
									if(val.id == valRef[0]) {
										sHtml = "<img src='"+val.imagen+"' onclick='$_centerMapMarker("+valRef[1]+", "+valRef[2]+")' />";
										$(nTd).html(sHtml);
										return false;
									}
								});
			  	      		}
						}],
					"aoColumns": [
					  			{"sClass" : "center", "bSortable": false },
					  			null,
					  			null,
					  			null,
					  			null,
					  			null,
					  			null,
					  			null,
					  			null,
					  			null,
					  			null,
					  			null,
					  			{ "sWidth": "5%", "sClass": "center", "bSortable": false } //Favorito
					  		]

				});

	$('.dataTables_scrollBody').css('background-color', '#E2E4FF');

	emptyTable = false;

	createUploader();

	//Eventos de Autofiltro
	$('#btnMedio').click( function(e) {
		$_autofilterProxyShowMapping(iFilterMapping.MEDIO);
	});
	$('#btnFormato').click( function(e) {
		$_autofilterProxyShowMapping(iFilterMapping.FORMATO);
	});
	$('#btnElemento').click( function(e) {
		$_autofilterProxyShowMapping(iFilterMapping.ELEMENTO);
	});
	$('#btnEVP').click( function(e) {
		$_autofilterProxyShowMapping(iFilterMapping.EVP);
	});
	$('#btnProvincia').click( function(e) {
		$_autofilterProxyShowMapping(iFilterMapping.PROVINCIA);
	});
	$('#btnLocalidad').click( function(e) {
		$_autofilterProxyShowMapping(iFilterMapping.LOCALIDAD);
	});
	$('#btnPOIs').click( function(e) {
		$_autofilterProxyShowMapping(iFilterMapping.POIS);
	});

	$_initAutofilterDialog();

	$("#mappingShareMapDialog").dialog({
		autoOpen:false,
		height:'auto',
		width:400,
		modal: true,
		closeOnEscape: false,
		//position : ['center',10],
    position : { my: "center", at: "center", of: window },
		dialogClass:'no-close',
		open: function() {
			$('#mappingShareMapDialog').css('background','#F5F5F5');
			$('#btnShareMapSalir').click(function(e){
				$('#mappingShareMapDialog').dialog('close');
			});
		}
	});

	$("#mappingExcelDialog").dialog({
		autoOpen:false,
		height:640,
		width:400,
		modal: true,
		closeOnEscape: false,
		//position : ['center',10],
    position : { my: "center", at: "center", of: window },
		dialogClass:'no-close',
		open: function() {
			$('#mappingExcelDialog').css('background','#F5F5F5');
		}
	});


	$("#mappingAudienciaDialog").dialog({
		autoOpen:false,
		height:'auto',
		width:1200,
		modal: true,
		closeOnEscape: false,
		//position : ['center',10],
    position : { my: "center", at: "center", of: window },
		dialogClass:'no-close',
		open: function() {
			$('#mappingAudienciaDialog').css('background','#F5F5F5');
		}
	});

	$("#mappingCampannaDialog").dialog({
		autoOpen:false,
		height:600,
		width:760,
		modal: true,
		closeOnEscape: false,
		//position : ['center',10],
    position : { my: "center", at: "center", of: window },
		dialogClass:'no-close',
		close: function(){
			oTableCampannas = undefined;
		},
		open: function() {
			$('#mappingCampannaDialog').css('background','#F5F5F5');
		}
	});

	$("#mappingFotosDialog").dialog({
		autoOpen:false,
		height:'auto',
		width:760,
		modal: true,
		closeOnEscape: false,
		//position : ['center',10],
    position : { my: "center", at: "center", of: window },
		dialogClass:'no-close',
		open: function() {
			$('#mappingFotosDialog').css('background','#F5F5F5');
		}
	});

	$("#mappingVerFiltros").dialog({
		autoOpen:false,
		height:'auto',
		width:350,
		modal: true,
		resizable: false,
		closeOnEscape: false,
		//position : ['center',10],
    position : { my: "center", at: "center", of: window },
		dialogClass:'no-close',
		open: function() {
			$('#mappingVerFiltros').css('background','#F5F5F5');
		}
	});

	$("#glosarioDialog").dialog({
		autoOpen:false,
		height:'auto',
		width:550,
		modal: true,
		resizable: false,
		closeOnEscape: false,
		//position : ['center',10],
    position : { my: "center", at: "center", of: window },
		dialogClass:'no-close',
		open: function() {
			$('#glosarioDialog').css('background','#F5F5F5');
		}
	});

	$("#mappingCampannasFileDialog").dialog({
		autoOpen:false,
		height:'auto',
		width:600,
		modal: true,
		closeOnEscape: false,
		//position : ['center',10],
    position : { my: "center", at: "center", of: window },
		dialogClass:'no-close',
		open: function() {
			$('#mappingCampannaDialog').css('background','#F5F5F5');
		}
	});


	$(document).ready(function() {
    // console.log('$_initShareMap');
		setTimeout(function(){
			$_initShareMap();
		},2000);
	});
}

function grabarMapa() {
  html2canvas($(".gm-style"), {
    useCORS: true,
          onrendered: function(canvas) {
              theCanvas = canvas;
              canvas.toBlob(function(blob) {
                  saveAs(blob, "Mapa.png");
              });
          }
      });

}

function $_initVerFiltros()
{
	$("#Filtros").treeview();
	$('#btnVerFiltrosSalir').click( function(e) {
		$('#mappingVerFiltros').dialog('close');
	});

	$('#btnLimpiarFiltros').click( function(e) {
		$("#cleanFilterDialog").dialog({
			autoOpen: false,
			height:'auto',
			width:350,
			//position : ['center',10],
      position : { my: "center", at: "center", of: window },
			open: function () {
				$('#cleanFilterDialog').css('background','#F5F5F5');
				$('#btnConfirmCleanFiltro').click( function(e) {
					joMedioMap = undefined;
					joFormatoMap = undefined;
					joElementosMap = undefined;
					joEVPMap = undefined;
					joProvinciaMap = undefined;
					joLocalidadMap = undefined;
					joEntidadPoisMap = undefined;
					joFavoritosMap = [];
          joPOISResultMap = [];
					joMarkerNotVisibleIds = [];
					joOverlayNotVisibleIds = [];
					joUbicacionesCantidad = [];

					jaDataPolygon = [
					                     	{circlePois: false, mtsRadio: 0, allMarkers: false},
					                     	{circleElementos: false, mtsRadio: 0},
					                     	{circleEVPs: false, mtsRadio: 0}
					                     ];

					joFilterUbicaciones = {
							joMedioIds : undefined,
							joFormatoIds : undefined,
							joElementosIds : undefined,
							joEVPIds: undefined,
							joProvinciaIds : undefined,
							joLocalidadIds: undefined,
							joEntidadPOIs: undefined,
							joFavoritosIds: joFavoritosMap,
              joPOISResultIds: joPOISResultMap
						};

					$.goMap.clearMarkers();

					oTableMAP.fnReloadAjax(actionForm + "?actionOfForm=searchMapping&emptyTable=true");
					bolPrintMarca = false;

					$('#cleanFilterDialog').dialog('close');
					$('#mappingVerFiltros').dialog('close');
				});
				$('#btnExitCleanFiltro').click( function(e) {
					$('#cleanFilterDialog').dialog('close');
				})
			}
		});
		$('#cleanFilterDialog').dialog('open');
	});
}

function $_imagenMappingProxyShow(idUbicacion) {
	$('#mappingFotosDialog').load('mappingFotos.php?idUbicacion=' + idUbicacion).dialog('open');
  //console.log('$_imagenMappingProxyShow');
}

function $_autofilterProxyShowMapping(filter)
{
	joDataRowEnabled = [];
	joDataWhereFilter = [];

	//Define filters parameters
	switch (filter) {

		case iFilterMapping.MEDIO:
			tableFilter = "map_medios";
			fieldsFilter = "descripcion";
			idTable = "idMedio";
			joDataFilter = joMedioMap;
			actualFilter = iFilterMapping.MEDIO;
			break;

		case iFilterMapping.FORMATO:
			tableFilter = "map_formato";
			fieldsFilter = "descripcion";
			idTable = "idFormato";
			joDataFilter = joFormatoMap;
			actualFilter = iFilterMapping.FORMATO;
			break;

		case iFilterMapping.ELEMENTO:
			tableFilter = "map_elementos";
			fieldsFilter = "descripcion";
			idTable = "idElemento";
			joDataFilter = joElementosMap;
			actualFilter = iFilterMapping.ELEMENTO;
			break;

		case iFilterMapping.EVP:
			tableFilter = "map_empresas";
			fieldsFilter = "descripcion";
			idTable = "idEmpresa";
			joDataFilter = joEVPMap;
			actualFilter = iFilterMapping.EVP;
			$('#autoFilterDialog').load(popAutoFilterEVPs).dialog('open');
			return;

		case iFilterMapping.PROVINCIA:
			tableFilter = "map_provincias";
			fieldsFilter = "descripcion";
			idTable = "idProvincia";
			joDataFilter = joProvinciaMap;
			actualFilter = iFilterMapping.PROVINCIA;
			break;

		case iFilterMapping.LOCALIDAD:
			tableFilter = "map_localidades";
			fieldsFilter = "descripcion";
			idTable = "idLocalidad";
			joDataFilter = joLocalidadMap;
			actualFilter = iFilterMapping.LOCALIDAD;
			joDataWhereFilter = {fieldFilter : 'idProvincia', dataWhereFilter : joProvinciaMap};
			break;

		case iFilterMapping.POIS:
			actualFilter = iFilterMapping.POIS;
			joDataFilter = joEntidadPoisMap;
			$('#autoFilterDialog').load(popAutoFilterPOIs).dialog('open');
			return;
	}

	$('#autoFilterDialog').load(popAutoFilter).dialog('open');
}

function $_initAutofilterDialog()
{
	$("#autoFilterDialog").dialog({
		autoOpen:false,
		height:'auto',
		width:514,
		//position : ['center',10],
    position : { my: "center", at: "center", of: window },
		closeOnEscape: false,
		dialogClass:'no-close',
		beforeClose: function( event, ui ) {
			switch (actualFilter) {
				case iFilterMapping.MEDIO:
					joMedioMap = joDataFilter;
					joFilterUbicaciones.joMedioIds = joDataFilter;
					break;

				case iFilterMapping.FORMATO:
					joFormatoMap = joDataFilter;
					joFilterUbicaciones.joFormatoIds = joDataFilter;
					break;

				case iFilterMapping.ELEMENTO:
					joElementosMap = joDataFilter;
					joFilterUbicaciones.joElementosIds = joDataFilter;
					break;
				case iFilterMapping.EVP:
					joEVPMap = joDataFilter;
					joFilterUbicaciones.joEVPIds = joDataFilter;
					break;
				case iFilterMapping.PROVINCIA:
					joProvinciaMap = joDataFilter;
					joFilterUbicaciones.joProvinciaIds = joDataFilter;
					break;
				case iFilterMapping.LOCALIDAD:
					joLocalidadMap = joDataFilter;
					joFilterUbicaciones.joLocalidadIds = joDataFilter;
					break;
				case iFilterMapping.POIS:
					joEntidadPoisMap = joDataFilter;
					joFilterUbicaciones.joEntidadPOIs = joDataFilter;
					break;
			}
		},
		open: function() {
			$('#autoFilterDialog').css('background','#F5F5F5');
		}
	});
}

function $_grabarFiltro()
{
	var paramData = 'actionOfForm=grabarFiltro' +
						'&joFilterUbicaciones=' + encodeURIComponent(JSON.stringify(joFilterUbicaciones)) +
						'&joMarkerNotVisibleIds=' + encodeURIComponent(JSON.stringify(joMarkerNotVisibleIds)) +
						'&joUbicacionesCantidad=' + encodeURIComponent(JSON.stringify(joUbicacionesCantidad));

	//if(!$.browser.msie)
		location.href = actionForm + '?' + paramData;
	//else
  //window.open(actionForm + '?' + paramData);

	return true;
}

function createUploader(){
    var uploader = new qq.FileUploader({
        element: document.getElementById('btnCargarFiltro'),
        action: 'uploadFiles.php',
        onComplete: function(id, fileName, responseJSON){
        	if(responseJSON.success)
    		{
        		var paramData = 'actionOfForm=cargarFiltro&fileName=' + fileName + '&idCampanna=' + actionPKCampanna;

        		$.ajax({
                    type: 'POST',
                    url: actionForm,
                    data: paramData,
        			dataType: 'json',
                    success: function(jsonObj)
                                        {
                                            if(jsonObj.status === 'OK')
                                            {
                                            	joFilterUbicaciones = jsonObj.joFilterUbicaciones;
                                            	if(!$.isEmptyObject(joFilterUbicaciones)) {

                                            		joFilterUbicaciones = eval('(' + jsonObj.joFilterUbicaciones + ')');

                                            		joMedioMap = joFilterUbicaciones.joMedioIds;
                                            		joFormatoMap = joFilterUbicaciones.joFormatoIds;
	                                            	joElementosMap = joFilterUbicaciones.joElementosIds;
	                                            	joEVPMap = joFilterUbicaciones.joEVPIds;
	                                            	joProvinciaMap = joFilterUbicaciones.joProvinciaIds;
	                                            	joLocalidadMap = joFilterUbicaciones.joLocalidadIds;
	                                            	joEntidadPoisMap = joFilterUbicaciones.joEntidadPOIs;
	                                            	joFavoritosMap = joFilterUbicaciones.joFavoritosIds;
                                                joPOISResultMap = joFilterUbicaciones.joPOISResultIds;

	                                            	//Ubicaciones no Visibles
	                                            	joUbicacionesCantidad = JSON.parse(jsonObj.joUbicacionesCantidad);
	                                            	joMarkerNotVisibleIds = JSON.parse(jsonObj.joMarkerNotVisibleIds);

	                                            	$_actulizarMapa();
                                            	}
                                            	else if(!$.isEmptyObject(jsonObj.joUbicacionesCantidad)){
	                                            	joUbicacionesCantidad = JSON.parse(jsonObj.joUbicacionesCantidad);
	                                            	joMarkerNotVisibleIds = [];
	                                            	$_actulizarMapa();
                                            	}

                                            	$_showMessage('OK', 'OK', jsonObj.msg);
                                            }
        									else
        										$_showMessage('ERR', 'ERROR', jsonObj.msg);
                                        }
                    });
    		}
        	else
        		$_showMessage('ERR', 'ERROR', responseJSON.error);
        },
    });
}

function $_actulizarMapa(fromXLS)
{
	if(fromXLS == undefined)
		fromXLS = 'N';

	if($.isEmptyObject(joFilterUbicaciones.joMedioIds)
      && $.isEmptyObject(joFilterUbicaciones.joFormatoIds)
      && $.isEmptyObject(joFilterUbicaciones.joElementosIds)
      && $.isEmptyObject(joFilterUbicaciones.joEVPIds)
      && $.isEmptyObject(joFilterUbicaciones.joProvinciaIds)
      && $.isEmptyObject(joFilterUbicaciones.joLocalidadIds)
      && $.isEmptyObject(joFilterUbicaciones.joFavoritosIds)
      && $.isEmptyObject(joFilterUbicaciones.joPOISResultIds)) {

		if(!$.isEmptyObject(joFilterUbicaciones.joEntidadPOIs) && fromXLS == 'N') {

			var paramData = 'actionOfForm=actualizarPOIsMapa&joEntidadPOIs=' + encodeURIComponent(JSON.stringify(joFilterUbicaciones.joEntidadPOIs));

      console.log('joFilterUbicaciones:');
      console.log(JSON.stringify(joFilterUbicaciones));

			$(".loader").show();

			$.ajax({
		      type: 'POST',
		      url: actionForm,
		      data: paramData,
				  dataType: 'json',
		      success: function(jsonObj) {
		                                if(jsonObj.status === 'OK') {
                                      // console.log('#1');
		                                	$.goMap.clearMarkers();
		                                	$.goMap.clearOverlays('circle');

		                                	var joDataPolygon = jaDataPolygon[polyConst.POIS];
                                      //console.log(jaDataPolygon);

		                                	arrDataPOIs = jsonObj.markers;

		                                	var latitude = undefined;
		                                	var longitude = undefined;
		                                	$.each(jsonObj.markers, function(i, item) {
		                                		if(latitude == undefined && longitude == undefined) {
		                                			latitude = item.latitude;
		                                			longitude = item.longitude;
		                                		}

		                                		if(joDataPolygon.circlePois) {
                                          //console.log('polys: '+joDataPolygon.circlePois);
		                                			$.goMap.createMarker({
			                                			latitude: item.latitude,
			                                      longitude: item.longitude,
			                                      html: item.html,
			                                      icon: item.icon,
			                                      draggable: false,
			                                      circlePoly: {radius: joDataPolygon.mtsRadio}
			                                		  });
		                                		} else {
                                          //console.log('no polys!!: '+joDataPolygon.circlePois);
			                                		$.goMap.createMarker({
			                                			latitude: item.latitude,
			                                            longitude: item.longitude,
			                                            html: item.html,
			                                            icon: item.icon,
			                                            draggable: false
			                                		});
		                                		}
		                                	});

		                                	$(".loader").hide();

		                                	$.goMap.setMap({
		                            			latitude: latitude,
		                                        longitude: longitude,
		                                		zoom: 10,
		                                	});
		                                }
		                            }
		        });

            if ($('#toolbar').hasClass('hidden')) $('#toolbar').toggleClass('hidden');
		}
		else if(!$.isEmptyObject(joUbicacionesCantidad)) {

			var paramData = 'actionOfForm=actualizarXLSMapa&fromXLS='+fromXLS+'&joUbicacionesCantidad=' + encodeURIComponent(JSON.stringify(joUbicacionesCantidad));

			$(".loader").show();

			$.ajax({
		        type: 'POST',
		        url: actionForm,
		        data: paramData,
				dataType: 'json',
		        success: function(jsonObj)
		                            {
		                                if(jsonObj.status === 'OK')
		                                {
                                      // console.log('#2');
                                      currMapJson = jsonObj.markers;

		                                	$.goMap.clearMarkers();

		                                	var joPolygon = jaDataPolygon[polyConst.POIS];
		                                	joImagenesMapa = jsonObj.imagesMapa;

		                                	// Dibujo POIs
		                                	var latitude = undefined;
		                                	var longitude = undefined;
		                                	$.each(arrDataPOIs, function(i, item) {
		                                		if(latitude == undefined && longitude == undefined) {
		                                			latitude = item.latitude;
		                                			longitude = item.longitude;
		                                		}

		                                		$.goMap.createMarker({
		                                			latitude: item.latitude,
		                                            longitude: item.longitude,
		                                            html: item.html,
		                                            icon: item.icon,
		                                            draggable: false
		                                		});
		                                	});

		                                	var latitude = undefined;
		                                	var longitude = undefined;
		                                	$.each(jsonObj.markers, function(i, item) {

		                                		var visiblePoint = joPolygon.allMarkers ? true : $.goMap.checkInPolygon(item.latitude, item.longitude);

		                                		if(latitude == undefined && longitude == undefined) {
		                                			latitude = item.latitude;
		                                			longitude = item.longitude;
		                                		}

		                                		if(!visiblePoint) {
		                                			joMarkerNotVisibleIds.push(item.id);
		                                		}
		                                		else {
		                                			var hasMatch = false;
		                                			for (var index = 0; index < joMarkerNotVisibleIds.length; ++index) {
		                                				var idPoint = joMarkerNotVisibleIds[index];
		                                				if(idPoint == item.id) {
		                                					joMarkerNotVisibleIds.push(item.id);
		                                					hasMatch = true;
		 	                                			   	break;
		                                				}
		                                			}

		                                			visiblePoint = !hasMatch;
		                                		}

		                                		$.goMap.createMarker({
		                                			id: item.id,
		                                			latitude: item.latitude,
		                                            longitude: item.longitude,
		                                            html: item.html,
		                                            icon: item.icon,
		                                            draggable: false,
		                                            visible: visiblePoint
		                                		});
		                                	});

		                                	$(".loader").hide();

		                                	$.goMap.setMap({
		                            			latitude: latitude,
		                                        longitude: longitude,
		                                		zoom: 10,
		                                	});

		                                	oTableMAP.fnReloadAjax(actionForm + "?actionOfForm=searchMapping");
                                      console.log('Reload oTableMAP at 1137');
		                                }
		                            },
		        error: function(){
		        	$(".loader").hide();
		        }
	        });

          if ($('#toolbar').hasClass('hidden')) $('#toolbar').toggleClass('hidden');

		}
		else
			return;
	}
	else {

		var paramData = 'actionOfForm=actualizarMapa&tipoFiltro=' + $('#tipoFiltro').val() + '&joFilterUbicaciones=' + encodeURIComponent(JSON.stringify(joFilterUbicaciones));

		$(".loader").show();

		$.ajax({
	        type: 'POST',
	        url: actionForm,
	        data: paramData,
			dataType: 'json',
	        success: function(jsonObj)
	                            {
	                                if(jsonObj.status === 'OK')
	                                {
                                    console.log(jaDataPolygon);
	                                	$.goMap.clearMarkers();
	                                	$.goMap.clearOverlays('polyline_encode');

	                                	var joPolygon = jaDataPolygon[polyConst.POIS];

	                                	var joDataPolygon = jaDataPolygon[polyConst.ELEMENTOS];

                                    if(!joDataPolygon.circleElementos) joDataPolygon = jaDataPolygon[polyConst.EVPS];

	                                	if(jsonObj.markers) {

                                      currMapJson = jsonObj.markers;

		                                	joImagenesMapa = jsonObj.imagesMapa;
		                                	var latitude = undefined;
		                                	var longitude = undefined;

		                                	$.each(jsonObj.markers, function(i, item) {

		                                		if(joDataPolygon.circleElementos || joDataPolygon.circleEVPs) {

		                                			if(item.id.indexOf('P') != -1) {

		                                				var visiblePoint = $.goMap.checkInPolygon(item.latitude, item.longitude);

				                                		if(!visiblePoint) {
				                                			joMarkerNotVisibleIds.push(item.id);
				                                		}
				                                		else {
				                                			var hasMatch = false;
				                                			for (var index = 0; index < joMarkerNotVisibleIds.length; ++index) {
				                                				var idPoint = joMarkerNotVisibleIds[index];
				                                				if(idPoint == item.id) {
				                                					joMarkerNotVisibleIds.push(item.id);
				                                					hasMatch = true;
				 	                                			   	break;
				                                				}
				                                			}

				                                			visiblePoint = !hasMatch;
				                                		}

				                                		$.goMap.createMarker({
				                                			id: item.id,
				                                			latitude: item.latitude,
				                                            longitude: item.longitude,
				                                            html: item.html,
				                                            icon: item.icon,
				                                            draggable: false,
				                                            visible: visiblePoint
				                                		});
		                                			}
		                                			else {
		                                				if(latitude == undefined && longitude == undefined) {
				                                			latitude = item.latitude;
				                                			longitude = item.longitude;
				                                		}

			                                			$.goMap.createMarker({
			                                				id: item.id,
				                                			latitude: item.latitude,
				                                            longitude: item.longitude,
				                                            html: item.html,
				                                            icon: item.icon,
				                                            draggable: false,
				                                            circlePoly: {radius: joDataPolygon.mtsRadio}
				                                		});
		                                			}
		                                		}
		                                		else {
                                          // console.log('joPolygon.allMarkers : '+joPolygon.allMarkers);

			                                		var visiblePoint = joPolygon.allMarkers ? true : $.goMap.checkInPolygon(item.latitude, item.longitude);

			                                		if(latitude == undefined && longitude == undefined && visiblePoint) {
			                                			latitude = item.latitude;
			                                			longitude = item.longitude;
			                                		}

			                                		if(!visiblePoint) {
                                            // console.log('not visible point! at 1271');
			                                			joMarkerNotVisibleIds.push(item.id);
			                                		}
			                                		else {
			                                			var hasMatch = false;
			                                			for (var index = 0; index < joMarkerNotVisibleIds.length; ++index) {
			                                				var idPoint = joMarkerNotVisibleIds[index];
			                                				if(idPoint == item.id) {
			                                					joMarkerNotVisibleIds.push(item.id);
			                                					hasMatch = true;
			 	                                			   	break;
			                                				}
			                                			}

			                                			visiblePoint = !hasMatch;
			                                		}

			                                		$.goMap.createMarker({
			                                			id: item.id,
			                                			latitude: item.latitude,
			                                            longitude: item.longitude,
			                                            html: item.html,
			                                            icon: item.icon,
			                                            draggable: false,
			                                            visible: visiblePoint
			                                		});
		                                		}
		                                	});

	                                	}

	                                	if(jsonObj.polyline) {
		                                	$.each(jsonObj.polyline, function(i, item) {
		                                		$.goMap.createOverlay({
		                                			type: 'polyline_encode',
		                                			id: item.id,
		                                			html: item.html,
		                                			encodePath: item.encodePath,
		                                			color: item.color,
		                                			weight: 4,
		                                			opacity: 1
		                                		});
		                                	});
	                                	}

	                                	$(".loader").hide();

	                                	$.goMap.setMap({
	                            			latitude: latitude,
	                                        longitude: longitude,
	                                		zoom: 10,
	                                	});

	                                	if(joDataPolygon.circleElementos || joDataPolygon.circleEVPs)
	                                		$.goMap.removeEmptyCirclePolygon();

	                                	oTableMAP.fnReloadAjax(actionForm + "?actionOfForm=searchMapping");
	                                	bolPrintMarca = false;

                                    console.log('Reload oTableMAP at 1299');

	                                }
	                            }
	        });
          if ($('#toolbar').hasClass('hidden')) $('#toolbar').toggleClass('hidden');
	}
}

function dynamicPOIs2(){

  $(".loader").show();

  $.ajax({
        type: 'POST',
        url: actionForm,
        data: {actionOfForm: 'actualizarMapa', tipoFiltro: $('#tipoFiltro').val(), joFilterUbicaciones: JSON.stringify(joFilterUbicaciones)},
    dataType: 'json',
        success: function(jsonObj)
                            {
                                if(jsonObj.status === 'OK')
                                {
                                  // console.log('#3');
                                  $.goMap.clearMarkers();
                                  $.goMap.clearOverlays('polyline_encode');

                                  jaDataPolygon = [
                                                       	{circlePois: false, mtsRadio: 0, allMarkers: false},
                                                       	{circleElementos: false, mtsRadio: 0},
                                                       	{circleEVPs: false, mtsRadio: 0}
                                                       ];

                                  var joPolygon = jaDataPolygon[polyConst.POIS];

                                  var joDataPolygon = jaDataPolygon[polyConst.ELEMENTOS];

                                  if(!joDataPolygon.circleElementos) joDataPolygon = jaDataPolygon[polyConst.EVPS];

                                  if(jsonObj.markers) {

                                    coords = parseMarkers(jsonObj, joPolygon, joDataPolygon);

                                  }

                                  if(jsonObj.polyline) {
                                    $.each(jsonObj.polyline, function(i, item) {
                                      $.goMap.createOverlay({
                                        type: 'polyline_encode',
                                        id: item.id,
                                        html: item.html,
                                        encodePath: item.encodePath,
                                        color: item.color,
                                        weight: 4,
                                        opacity: 1
                                      });
                                    });
                                  }

                                  $(".loader").hide();

                                  $.goMap.setMap({
                                    latitude: coords[0],
                                    longitude: coords[1],
                                    zoom: 10,
                                  });

                                  if(joDataPolygon.circleElementos || joDataPolygon.circleEVPs)
                                    $.goMap.removeEmptyCirclePolygon();

                                  oTableMAP.fnReloadAjax(actionForm + "?actionOfForm=searchMapping");
                                  bolPrintMarca = false;

                                  console.log('Reload oTableMAP at dynamicPOIs');

                                }
                            }
        });
        if ($('#toolbar').hasClass('hidden')) $('#toolbar').toggleClass('hidden');
}

function parseMarkers(jsonObj, joPolygon, joDataPolygon){

  currMapJson = jsonObj.markers;

  joImagenesMapa = jsonObj.imagesMapa;

  var latitude = undefined;
  var longitude = undefined;

  $.each(jsonObj.markers, function(i, item) {

    if(joDataPolygon.circleElementos || joDataPolygon.circleEVPs) { // Esta es la funcionalidad para radio alrededor de elementos

      if(item.id.indexOf('P') != -1) {

        var visiblePoint = $.goMap.checkInPolygon(item.latitude, item.longitude);

        if(!visiblePoint) {
          joMarkerNotVisibleIds.push(item.id);
        }
        else {
          var hasMatch = false;
          for (var index = 0; index < joMarkerNotVisibleIds.length; ++index) {
            var idPoint = joMarkerNotVisibleIds[index];
            if(idPoint == item.id) {
              joMarkerNotVisibleIds.push(item.id);
              hasMatch = true;
                break;
            }
          }

          visiblePoint = !hasMatch;
        }

        $.goMap.createMarker({
          id: item.id,
          latitude: item.latitude,
                longitude: item.longitude,
                html: item.html,
                icon: item.icon,
                draggable: false,
                visible: visiblePoint
        });
      }
      else {
        if(latitude == undefined && longitude == undefined) {
          latitude = item.latitude;
          longitude = item.longitude;
        }

        $.goMap.createMarker({
          id: item.id,
          latitude: item.latitude,
                longitude: item.longitude,
                html: item.html,
                icon: item.icon,
                draggable: false,
                circlePoly: {radius: joDataPolygon.mtsRadio}
        });
      }
    }
    else {
      // console.log('joPolygon.allMarkers : '+joPolygon.allMarkers);

      var visiblePoint = joPolygon.allMarkers ? true : $.goMap.checkInPolygon(item.latitude, item.longitude);

      if(latitude == undefined && longitude == undefined && visiblePoint) {
        latitude = item.latitude;
        longitude = item.longitude;
      }

      if(!visiblePoint) {
        // console.log('not visible point! at 1271');
        joMarkerNotVisibleIds.push(item.id);
      }
      else {
        var hasMatch = false;
        for (var index = 0; index < joMarkerNotVisibleIds.length; ++index) {
          var idPoint = joMarkerNotVisibleIds[index];
          if(idPoint == item.id) {
            joMarkerNotVisibleIds.push(item.id);
            hasMatch = true;
              break;
          }
        }

        visiblePoint = !hasMatch;
      }

      $.goMap.createMarker({
        id: item.id,
        latitude: item.latitude,
              longitude: item.longitude,
              html: item.html,
              icon: item.icon,
              draggable: false,
              visible: visiblePoint
      });
    }
  });

  var coords = [latitude, longitude];

  return coords;

}

function dynamicPOIs(){

  // var paramData = 'actionOfForm=actualizarPOIsMapa&joEntidadPOIs=' + encodeURIComponent(JSON.stringify(joFilterUbicaciones.joEntidadPOIs));

  joFilterUbicaciones = JSON.parse('{"joElementosIds":["26"],"joEVPIds":["4"],"joEntidadPOIs":["379"],"joFavoritosIds":[],"joPOISResultIds":[]}');

  jaDataPolygon = [
                        {circlePois: true, mtsRadio: 200, allMarkers: false},
                        {circleElementos: false, mtsRadio: 0},
                        {circleEVPs: false, mtsRadio: 0}
                       ];

  // var test = JSON.parse('{"joEntidadPOIs":["379"],"joFavoritosIds":[],"joPOISResultIds":[]}');

  $(".loader").show();

  $.ajax({
      type: 'POST',
      url: actionForm,
      data: {actionOfForm: 'actualizarPOIsMapa', joEntidadPOIs: JSON.stringify(joFilterUbicaciones.joEntidadPOIs)},
      dataType: 'json',
      success: function(jsonObj) {
                                if(jsonObj.status === 'OK') {

                                  markersJson = jsonObj;

                                  // console.log('#1');
                                  $.goMap.clearMarkers();
                                  $.goMap.clearOverlays('circle');

                                  var joDataPolygon = jaDataPolygon[polyConst.POIS];
                                  //console.log(jaDataPolygon);

                                  arrDataPOIs = jsonObj.markers;

                                  var latitude = undefined;
                                  var longitude = undefined;

                                  $.each(jsonObj.markers, function(i, item) {

                                    if(latitude == undefined && longitude == undefined) {
                                      latitude = item.latitude;
                                      longitude = item.longitude;
                                    }

                                    if(joDataPolygon.circlePois) {
                                      //console.log('polys: '+joDataPolygon.circlePois);
                                      $.goMap.createMarker({
                                        latitude: item.latitude,
                                        longitude: item.longitude,
                                        html: item.html,
                                        icon: item.icon,
                                        draggable: false,
                                        circlePoly: {radius: joDataPolygon.mtsRadio}
                                        });
                                    } else {
                                      //console.log('no polys!!: '+joDataPolygon.circlePois);
                                      $.goMap.createMarker({
                                        latitude: item.latitude,
                                              longitude: item.longitude,
                                              html: item.html,
                                              icon: item.icon,
                                              draggable: false
                                      });
                                    }
                                  });

                                  $(".loader").hide();

                                  $.goMap.setMap({
                                  latitude: latitude,
                                        longitude: longitude,
                                    zoom: 10,
                                  });
                                }
                            },
                            complete: function() {
                              dynamicPOIs2();
                            }
        });

}

function adjustPOIsRadio(){

  var radio = $('#dynamicradio').val();

  $.goMap.clearMarkers();
  $.goMap.clearOverlays('circle');

  var latitude = undefined;
  var longitude = undefined;

  $.each(markersJson.markers, function(i, item) {

    if(latitude == undefined && longitude == undefined) {
      latitude = item.latitude;
      longitude = item.longitude;
    }

    if(radio > 0) {
      //console.log('polys: '+joDataPolygon.circlePois);
      $.goMap.createMarker({
        latitude: item.latitude,
        longitude: item.longitude,
        html: item.html,
        icon: item.icon,
        draggable: false,
        circlePoly: {radius: radio}
        });
    } else {
      //console.log('no polys!!: '+joDataPolygon.circlePois);
      $.goMap.createMarker({
        latitude: item.latitude,
              longitude: item.longitude,
              html: item.html,
              icon: item.icon,
              draggable: false
      });
    }
  });

}

function $_setVisibleMarker(id, bVisible)
{
	for(var i = 0; i < joMarkerNotVisibleIds.length; i++)
	{
		if(joMarkerNotVisibleIds[i] == id)
		{
			joMarkerNotVisibleIds.splice(i,1);
			$.goMap.setVisibleMarkerById(id, bVisible);
			return;
		}
	}
	joMarkerNotVisibleIds.push(id);
	$.goMap.setVisibleMarkerById(id, bVisible);
}

function $_setVisibleOverlay(id, bVisible)
{
	for(var i = 0; i < joOverlayNotVisibleIds.length; i++)
	{
		if(joOverlayNotVisibleIds[i] == id)
		{
			joOverlayNotVisibleIds.splice(i,1);
			for(var j = 1; j < 50; j++) {
			    if($.inArray('buses_'+id+j, $.goMap.polylines_enc) > -1) {
			    	$.goMap.showHideOverlay('polyline_encode', 'buses_'+id+j, bVisible);
			    }
			    else
			    	break;
			}
		}
	}
	joOverlayNotVisibleIds.push(id);
	for(var j = 1; j < 50; j++) {
	    if($.inArray('buses_'+id+j, $.goMap.polylines_enc) > -1) {
	    	$.goMap.showHideOverlay('polyline_encode', 'buses_'+id+j, bVisible);
	    }
	    else
	    	break;
	}
}


function $_centerMapMarker(lat, lng)
{
	$('#referenceTable').removeClass('hideTable');
	$('#referenceTable').addClass('showTable');
	// $('#tablaReferencias').hide("slide", { direction: "left" }, 1000);
	$.goMap.setMap({
		latitude: lat,
        longitude: lng,
		zoom: 17,
	});
}

function $_initGlosarioPop()
{
	$('#btnGlosarioSalir').click( function(e) {
		$('#glosarioDialog').dialog('close');
	});

	oTableGlosario = $('#dt_glosario').dataTable( {
							//"bJQueryUI": true,
              "pagingType": "simple",
              //"sPaginationType": "two_button",
							"bInfo": false,
							"bLengthChange": false,
							"bProcessing": true,
							"sScrollY": "250px",
							"bScrollInfinite": true,
							"bScrollCollapse": false,
							"bPaginate": false,
							"iDisplayLength": -1,
							"bServerSide": true,
							"oLanguage": {
								"sUrl": "js/datatables/js/dataTables.es.txt"
							},
							"sAjaxSource": actionForm + "?actionOfForm=searchGlosario",
							"aoColumns": [
								  			{"sWidth": "7%"},
								  			null,
								  			{"bVisible": false},
								  			{ "sClass": "center", "bSortable": false }
								  		 ]
					});

	$('#dt_glosario').on('click', 'tbody td img', function () {
		var nTr = $(this).parents('tr')[0];
		if (oTableGlosario.fnIsOpen(nTr) )
		{
			this.src = "images/details_open.png";
			oTableGlosario.fnClose( nTr );
		}
		else
		{
			this.src = "images/details_close.png";
			oTableGlosario.fnOpen( nTr, fnFormatDetailsGlosario(nTr), 'details' );
		}
	});
}

function fnFormatDetailsGlosario (nTr)
{
	var aData = oTableGlosario.fnGetData(nTr);
	var sOut = "";
	var paramData =  "actionOfForm=searchGlosarioDetalle&idGlosario=" + aData[0];

    $.ajax({
        type: 'POST',
        url: actionForm,
        data: paramData,
        async: false,
		dataType: 'html',
        success: function(strHtml)
                            {
        						sOut = '<table cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">';
        						sOut += strHtml;
        						sOut += '</table>';
                            }
    });

	return sOut;
}

function $_addMappingFavoritoBulk() {
  var favs = $('*[id^="fav_grilla_"]');

  if ($('#bulkfav').is(':checked')) {

    joFavoritosMap = [];
    Array.prototype.forEach.call(favs, child => {
      // console.log(child)
      $_addMappingFavoritoAllON(child);
    });

  } else {

    Array.prototype.forEach.call(favs, child => {
      // console.log(child)
      $_addMappingFavoritoAllOFF(child);
    });

  }


  // favs.forEach(function(index) {
  //   // $_addMappingFavorito(index.parentNode);
  //   console.log(index.parentNode);
  // });

  // console.log(favs);

}

function $_addMappingFavoritoAllOFF(obj)
{
  var idFav = $(obj).attr('id').replace("fav_", "").replace("grilla_", "");
	joFavoritosMap = [];
	$("#fav_"+idFav).attr('src', 'images/fav_desactivado_tooltip.png');
	$("#fav_grilla_"+idFav).attr('src', 'images/fav_desactivado_tooltip.png');

	joFilterUbicaciones.joFavoritosIds = joFavoritosMap;
}

function $_addMappingFavoritoAllON(obj)
{
  if ($(obj).is(":visible")){
	  var idFav = $(obj).attr('id').replace("fav_", "").replace("grilla_", "");
		joFavoritosMap.push(idFav);
		$("#fav_"+idFav).attr('src', 'images/fav_activado_tooltip.png');
		$("#fav_grilla_"+idFav).attr('src', 'images/fav_activado_tooltip.png');
    }
    joFilterUbicaciones.joFavoritosIds = joFavoritosMap;

}

function $_addMappingFavorito(obj) {

	var idFav = $(obj).attr('id').replace("fav_", "").replace("grilla_", "");
	var exist = false;

	if(!$.isEmptyObject(joFavoritosMap)) {
		$.each(joFavoritosMap, function(i, value) {
			if(value == idFav) {
				joFavoritosMap.splice(i, 1);
				exist = true;
				return false;
			}
		});
	}

	if(!exist) {
		if(joFavoritosMap == undefined) joFavoritosMap = [];
		joFavoritosMap.push(idFav);
		$("#fav_"+idFav).attr('src', 'images/fav_activado_tooltip.png');
		$("#fav_grilla_"+idFav).attr('src', 'images/fav_activado_tooltip.png');
	}	else {
		$("#fav_"+idFav).attr('src', 'images/fav_desactivado_tooltip.png');
		$("#fav_grilla_"+idFav).attr('src', 'images/fav_desactivado_tooltip.png');
	}

	joFilterUbicaciones.joFavoritosIds = joFavoritosMap;
}

function $_initPopCampannas()
{
	$('#btnSalirCampannas').click( function(e) {
		$('#mappingCampannaDialog').dialog('close');
	});

	var gaiSelected;

	oTableCampannas = $('#dt_campannas').dataTable( {
							//"bJQueryUI": true,
              "pagingType": "simple",
              //"sPaginationType": "two_button",
							"bInfo": false,
							"bLengthChange": false,
							"bAutoWidth": false,
							"bProcessing": true,
							"bServerSide": true,
							"oLanguage": {
								"sUrl": "js/datatables/js/dataTables.es.txt"
							},
							"sAjaxSource": actionForm + "?actionOfForm=searchCampannas",
							"fnRowCallback": function( nRow, aData, iDisplayIndex ) {
								if ( aData[0] == actionPKCampanna ) {
							        $(nRow).addClass('row_selected');
								}
								return nRow;
							},
							"aoColumns": [
							  			null,
							  			null,
							  			{ "sClass": "center", "bSortable": false }
							  		]
						});

	$(document).off("click");
	$('#dt_campannas').on('click', 'tbody td img', function () {
		var nTr = $(this).parents('tr')[0];
		if (oTableCampannas.fnIsOpen(nTr) )
		{
			/* This row is already open - close it */
			this.src = "images/details_open.png";
			oTableCampannas.fnClose( nTr );
		}
		else
		{
			/* Open this row */
			this.src = "images/details_close.png";
			oTableCampannas.fnOpen( nTr, fnFormatDetailsCampania(nTr), 'details' );
		}
	});

	/* Click event handler */
	$('#dt_campannas').on('click', 'tbody tr', function () {
		if(oTableCampannas == undefined)
			return false;

		var aData = oTableCampannas.fnGetData(this);
		if(aData == null)
			return false;

		var iId = aData[0];
		actionPKCampanna = aData[0];

		gaiSelected =  [];
		if ( $(this).hasClass('row_selected') ) {
            $(this).removeClass('row_selected');
            actionPKCampanna = undefined;
        }
        else {
        	oTableCampannas.$('tr.row_selected').removeClass('row_selected');
            $(this).addClass('row_selected');
            gaiSelected[gaiSelected.length++] = iId;
        }
	});

	$('#dt_campannas').on('dblclick', 'tbody tr', function () {
		if(oTableCampannas == undefined)
			return false;

		var aData = oTableCampannas.fnGetData(this);
		var iId = aData[0];
		actionPKCampanna = aData[0];
		bLoadExcel = false;

		var paramData = 'actionOfForm=cargarFiltroCampanna&idCampanna=' + actionPKCampanna;

		$.ajax({
            type: 'POST',
            url: actionForm,
            data: paramData,
			dataType: 'json',
            success: function(jsonObj)
                                {
                                    if(jsonObj.status === 'OK')
                                    {
                                    	$_limpiarFiltrosCampanna();

                                    	if(!$.isEmptyObject(jsonObj.joFilterUbicaciones)) {

                                    		joFilterUbicaciones = jsonObj.joFilterUbicaciones;

	                                    	joElementosMap = joFilterUbicaciones.joElementosIds;
	                                    	joEVPMap = joFilterUbicaciones.joEVPIds;
	                                    	joProvinciaMap = joFilterUbicaciones.joProvinciaIds;
	                                    	joLocalidadMap = joFilterUbicaciones.joLocalidadIds;
	                                    	joEntidadPoisMap = joFilterUbicaciones.joEntidadPOIs;
	                                    	joFavoritosMap = joFilterUbicaciones.joFavoritosIds;
                                        joPOISResultMap = joFilterUbicaciones.joPOISResultIds;

	                                    	//Ubicaciones no Visibles
	                                    	joUbicacionesCantidad = JSON.parse(jsonObj.joUbicacionesCantidad);
	                                    	joMarkerNotVisibleIds = JSON.parse(jsonObj.joMarkerNotVisibleIds);

	                                    	$_actulizarMapa();
                                    	}

                                    	$('#titleCampanna').val(jsonObj.titleCampanna);
                                      //$('#titlemenu').toggleClass('hidden');
                                      setMenuState('loaded');


                                    	$_showMessage('OK', 'OK', jsonObj.msg);
                                    }
									else {
										//$('#titleCampanna').val(jsonObj.titleCampanna).hide();
										$_showMessage('ERR', 'ERROR', jsonObj.msg);
									}
                                }
        });

        $('#mappingCampannaDialog').dialog('close');
	});

	//Add Click for All Buttons in Search Form
	$("#btnAgregarCampanna").click( function(e) {
		addProxyShow();
    });

	$('#btnEditarCampanna').click( function(e) {
		editProxyShow();
	});

	$('#btnEliminarCampanna').click( function(e) {
		delProxyShow();
	});
}

function setMenuState(state) {
  switch (state) {
    case 'reset':
      if (!$('#titlemenu').hasClass('hidden')) $('#titlemenu').toggleClass('hidden');
      if (!$('#filtermenu').hasClass('hidden')) $('#filtermenu').toggleClass('hidden');
      if ($('#mainmenu').hasClass('hidden')) $('#mainmenu').toggleClass('hidden');
      $('#titleCampanna').val('Campaña sin nombre');
      if (!$('#toolbar').hasClass('hidden')) $('#toolbar').toggleClass('hidden');
    break;
    case 'loaded':
      if ($('#titlemenu').hasClass('hidden')) $('#titlemenu').toggleClass('hidden');
      if (!$('#filtermenu').hasClass('hidden')) $('#filtermenu').toggleClass('hidden');
      if (!$('#mainmenu').hasClass('hidden')) $('#mainmenu').toggleClass('hidden');
      if ($('#toolbar').hasClass('hidden')) $('#toolbar').toggleClass('hidden');
    break;
    case '':
    break;
  }

}

function delArchivoCampanna(idArchivoCampanna, idCampanna)
{
	var paramData = 'actionOfForm=deleteArchivo&idArchivoCampanna=' + idArchivoCampanna + '&idCampanna=' + idCampanna;

    $.ajax({
            type: 'POST',
            url: actionForm,
            data: paramData,
			dataType: 'json',
            success: function(jsonObj)
                                {
                                    if(jsonObj.status === 'OK')
										oTableCampannas.fnReloadAjax();
									else
										$_showMessage('ERR', 'ERROR', jsonObj.msg);
                                }
            });
}

function downloadArchivoCampanna(idArchivoCampanna, idCampanna)
{
	var paramData = 'actionOfForm=downloadArchivoCampanna&idArchivoCampanna=' + idArchivoCampanna;

	//if(!$.browser.msie)
		location.href = actionForm + '?' + paramData;
	//else
	//window.open(actionForm + '?' + paramData);

	return true;
}

function fnFormatDetailsCampania(nTr)
{
	var aData = oTableCampannas.fnGetData(nTr);

	if(aData == null)
		return;

	var sOut = "";
	var paramData =  "actionOfForm=searchCampannaDetalle&idCampanna=" + aData[0];

    $.ajax({
        type: 'POST',
        url: actionForm,
        data: paramData,
        async: false,
		dataType: 'html',
        success: function(strHtml)
                            {
        						sOut = '<table class="tableCampanna">';
        						sOut += strHtml;
        						sOut += '</table>';
                            }
    });

	return sOut;
}

function $_initCampannasFilePop()
{
	//Add Click for All Buttons in Pop Form
	$('#btnSalirCampannasFile').click( function(e) {
		$('#mappingCampannasFileDialog').dialog('close');
	});

	$('#btnGuardarCampannasFile').click( function(e) {

		if($('#descripcionNewCampanna').val() == '' && $('#cmbCampannasGuardadas').val() == 0)
		{
			$_showMessage('ALERT', 'ALERTA', 'Seleccione alguna Campa&ntilde;a o Ingrese la Descripci&oacute;n para una Nueva Campa&ntilde;a');
			return;
		}

		if($('#archivoCampanna').val() == '')
		{
			$_showMessage('ALERT', 'ALERTA', 'Ingrese el Nombre del Archivo de la Campa&ntilde;a');
			return;
		}

		var jaUbicacionesCantidad = [];

		oTableMAP.$('tr').each(function() {
		    var aData = oTableMAP.fnGetData(this);
		    var id = $(aData[12]).attr('id');

		    if($(this).is(":visible")){
		    	var idUbicacion = $('#'+id).attr('id').split("_")[2];
		    	var cantidad = $('#cant_'+idUbicacion).text();

			    var joUbiCantidad = {
			    		"idUbicacion": idUbicacion,
			    		"cantidad": cantidad
			    };

			    jaUbicacionesCantidad.push(joUbiCantidad);
		    }
		});

		if($.isEmptyObject(jaUbicacionesCantidad))
		{
			$_showMessage('ALERT', 'ALERTA', 'Filtre alg&uacute;n Elemento en el Mapa');
			return;
		}

		var paramData = 'actionOfForm=grabarCampannaFile' +
							'&idCampaniaGuardada=' + $('#cmbCampannasGuardadas').val() +
							'&descripcionCampaniaNew=' + $('#descripcionNewCampanna').val() +
							'&archivoCampanna=' + $('#archivoCampanna').val() +
							'&joFilterUbicaciones=[]' +
							'&joMarkerNotVisibleIds=[]'+
							'&joUbicacionesCantidad=' + encodeURIComponent(JSON.stringify(jaUbicacionesCantidad));

		$.ajax({
            type: 'POST',
            url: actionForm,
            data: paramData,
			dataType: 'json',
            success: function(jsonObj)
                                {
                                    if(jsonObj.status === 'OK'){
										$_showMessage('OK', 'OK', jsonObj.msg);
										$('#mappingCampannasFileDialog').dialog('close');
									}
									else
										$_showMessage('ERR', 'ERROR', jsonObj.msg);
                                }
            });


	});

	//Campannas Guardadas
	$.ajax({
	    url: actionForm,
	    type:'POST',
	    data: 'actionOfForm=getCampannasGuardadas',
	    dataType: 'json',
	    success: function(json) {
	    	$('#cmbCampannasGuardadas').append($('<option>').text('...').attr('value', 0));
	        $.each(json, function(i, value) {
	        	$('#cmbCampannasGuardadas').append($('<option>').text(value.descripcion).attr('value', value.idCampania));
	        });
	    }
	});

}

function $_initCampannasItemPop()
{
	//Add Click for All Buttons in Pop Form
	$('#btnSalirCampannasItem').click( function(e) {
		$('#campannasItemDialog').dialog('close');
	});

	$('#btnGuardarCampannasItem').click( function(e) {
		$('#mapCampannasItemPopForm').submit();
	});

	$_ValidatorSetDefaults();
	$_initPopValidator();

	//Populate Campanna Data
	if(action == iConst.EDIT)
	{
		$('#descripcion').val(joDataCampanna.descripcion);
		$('#detalle').val(joDataCampanna.detalle);
	}

	$("#campannasItemDialog").dialog({
		autoOpen:false,
		height:'auto',
		width:510,
		closeOnEscape: false,
		//position : ['center',10],
    position : { my: "center", at: "center", of: window },
		dialogClass:'no-close',
		beforeClose: function() { $("#campannasItemDialog").empty(); },
		open: function() {
			$('#campannasItemDialog').css('background','#F5F5F5');
		}
	});
}

function addProxyShow()
{
	//Reset State of action and IndexPK
	actionPKCampanna = undefined;
	action = iConst.NEW;
	oTableCampannas.$('tr.row_selected').removeClass('row_selected');
	$('#campannasItemDialog').load(popCampannasItem).dialog('open');
}

function editProxyShow()
{
	if(actionPKCampanna === undefined)
	{
		$_showMessage('ALERT', 'ALERTA', 'Seleccione alg&uacute;n item de la Grilla');
		return;
	}

	action = iConst.EDIT;
	var paramData =  "actionOfForm=" + iConst.EDIT + "&idCampanna=" + actionPKCampanna;

    $.ajax({
        type: 'POST',
        url: actionForm,
        data: paramData,
		dataType: 'json',
        success: function(jsonObj)
                            {
        						joDataCampanna = jsonObj;
        						//Open Pop Dialog
        						$('#campannasItemDialog').load(popCampannasItem).dialog('open');
                            }
        });

return true;
}

function delProxyShow()
{
	if(actionPKCampanna === undefined)
	{
		$_showMessage('ALERT', 'ALERTA', 'Seleccione alg&uacute;n item de la Grilla');
		return;
	}

	action = iConst.DELETE;

	$("#deleteCampannaDialog").dialog({
		autoOpen: false,
		width:350,
		//position: 'center',
    position : { my: "center", at: "center", of: window },
		open: function () {
			$('#deleteCampannaDialog').css('background','#F5F5F5');
			$('#btnConfirmDelete').click( function(e) {

				var paramData = 'actionOfForm=' + iConst.DELETE + '&idCampanna=' + actionPKCampanna;

			    $.ajax({
			            type: 'POST',
			            url: actionForm,
			            data: paramData,
						dataType: 'json',
			            success: function(jsonObj)
			                                {
			                                    if(jsonObj.status === 'OK'){
													$_showMessage('OK', 'OK', jsonObj.msg);

													//Reset State of action and IndexPK
													actionPKCampanna = undefined;
													action = iConst.NEW;
													oTableCampannas.fnReloadAjax();
												}
												else
													$_showMessage('ERR', 'ERROR', jsonObj.msg);
			                                    $('#deleteCampannaDialog').dialog('close');
			                                }
			            });
			});
			$('#btnExitDelete').click( function(e) {
				$('#deleteCampannaDialog').dialog('close');
			})
		}
	});
	$('#deleteCampannaDialog').dialog('open');
}


function $_saveCampanna(action)
{
	var paramData = 'actionOfForm=addOrEditCampanna&' + $("#mapCampannasItemPopForm").serialize();

	if(action == iConst.EDIT)
		paramData += '&idCampanna=' + actionPKCampanna;

    $.ajax({
        type: 'POST',
        url: actionForm,
        data: paramData,
		dataType: 'json',
        success: function(jsonObj){
            if(jsonObj.status === 'OK'){
				$_showMessage('OK', jsonObj.status, jsonObj.msg);
				$('#campannasItemDialog').dialog('close');
				oTableCampannas.fnReloadAjax();
			}
			else
				$_showMessage('ERR', jsonObj.status, jsonObj.msg);
        }
    });

return true;
}


function $_initPopValidator()
{
	$('#mapCampannasItemPopForm').validate({
		submitHandler: function() {
			$_saveCampanna(action);
		},
		rules: {
			descripcion: {required: true,minlength: 1}
		},
		messages: {
			descripcion: {required: '',minlength: ''}
		}
	});
}

function $_limpiarFiltrosCampanna()
{

  $(".loader").show();

  currMapJson = '';

	joElementosMap = undefined;
	joEVPMap = undefined;
	joProvinciaMap = undefined;
	joLocalidadMap = undefined;
	joEntidadPoisMap = undefined;
	joFavoritosMap = [];
  joPOISResultMap = [];
	joUbicacionesCantidad = [];
	joMarkerNotVisibleIds = [];
	bLoadExcel = false;

	jaDataPolygon = [
	                     	{circlePois: false, mtsRadio: 0, allMarkers: false},
	                     	{circleElementos: false, mtsRadio: 0},
	                     	{circleEVPs: false, mtsRadio: 0}
	                     ];

	joFilterUbicaciones = {
			joElementosIds : undefined,
			joEVPIds: undefined,
			joProvinciaIds : undefined,
			joLocalidadIds: undefined,
			joEntidadPOIs: undefined,
			joFavoritosIds: joFavoritosMap,
      joPOISResultIds: joPOISResultMap
		};

	oTableMAP.fnReloadAjax(actionForm + "?actionOfForm=searchMapping&emptyTable=true",function(e){$(".loader").hide();});
	bolPrintMarca = false;
  limpiarMapa();
  console.log('Reload oTableMAP at 1972');
}

function limpiarMapa(){
  $.goMap.clearOverlays('circle');
  $.goMap.clearMarkers();
  $.goMap.setMap({address: 'Argentina',zoom: 5});
  $.goMap.polygonData = [];

  // var text =
	// 		'polygon: ' + $.goMap.getOverlaysCount('polygon') + "\n" +
	// 		'polyline: ' + $.goMap.getOverlaysCount('polyline') + "\n" +
	// 		'circle: ' + $.goMap.getOverlaysCount('circle') + "\n" +
	// 		'rectangle: ' + $.goMap.getOverlaysCount('rectangle');
	// 	alert(text);

}

function $_initPopExcel()
{
	joDataElementosExcel = [];

	$('#btnSalirUploadExcel').click( function(e) {
		$('#mappingExcelDialog').dialog('close');
	});

	$.ajax({
	    url: actionForm,
	    type:'POST',
	    data: 'actionOfForm=getEVPs',
	    dataType: 'json',
	    success: function(json) {
	    	$('#cmbExcelEVP').append($('<option selected>').text("...").attr('value', ''));
	    	$('#cmbExcelEVP2').append($('<option selected>').text("...").attr('value', ''));
	        $.each(json, function(i, value) {
	        	$('#cmbExcelEVP').append($('<option>').text(value.descripcion).attr('value', value.idEmpresa));
	        	$('#cmbExcelEVP2').append($('<option>').text(value.descripcion).attr('value', value.idEmpresa));
	        });
	    }
	});

	$('#cmbExcelEVP, #cmbExcelEVP2').change(function(e){
		oTableFilter.fnReloadAjax(actionForm + "?actionOfForm=getElementosExcel&idEVP=" + $('#cmbExcelEVP').val() + "&idEVP2=" + $('#cmbExcelEVP2').val());
	});

	$.ajax({
	    url: actionForm,
	    type:'POST',
	    data: 'actionOfForm=getElementos',
	    dataType: 'json',
	    success: function(json) {
	    	$('#cmbExcelElemento').append($('<option selected>').text("...").attr('value', ''));
	        $.each(json, function(i, value) {
	        	$('#cmbExcelElemento').append($('<option>').text(value.descripcion).attr('value', value.idElemento));
	        });
	    }
	});

	var uploaderExcel = new qq.FileUploader({
        element: document.getElementById('btnFileUploadExcel'),
        action: 'uploadFiles.php',
        template: '<div class="qq-uploader">' +
			        '<div class="qq-upload-drop-area"><span>Drop files here to upload</span></div>' +
			        '<div class="qq-upload-button">Subir XLS</div>' +
			        '<ul class="qq-upload-list"></ul>' +
			     '</div>',
        onComplete: function(id, fileName, responseJSON){
        	if(responseJSON.success)
    		{
        		var idEvp2 = $('#cmbExcelEVP2').val() == "" ? $('#cmbExcelEVP').val() : $('#cmbExcelEVP2').val();

        		$(".loaderXLS").show();
        		var paramData = 'actionOfForm=cargarExcel&fileName=' + fileName + "&idEvp=" + $('#cmbExcelEVP').val() + "&idEvp2=" + idEvp2 + "&idElemento=" + $('#cmbExcelElemento').val() + "&elementos=" + JSON.stringify(joDataElementosExcel);

        		$.ajax({
                    type: 'POST',
                    url: actionForm,
                    data: paramData,
        			dataType: 'json',
                    success: function(jsonObj)
                                        {
                                            if(jsonObj.status === 'OK') {

                                            	bLoadExcel = true;
                                            	joUbicacionesCantidad = [];
                                            	$.each(jsonObj.joUbicacionesCantidad, function(i, item) {
                                            		joUbicacionesCantidad.push(item);
                                            	});

                                            	$_actulizarMapa('S');

                                            	$_showMessage('OK', 'OK', jsonObj.msg);
                                            	$('#mappingExcelDialog').dialog('close');
                                            }
        									else {

        										bLoadExcel = false;
        										joUbicacionesCantidad = [];
                                            	$.each(jsonObj.joUbicacionesCantidad, function(i, item) {
                                            		joUbicacionesCantidad.push(item);
                                            	});

                                            	$_actulizarMapa('S');

        										$_showMessage('ALERT', 'ALERTA', jsonObj.msg, 50000);
        										$('#mappingExcelDialog').dialog('close');
        									}
                                            $(".loaderXLS").hide();
                                        }
                    });
    		}
        	else {
        		bLoadExcel = true;
        		$_showMessage('ERR', 'ERROR', responseJSON.error);
        	}
        }
    });

	var oTableFilter = $('#dt_elementosExcel').dataTable( {
								//"bJQueryUI": true,
                "pagingType": "simple",
                //"sPaginationType": "two_button",
								"bInfo": false,
								"bLengthChange": false,
								"bProcessing": true,
								"sScrollY": "300px",
								"bScrollInfinite": true,
								"bScrollCollapse": true,
								"bPaginate": false,
								"iDisplayLength": -1,
								"bServerSide": true,
								"oLanguage": {
									"sUrl": "js/datatables/js/dataTables.es.txt"
								},
								"sAjaxSource": actionForm + "?actionOfForm=getElementosExcel",
								"fnDrawCallback": function(oSettings) {
									$('.selectAllXLS').unbind('click');
									$('.selectAllXLS').click(function(){
										$('.filterClassXLS').each(function() {
											$(this).trigger("click");
										});
									});
								},
								"aaSorting": [[ 1, "asc" ]],
								"aoColumns": [
									  			{"sWidth": "7%", "bSortable": false},
									  			null
									  		 ]
						});


}

function $_addRemoveElementoExcel(idElemento)
{
	var exist = false;

	if(!$.isEmptyObject(joDataElementosExcel)) {
		$.each(joDataElementosExcel, function(i, value) {
			if(value == idElemento) {
				joDataElementosExcel.splice(i, 1);
				exist = true;
				return false;
			}
		});
	}
	if(!exist)
		joDataElementosExcel.push(idElemento);
}

function $_initAudienciaPop() {

  console.log('$_initAudienciaPop');

	idAudiencia = undefined;
	jsonAudiencia = {
			jaEdades: [],
			jaSexo: [],
			jaMSE: [],
			jaPeriodo: []
		};

	$("#tabsMain").tabs();
	$("#tabs").tabs();




	$("#audienciaPlanDialog").dialog({
		autoOpen:false,
		height:'auto',
		width: 400,
		modal: true,
		resizable: false,
		closeOnEscape: false,
		//position : ['center',10],
    position : { my: "center", at: "center", of: window },
		dialogClass:'no-close',
		open: function() {
			$('#audienciaPlanDialog').css('background','#F5F5F5');
		}
	});


  $('#btnSalirAudiencia').click( function(e) {
		$('#btnAudiencia').trigger('click');
	});


	$('#btnGuardarAudiencia').click( function(e) {
		if(idAudiencia == undefined) {
			$_showMessage('ALERT', 'ALERTA', "Ejecute una Evaluaci&oacute;n de Audiencia");
			return false;
		}

		$('#audienciaPlanDialog').load(popAudienciaSaveForm).dialog('open');
	});

  $('#btnExcelAudiencia').click( function(e) {
    e.preventDefault();
		var jaIDAud = [];
		oTableAudElemento.$('tr').each(function() {
			var aData = oTableAudElemento.fnGetData(this);
			if($.inArray(aData[1], jaIDAud) == -1)
				jaIDAud.push(aData[1])
		});

		if($.isEmptyObject(jaIDAud)) {
			$_showMessage('ALERT', 'ALERTA', "Ejecute/Cargue una Evaluaci&oacute;n de Audiencia");
		}

		var paramData = 'actionOfForm=getAudienciaExcel&idMapProcesos=' + JSON.stringify(jaIDAud);
		window.open(actionForm + '?' + paramData, '_blank');

    $("#btnAudGeneral").trigger("click");

	});

	$('#btnEliminarAudiencia').click(function(e){
		if($('#cmbDescAudiencia').val() == "0")
		{
			$_showMessage('ALERT', 'ALERTA', 'Seleccione un Plan de Audiencia');
			return;
		}

		$("#deleteAudienciaDialog").dialog({
			autoOpen: false,
			width:350,
			//position: 'center',
			open: function () {
				$('#deleteAudienciaDialog').css('background','#F5F5F5');
			}
		});
		$('#deleteAudienciaDialog').dialog('open');
	});

	$('#btnConfirmDeleteAudiencia').click( function(e) {
		var paramData = 'actionOfForm=deleteAudienciasGuardadas&idMapAudiencia=' + $('#cmbDescAudiencia').val();

	    $.ajax({
	    		type: 'POST',
	            url: actionForm,
	            data: paramData,
				dataType: 'json',
	            success: function(jsonObj)
	                                {
	                                    if(jsonObj.status === 'OK')
	                                    	$_planesAudienciaGenerados();
										else
											$_showMessage('ERR', 'ERROR', jsonObj.msg);
	                                    $('#deleteAudienciaDialog').dialog('close');
	                                }
	            });
	});
	$('#btnExitDeleteAudiencia').click( function(e) {
		$('#deleteAudienciaDialog').dialog('close');
	});

	$_planesAudienciaGenerados();

	$('#btnRecuperarAudiencia').click( function(e) {
		if($('#cmbDescAudiencia').val() == 0) {
			$_showMessage('ALERT', 'ALERTA', "Seleccione un Plan de Audiencia Generado");
			return false;
		}

		$.blockUI({
	        baseZ: 2000,
	        timeout: 0,
	        message: '<h2>Cargando Informaci&oacute;n.<br /></h2><img src="images/ajax-loader-7.gif"><h2>Aguarde por favor...</h2>',
	        css: {
	            border: 'none',
	            padding: '15px',
	            backgroundColor: '#000',
	            '-webkit-border-radius': '10px',
	            '-moz-border-radius': '10px',
	            opacity: .5,
	            color: '#fff'
	        }
	    });


		var paramData = 'actionOfForm=getDatosAudienciaGuardada&idMapProcesos=' + $('#cmbDescAudiencia').val();

		$.ajax({
	        type: 'POST',
	        url: actionForm,
	        data: paramData,
			dataType: 'json',
	        success: function(jsonObj){
	        	$.unblockUI();
	        	if(jsonObj.status === 'OK'){
	        		idAudiencia = jsonObj.ID;

	        		$('#dt_audienciaGeneral tbody').empty();
	        		oTableAudGeneral.fnAddData(['Target', jsonObj.Target]);
	        		oTableAudGeneral.fnAddData(['Universo', jsonObj.Universo]);
	        		oTableAudGeneral.fnAddData(['Cantidad Ubicaciones', jsonObj.CantUbicaciones]);
	        		oTableAudGeneral.fnAddData(['Cobertura Neta', jsonObj.CoberturaNeta]);
	        		oTableAudGeneral.fnAddData(['Frecuencia', jsonObj.Frecuencia]);
	        		oTableAudGeneral.fnAddData(['Impactos Totales', jsonObj.Impactos]);
	        		oTableAudGeneral.fnAddData(['Cobertura %', jsonObj.Cobertura_Porc]);
	        		oTableAudGeneral.fnAddData(['PBR', jsonObj.PBR]);
	        		oTableAudGeneral.fnAddData(['CPR', jsonObj.CPR]);
	        		oTableAudGeneral.fnAddData(['CPM', jsonObj.CPM]);

	        		$('#dt_audienciaDetallada tbody').empty();
	        		oTableAudDetallada.fnAddData(jsonObj.Detallada);

					$('#dt_audienciaEmpresa tbody').empty();
					oTableAudEmpresa.fnAddData(jsonObj.PorEmpresa);

					$('#dt_audienciaElemento tbody').empty();
					oTableAudElemento.fnAddData(jsonObj.PorElemento);

					$('#dt_audienciaCircuito tbody').empty();
					oTableAudCircuito.fnAddData(jsonObj.PorCircuito);

					$('a[href="#tabs-Result"]').click();
				}
				else
					$_showMessage('ERR', jsonObj.status, jsonObj.msg);
	        },
	        error: function(){
	        	$.unblockUI();
	        }
	    });
	});

  $('#limpiar').click(function(e){
    e.preventDefault();
    $('#dt_audienciaGeneral tbody').empty();
  });

	$('#btnEvaluarAudiencia').click(function(e){
		e.preventDefault();

		// $.blockUI({
	  //       baseZ: 2000,
	  //       timeout: 0,
	  //       message: '<p>Ejecutando...</p><img src="images/ajax-loader-7.gif">',
	  //       css: {
	  //           border: 'none',
	  //           padding: '15px',
	  //           backgroundColor: '#000',
	  //           '-webkit-border-radius': '10px',
	  //           '-moz-border-radius': '10px',
	  //           opacity: .5,
	  //           color: '#fff'
	  //       }
	  //   });

    $(".loader").show();

    //limpio gráficos y minimapaudiencias
    $('#minimapaudiencias').empty();

    var canvas2p = $('#canvas2').parent();
    canvas2p.empty();
    canvas2p.append('<canvas id="canvas2"></canvas>');

    var canvas3p = $('#canvas3').parent();
    canvas3p.empty();
    canvas3p.append('<canvas id="canvas3"></canvas>');

    var canvas5p = $('#canvas5').parent();
    canvas5p.empty();
    canvas5p.append('<canvas id="canvas5"></canvas>');

    var canvas6p = $('#canvas6').parent();
    canvas6p.empty();
    canvas6p.append('<canvas id="canvas6"></canvas>');

    $.blockUI({
	        baseZ: 2000,
	        timeout: 0,
	        //message: '<p>Ejecutando...</p><img src="images/ajax-loader-7.gif">',
	        css: {
	            border: 'none',
	            padding: '0px',
	            backgroundColor: '#000',
	            '-webkit-border-radius': '10px',
	            '-moz-border-radius': '10px',
	            opacity: 0,
	            color: '#fff'
	        }
	    });



		//Serializo solamente los Objetos Visibles de la grilla
    //Acá debería privilegiar los favoritos, si los hay....
    //Creo que esto ya está hecho en joFavoritosIds/Map y joPOISResultIds/Map
		joUbiAudiencia = [];

    if (joFavoritosMap.length > 0) {

      joUbiAudiencia = joFavoritosMap;

    } else {

      oTableMAP.$('tr').each(function() {
          var aData = oTableMAP.fnGetData(this);
          var aPos = oTableMAP.fnGetPosition(this);
          var idUbicacion = ($(aData[12]).attr('id')).replace('fav_grilla_', '');

          if($(this).is(":visible")) {
          	joUbiAudiencia.push(idUbicacion);
          }
      });

    }



		//Serializo las Cantidades de Buses
		var joUbiBusesCantidad = [];
		var esBuses = false;
		if($('#grillaDetalleBuses').is(":visible")) {
			esBuses = true;
			oTableFiltroBuses.$('tr').each(function() {
			    var aData = oTableFiltroBuses.fnGetData(this);
			    var aPos = oTableFiltroBuses.fnGetPosition(this);

			    var arrIdsBuses = ($(aData[3]).attr('id')).replace('cantidad_buses_', '').split("_");

			    var joBuses = {
		    		'idUbicacion' : arrIdsBuses[0],
		    		'cantidad' : $('#'+$(aData[3]).attr('id')).val(),
		    		'idElemento' : arrIdsBuses[2],
		    		'idMapBuses' : arrIdsBuses[1]
			    };

			    joUbiBusesCantidad.push(joBuses);
			});
		}

		var paramData = 'actionOfForm=evaluarAudiencia&joAudiencia=' + JSON.stringify(jsonAudiencia) + "&joUbiAudiencia=" + encodeURIComponent(JSON.stringify(joUbiAudiencia)) + "&joUbiBusesCantidad=" + encodeURIComponent(JSON.stringify(joUbiBusesCantidad)) + "&esBuses=" + esBuses;

		setTimeout(function(){
			$.ajax({
		        type: 'POST',
		        url: actionForm,
		        data: paramData,
				dataType: 'json',
		        success: function(jsonObj){
              $(".loader").hide();
		        	$.unblockUI();
		        	if(jsonObj.status === 'OK'){
		        		idAudiencia = jsonObj.ID;

		        		$('#dt_audienciaGeneral tbody').empty();
                oTableAudGeneral.fnClearTable();
		        		oTableAudGeneral.fnAddData(['Target', jsonObj.Target]);
		        		oTableAudGeneral.fnAddData(['Universo', jsonObj.Universo]);
		        		oTableAudGeneral.fnAddData(['Cantidad Ubicaciones', jsonObj.CantUbicaciones]);
		        		oTableAudGeneral.fnAddData(['Cobertura Neta', jsonObj.CoberturaNeta]);
		        		oTableAudGeneral.fnAddData(['Frecuencia', jsonObj.Frecuencia]);
		        		oTableAudGeneral.fnAddData(['Impactos Totales', jsonObj.Impactos]);
		        		oTableAudGeneral.fnAddData(['Cobertura %', jsonObj.Cobertura_Porc]);
		        		oTableAudGeneral.fnAddData(['PBR', jsonObj.PBR]);
		        		oTableAudGeneral.fnAddData(['CPR', jsonObj.CPR]);
		        		oTableAudGeneral.fnAddData(['CPM', jsonObj.CPM]);

		        		$('#dt_audienciaDetallada tbody').empty();
                oTableAudDetallada.fnClearTable();
		        		oTableAudDetallada.fnAddData(jsonObj.Detallada);

						$('#dt_audienciaEmpresa tbody').empty();
            oTableAudEmpresa.fnClearTable();
						oTableAudEmpresa.fnAddData(jsonObj.PorEmpresa);

						$('#dt_audienciaElemento tbody').empty();
            oTableAudElemento.fnClearTable();
						oTableAudElemento.fnAddData(jsonObj.PorElemento);

						$('#dt_audienciaCircuito tbody').empty();
            oTableAudCircuito.fnClearTable();
						oTableAudCircuito.fnAddData(jsonObj.PorCircuito);

						$('a[href="#tabs-Result"]').click();

            chartGenero(jsonObj.PersonasCoberturaxSexo);
            chartEdades(jsonObj.PersonasCoberturaxEDAD);
            chartNSE(jsonObj.PersonasCoberturaxNSE);
            chartIntereses();
            showMinimapAudiencias();


					}
					else
						$_showMessage('ERR', jsonObj.status, jsonObj.msg);
		        },
		        error: function(){
              $(".loader").hide();
		        	$.unblockUI();
		        }
		    });
		}, 1000);
	});

	var oTableFiltroEdad = $('#dt_filtroEdad').dataTable( {
							//"bJQueryUI": true,
              "pagingType": "simple",
              //"sPaginationType": "two_button",
							"bInfo": false,
							"bLengthChange": false,
							"bAutoWidth": false,
							"bProcessing": true,
							"bServerSide": true,
							"bFilter": false,
							"bPaginate": false,
							"oLanguage": {
								"sUrl": "js/datatables/js/dataTables.es.txt"
							},
							"sAjaxSource": actionForm + "?actionOfForm=searchFiltroEdad",
							"fnDrawCallback": function(oSettings, json) {
								oTableFiltroEdad.$('tr').each(function() {
									var aData = oTableFiltroEdad.fnGetData(this);
									$(aData[0]).trigger("click");
								});
							},
							"aoColumns": [
							  			{ "sWidth": "10%", "sClass": "center", "bSortable": false },
							  			{ "sWidth": "60%"}
							  		]

						});

	var oTableFiltroSexo = $('#dt_filtroSexo').dataTable( {
							//"bJQueryUI": true,
              "pagingType": "simple",
              //"sPaginationType": "two_button",
							"bInfo": false,
							"bLengthChange": false,
							"bAutoWidth": false,
							"bProcessing": true,
							"bServerSide": true,
							"bFilter": false,
							"bPaginate": false,
							"oLanguage": {
								"sUrl": "js/datatables/js/dataTables.es.txt"
							},
							"sAjaxSource": actionForm + "?actionOfForm=searchFiltroSexo",
							"fnDrawCallback": function(oSettings, json) {
								oTableFiltroSexo.$('tr').each(function() {
									var aData = oTableFiltroSexo.fnGetData(this);
									$(aData[0]).trigger("click");
								});
							},
							"aoColumns": [
							  			{ "sWidth": "10%", "sClass": "center", "bSortable": false },
							  			{ "sWidth": "60%"}
							  		]

						});

	var oTableFiltroNSE = $('#dt_filtroNSE').dataTable( {
							//"bJQueryUI": true,
              "pagingType": "simple",
              //"sPaginationType": "two_button",
							"bInfo": false,
							"bLengthChange": false,
							"bAutoWidth": false,
							"bProcessing": true,
							"bServerSide": true,
							"bFilter": false,
							"bPaginate": false,
							"oLanguage": {
								"sUrl": "js/datatables/js/dataTables.es.txt"
							},
							"sAjaxSource": actionForm + "?actionOfForm=searchFiltroNSE",
							"fnDrawCallback": function(oSettings, json) {
								oTableFiltroNSE.$('tr').each(function() {
									var aData = oTableFiltroNSE.fnGetData(this);
									$(aData[0]).trigger("click");
								});
							},
							"aoColumns": [
							  			{ "sWidth": "10%", "sClass": "center", "bSortable": false },
							  			{ "sWidth": "60%"}
							  		]

						});

	var oTableFiltroPeriodo = $('#dt_filtroPeriodo').dataTable( {
							//"bJQueryUI": true,
              "pagingType": "simple",
              //"sPaginationType": "two_button",
							"bInfo": false,
							"bLengthChange": false,
							"bAutoWidth": false,
							"bProcessing": true,
							"bServerSide": true,
							"bFilter": false,
							"bPaginate": false,
							"oLanguage": {
								"sUrl": "js/datatables/js/dataTables.es.txt"
							},
							"sAjaxSource": actionForm + "?actionOfForm=searchFiltroPeriodo",
							"fnDrawCallback": function(oSettings, json) {
								oTableFiltroPeriodo.$('tr').each(function() {
									var aData = oTableFiltroPeriodo.fnGetData(this);
									$(aData[0]).trigger("click");
								});
							},
							"aoColumns": [
							  			{ "sWidth": "10%", "sClass": "center", "bSortable": false },
							  			{ "sWidth": "60%"}
							  		]

						});

  // $('#dt_filtroComun tbody').empty();
  // if (oTableFiltroComun) oTableFiltroComun.destroy();

  console.log('joFilterUbicaciones');
  console.log(joFilterUbicaciones);
  console.log('joUbicacionesCantidad');
  console.log(joUbicacionesCantidad);

	var oTableFiltroComun = $('#dt_filtroComun').DataTable( {
		//"bJQueryUI": true,
    "pagingType": "simple",
    //"sPaginationType": "two_button",
		"bInfo": false,
		"bLengthChange": false,
		"bAutoWidth": false,
		"bProcessing": true,
		"bServerSide": true,
		"bFilter": false,
		"bPaginate": false,
		"oLanguage": {
			"sUrl": "js/datatables/js/dataTables.es.txt"
		},
		"sAjaxSource": actionForm + "?actionOfForm=searchFiltroComun&joFilterUbicaciones=" + encodeURIComponent(JSON.stringify(joFilterUbicaciones)) + '&joUbicacionesCantidad=' + encodeURIComponent(JSON.stringify(joUbicacionesCantidad)),
		"aoColumns": [
		  			null,
		  			null,
		  			null,
		  			null
		  		]

	});

	var esBuses = false;
	var jaElementosBuses = ["153","144","223","224","102","225","226","78"];
	$.each(jaElementosBuses, function(i, ite) {
		if($.inArray(ite, joFilterUbicaciones.joElementosIds) > -1) {
			esBuses = true;
			return false;
		}
	});

	if(esBuses) {
		$('#grillaDetalleComun').hide();
		$('#grillaDetalleBuses').show();
	}
	else {
		$('#grillaDetalleComun').show();
		$('#grillaDetalleBuses').hide();
	}

	var oTableFiltroBuses = $('#dt_filtroBuses').dataTable( {
		//"bJQueryUI": true,
    "pagingType": "simple",
    //"sPaginationType": "two_button",
		"bInfo": false,
		"bLengthChange": false,
		"bAutoWidth": false,
		"bProcessing": true,
		"bServerSide": true,
		"bFilter": false,
		"bPaginate": false,
		"oLanguage": {
			"sUrl": "js/datatables/js/dataTables.es.txt"
		},
		"sAjaxSource": actionForm + "?actionOfForm=searchFiltroBuses&joFilterUbicaciones=" + encodeURIComponent(JSON.stringify(joFilterUbicaciones)) + '&joUbicacionesCantidad=' + encodeURIComponent(JSON.stringify(joUbicacionesCantidad)),
		"aoColumns": [
		  			null,
		  			null,
		  			null,
		  			null
		  		]

	});

	//Tablas Audiencia
	oTableAudGeneral = $('#dt_audienciaGeneral').dataTable( {
		//"bJQueryUI": true,
		"bAutoWidth": false,
		"bInfo": false,
		"bRetrieve": true,
		"bFilter": false,
		"bPaginate": false,
		"bLengthChange": false,
    //     "sScrollY" : "200px",
         // "bScrollInfinite": true,
         // "bScrollCollapse" : false,
		"bSearchable":false,
		"bProcessing": false,
		"bSort": false,
		"aoColumns": [
		               	null,
		 				null
		 			],
		"aaData": []
	});

	oTableAudDetallada = $('#dt_audienciaDetallada').dataTable( {
		//"bJQueryUI": true,
		"bAutoWidth": false,
		"bInfo": false,
		"bRetrieve": true,
		"bFilter": false,
		"bPaginate": false,
		"bLengthChange": false,
        //"sScrollY" : "auto",
        "bScrollInfinite": true,
        "bScrollCollapse" : false,
		"bSearchable":false,
		"bProcessing": false,
		"aoColumns": [
		              	null,
		              	null,
		              	null,
		              	null,
		              	null,
		              	null,
		              	null,
		              	null,
		              	null,
		              	null,
		              	null,
		              	null,
						null
					],
		"aaData": []
	});

	oTableAudEmpresa = $('#dt_audienciaEmpresa').dataTable( {
		//"bJQueryUI": true,
		"bAutoWidth": false,
		"bInfo": false,
		"bRetrieve": true,
		"bFilter": false,
		"bPaginate": false,
        // "sScrollY" : "200px",
        "bScrollInfinite": true,
        "bScrollCollapse" : false,
		"bLengthChange": false,
		"bSearchable":false,
		"bProcessing": false,
		"aoColumns": [
		              	null,
		              	null,
		              	null,
		              	null,
		              	null,
		              	null,
		              	null,
		              	null
					],
		"aaData": []
	});

	oTableAudElemento = $('#dt_audienciaElemento').dataTable( {
		//"bJQueryUI": true,
		"bAutoWidth": false,
		"bInfo": false,
		"bRetrieve": true,
		"bFilter": false,
		"bPaginate": false,
        // "sScrollY" : "200px",
        "bScrollInfinite": true,
        "bScrollCollapse" : false,
		"bLengthChange": false,
		"bSearchable":false,
		"bProcessing": false,
		"aoColumns": [
		              	null,
		              	{"bVisible": false}, //ID Audiencia
		              	null,
		              	null,
		              	null,
		              	null,
		              	null,
		              	null,
		              	null
					],
		"aaData": []
	});

	oTableAudCircuito = $('#dt_audienciaCircuito').dataTable( {
		//"bJQueryUI": true,
		"bAutoWidth": false,
		"bInfo": false,
		"bRetrieve": true,
		"bFilter": false,
		"bPaginate": false,
        // "sScrollY" : "200px",
        "bScrollInfinite": true,
        "bScrollCollapse" : false,
		"bLengthChange": false,
		"bSearchable":false,
		"bProcessing": false,
		"aoColumns": [
		              	null,
		              	null,
		              	null,
		              	null,
		              	null,
		              	null,
		              	null,
		              	null,
						null,
						null
					],
		"aaData": []
	});
}

function $_planesAudienciaGenerados()
{
	idAudiencia = undefined;
	$('#cmbDescAudiencia').empty();

	//Planes Generados
	$.ajax({
	    url: actionForm,
	    type:'POST',
	    data: 'actionOfForm=getAudienciasGuardadas',
	    dataType: 'json',
	    success: function(json) {
	    	$('#cmbDescAudiencia').append($('<option>').text('Planes Generados').attr('value', 0));
	        $.each(json, function(i, value) {
	        	$('#cmbDescAudiencia').append($('<option>').text(value.descripcion).attr('value', value.idMapAudiencia));
	        });
	    }
	});
}

function $_initSavePlanAudienciaPop()
{
	//Add Click for All Buttons in Pop Form
	$('#btnAudienciaSalir').click( function(e) {
		$('#audienciaPlanDialog').dialog('close');
	});

	$('#btnAudienciaGuardar').click( function(e) {
		$('#audienciaPlanPopForm').submit();
	});

	$_ValidatorSetDefaults();

	$('#audienciaPlanPopForm').validate({
		submitHandler: function() {
			var paramData = 'actionOfForm=addOrEditAudienciaPlan&' + $("#audienciaPlanPopForm").serialize();
				paramData += '&idMapAudiencia=' + idAudiencia;

		    $.ajax({
		        type: 'POST',
		        url: actionForm,
		        data: paramData,
				dataType: 'json',
		        success: function(jsonObj){
		            if(jsonObj.status === 'OK'){
						$_showMessage('OK', jsonObj.status, jsonObj.msg);
						$_planesAudienciaGenerados();
						$('#audienciaPlanDialog').dialog('close');
					}
					else
						$_showMessage('ERR', jsonObj.status, jsonObj.msg);
		        }
		    });
		},
		rules: {
			descripcionPlan: {required: true,minlength: 1}
		},
		messages: {
			descripcionPlan: {required: '',minlength: ''}
		}
	});
}

function $_addRemoveFilterAudiencia(idFiltro, tipo)
{
	var exist;

	switch (tipo) {
		case 'edad':
			if(!$.isEmptyObject(jsonAudiencia.jaEdades)) {
				$.each(jsonAudiencia.jaEdades, function(i, iEdades) {
					if(iEdades == idFiltro) {
						jsonAudiencia.jaEdades.splice(i, 1);
						exist = true;
						return false;
					}
				});
			}
			if(!exist)
				jsonAudiencia.jaEdades.push(idFiltro);
			break;
		case 'sexo':
			if(!$.isEmptyObject(jsonAudiencia.jaSexo)) {
				$.each(jsonAudiencia.jaSexo, function(i, iSexo) {
					if(iSexo == idFiltro) {
						jsonAudiencia.jaSexo.splice(i, 1);
						exist = true;
						return false;
					}
				});
			}
			if(!exist)
				jsonAudiencia.jaSexo.push(idFiltro);
			break;
		case 'nse':
			if(!$.isEmptyObject(jsonAudiencia.jaMSE)) {
				$.each(jsonAudiencia.jaMSE, function(i, iMSE) {
					if(iMSE == idFiltro) {
						jsonAudiencia.jaMSE.splice(i, 1);
						exist = true;
						return false;
					}
				});
			}
			if(!exist)
				jsonAudiencia.jaMSE.push(idFiltro);
			break;
		case 'periodo':
			if(!$.isEmptyObject(jsonAudiencia.jaPeriodo)) {
				$.each(jsonAudiencia.jaPeriodo, function(i, iPeriodo) {
					if(iPeriodo == idFiltro) {
						jsonAudiencia.jaPeriodo.splice(i, 1);
						exist = true;
						return false;
					}
				});
			}
			if(!exist)
				jsonAudiencia.jaPeriodo.push(idFiltro);
			break;
	}
}

function $_initShareMap()
{

	var $uniqueID = getParameterByName('uniqueID');
	var paramData = 'actionOfForm=cargarShareMap&uniqueID=' + $uniqueID;

  // console.log('$uniqueID:'+$uniqueID);

	if ($uniqueID == "") return;

	$.ajax({
        type: 'POST',
        url: actionForm,
        data: paramData,
		dataType: 'json',
        success: function(jsonObj)
                            {
                                if(jsonObj.status === 'OK') {

                                	joFilterUbicaciones = jsonObj.joFilterUbicaciones;
                                	if(!$.isEmptyObject(joFilterUbicaciones)) {

                                		joFilterUbicaciones = eval('(' + jsonObj.joFilterUbicaciones + ')');

                                		joMedioMap = joFilterUbicaciones.joMedioIds;
                                		joFormatoMap = joFilterUbicaciones.joFormatoIds;
                                    	joElementosMap = joFilterUbicaciones.joElementosIds;
                                    	joEVPMap = joFilterUbicaciones.joEVPIds;
                                    	joProvinciaMap = joFilterUbicaciones.joProvinciaIds;
                                    	joLocalidadMap = joFilterUbicaciones.joLocalidadIds;
                                    	joEntidadPoisMap = joFilterUbicaciones.joEntidadPOIs;
                                    	joFavoritosMap = joFilterUbicaciones.joFavoritosIds;
                                      joPOISResultMap = joFilterUbicaciones.joPOISResultIds;

                                    	//Ubicaciones no Visibles
                                    	joUbicacionesCantidad = JSON.parse(jsonObj.joUbicacionesCantidad);
                                    	joMarkerNotVisibleIds = JSON.parse(jsonObj.joMarkerNotVisibleIds);

                                    	$_actulizarMapa();
                                	}
                                	else if(!$.isEmptyObject(jsonObj.joUbicacionesCantidad)){
                                    	joUbicacionesCantidad = JSON.parse(jsonObj.joUbicacionesCantidad);
                                    	joMarkerNotVisibleIds = [];
                                    	$_actulizarMapa();
                                	}

                                	$_showMessage('OK', 'OK', jsonObj.msg);
                                } else {
									                $_showMessage('ERR', 'ERROR', jsonObj.msg);
                                  // console.log('err:'+jsonObj.msg);
                                  }

                            }
        });
}

function getParameterByName(name) {
    name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
    var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
        results = regex.exec(location.search);
    return results == null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
}

function getParameterByName_new(name) {
    if (name !== "" && name !== null && name != undefined) {
        name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
        var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
            results = regex.exec(location.search);
        return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
    } else {
        var arr = location.href.split("/");
        return arr[arr.length - 1];
    }

}

//Charts

function chartGenero(values){

    //console.log(values);

    var valores = values.split(';');

		var color = Chart.helpers.color;
		var barChartData = {
			labels: ['Género'],
			datasets: [{
				label: 'Hombres',
				backgroundColor: color(window.chartColors.blue).alpha(0.5).rgbString(),
				borderColor: window.chartColors.blue,
				borderWidth: 1,
				data: [
					valores[1]
				]
			},
      {
        label: 'Mujeres',
        backgroundColor: color(window.chartColors.red).alpha(0.5).rgbString(),
        borderColor: window.chartColors.red,
        borderWidth: 1,
        data: [
          valores[3]
        ]
      }]
		};

  var ctx = document.getElementById('canvas3').getContext('2d');
			window.myBar = new Chart(ctx, {
				type: 'bar',
				data: barChartData,
				options: {
					responsive: true,
					legend: {
						position: 'top',
					},
					title: {
						display: true,
						text: 'Género'
					},
          scales: {
              yAxes: [{
                  ticks: {
                      beginAtZero: true
                  }
              }]
          }
				}
			});
}

function chartEdades(values){

    //console.log(values);

    var valores = values.split(';');

		var color = Chart.helpers.color;
		var barChartData = {
			labels: ['Edades'],
			datasets: [{
				label: '18-30',
				backgroundColor: color(window.chartColors.orange).alpha(0.5).rgbString(),
				borderColor: window.chartColors.orange,
				borderWidth: 1,
				data: [
					valores[1]
				]
			},
      {
        label: '31-45',
        backgroundColor: color(window.chartColors.green).alpha(0.5).rgbString(),
        borderColor: window.chartColors.green,
        borderWidth: 1,
        data: [
          valores[3]
        ]
      },
      {
        label: '46+',
        backgroundColor: color(window.chartColors.purple).alpha(0.5).rgbString(),
        borderColor: window.chartColors.purple,
        borderWidth: 1,
        data: [
          valores[5]
        ]
      }]
		};

  var ctx = document.getElementById('canvas5').getContext('2d');
			window.myBar = new Chart(ctx, {
				type: 'bar',
				data: barChartData,
				options: {
					responsive: true,
					legend: {
						position: 'top',
					},
					title: {
						display: true,
						text: 'Edades'
					},
          scales: {
              yAxes: [{
                  ticks: {
                      beginAtZero: true
                  }
              }]
          }
				}
			});
}

function chartNSE(values){

  //console.log(values);

  var valores = values.split(';');

  var config = {
  			type: 'doughnut',
  			data: {
  				datasets: [{
  					data: [
  						valores[1],
  						valores[3],
  						valores[5]
  					],
  					backgroundColor: [
  						window.chartColors.red,
  						// window.chartColors.orange,
  						window.chartColors.yellow,
  						// window.chartColors.green,
  						window.chartColors.blue,
  					],
  					label: 'NSE'
  				}],
  				labels: [
  					'Alto',
  					'Medio',
  					'Bajo'
  				]
  			},
  			options: {
  				responsive: true,
  				legend: {
  					position: 'top',
  				},
  				title: {
  					display: true,
  					text: 'NSE'
  				},
  				animation: {
  					animateScale: true,
  					animateRotate: true
  				}
  			}
  		};

      var ctx = document.getElementById('canvas2').getContext('2d');
			window.myDoughnut = new Chart(ctx, config);

}

function chartIntereses(){
  var config = {
  			type: 'doughnut',
  			data: {
  				datasets: [{
  					data: [
  						100
  					],
  					backgroundColor: [
  						//window.chartColors.red,
  						//window.chartColors.orange,
  						// window.chartColors.yellow,
  						//window.chartColors.green,
  						// window.chartColors.blue,
              window.chartColors.gray
  					],
  					label: 'Intereses'
  				}],
  				labels: [
  					'No disponible'
  				]
  			},
  			options: {
  				responsive: true,
  				legend: {
  					position: 'top',
  				},
  				title: {
  					display: true,
  					text: 'Intereses'
  				},
  				animation: {
  					animateScale: true,
  					animateRotate: true
  				}
  			}
  		};

      var ctx = document.getElementById('canvas6').getContext('2d');
			window.myDoughnut = new Chart(ctx, config);
}
