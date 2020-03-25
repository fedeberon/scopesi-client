/*
 * Created by Federico Pisarello - SisDev Software (c) 2012 - fpisarello@sisdevsoft.com
 */

var actionForm = 'auditoriaAction.php';
var popCampannaForm = 'auditoriaCampannaPop.php';
var popInformeForm = 'auditoriaInformePop.php';
var popCircuitoForm = 'auditoriaCircuitoPop.php';
var popCircuitoDetalleForm = 'auditoriaCircuitoDetallePop.php';
var popAudiencia = "auditoriaAudienciaPop.php";
var popAudienciaSaveForm = "auditoriaAudienciaSavePop.php";
var popMapaForm = 'auditoriaMapping.php';
var popAutoFilter = "autoFilterPop.php";

var oTableCampanna;
var oTableInforme;
var tipoMapa = '';

var tipoFiltroAuditoria;

var oTableCampannaCircuito;
var oTableCampannaCircuitoDetalle;

var idCampannaSelect = undefined;
var idCircuitoSelect = undefined;

//Variables de Filtro Campannas
var joCampannas = undefined;

//Variables de Filtro Informes
var joCampannasInf = undefined;
var joElementosInf = undefined;
var joEVPInf = undefined;
var joProvinciaInf = undefined;
var joLocalidadInf = undefined;
var joFrecuenciaInf = undefined;

//Campannas Habilitadas por Contrato
var joCampannasHab = undefined;

var txtMesAnnoDesde;
var txtMesAnnoHasta;
var chkMesAnio = false;
var chkCampanna = false;

var txtMesAnnoDesdeInf;
var txtMesAnnoHastaInf;
var chkMesAnioInf = false;
var chkCampannaInf = false;
var chkElementoInf = false;
var chkEVPInf = false;
var chkProvinciaInf = false;
var chkLocalidadInf = false;
var chkFrecuenciaInf = false;

var iFilterCampanna = new fn_colFilterCampanna();
var iFilterInforme = new fn_colFilterInforme();

var joCampannasSelect = [];
var joCircuitosSelect = [];

function fn_colFilterCampanna()
{
	this.CAMPANNA = 0;
}

function fn_colFilterInforme()
{
	this.CAMPANNA = -1;
	this.ELEMENTO = 0;
	this.EVP = 1;
	this.PROVINCIA = 2;
	this.LOCALIDAD = 3;
	this.DESDE = 4;
	this.HASTA = 5;
	this.CONTROL = 6;
	this.FRECUENCIA = 7;
}

var actualFilter;
var emptyTable = true;

