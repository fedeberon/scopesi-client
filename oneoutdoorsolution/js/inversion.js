/*
 * Created by Federico Pisarello - SisDev Software (c) 2012 - fpisarello@sisdevsoft.com
 */

var actionForm = 'inversionAction.php';
var popForm = 'inversionPop.php';
var popCreatividades = 'creatividadesPop.php';
var popAutoFilter = "autoFilterPop.php";

var oTable;

var joSegmento = undefined;
var joRubro = undefined;
var joSectores = undefined;
var joEVP = undefined;
var joAnunciante = undefined;
var joProducto = undefined;
var joMedio = undefined;

var joRubrosHab = undefined;

var txtMesAnnoDesde;
var txtMesAnnoHasta;

var chkMesAnio = false;
var chkApertura = false;
var chkSector = false;
var chkRubro = false;
var chkSegmento = false;
var chkEVP = false;
var chkAnunciante = false;
var chkProducto = false;
var chkMedio = false;
var chkPeriodo = false;
var chkTipoDisp = false;

var divIdAutofilter = undefined;
var actualFilter;
var emptyTable = true;

var iColTable = new fn_colTable();
function fn_colTable()
{
	this.SECTOR = 0;
	this.RUBRO = 1;
	this.SEGMENTO = 2;
	this.MES = 3;
	this.ANNO = 4;
	this.EVP = 5;
	this.ANUNCIANTE = 6;
	this.PRODUCTO = 7;
	this.PERIODO = 8;
	this.MEDIO = 9;
	this.TIPODISPO = 10;
	this.IMPORTE = 11;
}

function $_init()
{
	$('.btnNav').each(function (i){
		$(this).removeClass($(this).attr('id')+'Act');
		$(this).addClass($(this).attr('id'));
	});
	$('#btnInv').addClass('btnInvAct');
	
	var gaiSelected;
	
	$_getRubrosHab();
	
	$(document).ready(function() {
    	oTable = $('#dt_inversiones').dataTable( {
					"bJQueryUI": true,
					"sPaginationType": "two_button",
					"bInfo": false,
					"bAutoWidth": false,
					"bProcessing": true,
					"bServerSide": true,
					"oLanguage": {
						"sUrl": "js/datatables/js/dataTables.es.txt"
					},
					"sAjaxSource": actionForm + "?actionOfForm=search&emptyTable=" + emptyTable,
					"aoColumns": [
					  			{"bVisible": false, "bSearchable": false }, //Sector
					  			{"bVisible": false, "bSearchable": false }, //Rubro
					  			{"bVisible": false, "bSearchable": false }, //Segmento
					  			{"bVisible": false, "bSearchable": false }, //Mes
					  			{"bVisible": false, "bSearchable": false }, //Anno
					  			{"bVisible": false, "bSearchable": false }, //EVP
					  			{"bVisible": false, "bSearchable": false }, //Anunciante
					  			{"bVisible": false, "bSearchable": false }, //Producto
					  			{"bVisible": false, "bSearchable": false }, //Periodo
					  			{"bVisible": false, "bSearchable": false }, //Medio
					  			{"bVisible": false, "bSearchable": false }, //Tipo Disp.
					  			{
					  				"bSearchable": false,
					            	"sType": "numeric",
					            	"bSortable": false,
					            	"sClass": "textAlignRight",
					            	"fnRender": function (oObj) {    
					            		return RenderDecimalNumber(oObj);
					            	}
					  			} //Sumatoria Importe
					  		]

			});
    	
    	emptyTable = false;
    	
    	$("#btnFiltro").click( function(e) {
    		$('#inversionDialog').load(popForm).dialog('open');
    	});
    	
    	$('#btnExcel').click( function(e) {
    		$_exportarExcel();
    	});
    	
    	$('#btnExcelAdmin').click( function(e) {
    		$_exportarExcelAdmin();
    	});
    	
    	$('#btnCreatividades').click( function(e) {
    		$_verCreatividades();
    	});
    	
    	$('#btnGrabarFiltro').click( function(e) {
    		$_grabarFiltro();
    	});
    	
    	//Cargar Filtro
    	createUploader();
    	
    	$("#inversionDialog").dialog({
    		autoOpen:false, 
    		height:487, 
    		width:407, 
    		dialogClass:'no-close',
    		open: function() {
    			$('#inversionDialog').css('background','#F5F5F5');
    		}
    	});
    	
    	$("#creatividadesDialog").dialog({
    		autoOpen:false, 
    		height:510, 
    		width:760, 
    		dialogClass:'no-close',
    		open: function() {
    			$('#creatividadesDialog').css('background','#F5F5F5');
    		}
    	});
	});
}

