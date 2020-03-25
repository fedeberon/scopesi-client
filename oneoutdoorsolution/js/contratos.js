/*
 * Created by Federico Pisarello - SisDev Software (c) 2012 - fpisarello@sisdevsoft.com
 */

var popForm = 'contratosPop.php';
var popInversionForm = 'contratosInversionPop.php';
var popAuditoriaForm = 'contratosAuditoriaPop.php';
var popMappingForm = 'contratosMappingPop.php';
var popCopiarForm = 'contratosCopiarPop.php';
var actionForm = 'contratosAction.php';

var joDataContratos;
var joDataContratosItems = [];
var joDataContratosAudItems = [];
var oTable;
var oTableInversion;
var oTableAuditoria;
var oTableMapping;

/** Variable of tracking the IndexPK **/
var actionPK = undefined;
var actionInversionPK = undefined;
var actionAuditoriaPK = undefined;
var actionMappingPK = undefined;
var action = undefined;

function $_init()
{
	$('.btnNav').each(function (i){
		$(this).removeClass($(this).attr('id')+'Act');
		$(this).addClass($(this).attr('id'));
	});
	$('#btnCont').addClass('btnContAct');

	var gaiSelected;

	$(document).ready(function() {
    	oTable = $('#dt_contratos').dataTable( {
					"bJQueryUI": true,
					"sPaginationType": "two_button",
					"bInfo": false,
					"bLengthChange": false,
					"bAutoWidth": false,
					"bProcessing": true,
					"bServerSide": true,
					"oLanguage": {
						"sUrl": "js/datatables/js/dataTables.es.txt"
					},
					"sAjaxSource": actionForm + "?actionOfForm=search",
					"fnRowCallback": function( nRow, aData, iDisplayIndex ) {
						if ( jQuery.inArray(aData[0], gaiSelected) != -1 )
						{
					        $(nRow).addClass('row_selected');
						}
						return nRow;
					},
					"aoColumns": [
					  			{ "sWidth": "7%"},
					  			{ "sWidth": "60%"},
					  			{ "sWidth": "13%"},
					  			{ "sWidth": "10%", "sClass": "center", "bSortable": false }
					  		]

				});

    	/* Click event handler */
    	// $('#dt_contratos tbody tr').live('click', function () {
			$('#dt_contratos').on('click', 'tbody tr', function () {
    		var aData = oTable.fnGetData(this);
    		var iId = aData[0];
    		actionPK = aData[0];

    		gaiSelected =  [];
    		if ( $(this).hasClass('row_selected') ) {
	            $(this).removeClass('row_selected');
	            actionPK = undefined;
	        }
	        else {
	            oTable.$('tr.row_selected').removeClass('row_selected');
	            $(this).addClass('row_selected');
	            gaiSelected[gaiSelected.length++] = iId;
	        }
    	} );

	});

	$("#contratosDialog").dialog({
		autoOpen:false,
		height:450,
		width:510,
		closeOnEscape: false,
		position : {
			my: 'center',
			at: 'center',
			of: window
		},
		dialogClass:'no-close',
		beforeClose: function() { $_ClearContratosPopForm(); },
		open: function() {
			$('#contratosDialog').css('background','#F5F5F5');
		}
	});

	$("#contratosCopiarDialog").dialog({
		autoOpen:false,
		height:450,
		width:330,
		closeOnEscape: false,
		position : {
			my: 'center',
			at: 'center',
			of: window
		},
		dialogClass:'no-close',
		open: function() {
			$('#contratosCopiarDialog').css('background','#F5F5F5');
		}
	});

	//Inversion Pop
	$("#contratosInversionDialog").dialog({
		autoOpen:false,
		height:520,
		width:728,
		resizable: false,
		closeOnEscape: false,
		position : {
			my: 'center',
			at: 'center',
			of: window
		},
		dialogClass:'no-close',
		open: function() {
			$('#contratosInversionDialog').css('background','#F5F5F5');
		}
	});

	//Auditoria Pop
	$("#contratosAuditoriaDialog").dialog({
		autoOpen:false,
		height:520,
		width:728,
		resizable: false,
		closeOnEscape: false,
		position : {
			my: 'center',
			at: 'center',
			of: window
		},
		dialogClass:'no-close',
		open: function() {
			$('#contratosAuditoriaDialog').css('background','#F5F5F5');
		}
	});

	//Mapping Pop
	$("#contratosMappingDialog").dialog({
		autoOpen:false,
		height:520,
		width:728,
		resizable: false,
		closeOnEscape: false,
		position : {
			my: 'center',
			at: 'center',
			of: window
		},
		dialogClass:'no-close',
		open: function() {
			$('#contratosMappingDialog').css('background','#F5F5F5');
		}
	});

	//Add Click for All Buttons in Search Form
	$("#btnAgregar").click( function(e) {
		addProxyShow();
    });

	$('#btnEditar').click( function(e) {
		editProxyShow();
	});

	$('#btnEliminar').click( function(e) {
		delProxyShow();
	});

	$('#btnCopiar').click( function(e) {
		copiarProxyShow();
	});
}