function $_init()
{
	$('.btnNav').each(function (i){
		$(this).removeClass($(this).attr('id')+'Act');
		$(this).addClass($(this).attr('id'));
	});
	$('#btnAudi').addClass('btnAudiAct');

	var gaiSelected;

	$_getCampannasHab();

	$(document).ready(function() {
		oTableCampanna = $('#dt_auditoriasCampannas').dataTable( {
					"bJQueryUI": true,
					"sPaginationType": "two_button",
					"bInfo": false,
					"bAutoWidth": false,
					"bProcessing": true,
					"bServerSide": true,
					"oLanguage": {
						"sUrl": "js/datatables/js/dataTables.es.txt"
					},
					"sAjaxSource": actionForm + "?actionOfForm=searchCampanna&emptyTable=" + emptyTable,
					"fnDrawCallback": function(oSettings) {
						$('.chkClassCampania').each(function() {
							var chkValue = $(this);
							$.each(joCampannasSelect, function(i, value) {
								if(value == chkValue.attr('id')) {
									chkValue.attr('checked', true);
									return false;
								}
							});
						});
					},
					"aoColumns": [
					  			null, //Id
					  			null, //Nombre Campanna
					  			null, //Producto
					  			null, //Agencia
					  			{ "sWidth": "10%", "sClass": "center", "bSortable": false }, //Btn. Ingresar
					  			{ "sWidth": "10%", "sClass": "center", "bSortable": false }, //Btn. auditoria
					  			{ "sWidth": "10%", "sClass": "center", "bSortable": false }, //Btn. Download ZIP
					  		]
			});


		oTableInforme = $('#dt_auditoriaInformes').dataTable( {
			"bJQueryUI": true,
			"sPaginationType": "two_button",
			"bInfo": false,
			"bProcessing": true,
			"bAutoWidth": false,
			"bServerSide": true,
			"oLanguage": {
				"sUrl": "js/datatables/js/dataTables.es.txt"
			},
			"sAjaxSource": actionForm + "?actionOfForm=searchInforme&emptyTable=" + emptyTable,
			"aoColumns": [
				  			{"bVisible": false, "bSearchable": false }, //Dispositivo
				  			{"bVisible": false, "bSearchable": false }, //Empresa
				  			{"bVisible": false, "bSearchable": false }, //Provincia
				  			{"bVisible": false, "bSearchable": false }, //Localidad
				  			{"bVisible": false, "bSearchable": false }, //Desde
				  			{"bVisible": false, "bSearchable": false }, //Hasta
				  			{"bVisible": false, "bSearchable": false }, //Control
				  			{"bVisible": false, "bSearchable": false }, //Frecuencia
				  			{
				  				"bSearchable": false,
				            	"sType": "numeric",
				            	"bSortable": false,
				            	"sClass": "textAlignRight",
				            	"fnRender": function (oObj) {
				            		return RenderDecimalNumber(oObj);
				            	}
				  			}, //Sumatoria Exhibido OK
				  			{
				  				"bSearchable": false,
				            	"sType": "numeric",
				            	"bSortable": false,
				            	"sClass": "textAlignRight",
				            	"fnRender": function (oObj) {
				            		return RenderDecimalNumber(oObj);
				            	}
				  			}, //Sumatoria Sin Afiche
				  			{
				  				"bSearchable": false,
				            	"sType": "numeric",
				            	"bSortable": false,
				            	"sClass": "textAlignRight",
				            	"fnRender": function (oObj) {
				            		return RenderDecimalNumber(oObj);
				            	}
				  			}, //Sumatoria Con Desperfectos
				  			{
				  				"bSearchable": false,
				            	"sType": "numeric",
				            	"bSortable": false,
				            	"sClass": "textAlignRight",
				            	"fnRender": function (oObj) {
				            		return RenderDecimalNumber(oObj);
				            	}
				  			} //Sumatoria Total Exhibido
			  		]
			});

    	emptyTable = false;

    	$("#btnCampannas").click( function(e) {
    		tipoFiltroAuditoria = "CAMP";
				$('#auditoriaCampannaDialog').dialog({
					position:{
						my: 'center',
						at: 'center',
						of: window
					}
				});
    		$('#auditoriaCampannaDialog').load(popCampannaForm).dialog('open');
    	});

    	$('#btnInformes').click( function(e) {
    		tipoFiltroAuditoria = "INFO";
				$('#auditoriaInformeDialog').dialog({
					position:{
						my: 'center',
						at: 'center',
						of: window
					}
				});
    		$('#auditoriaInformeDialog').load(popInformeForm).dialog('open');
    	});


    	$('#btnMapa').click( function(e) {
    		if($.isEmptyObject(joCampannasSelect)) {
    			$_showMessage('ALERT', 'ALERTA', 'Seleccione alguna Campa&ntilde;a');
    		}
    		else {
    			tipoMapa = 'MP';
					$('#auditoriaMapaDialog').dialog({
						position:{
							my: 'center',
							at: 'center',
							of: window
						}
					});
    			$('#auditoriaMapaDialog').load(popMapaForm + '?tipoMapa='+ tipoMapa + '&joCampannasSelect='+ encodeURIComponent(JSON.stringify(joCampannasSelect))).dialog('open');
    		}

    	});

    	$('#btnExcelInforme').click( function(e) {
    		$_exportarExcel();
    	});

    	$("#auditoriaMapaDialog").dialog({
    		autoOpen:false,
    		height:600,
    		width:1024,
    		modal: true,
    		maxHeight: false,
    		closeOnEscape: false,
    		position : ['center',10],
    		dialogClass:'no-close',
    		open: function() {
    			$('#auditoriaMapaDialog').css('background','#F5F5F5');
    		}
    	});

    	$("#auditoriaCampannaDialog").dialog({
    		autoOpen:false,
    		height:'auto',
    		width:330,
    		modal: true,
    		closeOnEscape: false,
    		position : ['center',10],
    		dialogClass:'no-close',
    		open: function() {
    			$('#auditoriaCampannaDialog').css('background','#F5F5F5');
    		}
    	});

    	$("#auditoriaFotosDialog").dialog({
    		autoOpen:false,
    		height:'auto',
    		width:760,
    		modal: true,
    		closeOnEscape: false,
    		position : ['center',10],
    		dialogClass:'no-close',
    		open: function() {
    			$('#auditoriaFotosDialog').css('background','#F5F5F5');
    		}
    	});

    	$("#auditoriaInformeDialog").dialog({
    		autoOpen:false,
    		height:'auto',
    		width:340,
    		modal: true,
    		closeOnEscape: false,
    		position : ['center',10],
    		dialogClass:'no-close',
    		open: function() {
    			$('#auditoriaInformeDialog').css('background','#F5F5F5');
    		}
    	});

    	$("#auditoriaCircuitoDialog").dialog({
    		autoOpen:false,
    		height:'auto',
    		width:1024,
    		modal: true,
    		closeOnEscape: false,
    		position : ['center',10],
    		dialogClass:'no-close',
    		open: function() {
    			$('#auditoriaCircuitoDialog').css('background','#F5F5F5');
    		}
    	});

    	$("#auditoriaCircuitoDetalleDialog").dialog({
    		autoOpen:false,
    		height:'auto',
    		width:1024,
    		closeOnEscape: false,
    		position : ['center',10],
    		dialogClass:'no-close',
    		open: function() {
    			$('#auditoriaCircuitoDetalleDialog').css('background','#F5F5F5');
    		}
    	});

    	$("#auditoriaAudienciaDialog").dialog({
    		autoOpen:false,
    		height:'auto',
    		width: 900,
    		modal: true,
    		resizable: false,
    		closeOnEscape: false,
    		position : ['center',10],
    		dialogClass:'no-close',
    		open: function() {
    			$('#auditoriaAudienciaDialog').css('background','#F5F5F5');
    		}
    	});
	});
}