function $_getRubrosHab()
{
	var paramData =  "actionOfForm=getRubrosHab";
	
    $.ajax({
        type: 'POST',
        url: actionForm,
        data: paramData,
		dataType: 'json',
        success: function(jsonObj)
                            {
        						joRubrosHab = jsonObj;
                            }
        });
}

function $_verCreatividades()
{
	$('#creatividadesDialog').load(popCreatividades).dialog('open');
}

function $_exportarExcel()
{
	var paramData = 'actionOfForm=exportXLS&' + $("#inversionPopForm").serialize()
							+ '&jsonDataAnunciante=' + encodeURIComponent(JSON.stringify(joAnunciante))
							+ '&jsonDataSectores=' + encodeURIComponent(JSON.stringify(joSectores))
							+ '&jsonDataRubro=' + encodeURIComponent(JSON.stringify(joRubro))
							+ '&jsonDataSegmento=' + encodeURIComponent(JSON.stringify(joSegmento))
							+ '&jsonDataEVP=' + encodeURIComponent(JSON.stringify(joEVP))
							+ '&jsonDataMedio=' + encodeURIComponent(JSON.stringify(joMedio))
							+ '&jsonDataProducto=' + encodeURIComponent(JSON.stringify(joProducto));
	
	if(!$.browser.msie)
		location.href = actionForm + '?' + paramData;
	else
		window.open(actionForm + '?' + paramData);

return true;
}

function $_exportarExcelAdmin()
{
	var paramData = 'actionOfForm=exportXLSAdmin&' + $("#inversionPopForm").serialize()
							+ '&jsonDataAnunciante=' + encodeURIComponent(JSON.stringify(joAnunciante))
							+ '&jsonDataSectores=' + encodeURIComponent(JSON.stringify(joSectores))
							+ '&jsonDataRubro=' + encodeURIComponent(JSON.stringify(joRubro))
							+ '&jsonDataSegmento=' + encodeURIComponent(JSON.stringify(joSegmento))
							+ '&jsonDataEVP=' + encodeURIComponent(JSON.stringify(joEVP))
							+ '&jsonDataMedio=' + encodeURIComponent(JSON.stringify(joMedio))
							+ '&jsonDataProducto=' + encodeURIComponent(JSON.stringify(joProducto));
	
	if(!$.browser.msie)
		location.href = actionForm + '?' + paramData;
	else
		window.open(actionForm + '?' + paramData);

return true;
}
	
function RenderDecimalNumber(oObj) {
	var num = new NumberFormat();    
	num.setInputDecimal('.');    
	num.setNumber(oObj.aData[oObj.iDataColumn]);     
	num.setPlaces(2, true);        
	num.setCurrency(true);     
	num.setNegativeFormat(num.LEFT_DASH);     
	return num.toFormatted();
}