function $_detalleProxyShow(idContrato)
{
	$.ajax({
        type: 'POST',
        url: actionForm,
        data: {actionOfForm: 'getTipoContrato', idContrato: idContrato},
		dataType: 'json',
        success: function(jsonObj)
                            {
                                if(jsonObj.tipo === 'I'){
                                	actionInversionPK = idContrato;
                                	$_inversionesProxyShow(idContrato);
                    			}
                    			else if(jsonObj.tipo === 'A'){
                    				actionAuditoriaPK = idContrato;
                    				$_auditoriasProxyShow(idContrato);
                    			}
                    			else if(jsonObj.tipo === 'M'){
                    				actionMappingPK = idContrato;
                    				$_mappingProxyShow(idContrato);
                    			}
                            }
        });
}

function $_initPop()
{
	//Add Click for All Buttons in Pop Form
	$('#btnSalir').click( function(e) {
		$('#contratosDialog').dialog('close');
	});

	$('#btnGuardar').click( function(e) {
		$('#contratosPopForm').submit();
	});

	$_ValidatorSetDefaults();
	$_initPopValidator();

	//Populate User Data
	if(action == iConst.EDIT)
	{
		$('#descripcion').val(joDataContratos.descripcion);
		$('#cmbTipo').val(joDataContratos.tipoContrato);
		$('#observacion').val(joDataContratos.observacion);
	}
}

function $_initCopiarPop()
{

	$('#btnSalirCopiar').click( function(e) {
		$('#contratosCopiarDialog').dialog('close');
	});

	$('#btnGuardarCopiar').click( function(e) {
		$.ajax({
	        type: 'POST',
	        url: actionForm,
	        data: 'actionOfForm=copiarContrato&idContratoDesde=' + $('#cmbContratos').val() + '&idContrato=' + actionPK,
			dataType: 'json',
	        success: function(jsonObj)
	                            {
	                                if(jsonObj.status === 'OK'){
	                                	$_showMessage('OK', jsonObj.status, jsonObj.msg);
	                    				$('#contratosCopiarDialog').dialog('close');
	                    			}
	                    			else
	                    				$_showMessage('ERR', jsonObj.status, jsonObj.msg);
	                            }
	        });
	});

	//Contratos
	$.ajax({
	    url: actionForm,
	    type:'POST',
	    data: 'actionOfForm=getContratos&idContrato=' + actionPK,
	    dataType: 'json',
	    success: function(json) {
	    	$('#cmbContratos').append($('<option>').text('...').attr('value', 0));
	        $.each(json, function(i, value) {
	        	$('#cmbContratos').append($('<option>').text(value.descripcion).attr('value', value.idContrato));
	        });
	    }
	});
}

