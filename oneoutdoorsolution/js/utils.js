/*
 * Creado por Federico Pisarello - SisDev Software (c) 2012 - fpisarello@sisdevsoft.com
 */

//Roles
var iConst = new fn_const();
function fn_const()
{
	this.NEW = "NEW";
	this.EDIT = "EDIT";
	this.DELETE = "DELETE";
	this.DELETEITEM = "DELETEITEM";
	this.CONSULT = "CONSULT";
	this.UPLOAD = "UPLOAD";
}

var classConst = new fn_classConst();
function fn_classConst()
{
	this.HIGHLIGHT = "ui-state-highlight";
	this.ERROR = "ui-state-error";
}

var polyConst = new fn_polyConst();
function fn_polyConst()
{
	this.POIS = 0;
	this.ELEMENTOS = 1;
	this.EVPS = 2;
}

function $_ajax_showLoader(loaderId)
{
	$('#' + loaderId).show();
}

function $_ajax_hideLoader(loaderId)
{
	$('#' + loaderId).hide();
}

function $_trim(str)
{
   return str.replace(/^\s*|\s*$/g,"");
}

function $_replace(s, r, w){
     return s.split(r).join(w);
}

function $_zeroPad(num) {

	var s = '0'+num;

	return s.substring(s.length-2)
};

function $_getPageScrollTop(){
	var yScrolltop;
	var xScrollleft;
	if (self.pageYOffset || self.pageXOffset) {
		yScrolltop = self.pageYOffset;
		xScrollleft = self.pageXOffset;
	} else if (document.documentElement && document.documentElement.scrollTop || document.documentElement.scrollLeft ){	 // Explorer 6 Strict
		yScrolltop = document.documentElement.scrollTop;
		xScrollleft = document.documentElement.scrollLeft;
	} else if (document.body) {// all other Explorers
		yScrolltop = document.body.scrollTop;
		xScrollleft = document.body.scrollLeft;
	}
	arrayPageScroll = new Array(xScrollleft,yScrolltop)
	return arrayPageScroll;
}

function $_getPageSize(){
	var de = document.documentElement;
	var w = window.innerWidth || self.innerWidth || (de&&de.clientWidth) || document.body.clientWidth;
	var h = window.innerHeight || self.innerHeight || (de&&de.clientHeight) || document.body.clientHeight
	arrayPageSize = new Array(w,h)
	return arrayPageSize;
}

/** GLOBAL MESSAGE **/
var isShown = false;
var request_timer = false;

setTimeout(function(){$("#globalMessImgClose").click($_closeMessage);}, 500);

function $_closeMessage()
{
	$('#globalMess').fadeOut();
	isShown = false;
}

function $_showMessage(tipo, titulo, mensaje, timeOut)
{
	var messageTimeOut = 2000; // 5 segundos

	if(timeOut !== undefined)
		messageTimeOut = timeOut;

	//configuramos la estetica del mensaje
	$('#globalMess').removeClass();
	$('#globalMess').addClass('alert');
	switch(tipo)
	{
		case "ERR": // si es un mensaje de error
			$('#globalMess').addClass('mensajesErr');
			$('#globalMess').addClass('alert-warning');
			break;
		case "OK": // si es un mensaje de ok
			$('#globalMess').addClass('mensajesOk');
			$('#globalMess').addClass('alert-success');
			break;
		case "ALERT": // si es un mensaje de alerta
			$('#globalMess').addClass('mensajesAlert');
			$('#globalMess').addClass('alert-primary');
			break;
	}

	$('#tituloMess').html(titulo);
	$('#textoMess').html(mensaje);

	$_centrar();
	if(!isShown){
		$('#globalMess').slideDown('fast');
		isShown = true;
	}

	// limpio timeout's pendientes, si es que hay!!
	if (request_timer)
		clearTimeout(request_timer);

	// utilizo un timer para ocultar el mensaje en un determinado tiempo (messageTimeOut)
	if(timeOut !== -1)
		request_timer = setTimeout($_closeMessage, messageTimeOut);

	//queremos que siempre se quede centrado horizontalmente y en la linea del scroll verticalmente
	$(window).scroll($_centrar);
	$(window).resize($_centrar);
}

function $_showUrgentMessage(tipo, titulo, mensaje, timeOut)
{
	//limpio el timeout pendiente, si es que hay!!
	if(request_timer)
		clearTimeout(request_timer);

	$('#globalMess').hide();
	isShown = false;

	$_showMessage(tipo, titulo, mensaje, timeOut);
}

function $_centrar(){
	var pagesize = $_getPageSize();
	var arrayPageScroll = $_getPageScrollTop();
	$("#globalMess")
	.css(
		{
			left: (arrayPageScroll[0] + (pagesize[0] - $('#globalMess').width())/2) + "px",
			top:  arrayPageScroll[1] + "px"
		});
}
/** GLOBAL MESSAGE **/

function $_mailValido(mail)
{
	var filter  = /^[a-zA-Z][\w\.-]*[a-zA-Z0-9]@[a-zA-Z0-9][\w\.-]*[a-zA-Z0-9]\.[a-zA-Z][a-zA-Z\.]*[a-zA-Z]$/;
	if (filter.test(mail))
		return true;
	else
		return false;
}

function $_highLightFormFields(arrFields)
{
	if(arrFields!=undefined){
		for (key in arrFields)
			$('#' + arrFields[key]).effect("highlight", {color:'#FF0000'}, 1000);
	}
}

/* Not implemented */
function $_notImplemented()
{
	$_showMessage('ALERT', 'ALERTA', 'Funci&oacute;n no implementada!');
}