function $_initPop()
{		
	//Mascaras
	$("#txtMesAnnoDesde").mask("99/9999");
	$("#txtMesAnnoHasta").mask("99/9999");
	
	//Estado de Checkbox y Textbox
	$('#chkMesAnno').attr('checked', chkMesAnio);
	$('#chkMesAnnoApertura').attr('checked', chkApertura);
	$('#chkSectores').attr('checked', chkSector);
	$('#chkRubro').attr('checked', chkRubro);
	$('#chkSegmento').attr('checked', chkSegmento);
	$('#chkEVP').attr('checked', chkEVP);
	$('#chkAnunciante').attr('checked', chkAnunciante);
	$('#chkProducto').attr('checked', chkProducto);
	$('#chkMedio').attr('checked', chkMedio);
	$('#chkPeriodo').attr('checked', chkPeriodo);
	$('#chkTipoDispo').attr('checked', chkTipoDisp);
	$('#txtMesAnnoDesde').val(txtMesAnnoDesde);
	$('#txtMesAnnoHasta').val(txtMesAnnoHasta);
	
	//Eventos
	$('#chkMesAnno').click( function(e) {
		chkMesAnio = $('#chkMesAnno').is(':checked');
	});
	$('#chkMesAnnoApertura').click( function(e) {
		chkApertura = $('#chkMesAnnoApertura').is(':checked');
	});
	$('#chkSectores').click( function(e) {
		chkSector = $('#chkSectores').is(':checked');
		if(!$('#chkSectores').is(':checked'))
			joSectores = undefined;
	});
	$('#chkRubro').click( function(e) {
		chkRubro = $('#chkRubro').is(':checked');
		if(!$('#chkRubro').is(':checked'))
			joRubros = undefined;
	});
	$('#chkSegmento').click( function(e) {
		chkSegmento = $('#chkSegmento').is(':checked');
		if(!$('#chkSegmento').is(':checked'))
			joSegmento = undefined;
	});
	$('#chkEVP').click( function(e) {
		chkEVP = $('#chkEVP').is(':checked');
		if(!$('#chkEVP').is(':checked'))
			joEVP = undefined;
	});
	$('#chkAnunciante').click( function(e) {
		chkAnunciante = $('#chkAnunciante').is(':checked');
		if(!$('#chkAnunciante').is(':checked'))
			joAnunciante = undefined;
	});
	$('#chkProducto').click( function(e) {
		chkProducto = $('#chkProducto').is(':checked');
		if(!$('#chkProducto').is(':checked'))
			joProducto = undefined;
	});
	$('#chkMedio').click( function(e) {
		chkMedio = $('#chkMedio').is(':checked');
		if(!$('#chkMedio').is(':checked'))
			joMedio = undefined;
	});
	$('#chkPeriodo').click( function(e) {
		chkPeriodo = $('#chkPeriodo').is(':checked');
	});
	$('#chkTipoDispo').click( function(e) {
		chkTipoDisp = $('#chkTipoDispo').is(':checked');
	});
	
	$('#btnSalir').click( function(e) {
		$('#inversionDialog').dialog('close');
	});
	
	$('#txtMesAnnoDesde').blur( function (e) {
		txtMesAnnoDesde = $('#txtMesAnnoDesde').val();
	});
	
	$('#txtMesAnnoHasta').blur( function (e) {
		txtMesAnnoHasta = $('#txtMesAnnoHasta').val();
	});
	
	$('#chkMesAnno').click( function (e) {
		if($('#chkMesAnno').is(':checked'))
			$('#chkMesAnnoApertura').removeAttr('disabled');
		else
			$('#chkMesAnnoApertura').attr('disabled','disabled');
	});
	
	
	//Eventos de Autofiltro
	$('#btnSectores').click( function(e) {
		$_autofilterProxyShow(iColTable.SECTOR);
	});
	
	$('#btnRubro').click( function(e) {
		$_autofilterProxyShow(iColTable.RUBRO);
	});
	
	$('#btnSegmento').click( function(e) {
		$_autofilterProxyShow(iColTable.SEGMENTO);
	});
	
	$('#btnEVP').click( function(e) {
		$_autofilterProxyShow(iColTable.EVP);
	});
	
	$('#btnAnunciante').click( function(e) {
		$_autofilterProxyShow(iColTable.ANUNCIANTE);
	});
	
	$('#btnProducto').click( function(e) {
		$_autofilterProxyShow(iColTable.PRODUCTO);
	});
	
	$('#btnMedio').click( function(e) {
		$_autofilterProxyShow(iColTable.MEDIO);
	});
	
	$('#chkSectores').click( function(e) {
		if(!$('#chkSectores').is(':checked'))
			joSectores = undefined;
	});
	
	$('#chkRubro').click( function(e) {
		if(!$('#chkRubro').is(':checked'))
			joRubro = undefined;
	});
	
	$('#chkSegmento').click( function(e) {
		if(!$('#chkSegmento').is(':checked'))
			joSegmento = undefined;
	});
	
	$('#chkAnunciante').click( function(e) {
		if(!$('#chkAnunciante').is(':checked'))
			joAnunciante = undefined;
	});
	
	$('#chkEVP').click( function(e) {
		if(!$('#chkEVP').is(':checked'))
			joEVP = undefined;
	});

	$('#chkMedio').click( function(e) {
		if(!$('#chkMedio').is(':checked'))
			joMedio = undefined;
	});
	
	$('#chkProducto').click( function(e) {
		if(!$('#chkProducto').is(':checked'))
			joProducto = undefined;
	});
	
	$('#btnGenerarInforme').unbind('click');
	$('#btnGenerarInforme').click( function(e) {
		var paramData = 'actionOfForm=search&' + $("#inversionPopForm").serialize()
							+ '&jsonDataAnunciante=' + encodeURIComponent(JSON.stringify(joAnunciante))
							+ '&jsonDataSectores=' + encodeURIComponent(JSON.stringify(joSectores))
							+ '&jsonDataRubro=' + encodeURIComponent(JSON.stringify(joRubro))
							+ '&jsonDataSegmento=' + encodeURIComponent(JSON.stringify(joSegmento))
							+ '&jsonDataEVP=' + encodeURIComponent(JSON.stringify(joEVP))
							+ '&jsonDataMedio=' + encodeURIComponent(JSON.stringify(joMedio))
							+ '&jsonDataProducto=' + encodeURIComponent(JSON.stringify(joProducto));
		
		oTable.fnReloadAjax(actionForm + '?' + paramData + '&emptyTable=' + emptyTable, function() {
			for(iCol=0;iCol<oTable.fnSettings().aoColumns.length;iCol++) {
				var bVis = oTable.fnSettings().aoColumns[iCol].bVisible;
				switch (iCol) {
					case iColTable.SECTOR:
						oTable.fnSettings().aoColumns[iCol].bSearchable = $('#chkSectores').is(':checked');
						oTable.fnSetColumnVis(iCol, $('#chkSectores').is(':checked'));
						break;
					
					case iColTable.RUBRO:
						oTable.fnSettings().aoColumns[iCol].bSearchable = $('#chkRubro').is(':checked');
						oTable.fnSetColumnVis(iCol, $('#chkRubro').is(':checked'));
						break;

					case iColTable.SEGMENTO:
						oTable.fnSettings().aoColumns[iCol].bSearchable = $('#chkSegmento').is(':checked');
						oTable.fnSetColumnVis(iCol, $('#chkSegmento').is(':checked'));
						break;

					case iColTable.ANNO:
						oTable.fnSettings().aoColumns[iCol].bSearchable = $('#chkMesAnnoApertura').is(':checked');
						oTable.fnSetColumnVis(iCol, $('#chkMesAnnoApertura').is(':checked'));
						break;

					case iColTable.ANUNCIANTE:
						oTable.fnSettings().aoColumns[iCol].bSearchable = $('#chkAnunciante').is(':checked');
						oTable.fnSetColumnVis(iCol, $('#chkAnunciante').is(':checked'));
						break;

					case iColTable.EVP:
						oTable.fnSettings().aoColumns[iCol].bSearchable = $('#chkEVP').is(':checked');
						oTable.fnSetColumnVis(iCol, $('#chkEVP').is(':checked'));
						break;

					case iColTable.MEDIO:
						oTable.fnSettings().aoColumns[iCol].bSearchable = $('#chkMedio').is(':checked');
						oTable.fnSetColumnVis(iCol, $('#chkMedio').is(':checked'));
						break;

					case iColTable.MES:
						oTable.fnSettings().aoColumns[iCol].bSearchable = $('#chkMesAnnoApertura').is(':checked');
						oTable.fnSetColumnVis(iCol, $('#chkMesAnnoApertura').is(':checked'));
						break;

					case iColTable.PERIODO:
						oTable.fnSettings().aoColumns[iCol].bSearchable = $('#chkPeriodo').is(':checked');
						oTable.fnSetColumnVis(iCol, $('#chkPeriodo').is(':checked'));
						break;

					case iColTable.PRODUCTO:
						oTable.fnSettings().aoColumns[iCol].bSearchable = $('#chkProducto').is(':checked');
						oTable.fnSetColumnVis(iCol, $('#chkProducto').is(':checked'));
						break;

					case iColTable.TIPODISPO:
						oTable.fnSettings().aoColumns[iCol].bSearchable = $('#chkTipoDispo').is(':checked');
						oTable.fnSetColumnVis(iCol, $('#chkTipoDispo').is(':checked'));
						break;
						
					default:
						break;
				}
			}
		});
	});
	
	$("#autoFilterDialog").dialog({
		autoOpen:false, 
		height:540, 
		width:514,
		closeOnEscape: false,
		dialogClass:'no-close',
		beforeClose: function(event, ui) {
			switch (actualFilter) {
				case iColTable.SECTOR:
					joSectores = joDataFilter;
					break;
					
				case iColTable.PRODUCTO:
					joProducto = joDataFilter;
					break;
				
				case iColTable.RUBRO:
					joRubro = joDataFilter;
					break;
					
				case iColTable.SEGMENTO:
					joSegmento = joDataFilter;
					break;
					
				case iColTable.EVP:
					joEVP = joDataFilter;
					break;
					
				case iColTable.ANUNCIANTE:
					joAnunciante = joDataFilter;
					break;
					
				case iColTable.MEDIO:
					joMedio = joDataFilter;
					break;
			}
		},
		open: function() {
			$('#autoFilterDialog').css('background','#F5F5F5');
		}
	});
}