function $_getCampannasHab()
{
	var paramData =  "actionOfForm=getCampannasHab";

    $.ajax({
        type: 'POST',
        url: actionForm,
        data: paramData,
		dataType: 'json',
        success: function(jsonObj)
                            {
        						joCampannasHab = jsonObj;
                            }
        });
}

function RenderDecimalNumber(oObj) {
	var num = new NumberFormat();
	num.setInputDecimal('.');
	num.setNumber(oObj.aData[oObj.iDataColumn]);
	num.setPlaces(0, true);
	num.setNegativeFormat(num.LEFT_DASH);
	return num.toFormatted();
}

function $_initInformesPop()
{
	//Mascaras
	$("#txtMesAnnoDesdeInf").mask("99/9999");
	$("#txtMesAnnoHastaInf").mask("99/9999");

	//Estado de Checkbox y Textbox
	$('#chkMesAnnoInf').attr('checked', chkMesAnioInf);
	$('#chkCampannaInf').attr('checked', chkCampannaInf);
	$('#chkElementoInf').attr('checked', chkElementoInf);
	$('#chkEVPInf').attr('checked', chkEVPInf);
	$('#chkProvinciaInf').attr('checked', chkProvinciaInf);
	$('#chkLocalidadInf').attr('checked', chkLocalidadInf);
	$('#chkFrecuenciaInf').attr('checked', chkFrecuenciaInf);
	$('#txtMesAnnoDesdeInf').val(txtMesAnnoDesdeInf);
	$('#txtMesAnnoHastaInf').val(txtMesAnnoHastaInf);

	//Eventos
	$('#chkMesAnnoInf').click( function(e) {
		chkMesAnioInf = $('#chkMesAnnoInf').is(':checked');
	});
	$('#chkCampannaInf').click( function(e) {
		chkCampannaInf = $('#chkCampannaInf').is(':checked');
		if(!$('#chkCampannaInf').is(':checked'))
			chkCampannaInf = undefined;
	});
	$('#chkElementoInf').click( function(e) {
		chkElementoInf = $('#chkElementoInf').is(':checked');
		if(!$('#chkElementoInf').is(':checked'))
			joElementosInf = undefined;
	});
	$('#chkEVPInf').click( function(e) {
		chkEVPInf = $('#chkEVPInf').is(':checked');
		if(!$('#chkEVPInf').is(':checked'))
			joEVPInf = undefined;
	});
	$('#chkProvinciaInf').click( function(e) {
		chkProvinciaInf = $('#chkProvinciaInf').is(':checked');
		if(!$('#chkProvinciaInf').is(':checked'))
			joProvinciaInf = undefined;
	});
	$('#chkLocalidadInf').click( function(e) {
		chkLocalidadInf = $('#chkLocalidadInf').is(':checked');
		if(!$('#chkLocalidadInf').is(':checked'))
			joLocalidadInf = undefined;
	});
	$('#chkFrecuenciaInf').click( function(e) {
		chkFrecuenciaInf = $('#chkFrecuenciaInf').is(':checked');
		if(!$('#chkFrecuenciaInf').is(':checked'))
			joFrecuenciaInf = undefined;
	});

	$('#btnSalirInf').click( function(e) {
		$('#auditoriaInformeDialog').dialog('close');
	});

	$('#txtMesAnnoDesdeInf').blur( function (e) {
		txtMesAnnoDesdeInf = $('#txtMesAnnoDesdeInf').val();
	});

	$('#txtMesAnnoHastaInf').blur( function (e) {
		txtMesAnnoHastaInf = $('#txtMesAnnoHastaInf').val();
	});

	//Eventos de Autofiltro
	$('#btnCampannaInf').click( function(e) {
		$_autofilterProxyShowInforme(iFilterInforme.CAMPANNA);
	});

	$('#btnElementoInf').click( function(e) {
		$_autofilterProxyShowInforme(iFilterInforme.ELEMENTO);
	});
	$('#btnEVPInf').click( function(e) {
		$_autofilterProxyShowInforme(iFilterInforme.EVP);
	});
	$('#btnProvinciaInf').click( function(e) {
		$_autofilterProxyShowInforme(iFilterInforme.PROVINCIA);
	});
	$('#btnLocalidadInf').click( function(e) {
		$_autofilterProxyShowInforme(iFilterInforme.LOCALIDAD);
	});
	$('#btnFrecuenciaInf').click( function(e) {
		$_autofilterProxyShowInforme(iFilterInforme.FRECUENCIA);
	});

	$('#chkCampannaInf').click( function(e) {
		if(!$('#chkCampannaInf').is(':checked'))
			joCampannasInf = undefined;
	});
	$('#chkElementoInf').click( function(e) {
		if(!$('#chkElementoInf').is(':checked'))
			joElementosInf = undefined;
	});
	$('#chkEVPInf').click( function(e) {
		if(!$('#chkEVPInf').is(':checked'))
			joEVPInf = undefined;
	});
	$('#chkProvinciaInf').click( function(e) {
		if(!$('#chkProvinciaInf').is(':checked'))
			joProvinciaInf = undefined;
	});
	$('#chkLocalidadInf').click( function(e) {
		if(!$('#chkLocalidadInf').is(':checked'))
			joLocalidadInf = undefined;
	});
	$('#chkFrecuenciaInf').click( function(e) {
		if(!$('#chkFrecuenciaInf').is(':checked'))
			joFrecuenciaInf = undefined;
	});

	$('#btnGenerarInformeInf').click( function(e) {
		$('#campannas').hide();
		$('#informes').show();

		var paramData = 'actionOfForm=searchInforme&' + $("#auditoriaInformePopForm").serialize()
							+ '&jsonDataCampannas=' + encodeURIComponent(JSON.stringify(joCampannasInf))
							+ '&jsonDataElementos=' + encodeURIComponent(JSON.stringify(joElementosInf))
							+ '&jsonDataEVP=' + encodeURIComponent(JSON.stringify(joEVPInf))
							+ '&jsonDataProvincia=' + encodeURIComponent(JSON.stringify(joProvinciaInf))
							+ '&jsonDataLocalidad=' + encodeURIComponent(JSON.stringify(joLocalidadInf))
							+ '&jsonDataFrecuencia=' + encodeURIComponent(JSON.stringify(joFrecuenciaInf));

		oTableInforme.fnReloadAjax(actionForm + '?' + paramData);

		for(iCol=0;iCol<oTableInforme.fnSettings().aoColumns.length;iCol++) {
			var bVis = oTableInforme.fnSettings().aoColumns[iCol].bVisible;
			switch (iCol) {
				case iFilterInforme.ELEMENTO:
					oTableInforme.fnSettings().aoColumns[iCol].bSearchable = $('#chkElementoInf').is(':checked');
					oTableInforme.fnSetColumnVis(iCol, $('#chkElementoInf').is(':checked'));
					break;

				case iFilterInforme.EVP:
					oTableInforme.fnSettings().aoColumns[iCol].bSearchable = $('#chkEVPInf').is(':checked');
					oTableInforme.fnSetColumnVis(iCol, $('#chkEVPInf').is(':checked'));
					break;

				case iFilterInforme.PROVINCIA:
					oTableInforme.fnSettings().aoColumns[iCol].bSearchable = $('#chkProvinciaInf').is(':checked');
					oTableInforme.fnSetColumnVis(iCol, $('#chkProvinciaInf').is(':checked'));
					break;

				case iFilterInforme.LOCALIDAD:
					oTableInforme.fnSettings().aoColumns[iCol].bSearchable = $('#chkLocalidadInf').is(':checked');
					oTableInforme.fnSetColumnVis(iCol, $('#chkLocalidadInf').is(':checked'));
					break;

				case iFilterInforme.DESDE:
				case iFilterInforme.HASTA:
				case iFilterInforme.CONTROL:
					oTableInforme.fnSettings().aoColumns[iCol].bSearchable = $('#chkMesAnnoInf').is(':checked');
					oTableInforme.fnSetColumnVis(iCol, $('#chkMesAnnoInf').is(':checked'));
					break;

				case iFilterInforme.FRECUENCIA:
					oTableInforme.fnSettings().aoColumns[iCol].bSearchable = $('#chkFrecuenciaInf').is(':checked');
					oTableInforme.fnSetColumnVis(iCol, $('#chkFrecuenciaInf').is(':checked'));
					break;

				default:
					break;
			}
		}
	});

	$_initAutofilterDialog();
}