function $_ValidatorSetDefaults()
{

	$.validator.setDefaults({
		invalidHandler:function(form, validator) {
			var errors = validator.numberOfInvalids();
			if (errors)
			{
				$_showMessage('ALERT', 'ALERTA', strMenssageErrorMandatoryFields);

				var invalidPanels = $(validator.invalidElements()).closest(".ui-tabs-panel", form);
				if (invalidPanels.size() > 0)
				{
					 $.each($.unique(invalidPanels.get()), function() {

				          $(this).siblings(".ui-tabs-nav")
				            .find("a[href='#" + this.id + "']").parent().not(".ui-tabs-selected")
				            .addClass(classConst.ERROR)
				            .show("pulsate",{times: 3});
				        });
				}
			}
		},
		highlight: function(input) {
		    $(input).addClass(classConst.HIGHLIGHT);
		},
		unhighlight: function(input) {
		    $(input).removeClass(classConst.HIGHLIGHT);

		    var $panel = $(input).closest(".ui-tabs-panel", input.form);
		    if ($panel.size() > 0)
		    {
		    	if ($panel.find("." + classConst.HIGHLIGHT).size() == 0)
			    {
			        $panel.siblings(".ui-tabs-nav").find("a[href='#" + $panel[0].id + "']")
			        .parent().removeClass(classConst.ERROR);
			    }
		    }
		}
	});
}

//Reload Datatables Server Side AJAX from original file
jQuery.fn.dataTableExt.oApi.fnReloadAjax = function ( oSettings, sNewSource, fnCallback, bStandingRedraw )
{
    // DataTables 1.10 compatibility - if 1.10 then `versionCheck` exists.
    // 1.10's API has ajax reloading built in, so we use those abilities
    // directly.
    if ( jQuery.fn.dataTable.versionCheck ) {
        var api = new jQuery.fn.dataTable.Api( oSettings );

        if ( sNewSource ) {
            api.ajax.url( sNewSource ).load( fnCallback, !bStandingRedraw );
        }
        else {
            api.ajax.reload( fnCallback, !bStandingRedraw );
        }
        return;
    }

    if ( sNewSource !== undefined && sNewSource !== null ) {
        oSettings.sAjaxSource = sNewSource;
    }

    // Server-side processing should just call fnDraw
    if ( oSettings.oFeatures.bServerSide ) {
        this.fnDraw();
        //return;
    }

    this.oApi._fnProcessingDisplay( oSettings, true );
    var that = this;
    var iStart = oSettings._iDisplayStart;
    var aData = [];

    this.oApi._fnServerParams( oSettings, aData );

    oSettings.fnServerData.call( oSettings.oInstance, oSettings.sAjaxSource, aData, function(json) {
        /* Clear the old information from the table */
        that.oApi._fnClearTable( oSettings );

        /* Got the data - add it to the table */
        var aData =  (oSettings.sAjaxDataProp !== "") ?
            that.oApi._fnGetObjectDataFn( oSettings.sAjaxDataProp )( json ) : json;

        for ( var i=0 ; i<aData.length ; i++ )
        {
            that.oApi._fnAddData( oSettings, aData[i] );
        }

        oSettings.aiDisplay = oSettings.aiDisplayMaster.slice();

        that.fnDraw();

        if ( bStandingRedraw === true )
        {
            oSettings._iDisplayStart = iStart;
            that.oApi._fnCalculateEnd( oSettings );
            that.fnDraw( false );
        }

        that.oApi._fnProcessingDisplay( oSettings, false );

        /* Callback user function - for event handlers etc */
        if ( typeof fnCallback == 'function' && fnCallback !== null )
        {
            fnCallback( oSettings );
        }
    }, oSettings );
};

/*
 * Function: fnGetColumnData
 * Purpose:  Return an array of table values from a particular column.
 * Returns:  array string: 1d data array
 * Inputs:   object:oSettings - dataTable settings object. This is always the last argument past to the function
 *           int:iColumn - the id of the column to extract the data from
 *           bool:bUnique - optional - if set to false duplicated values are not filtered out
 *           bool:bFiltered - optional - if set to false all the table data is used (not only the filtered)
 *           bool:bIgnoreEmpty - optional - if set to false empty values are not filtered from the result array
 * Author:   Benedikt Forchhammer <b.forchhammer /AT\ mind2.de>
 */
$.fn.dataTableExt.oApi.fnGetColumnData = function ( oSettings, iColumn, bUnique, bFiltered, bIgnoreEmpty ) {
    // check that we have a column id
    if ( typeof iColumn == "undefined" ) return new Array();

    // by default we only wany unique data
    if ( typeof bUnique == "undefined" ) bUnique = true;

    // by default we do want to only look at filtered data
    if ( typeof bFiltered == "undefined" ) bFiltered = true;

    // by default we do not wany to include empty values
    if ( typeof bIgnoreEmpty == "undefined" ) bIgnoreEmpty = true;

    // list of rows which we're going to loop through
    var aiRows;

    // use only filtered rows
    if (bFiltered == true) aiRows = oSettings.aiDisplay;
    // use all rows
    else aiRows = oSettings.aiDisplayMaster; // all row numbers

    // set up data array
    var asResultData = new Array();

    for (var i=0,c=aiRows.length; i<c; i++) {
        iRow = aiRows[i];
        var aData = this.fnGetData(iRow);
        var sValue = aData[iColumn];

        // ignore empty values?
        if (bIgnoreEmpty == true && sValue.length == 0) continue;

        // ignore unique values?
        else if (bUnique == true && jQuery.inArray(sValue, asResultData) > -1) continue;

        // else push the value onto the result data array
        else asResultData.push(sValue);
    }

    return asResultData;
}