function $_autofilterProxyShow(filter)
{
	joDataRowEnabled = [];
	joDataWhereFilter = [];
	
	//Define filters parameters
	switch (filter) {
		case iColTable.PRODUCTO:
			tableFilter = "productos";
			fieldsFilter = "descripcion";
			idTable = "idProducto";
			joDataFilter = joProducto;
			actualFilter = iColTable.PRODUCTO;
			break;
		
		case iColTable.SECTOR:
			tableFilter = "sectores";
			fieldsFilter = "descripcion";
			idTable = "idSector";
			joDataFilter = joSectores;
			actualFilter = iColTable.SECTOR;
			break;
			
		case iColTable.SEGMENTO:
			tableFilter = "segmentos";
			fieldsFilter = "descripcion";
			idTable = "idSegmento";
			joDataFilter = joSegmento;
			actualFilter = iColTable.SEGMENTO;
			break;
			
		case iColTable.RUBRO:
			tableFilter = "rubros";
			fieldsFilter = "descripcion";
			idTable = "idRubros";
			joDataFilter = joRubro;
			actualFilter = iColTable.RUBRO;
			joDataRowEnabled = joRubrosHab;
			joDataWhereFilter = {fieldFilter : 'idSector', dataWhereFilter : joSectores};
			break;
		
		case iColTable.EVP:
			tableFilter = "empresas";
			fieldsFilter = "descripcion";
			idTable = "idEmpresa";
			joDataFilter = joEVP;
			actualFilter = iColTable.EVP;
			break;
			
		case iColTable.ANUNCIANTE:
			tableFilter = "anunciantes";
			fieldsFilter = "descripcion";
			idTable = "idAnunciante";
			joDataFilter = joAnunciante;
			actualFilter = iColTable.ANUNCIANTE;
			break;

		case iColTable.MEDIO:
			tableFilter = "medios";
			fieldsFilter = "descripcion";
			idTable = "idmedio";
			joDataFilter = joMedio;
			actualFilter = iColTable.MEDIO;
			break;
	}
	
	$('#autoFilterDialog').load(popAutoFilter).dialog('open');
}