function $_initCampannasPop()
{
	//Mascaras
	$("#txtMesAnnoDesde").mask("99/9999");
	$("#txtMesAnnoHasta").mask("99/9999");

	//Estado de Checkbox y Textbox
	$('#chkMesAnno').attr('checked', chkMesAnio);
	$('#chkCampanna').attr('checked', chkCampanna);
	$('#txtMesAnnoDesde').val(txtMesAnnoDesde);
	$('#txtMesAnnoHasta').val(txtMesAnnoHasta);

	//Eventos
	$('#chkMesAnno').click( function(e) {
		chkMesAnio = $('#chkMesAnno').is(':checked');
	});
	$('#chkCampanna').click( function(e) {
		chkCampanna = $('#chkCampanna').is(':checked');
		if(!$('#chkCampannas').is(':checked'))
			joCampannas = undefined;
	});

	$('#btnSalirCamp').click( function(e) {
		$('#auditoriaCampannaDialog').dialog('close');
	});

	$('#txtMesAnnoDesde').blur( function (e) {
		txtMesAnnoDesde = $('#txtMesAnnoDesde').val();
	});

	$('#txtMesAnnoHasta').blur( function (e) {
		txtMesAnnoHasta = $('#txtMesAnnoHasta').val();
	});

	//Eventos de Autofiltro
	$('#btnCampanna').click( function(e) {
		$_autofilterProxyShowCampanna(iFilterCampanna.CAMPANNA);
	});


	$('#chkCampanna').click( function(e) {
		if(!$('#chkCampanna').is(':checked'))
			joCampannas = undefined;
	});

	$('#btnGenerarInformeCamp').click( function(e) {
		//Borro las Campannas Seleccionadas
		joCampannasSelect = [];

		$('#informes').hide();
		$('#campannas').show();
		var paramData = 'actionOfForm=searchCampanna&' + $("#auditoriaCampannaPopForm").serialize()
							+ '&jsonDataCampannas=' + encodeURIComponent(JSON.stringify(joCampannas));

		oTableCampanna.fnReloadAjax(actionForm + '?' + paramData);
		$('#dt_auditoriasCampannas').removeAttr('width');
	});

	$_initAutofilterDialog();
}