function $_initPopMapping()
{
	$(document).ready(function() {
		$('#btnSalirMapping').click( function(e) {
			$('#contratosMappingDialog').dialog('close');
		});

		$('#btnGuardarMapping').click( function(e) {

			joDataContratosItems = [];
			$.each(oTableMapping.fnGetNodes(), function(index, value) {
				if($(".chkClass", value).is(':checked')) {
					var chkValue = $(".chkClass", value).val();

					var joContratoItem = {idEVP : chkValue};
					joDataContratosItems.push(joContratoItem);
				}
			});

			var paramData =  "actionOfForm=saveContratosItemsMapping&idContrato=" + actionMappingPK + "&joDataContratosItems=" +  encodeURIComponent(JSON.stringify(joDataContratosItems));

			$.ajax({
		        type: 'POST',
		        url: actionForm,
		        data: paramData,
				dataType: 'json',
		        success: function(jsonObj)
		                            {
		                                if(jsonObj.status === 'OK'){
		                                	$_showMessage('OK', jsonObj.status, jsonObj.msg);
		                    				$('#contratosMappingDialog').dialog('close');
		                    			}
		                    			else
		                    				$_showMessage('ERR', jsonObj.status, jsonObj.msg);
		                            }
		        });
		});

		oTableMapping = $('#dt_contratosMapping').dataTable( {
					"bJQueryUI": true,
					"sPaginationType": "two_button",
					"bInfo": false,
					"bLengthChange": false,
					"bProcessing": true,
					"bServerSide": true,
					"bFilter": false,
					"sScrollY": "300px",
					"bScrollInfinite": true,
					"bScrollCollapse": true,
					"bPaginate": false,
					"oLanguage": {
						"sUrl": "js/datatables/js/dataTables.es.txt"
					},
					"iDisplayLength": -1,
					"fnDrawCallback": function(oSettings) {

						$.each(oTableMapping.fnGetNodes(), function(index, value) {
							var chkValue = $(".chkClass", value).val();
							$.each(joDataContratosMapItems, function(i, v) {
							    if (v.idEVP == chkValue)
							    	$(".chkClass", value).attr('checked', true);
							});
						});

						$(".chkClass").click( function(e) {
							var chkSelectec = $(this)
							$.each(joDataContratosMapItems, function(i, v) {
							    if (v.idEVP == chkSelectec.val()) {
							    	if(!$(this).is(':checked')) {
							    		joDataContratosMapItems.splice(i, 1);
							    		return false;
							    	}
							    }
							    else if(v.idEVP > chkSelectec)
							    	return false;
							});
						});
					},
					"sAjaxSource": actionForm + "?actionOfForm=searchMapping&idContrato=" + actionMappingPK,
					"aoColumns": [
					  			{ "sWidth": "7%", "sClass": "center", "bSortable": false },
					  			{ "bVisible": false},
					  			null
					  		]

				});
	});
}

function $_initPopAuditoria()
{
	$(document).ready(function() {
		$('#btnSalirAuditoria').click( function(e) {
			$('#contratosAuditoriaDialog').dialog('close');
		});

		$('#btnGuardarAuditoria').click( function(e) {

			joDataContratosItems = [];
			$.each(oTableAuditoria.fnGetNodes(), function(index, value) {
				if($(".chkClass", value).is(':checked')) {
					var chkValue = $(".chkClass", value).val();

					var joContratoItem = {idCampanna : chkValue};
					joDataContratosItems.push(joContratoItem);
				}
			});

			var paramData =  "actionOfForm=saveContratosItemsAuditoria&idContrato=" + actionAuditoriaPK + "&joDataContratosItems=" +  encodeURIComponent(JSON.stringify(joDataContratosItems));

			$.ajax({
		        type: 'POST',
		        url: actionForm,
		        data: paramData,
				dataType: 'json',
		        success: function(jsonObj)
		                            {
		                                if(jsonObj.status === 'OK'){
		                                	$_showMessage('OK', jsonObj.status, jsonObj.msg);
		                    				$('#contratosAuditoriaDialog').dialog('close');
		                    			}
		                    			else
		                    				$_showMessage('ERR', jsonObj.status, jsonObj.msg);
		                            }
		        });
		});

		oTableAuditoria = $('#dt_contratosAuditoria').dataTable( {
					"bJQueryUI": true,
					"sPaginationType": "two_button",
					"bInfo": false,
					"bLengthChange": false,
					"bProcessing": true,
					"bServerSide": true,
					"bFilter": false,
					"sScrollY": "300px",
					"bScrollInfinite": true,
					"bScrollCollapse": true,
					"bPaginate": false,
					"iDisplayLength": -1,
					"oLanguage": {
						"sUrl": "js/datatables/js/dataTables.es.txt"
					},
					"fnDrawCallback": function(oSettings) {

						$.each(oTableAuditoria.fnGetNodes(), function(index, value) {
							var chkValue = $(".chkClass", value).val();
							$.each(joDataContratosAudItems, function(i, v) {
							    if (v.idCampanna == chkValue)
							    	$(".chkClass", value).attr('checked', true);
							});
						});

						$(".chkClass").click( function(e) {
							var chkSelectec = $(this)
							$.each(joDataContratosAudItems, function(i, v) {
							    if (v.idCampanna == chkSelectec.val()) {
							    	if(!$(this).is(':checked')) {
							    		joDataContratosAudItems.splice(i, 1);
							    		return false;
							    	}
							    }
							    else if(v.idCampanna > chkSelectec)
							    	return false;
							});
						});
					},
					"sAjaxSource": actionForm + "?actionOfForm=searchAuditoria&idContrato=" + actionAuditoriaPK,
					"aoColumns": [
					  			{ "sWidth": "7%", "sClass": "center", "bSortable": false },
					  			{ "bVisible": false},
					  			null,
					  			null
					  		]

				});
	});
}

