/*
 * Created by Federico Pisarello - SisDev Software (c) 2012 - fpisarello@sisdevsoft.com
 */

var tableFilter;
var fieldsFilter;
var idTable;
var actionAutoFilter = "autoFilterAction.php";

var joDataFilter;
var joDataRowEnabled;
var joDataWhereFilter;
var joDataFechas;

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

						if(!$.isEmptyObject(joDataRowEnabled)) {
							chkValue.attr('disabled','disabled');
							$.each(joDataRowEnabled, function(i, value) {
								if(value == chkValue.attr('id')) {
									chkValue.removeAttr('disabled');
									chkValue.parent().next('td').addClass('negrita');
									return false;
								}
							});
						}
					});
				},
				"sAjaxSource": actionAutoFilter + "?actionOfForm=search&fieldsOnly=" + fieldsFilter + "&table=" + tableFilter + '&idTable=' + idTable + '&where=' + JSON.stringify(joDataWhereFilter) + '&fecha=' + JSON.stringify(joDataFechas),
				"aaSorting": [[ 1, "asc" ]],
				"aoColumns": [
					  			{"sWidth": "7%", "bSortable": false},
					  			null
					  		 ]
		});
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