function $_initAutofilterDialog()
{
	$("#autoFilterDialog").dialog({
		autoOpen:false,
		height:540,
		width:514,
		closeOnEscape: false,
		dialogClass:'no-close',
		beforeClose: function( event, ui ) {
			if(tipoFiltroAuditoria == 'CAMP') {
				switch (actualFilter) {
					case iFilterCampanna.CAMPANNA:
						joCampannas = joDataFilter;
						break;
				}
			}

			if(tipoFiltroAuditoria == 'INFO') {
				switch (actualFilter) {
					case iFilterInforme.CAMPANNA:
						joCampannasInf = joDataFilter;
						break;
					case iFilterInforme.ELEMENTO:
						joElementosInf = joDataFilter;
						break;
					case iFilterInforme.EVP:
						joEVPInf = joDataFilter;
						break;
					case iFilterInforme.PROVINCIA:
						joProvinciaInf = joDataFilter;
						break;
					case iFilterInforme.LOCALIDAD:
						joLocalidadInf = joDataFilter;
						break;
					case iFilterInforme.FRECUENCIA:
						joFrecuenciaInf = joDataFilter;
						break;
				}
			}
		},
		open: function() {
			$('#autoFilterDialog').css('background','#F5F5F5');
			$('#autoFilterDialog').bind( "dialogbeforeclose", function(event, ui) {

			});
		}
	});
}

function $_autofilterProxyShowCampanna(filter)
{
	joDataRowEnabled = [];
	joDataWhereFilter = [];

	//Define filters parameters
	switch (filter) {
		case iFilterCampanna.CAMPANNA:
			tableFilter = "aud_campanias";
			fieldsFilter = "descripcion";
			idTable = "idCampania";
			joDataFilter = joCampannas;
			actualFilter = iFilterCampanna.CAMPANNA;
			joDataRowEnabled = joCampannasHab;
			joDataFechas = {fechaDesde: $('#txtMesAnnoDesde').val(), fechaHasta: $('#txtMesAnnoHasta').val()}
			break;
	}

	$('#autoFilterDialog').load(popAutoFilter).dialog('open');
}

function $_autofilterProxyShowInforme(filter)
{
	joDataRowEnabled = [];
	joDataWhereFilter = [];

	//Define filters parameters
	switch (filter) {
		case iFilterInforme.CAMPANNA:
			tableFilter = "aud_campanias";
			fieldsFilter = "descripcion";
			idTable = "idCampania";
			joDataFilter = joCampannasInf;
			actualFilter = iFilterInforme.CAMPANNA;
			joDataRowEnabled = joCampannasHab;
			joDataFechas = {fechaDesde: $('#txtMesAnnoDesdeInf').val(), fechaHasta: $('#txtMesAnnoHastaInf').val()}
			break;

		case iFilterInforme.ELEMENTO:
			tableFilter = "aud_elementos";
			fieldsFilter = "descripcion";
			idTable = "idElemento";
			joDataFilter = joElementosInf;
			actualFilter = iFilterInforme.ELEMENTO;
			break;

		case iFilterInforme.EVP:
			tableFilter = "aud_empresas";
			fieldsFilter = "descripcion";
			idTable = "idEmpresa";
			joDataFilter = joEVPInf;
			actualFilter = iFilterInforme.EVP;
			break;

		case iFilterInforme.PROVINCIA:
			tableFilter = "aud_provincias";
			fieldsFilter = "descripcion";
			idTable = "idProvincia";
			joDataFilter = joProvinciaInf;
			actualFilter = iFilterInforme.PROVINCIA;
			break;

		case iFilterInforme.LOCALIDAD:
			tableFilter = "aud_localidades";
			fieldsFilter = "descripcion";
			idTable = "idLocalidad";
			joDataFilter = joLocalidadInf;
			actualFilter = iFilterInforme.LOCALIDAD;
			joDataWhereFilter = {fieldFilter : 'idProvincia', dataWhereFilter : joProvinciaInf};
			break;

		case iFilterInforme.FRECUENCIA:
			tableFilter = "aud_frecuenciacontroles";
			fieldsFilter = "descripcion";
			idTable = "idFrecuencia";
			joDataFilter = joFrecuenciaInf;
			actualFilter = iFilterInforme.FRECUENCIA;
			break;
	}

	$('#autoFilterDialog').load(popAutoFilter).dialog('open');
}