function $_initPopInversion()
{
	$(document).ready(function() {
		$('#btnSalirInversion').click( function(e) {
			$('#contratosInversionDialog').dialog('close');
		});

		$('#btnGuardarInversion').click( function(e) {

			joDataContratosItems = [];
			$.each(oTableInversion.fnGetNodes(), function(index, value) {
				if($(".chkClass", value).is(':checked')) {
					var chkValue = $(".chkClass", value).val();
					var fefDesde = $(".gridInputDateFrom", value).val();
					var fefHasta = $(".gridInputDateTo", value).val();
					var chkCreatividades = $(".chkCreatividades", value).is(':checked');

					var joContratoItem = {
							idRubro : chkValue,
							fechaDesde : fefDesde,
							fechaHasta : fefHasta,
							creatividades : chkCreatividades
					};
					joDataContratosItems.push(joContratoItem);
				}
			});

			var paramData =  "actionOfForm=saveContratosItemsInversion&idContrato=" + actionInversionPK + "&joDataContratosItems=" +  encodeURIComponent(JSON.stringify(joDataContratosItems));

			$.ajax({
		        type: 'POST',
		        url: actionForm,
		        data: paramData,
				dataType: 'json',
		        success: function(jsonObj)
		                            {
		                                if(jsonObj.status === 'OK'){
		                                	$_showMessage('OK', jsonObj.status, jsonObj.msg);
		                    				$('#contratosInversionDialog').dialog('close');
		                    			}
		                    			else
		                    				$_showMessage('ERR', jsonObj.status, jsonObj.msg);
		                            }
		        });
		});

		oTableInversion = $('#dt_contratosInversion').dataTable( {
					"bJQueryUI": true,
					"sPaginationType": "two_button",
					"bInfo": false,
					"bLengthChange": false,
					"bProcessing": true,
					"bServerSide": true,
					"bFilter": false,
					"sScrollY": "300px",
					"bScrollInfinite": true,
					"bScrollCollapse": true,
					"oLanguage": {
						"sUrl": "js/datatables/js/dataTables.es.txt"
					},
					"bPaginate": false,
					"iDisplayLength": -1,
					"fnDrawCallback": function(oSettings) {

						$.each(oTableInversion.fnGetNodes(), function(index, value) {
							var chkValue = $(".chkClass", value).val();
							$.each(joDataContratosItems, function(i, v) {
							    if (v.idRubro == chkValue) {
							    	$(".chkClass", value).attr('checked', true);
							    	$(".gridInputDateFrom", value).val(v.fechaDesde);
							    	$(".gridInputDateTo", value).val(v.fechaHasta);
							    	$(".chkCreatividades", value).attr('checked', v.creatividades);
							        return;
							    }
							    else if(v.idRubro > chkValue)
							    	return;
							});
						});

						$(".chkClass").click( function(e) {
							var chkSelectec = $(this)
							$.each(joDataContratosItems, function(i, v) {
							    if (v.idRubro == chkSelectec.val()) {
							    	if(!$(this).is(':checked')) {
							    		joDataContratosItems.splice(i, 1);
							    	}
							        return false;
							    }
							    else if(v.idRubro > chkSelectec.val())
							    	return false;
							});
						});

						$(".gridInputDateFrom").blur( function (e) {
							var idRubroSel = $(this).parent().parent().children().find('input#idRubro.chkClass').val();
							var txtFechaDesde = $(this)
							$.each(joDataContratosItems, function(i, v) {
							    if (v.idRubro == idRubroSel) {
							    	v.fechaDesde = txtFechaDesde.val();
							        return;
							    }
							    else if(v.idRubro > idRubroSel)
							    	return;
							});
						});

						$(".gridInputDateTo").blur( function (e) {
							var idRubroSel = $(this).parent().parent().children().find('input#idRubro.chkClass').val();
							var txtFechaHasta = $(this)
							$.each(joDataContratosItems, function(i, v) {
							    if (v.idRubro == idRubroSel) {
							    	v.fechaHasta = txtFechaHasta.val();
							        return;
							    }
							    else if(v.idRubro > idRubroSel)
							    	return;
							});
						});

						$(".chkCreatividades").blur( function (e) {
							var idRubroSel = $(this).parent().parent().children().find('input#idRubro.chkClass').val();
							var chkCreatividades = $(this)
							$.each(joDataContratosItems, function(i, v) {
							    if (v.idRubro == idRubroSel) {
							    	v.creatividades = chkCreatividades.is(':checked');
							        return;
							    }
							    else if(v.idRubro > idRubroSel)
							    	return;
							});
						});

					},
					"sAjaxSource": actionForm + "?actionOfForm=searchInversion&idContrato=" + actionInversionPK,
					"aoColumns": [
					  			{ "sWidth": "7%", "sClass": "center", "bSortable": false },
					  			{ "bVisible": false},
					  			null,
					  			{ "sWidth": "20%", "sClass": "center gridInput", "bSortable": false },
					  			{ "sWidth": "20%", "sClass": "center gridInput", "bSortable": false },
					  			{ "sWidth": "7%", "sClass": "center", "bSortable": false }
					  		]

				});

    	// $('#dt_contratosInversion tbody td input').live('focus', function (e){
			$('#dt_contratosInversion').on('focus', 'tbody td input', function (e){
    		$(this).select();
    	});

			// $('#dt_contratosInversion tbody td.gridInput input').live('focus', function (e){
    	$('#dt_contratosInversion').on('focus', 'tbody td.gridInput input', function (e){
    		$(this).unmask();
    		$(this).attr('maxlength', 7);
    		$(this).mask('99/9999');
    	});

			// $('#dt_contratosInversion tbody td input').live('blur', function (e){
    	$('#dt_contratosInversion').on('blur', 'tbody td input', function (e){
    		$(this).unmask();
    		if($(this).val().indexOf('_')!=-1)
    			$(this).val('');
    		return false;
    	});
	});
}

