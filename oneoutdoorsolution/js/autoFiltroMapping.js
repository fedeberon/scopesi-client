/*
 * Created by Federico Pisarello - SisDev Software (c) 2012 - fpisarello@sisdevsoft.com
 */

var tableFilter;
var fieldsFilter;
var idTable;
var actionAutoFilter = "autoFilterMappingAction.php";

var joDataFilter;
var joDataRowEnabled;
var joDataWhereFilter;
var joDataFechas;
var bolPrintMarca = false;

function $_initAutoFiltroPop()
{
	$('#btnAutoFiltroSalir').click( function(e) {
		$('#autoFilterDialog').dialog('close');
	});

	if(joDataFilter == undefined)
		joDataFilter = [];

	if(joDataRowEnabled == undefined)
		joDataRowEnabled = [];

	if(joDataWhereFilter == undefined)
		joDataWhereFilter = [];

	if(joDataFechas == undefined)
		joDataFechas = [];

	//Elementos
	if(!$.isEmptyObject(jaDataPolygon[polyConst.ELEMENTOS])) {
		var joDataPolygon = jaDataPolygon[polyConst.ELEMENTOS];
		$('#circleElementos').attr('checked', joDataPolygon.circleElementos);
		$('#mtsRadioCircleElementos').val(joDataPolygon.mtsRadio);
	}

	$('#circleElementos').click(function(){
		var joDataPolygon = jaDataPolygon[polyConst.ELEMENTOS];
		joDataPolygon.circleElementos = $(this).attr('checked');
	});

	$('#mtsRadioCircleElementos').blur(function(){
		var mtsRadioCircleElementos = jaDataPolygon[polyConst.ELEMENTOS];
		joDataPolygon.mtsRadio = $(this).val();
	});

	//EVPs
	if(!$.isEmptyObject(jaDataPolygon[polyConst.EVP])) {
		var joDataPolygon = jaDataPolygon[polyConst.EVP];
		$('#circleEVP').attr('checked', joDataPolygon.circleEVPs);
		$('#mtsRadioCircleEVP').val(joDataPolygon.mtsRadio);
	}

	$('#circleEVP').click(function(){
		var joDataPolygon = jaDataPolygon[polyConst.EVPS];
		joDataPolygon.circleEVPs = $(this).attr('checked');
	});

	$('#mtsRadioCircleEVP').blur(function(){
		var joDataPolygon = jaDataPolygon[polyConst.EVPS];
		joDataPolygon.mtsRadio = $(this).val();
	});

	var oTableFilter = $('#dt_autofiltro').dataTable( {
				"bJQueryUI": true,
				//"pagingType": "simple",
				"sPaginationType": "two_button",
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
				"fnDrawCallback": function(oSettings) {
					$('.filterClass').each(function() {
						var chkValue = $(this);
						$.each(joDataFilter, function(i, value) {
							if(value == chkValue.attr('id')) {
								chkValue.attr('checked', true);
								return false;
							}
						});
					});
				},
				"sAjaxSource": actionAutoFilter + "?actionOfForm=search&fieldsOnly=" + fieldsFilter + "&table=" + tableFilter + '&idTable=' + idTable + '&where=' + JSON.stringify(joDataWhereFilter) + '&fecha=' + JSON.stringify(joDataFechas) + '&joFilterUbicaciones=' + JSON.stringify(joFilterUbicaciones),
				"aaSorting": [[ 1, "asc" ]],
				"aoColumns": [
					  			{"sWidth": "7%", "bSortable": false},
					  			null
					  		 ]
		});

	switch (actualFilter) {
		case iFilterMapping.ELEMENTO:
			$('#polyElementosCircle').show();
			$('#polyEVPCircle').hide();
			break;
		case iFilterMapping.EVP:
			$('#polyElementosCircle').hide();
			$('#polyEVPCircle').show();
			break;
		default:
			$('#polyElementosCircle').hide();
			$('#polyEVPCircle').hide();
			break;
	}

	$("#selectAllHeader").click( function(e) {
		$('.filterClass').each(function() {
			if(bolPrintMarca)
				$(this).attr('checked', false);
			else
				$(this).attr('checked', true);
			$_addRemoveAllFilter($(this).attr('id'));
		});
		if(bolPrintMarca)
			joDataFilter = [];
		bolPrintMarca = !bolPrintMarca;
	});
}

function $_addRemoveAllFilter(valFilter)
{
	var exist = false;

	if(!$.isEmptyObject(joDataFilter)) {
		$.each(joDataFilter, function(i, value) {
			if(value == valFilter) {
				exist = true;
				return false;
			}
		});
	}
	if(!exist)
		joDataFilter.push(valFilter);
}

function $_addRemoveFilter(valFilter)
{
	var exist = false;

	if(!$.isEmptyObject(joDataFilter)) {
		$.each(joDataFilter, function(i, value) {
			if(value == valFilter) {
				joDataFilter.splice(i, 1);
				exist = true;
				return false;
			}
		});
	}
	if(!exist)
		joDataFilter.push(valFilter);
}
