/*
 * Created by Federico Pisarello - SisDev Software (c) 2012 - fpisarello@sisdevsoft.com
 */

var popForm = 'usuariosPop.php';
var actionForm = 'usuariosAction.php';

var joDataUser;
var oTable;

/** Variable of tracking the IndexPK **/
var actionPK = undefined;
var action = undefined;

var idTipoUsuario;
var idAnunciante;
var idProducto;
var idContratoInv;
var idContratoAud;
var idContratoMap;

function $_init()
{
	$('.btnNav').each(function (i){
		$(this).removeClass($(this).attr('id')+'Act');
		$(this).addClass($(this).attr('id'));
	});
	$('#btnUsu').addClass('btnUsuAct');

	var gaiSelected;

	$(document).ready(function() {
    	oTable = $('#dt_usuarios').dataTable( {
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
					  			null,
					  			null,
					  			null,
					  			null,
					  			null,
					  			null,
					  			null
					  		]

				});

    	/* Click event handler */
    	// $('#dt_usuarios tbody tr').live('click', function () {
			$('#dt_usuarios').on('click', 'tbody tr', function (event) {
    		var aData = oTable.fnGetData(this);
				console.log($(this));
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

	$("#usuariosDialog").dialog({
		autoOpen:false,
		height:'auto',
		width:510,
		modal: true,
		resizable: false,
		closeOnEscape: false,
		position : ['center',10],
		dialogClass:'no-close',
		beforeClose: function() { $_ClearUsuariosPopForm(); },
		open: function() {
			$('#usuariosDialog').css('background','#F5F5F5');
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
}

function $_initPop()
{
	//Add Click for All Buttons in Pop Form
	$('#btnSalir').click( function(e) {
		$('#usuariosDialog').dialog('close');
	});

	$('#btnGuardar').click( function(e) {
		$('#usuariosPopForm').submit();
	});

	var idTipoUsuario;
	var idCuenta;
	var idAnunciante;

	$_ValidatorSetDefaults();
	$_initPopValidator();
	$_agregaSistemaModulos();

	//Populate User Data
	if(action == iConst.EDIT)
	{
		$('#usuario').val(joDataUser.usuario);
		$('#password').val(joDataUser.password);
		$('#nombreCompleto').val(joDataUser.nombreCompleto);
		$('#eMail').val(joDataUser.eMail);
		$('#telefono').val(joDataUser.telefono);
		$('#cargo').val(joDataUser.cargo);
		idTipoUsuario = joDataUser.idTipoUsuario;
		idAnunciante = joDataUser.idAnunciante;
		idProducto = joDataUser.idProducto;
		idContratoInv = joDataUser.idContratoInv;
		idContratoAud = joDataUser.idContratoAud;
		idContratoMap = joDataUser.idContratoMap;
	}

	//Populate Combos

	//Tipos de Usuario
	$.ajax({
	    url: actionForm,
	    type:'POST',
	    data: 'actionOfForm=getTiposUsuario',
	    dataType: 'json',
	    success: function(json) {
	        $.each(json, function(i, value) {
	        	if(value.idTipoUsuario == idTipoUsuario)
	        		$('#cmbTipoUsuario').append($('<option selected>').text(value.descripcion).attr('value', value.idTipoUsuario));
	        	else
	        		$('#cmbTipoUsuario').append($('<option>').text(value.descripcion).attr('value', value.idTipoUsuario));
	        });
	    }
	});

	//Cuentas
	$.ajax({
	    url: actionForm,
	    type:'POST',
	    data: 'actionOfForm=getCuentas',
	    dataType: 'json',
	    success: function(json) {
	    	$('#cmbCuenta').append($('<option>').text('...').attr('value', 0));
	        $.each(json, function(i, value) {
	        	if(value.idAnunciante == idAnunciante)
	        		$('#cmbCuenta').append($('<option selected>').text(value.descripcion).attr('value', value.idAnunciante));
	        	else
	        		$('#cmbCuenta').append($('<option>').text(value.descripcion).attr('value', value.idAnunciante));
	        });
	    }
	});

	//Productos
	$.ajax({
	    url: actionForm,
	    type:'POST',
	    data: 'actionOfForm=getProductos',
	    dataType: 'json',
	    success: function(json) {
	    	$('#cmbProducto').append($('<option>').text('...').attr('value', 0));
	        $.each(json, function(i, value) {
	        	if(value.idProducto == idProducto)
	        		$('#cmbProducto').append($('<option selected>').text(value.descripcion).attr('value', value.idProducto));
	        	else
	        		$('#cmbProducto').append($('<option>').text(value.descripcion).attr('value', value.idProducto));
	        });
	    }
	});

	//Contratos
	$.ajax({
	    url: actionForm,
	    type:'POST',
	    data: 'actionOfForm=getContratos&tipoContrato=I',
	    dataType: 'json',
	    success: function(json) {
	    	$('#cmbContratoInv').append($('<option>').text('...').attr('value', 0));
	        $.each(json, function(i, value) {
	        	if(value.idContrato == idContratoInv)
	        		$('#cmbContratoInv').append($('<option selected>').text(value.descripcion).attr('value', value.idContrato));
	        	else
	        		$('#cmbContratoInv').append($('<option>').text(value.descripcion).attr('value', value.idContrato));
	        });
	    }
	});

	$.ajax({
	    url: actionForm,
	    type:'POST',
	    data: 'actionOfForm=getContratos&tipoContrato=A',
	    dataType: 'json',
	    success: function(json) {
	    	$('#cmbContratoAud').append($('<option>').text('...').attr('value', 0));
	        $.each(json, function(i, value) {
	        	if(value.idContrato == idContratoAud)
	        		$('#cmbContratoAud').append($('<option selected>').text(value.descripcion).attr('value', value.idContrato));
	        	else
	        		$('#cmbContratoAud').append($('<option>').text(value.descripcion).attr('value', value.idContrato));
	        });
	    }
	});

	$.ajax({
	    url: actionForm,
	    type:'POST',
	    data: 'actionOfForm=getContratos&tipoContrato=M',
	    dataType: 'json',
	    success: function(json) {
	    	$('#cmbContratoMap').append($('<option>').text('...').attr('value', 0));
	        $.each(json, function(i, value) {
	        	if(value.idContrato == idContratoMap)
	        		$('#cmbContratoMap').append($('<option selected>').text(value.descripcion).attr('value', value.idContrato));
	        	else
	        		$('#cmbContratoMap').append($('<option>').text(value.descripcion).attr('value', value.idContrato));
	        });
	    }
	});
}

function addProxyShow()
{
	//Reset State of action and IndexPK
	actionPK = undefined;
	action = iConst.NEW;
	oTable.$('tr.row_selected').removeClass('row_selected');
	$('#usuariosDialog').load(popForm).dialog('open');
}

function editProxyShow()
{
	if(actionPK === undefined)
	{
		$_showMessage('ALERT', 'ALERTA', strMenssageSelectionInGrid);
		return;
	}

	action = iConst.EDIT;
	var paramData =  "actionOfForm=" + iConst.EDIT + "&idUsuario=" + actionPK;

    $.ajax({
        type: 'POST',
        url: actionForm,
        data: paramData,
		dataType: 'json',
        success: function(jsonObj)
                            {
        						joDataUser = jsonObj;
        						//Open Pop Dialog
        						$('#usuariosDialog').load(popForm).dialog('open');
                            }
        });

return true;
}

function $_saveUsuario(action)
{
	var paramData = 'actionOfForm=addOrEdit&' + $("#usuariosPopForm").serialize();

	if(action == iConst.EDIT)
		paramData += '&idUsuario=' + actionPK;

    $.ajax({
        type: 'POST',
        url: actionForm,
        data: paramData,
		dataType: 'json',
        success: function(jsonObj){
            if(jsonObj.status === 'OK'){
				$_showMessage('OK', jsonObj.status, jsonObj.msg);
				$('#usuariosDialog').dialog('close');
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

	$("#deleteUsuariosDialog").dialog({
		autoOpen: false,
		width:350,
		position: 'center',
		open: function () {
			$('#deleteUsuariosDialog').css('background','#F5F5F5');
			$('#btnConfirmDelete').click( function(e) {
				$_del();
			});
			$('#btnExitDelete').click( function(e) {
				$('#deleteUsuariosDialog').dialog('close');
			})
		}
	});
	$('#deleteUsuariosDialog').dialog('open');
}

function $_del()
{
	var paramData = 'actionOfForm=' + action + '&idUsuario=' + actionPK;

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
                                    $('#deleteUsuariosDialog').dialog('close');
                                }
            });

return true;
}

function $_initPopValidator()
{
	$('#usuariosPopForm').validate({
		submitHandler: function() {
			$_saveUsuario(action);
		},
		rules: {
			usuario: {required: true,minlength: 1},
			password: {required: true,minlength: 1},
			nombreCompleto: {required: true,minlength: 1},
			cmbTipoUsuario: {required: true,minlength: 1}
		},
		messages: {
			usuario: {required: '',minlength: ''},
			password: {required: '',minlength: ''},
			nombreCompleto: {required: '',minlength: ''},
			cmbTipoUsuario: {required: '',minlength: ''}
		}
	});
}

function $_agregaSistemaModulos()
{
	paramData = "actionOfForm=getSistemaModulos";

	if(action == iConst.EDIT)
		paramData += '&idUsuario=' + actionPK;

    $.ajax({
        type: 'POST',
        url: actionForm,
        data: paramData,
		dataType: 'json',
        success: function(jsonObj)
                            {
                                if(jsonObj.status === 'OK'){
                                	var htmlTablesRest = "<table>";
                                	$.each(jsonObj.items, function(i, item) {
                                		htmlTablesRest += "<tr><td><input type='checkbox' id='modulo" + item.idMenu + "' name='modulo" + item.idMenu + "' value='" + item.idMenu + "' " + item.selectedMenu + ">" + item.descripcion + "</td></tr>";
    								});
                                	htmlTablesRest += "</table>";
                                	$('#sistemaModulos').html(htmlTablesRest);
								}
                                else if(jsonObj.status === 'EMPTY'){
                                	$('#sistemaModulos').empty();
                                }
                            }
        });
}

function $_ClearUsuariosPopForm()
{
	$("#usuariosDialog").empty();
}