function copiarProxyShow()
{
	if(actionPK === undefined)
	{
		$_showMessage('ALERT', 'ALERTA', strMenssageSelectionInGrid);
		return;
	}

	$('#contratosCopiarDialog').load(popCopiarForm).dialog('open');

}

function addProxyShow()
{
	//Reset State of action and IndexPK
	actionPK = undefined;
	action = iConst.NEW;
	oTable.$('tr.row_selected').removeClass('row_selected');
	$('#contratosDialog').load(popForm).dialog('open');
}

function editProxyShow()
{
	if(actionPK === undefined)
	{
		$_showMessage('ALERT', 'ALERTA', strMenssageSelectionInGrid);
		return;
	}

	action = iConst.EDIT;
	var paramData =  "actionOfForm=" + iConst.EDIT + "&idContrato=" + actionPK;

    $.ajax({
        type: 'POST',
        url: actionForm,
        data: paramData,
		dataType: 'json',
        success: function(jsonObj)
                            {
        						joDataContratos = jsonObj;
        						//Open Pop Dialog
        						$('#contratosDialog').load(popForm).dialog('open');
                            }
        });

return true;
}

function $_inversionesProxyShow(idContrato)
{
	actionInversionPK = idContrato;
	var paramData = 'actionOfForm=getContratoInversion&idContrato=' + actionInversionPK;

    $.ajax({
        type: 'POST',
        url: actionForm,
        data: paramData,
		dataType: 'json',
        success: function(jsonObj){
        	if(!$.isEmptyObject(jsonObj))
        		joDataContratosItems = jsonObj;
        	else
        		joDataContratosItems = [];
        	$('#contratosInversionDialog').load(popInversionForm).dialog('open');
        }
    })
}