function $_selectCampanna(idCampanna)
{

	for(var i = 0; i < joCampannasSelect.length; i++)
	{
		if(joCampannasSelect[i] == idCampanna)
		{
			joCampannasSelect.splice(i,1);
			return;
		}
	}
	joCampannasSelect.push(idCampanna);
}

function $_selectCircuito(idCircuito)
{

	for(var i = 0; i < joCircuitosSelect.length; i++)
	{
		if(joCircuitosSelect[i] == idCircuito)
		{
			joCircuitosSelect.splice(i,1);
			return;
		}
	}
	joCircuitosSelect.push(idCircuito);
}

function $_detalleCircuitoProxyShow(idCampanna)
{
	joCircuitosSelect = [];
	idCampannaSelect = idCampanna;
	$('#auditoriaCircuitoDialog').load(popCircuitoForm).dialog('open');
}

function $_detalleCircuitoDetalleProxyShow(idCircuito)
{
	idCircuitoSelect = idCircuito;
	$('#auditoriaCircuitoDetalleDialog').load(popCircuitoDetalleForm).dialog('open');
}

function $_initCampannasCircuitoPop()
{
	oTableCampannaCircuito = $('#dt_auditoriasCampannasCircuito').dataTable( {
				"bJQueryUI": true,
				"sPaginationType": "two_button",
				"bInfo": false,
				"bAutoWidth": false,
				"bProcessing": true,
				"bServerSide": true,
				"oLanguage": {
					"sUrl": "js/datatables/js/dataTables.es.txt"
				},
				"sAjaxSource": actionForm + "?actionOfForm=searchCampannaCircuito&idCampanna=" + idCampannaSelect,
				"fnDrawCallback": function(oSettings) {
					$('.chkClassCircuito').each(function() {
						var chkValue = $(this);
						$.each(joCircuitosSelect, function(i, value) {
							if(value == chkValue.attr('id')) {
								chkValue.attr('checked', true);
								return false;
							}
						});
					});
				},
				"aoColumns": [
				  			null, //Nombre Campanna
				  			null, //Fecha Inicio
				  			null, //Fecha Fin
				  			null, //Nombre EVP
				  			null, //Elemento
				  			null, //Circuito
				  			null, //Provincia
				  			null, //Localidad
				  			null, //Cant. Pautada
				  			{ "sWidth": "10%", "sClass": "center", "bSortable": false }, //Btn. Ingresar
				  			{ "sWidth": "10%", "sClass": "center", "bSortable": false } //Btn. auditoria
				  		]
		});

	$('#dt_auditoriasCampannasCircuito > thead > tr > th').each(function(key, value) {
		if($(value).hasClass("chkMapping")) {
			$(value).click( function(e) {
			    $.ajax({
			        type: 'POST',
			        url: actionForm,
			        data: {'actionOfForm' : 'marcarCircuitos', 'joCircuitosSelect' : JSON.stringify(joCircuitosSelect), 'idCampanna' : idCampannaSelect},
					dataType: 'json',
			        success: function(jsonObj)
	                            {
	        						if(jsonObj) {
	        							joCircuitosSelect = jsonObj;
	        							$('.chkClassCircuito').each(function() {
	        								var chkValue = $(this);
	        								if(jQuery.inArray(chkValue.attr('id'), joCircuitosSelect) == -1)
	        									chkValue.attr('checked', false);
	        								else
	        									chkValue.attr('checked', true);
	        							});
	        						}
	                            }
			    });

			});
		}
	});

	$('#btnMapaDetalle').click( function(e) {
		if($.isEmptyObject(joCircuitosSelect)) {
			$_showMessage('ALERT', 'ALERTA', 'Seleccione algun Circuito');
		}
		else {
			tipoMapa = 'MC';
			$('#auditoriaMapaDialog').load(popMapaForm + '?tipoFiltro=' + $('#cmbFiltro').val() + '&tipoMapa='+ tipoMapa + '&joCircuitosSelect='+ JSON.stringify(joCircuitosSelect)).dialog('open');
		}
	});

	$('#btnSalirCampCircuito').click( function(e) {
		$('#auditoriaCircuitoDialog').dialog('close');
	});
}

