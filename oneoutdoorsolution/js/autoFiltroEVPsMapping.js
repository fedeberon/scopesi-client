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

var oTableFilter

function $_initAutoFiltroEVPsPop()
{
	$('#btnAutoFiltroEVPsSalir').click( function(e) {
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

	oTableFilter = $('#dt_autofiltro').dataTable( {
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
				"aaSorting": [[ 4, "asc" ]],
				"sAjaxSource": actionAutoFilter + "?actionOfForm=searchEVPs&fieldsOnly=" + fieldsFilter + "&table=" + tableFilter + '&idTable=' + idTable + '&where=' + JSON.stringify(joDataWhereFilter) + '&fecha=' + JSON.stringify(joDataFechas) + '&joFilterUbicaciones=' + JSON.stringify(joFilterUbicaciones),
				"aoColumns": [
					  			{"sWidth": "7%", "bSortable": false},
					  			{"bVisible": false},
					  			null,
					  			{ "sClass": "center", "bSortable": false },
					  			{"bVisible": false},
					  			{ "sClass": "center", "bSortable": false }
					  		 ]
	});

	$(document).off("click");
	$('#dt_autofiltro').on('click', 'tbody td img', function () {
		var nTr = $(this).parents('tr')[0];
		if (oTableFilter.fnIsOpen(nTr) )
		{
			/* This row is already open - close it */
			this.src = "images/details_open.png";
			oTableFilter.fnClose( nTr );
		}
		else
		{
			/* Open this row */
			this.src = "images/details_close.png";
			oTableFilter.fnOpen( nTr, fnFormatDetailsEVPsAutoFiltro(nTr), 'details' );
		}
	});

	switch (actualFilter) {
		case iFilterMapping.EVP:
			$('#polyElementosCircle').hide();
			$('#polyEVPCircle').show();
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

function fnFormatDetailsEVPsAutoFiltro (nTr){
	var aData = oTableFilter.fnGetData(nTr);
	var sOut = "";
	var paramData =  "actionOfForm=searchEVPsDetalle&idEmpresa=" + aData[1];

    $.ajax({
        type: 'POST',
        url: actionAutoFilter,
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