function $_auditoriasProxyShow(idContrato)
{
	actionAuditoriaPK = idContrato;
	var paramData = 'actionOfForm=getContratoAuditoria&idContrato=' + actionAuditoriaPK;

    $.ajax({
        type: 'POST',
        url: actionForm,
        data: paramData,
		dataType: 'json',
        success: function(jsonObj){
        	if(!$.isEmptyObject(jsonObj))
        		joDataContratosAudItems = jsonObj;
        	else
        		joDataContratosAudItems = [];
        	$('#contratosAuditoriaDialog').load(popAuditoriaForm).dialog('open');
        }
    })
}

function $_mappingProxyShow(idContrato)
{
	actionAuditoriaPK = idContrato;
	var paramData = 'actionOfForm=getContratoMapping&idContrato=' + actionMappingPK;

    $.ajax({
        type: 'POST',
        url: actionForm,
        data: paramData,
		dataType: 'json',
        success: function(jsonObj){
        	if(!$.isEmptyObject(jsonObj))
        		joDataContratosMapItems = jsonObj;
        	else
        		joDataContratosMapItems = [];
        	$('#contratosMappingDialog').load(popMappingForm).dialog('open');
        }
    })
}


function $_saveContrato(action)
{
	var paramData = 'actionOfForm=addOrEdit&' + $("#contratosPopForm").serialize();

	if(action == iConst.EDIT)
		paramData += '&idContrato=' + actionPK;

    $.ajax({
        type: 'POST',
        url: actionForm,
        data: paramData,
		dataType: 'json',
        success: function(jsonObj){
            if(jsonObj.status === 'OK'){
				$_showMessage('OK', jsonObj.status, jsonObj.msg);
				$('#contratosDialog').dialog('close');
				oTable.fnReloadAjax();
			}
			else
				$_showMessage('ERR', jsonObj.status, jsonObj.msg);
        }
    });

return true;
}

function delProxyShow()
{
	if(actionPK === undefined)
	{
		$_showMessage('ALERT', 'ALERTA', strMenssageSelectionInGrid);
		return;
	}

	action = iConst.DELETE;

	$("#deleteContratosDialog").dialog({
		autoOpen: false,
		width:350,
		position: 'center',
		open: function () {
			$('#deleteContratosDialog').css('background','url(imagenes/fondo_texto.jpg)');
			$('#btnConfirmDelete').click( function(e) {
				$_del();
			});
			$('#btnExitDelete').click( function(e) {
				$('#deleteContratosDialog').dialog('close');
			})
		}
	});
	$('#deleteContratosDialog').dialog('open');
}

function $_del()
{
	var paramData = 'actionOfForm=' + action + '&idContrato=' + actionPK;

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
										actionPK = undefined;
										action = iConst.NEW;
										oTable.fnReloadAjax();
									}
									else
										$_showMessage('ERR', 'ERROR', jsonObj.msg);
                                    $('#deleteContratosDialog').dialog('close');
                                }
            });

return true;
}

function $_initPopValidator()
{
	$('#contratosPopForm').validate({
		submitHandler: function() {
			$_saveContrato(action);
		},
		rules: {
			descripcion: {required: true,minlength: 1}
		},
		messages: {
			descripcion: {required: '',minlength: ''}
		}
	});
}

function $_ClearContratosPopForm()
{
	$("#contratosDialog").empty();
}