function $_initCampannasCircuitoDetallePop()
{
	oTableCampannaCircuito = $('#dt_auditoriasCampannasCircuitoDetalle').dataTable( {
				"bJQueryUI": true,
				"sPaginationType": "two_button",
				"bInfo": false,
				"bAutoWidth": false,
				"bProcessing": true,
				"bServerSide": true,
				"oLanguage": {
					"sUrl": "js/datatables/js/dataTables.es.txt"
				},
				"sAjaxSource": actionForm + "?actionOfForm=searchCampannaCircuitoDetalle&idCircuito=" + idCircuitoSelect,
				"aoColumns": [
				  			null, //Circuito
				  			null, //Orden
				  			null, //Direccion
				  			null, //Estado BE
				  			null, //Cantidad BE
				  			null, //Estado CD
				  			null, //Cantidad CD
				  			null, //Estado SA
				  			null, //Cantidad SA
				  			{ "sWidth": "10%", "sClass": "center", "bSortable": false } //Btn. Imagenes
				  		]
		});

	$('#btnSalirCampCircuitoDetalle').click( function(e) {
		$('#auditoriaCircuitoDetalleDialog').dialog('close');
	});
}

function $_imagenCircuitoProxyShow(idCircuito, orden) {
	$('#auditoriaFotosDialog').load('auditoriaFotos.php?idCircuito=' + idCircuito + '&orden=' + orden).dialog('open');
}

function $_initAuditoriaMapaPop()
{
	$('.campItm').click( function(e) {
		var fullFileName = $(this).css('background-image');
		var iPos = fullFileName.lastIndexOf('/');
		var nomArchivo = $(this).css('background-image').substring(++iPos, fullFileName.length-1).replace("\"", "");
		if(nomArchivo == 'itemSel.png') {
			$(this).css('background-image', $(this).css('background-image').replace('itemSel.png', 'itemUnsel.png'));
			var idIcon = 'img' + $(this).attr('id');
			$('#mapa').gMapHideMarkets({iconUrl: $('#'+idIcon).css('background-image')});
		}
		else {
			$(this).css('background-image', $(this).css('background-image').replace('itemUnsel.png', 'itemSel.png'));
			var idIcon = 'img' + $(this).attr('id');
			$('#mapa').gMapShowMarkets({iconUrl: $('#'+idIcon).css('background-image')});
		}
	});

	if(tipoMapa == 'MP') {
		paramData = {
				actionOfForm : 'auditoriaCampannaMarketsMap',
				joCampannasSelect: JSON.stringify(joCampannasSelect)
		}
	}

	if(tipoMapa == 'MC') {
		if($('#cmbFiltro').val() == 'SEM')
			paramData = {
				actionOfForm : 'auditoriaCampannaSemaforoMarketsMap',
				joCircuitosSelect: JSON.stringify(joCircuitosSelect)
			}
		else
			paramData = {
				actionOfForm : 'auditoriaCampannaDetalleMarketsMap',
				joCircuitosSelect: JSON.stringify(joCircuitosSelect),
				tipoFiltro : $('#cmbFiltro').val()
			}
	}

    $.ajax({
        type: 'POST',
        url: actionForm,
        data: paramData,
		dataType: 'json',
        success: function(jsonObj)
                            {
					        	$('#mapa').gMap({
					        		scrollwheel: true,
					        		markers: jsonObj.markers,
					        		zoom: 10
					        	});

					        	$.each(jsonObj.imagesMapa, function(i, item) {
					        		$('#img'+item.id).css('background-image','url('+item.imagen+')');
					        	});
                            }
    });

	$('#btnSalirMapa').click( function(e) {
		$('#auditoriaMapaDialog').dialog('close');
	});

	$('#btnAudiencia').click( function(e) {
		$('#auditoriaAudienciaDialog').load(popAudiencia).dialog('open');
	});
}

function $_exportarExcel()
{
	var paramData = 'actionOfForm=exportXLS&' + $("#auditoriaInformePopForm").serialize()
							+ '&jsonDataCampannas=' + encodeURIComponent(JSON.stringify(joCampannasInf))
							+ '&jsonDataElementos=' + encodeURIComponent(JSON.stringify(joElementosInf))
							+ '&jsonDataEVP=' + encodeURIComponent(JSON.stringify(joEVPInf))
							+ '&jsonDataProvincia=' + encodeURIComponent(JSON.stringify(joProvinciaInf))
							+ '&jsonDataLocalidad=' + encodeURIComponent(JSON.stringify(joLocalidadInf))
							+ '&jsonDataFrecuencia=' + encodeURIComponent(JSON.stringify(joFrecuenciaInf));

	if(!$.browser.msie)
		location.href = actionForm + '?' + paramData;
	else
		window.open(actionForm + '?' + paramData);

return true;
}

function $_downloadCircuitoProxyShow(idCampanna)
{
	var paramData = "actionOfForm=getFotosAuditoriaCampannaZip&idCampanna=" + idCampanna;

	if(!$.browser.msie)
		location.href = actionForm + '?' + paramData;
	else
		window.open(actionForm + '?' + paramData);
}

