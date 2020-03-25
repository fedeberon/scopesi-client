
function $_init()
{

    $("#alertFotosDialog").dialog({
        autoOpen:false,
        height:520,
        width:760,
        modal: true,
        closeOnEscape: false,
        position : {
    			my: 'center',
    			at: 'center',
    			of: window
    		},
        dialogClass:'no-close',
        open: function() {
            $('#alertFotosDialog').css('background','#F5F5F5');
        }
    });
    $("#btnGetFotos").click( function(e) {
        var dia = $('#cmbDias').val();
        console.log(dia);
        $_imagenAlertProxyShow(dia);
    });


}
function $_imagenAlertProxyShow(dia) {
	$('#alertFotosDialog').load('alertFotos.php?dia=' +dia).dialog('open');
}