function createUploader(){            
    var uploader = new qq.FileUploader({
        element: document.getElementById('btnCargarFiltro'),
        action: 'uploadFiles.php',
        onComplete: function(id, fileName, responseJSON){
        	if(responseJSON.success)
    		{
        		var paramData = 'actionOfForm=cargarFiltro&fileName=' + fileName;
        		
        		$.ajax({
                    type: 'POST',
                    url: actionForm,
                    data: paramData,
        			dataType: 'json',
                    success: function(jsonObj)
                                        {
                                            if(jsonObj.status === 'OK')
                                            {
                                            	var joFilterInversion = eval('(' + jsonObj.joFilterInversion + ')');
                                            	
                                            	joSegmento = joFilterInversion.joFiltroSegmento;
                                            	joRubro = joFilterInversion.joFiltroRubro;
                                            	joSectores = joFilterInversion.joFiltroSectores;
                                            	joEVP = joFilterInversion.joFiltroEVP;
                                            	joAnunciante =  joFilterInversion.joFiltroAnunciante;
                                            	joProducto = joFilterInversion.joFiltroProducto;
                                            	joMedio = joFilterInversion.joFiltroMedio;
                                            	
                                            	chkSector = joFilterInversion.joCheckBox.chkSector;
                                            	chkRubro = joFilterInversion.joCheckBox.chkRubro;
                                            	chkSegmento = joFilterInversion.joCheckBox.chkSegmento;
                                            	chkEVP = joFilterInversion.joCheckBox.chkEVP;
                                            	chkAnunciante = joFilterInversion.joCheckBox.chkAnunciante;
                                            	chkProducto = joFilterInversion.joCheckBox.chkProducto;
                                            	chkMedio = joFilterInversion.joCheckBox.chkMedio;
                                            	chkPeriodo = joFilterInversion.joCheckBox.chkPeriodo;
                                            	chkTipoDisp = joFilterInversion.joCheckBox.chkTipoDisp;
                                            	                                            	
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

function $_grabarFiltro()
{	
	var joFilterInversion = {
								joFiltroSegmento: joSegmento,
								joFiltroRubro: joRubro,
								joFiltroSectores: joSectores,
								joFiltroEVP: joEVP,
								joFiltroAnunciante: joAnunciante,
								joFiltroProducto: joProducto,
								joFiltroMedio: joMedio,
								joCheckBox: {'chkSector': chkSector, 'chkRubro' : chkRubro, 'chkSegmento' : chkSegmento, 'chkEVP' : chkEVP, 'chkAnunciante' : chkAnunciante, 'chkProducto' : chkProducto, 'chkMedio' : chkMedio, 'chkPeriodo' : chkPeriodo,  'chkTipoDisp' : chkTipoDisp}
							};
	
	var paramData = 'actionOfForm=grabarFiltro&joFilterInversion=' + encodeURIComponent(JSON.stringify(joFilterInversion))

	if(!$.browser.msie)
		location.href = actionForm + '?' + paramData;
	else
		window.open(actionForm + '?' + paramData);

	return true;
}