/////////////////////
//Audiencia
////////////////////
function $_initAudienciaPop()
{
	idAudiencia = undefined;
	jsonAudiencia = {
			jaEdades: [],
			jaSexo: [],
			jaMSE: [],
			jaPeriodo: []
		};

	$( "#tabs" ).tabs();

	$("#audienciaPlanDialog").dialog({
		autoOpen:false,
		height:'auto',
		width: 400,
		modal: true,
		resizable: false,
		closeOnEscape: false,
		position : ['center',10],
		dialogClass:'no-close',
		open: function() {
			$('#audienciaPlanDialog').css('background','#F5F5F5');
		}
	});

	$('#btnSalirAudiencia').click( function(e) {
		$('#auditoriaAudienciaDialog').dialog('close');
	});

	$('#btnGuardarAudiencia').click( function(e) {
		if(idAudiencia == undefined) {
			$_showMessage('ALERT', 'ALERTA', "Ejecute una Evaluaci&oacute;n de Audiencia");
			return false;
		}

		$('#audienciaPlanDialog').load(popAudienciaSaveForm).dialog('open');
	});

	$('#btnExcelAudiencia').click( function(e) {
		var jaIDAud = [];
		oTableAudElemento.$('tr').each(function() {
			var aData = oTableAudElemento.fnGetData(this);
			if($.inArray(aData[1], jaIDAud) == -1)
				jaIDAud.push(aData[1])
		});

		if($.isEmptyObject(jaIDAud)) {
			$_showMessage('ALERT', 'ALERTA', "Ejecute/Cargue una Evaluaci&oacute;n de Audiencia");
		}

		var paramData = 'actionOfForm=getAudienciaExcel&idMapProcesos=' + JSON.stringify(jaIDAud) + "&joCircuitos=" + encodeURIComponent(JSON.stringify(joCircuitosSelect));
		window.open(actionForm + '?' + paramData, '_blank');

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
			position: 'center',
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


		var paramData = 'actionOfForm=getDatosAudienciaGuardada&idMapProcesos=' + $('#cmbDescAudiencia').val() + "&joCircuitos=" + encodeURIComponent(JSON.stringify(joCircuitosSelect));

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
				}
				else
					$_showMessage('ERR', jsonObj.status, jsonObj.msg);
	        },
	        error: function(){
	        	$.unblockUI();
	        }
	    });
	});

	$('#btnEvaluarAudiencia').click(function(e){
		e.preventDefault();

		$.blockUI({
	        baseZ: 2000,
	        timeout: 0,
	        message: '<h2>Evaluando Informaci&oacute;n.<br /></h2><img src="images/ajax-loader-7.gif"><h2>Aguarde por favor...</h2>',
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

		var paramData = 'actionOfForm=evaluarAudiencia&joAudiencia=' + JSON.stringify(jsonAudiencia) + "&joCircuitos=" + encodeURIComponent(JSON.stringify(joCircuitosSelect));

		setTimeout(function(){
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
					}
					else
						$_showMessage('ERR', jsonObj.status, jsonObj.msg);
		        },
		        error: function(){
		        	$.unblockUI();
		        }
		    });
		}, 1000);
	});

	var oTableFiltroEdad = $('#dt_filtroEdad').dataTable( {
							"bJQueryUI": true,
							"sPaginationType": "two_button",
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
							"bJQueryUI": true,
							"sPaginationType": "two_button",
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
							"bJQueryUI": true,
							"sPaginationType": "two_button",
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
							"bJQueryUI": true,
							"sPaginationType": "two_button",
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

	//Tablas Audiencia
	oTableAudGeneral = $('#dt_audienciaGeneral').dataTable( {
		"bJQueryUI": true,
		"bAutoWidth": true,
		"bInfo": false,
		"bRetrieve": true,
		"bFilter": false,
		"bPaginate": false,
		"bLengthChange": false,
        "sScrollY" : "200px",
        "bScrollInfinite": true,
        "bScrollCollapse" : false,
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
		"bJQueryUI": true,
		"bAutoWidth": true,
		"bInfo": false,
		"bRetrieve": true,
		"bFilter": false,
		"bPaginate": false,
		"bLengthChange": false,
        "sScrollY" : "200px",
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
		"bJQueryUI": true,
		"bAutoWidth": true,
		"bInfo": false,
		"bRetrieve": true,
		"bFilter": false,
		"bPaginate": false,
        "sScrollY" : "200px",
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
		"bJQueryUI": true,
		"bAutoWidth": true,
		"bInfo": false,
		"bRetrieve": true,
		"bFilter": false,
		"bPaginate": false,
        "sScrollY" : "200px",
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
		"bJQueryUI": true,
		"bAutoWidth": true,
		"bInfo": false,
		"bRetrieve": true,
		"bFilter": false,
		"bPaginate": false,
        "sScrollY" : "200px",
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
	    	$('#cmbDescAudiencia').append($('<option>').text('...').attr('value', 0));
	        $.each(json, function(i, value) {
	        	$('#cmbDescAudiencia').append($('<option>').text(value.descripcion).attr('value', value.idAudAudiencia));
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
				paramData += '&idAudAudiencia=' + idAudiencia;

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
