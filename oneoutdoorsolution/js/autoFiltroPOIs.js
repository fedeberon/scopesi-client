/*
 * Created by Federico Pisarello - SisDev Software (c) 2012 - fpisarello@sisdevsoft.com
 */

var tableFilter;
var fieldsFilter;
var idTable;
var actionAutoFilter = "autoFilterMappingAction.php";

var joDataFilter;


var oTableFilter;

function $_initAutoFiltroPOIsPop()
{
	$('#btnAutoFiltroPOIsSalir').click( function(e) {
		oTableFilter = undefined;
		$('#autoFilterDialog').dialog('close');
	});

	if(joDataFilter == undefined)
		joDataFilter = [];

	if(!$.isEmptyObject(jaDataPolygon[polyConst.POIS])) {
		var joDataPolygon = jaDataPolygon[polyConst.POIS];
		$('#circlePois').attr('checked', joDataPolygon.circlePois);
		$('#mtsRadioCircle').val(joDataPolygon.mtsRadio);
		$('#allMarkerPois').attr('checked', joDataPolygon.allMarkers);
	}

	$('#circlePois').click(function(){
		var joDataPolygon = jaDataPolygon[polyConst.POIS];
		//joDataPolygon.circlePois = $(this).attr('checked');
		joDataPolygon.circlePois = $(this).is(':checked');
		//console.log($(this).is(':checked'));
	});

	$('#allMarkerPois').click(function(){
		var joDataPolygon = jaDataPolygon[polyConst.POIS];
		joDataPolygon.allMarkers = $(this).attr('checked');
	});

	$('#mtsRadioCircle').blur(function(){
		var joDataPolygon = jaDataPolygon[polyConst.POIS];
		joDataPolygon.mtsRadio = $(this).val();
	});

	oTableFilter = $('#dt_autofiltro').dataTable( {
				"bJQueryUI": true,
				//"pagingType": "simple",
				"sPaginationType": "two_button",
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
				"fnDrawCallback": function(oSettings) {
					$('.filterClass').each(function() {
						$(this).click( function(e) {
							var parentChecked = $(this).is(':checked');
							$('.details > table > tbody > tr > td').children().each(function(){
								$(this).attr('checked', parentChecked);
								$_addRemoveFilter($(this).attr('id'));
							});
						});
					});
				},
				"sAjaxSource": actionAutoFilter + "?actionOfForm=searchPOIs",
				"aaSorting": [[ 1, "asc" ]],
				"aoColumns": [
					  			{"sWidth": "7%"},
					  			{"bVisible": false},
					  			null,
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
				oTableFilter.fnOpen( nTr, fnFormatDetailsAutoFiltro(nTr), 'details' );
			}
		});
}

function fnFormatDetailsAutoFiltro (nTr){
	var aData = oTableFilter.fnGetData(nTr);
	var sOut = "";
	var paramData =  "actionOfForm=searchPOIsDetalle&idSector=" + aData[1] + "&joEntidadPOIIds=" + joDataFilter + "&joProvincias=" + joFilterUbicaciones.joProvinciaIds;

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
