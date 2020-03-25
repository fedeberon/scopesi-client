<?php
session_start();
require("includes/funciones.inc.php");
require("includes/constants.php");

// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

$DB = ADONewConnection('mysqli');
$DB->Connect();
$DB->Execute("SET NAMES utf8;");
//$DB->debug=true;

//LOGIN
if(!empty($_POST['txtUser']))
{
	$rsUser = $DB->Execute(
		    "SELECT * FROM usuarios u 
		 	JOIN map_anunciantes m on m.idAnunciante = u.idAnunciante	
			WHERE usuario = ? AND password = ? AND estado <> 'B'", 
				array($_REQUEST['txtUser'], fnEncrypt($_REQUEST["txtPass"])));
	if (!$rsUser->EOF)
	{
		$_SESSION['idUser'] = $rsUser->fields('idUsuario');
		$_SESSION['userName'] = $rsUser->fields("usuario");
		$_SESSION['userCompleteName'] = $rsUser->fields("nombreCompleto");
		$_SESSION['userType'] = $rsUser->fields("idTipoUsuario");
		$_SESSION['idContratoInv'] = $rsUser->fields("idContratoInv");
		$_SESSION['idContratoAud'] = $rsUser->fields("idContratoAud");
		$_SESSION['idContratoMap'] = $rsUser->fields("idContratoMap");
		$_SESSION['empresa'] = $rsUser->fields("Descripcion");
		$_SESSION['eMail'] = $rsUser->fields("eMail");

		setcookie("Authorization", "Basic " . $rsUser->fields('usuario'), -1 , "/");
		setcookie("Token", fnEncrypt($_REQUEST["txtPass"]), -1 , "/");

		Header("Location: principal.php");
		exit();
	}
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Scopesi - ONE Outdoor Solution</title>
	<link href='http://fonts.googleapis.com/css?family=Roboto' rel='stylesheet' type='text/css' />
	<link rel="stylesheet" type="text/css" href="css/css.css" />
	<link rel="shortcut icon" type="image/png" href="images/favicon.png"/>

	<!-- General Scripts -->
	<script type="text/javascript" src="js/jquery/jquery-1.7.1.min.js"></script>
	<script type="text/javascript" src="js/jquery/jquery-ui-1.8.16.custom.min.js"></script>
	<script type="text/javascript" src="js/jquery/jquery.blockUI-2.53.js"></script>
	<!-- Form Script -->
	<script type="text/javascript" src="js/login.js"></script>
	<script src="js/datatables/js/jquery.dataTables.js" type="text/javascript" ></script>
	<script type="text/javascript" src="js/utils.js"></script>
</head>
<body class="dark">
	<div id="logoLog">
		<img src="images/logo_oos_white.png" height="92" width="342" />
	</div>
	<div id="cajaLog">
		<form id="frmLogin" method="post" action="login.php" onsubmit="return $_validaCamposMandatoriosForm();">
			<table>
				<tr>
					<td><input class="logInput" type="text" name="txtUser" id="txtUser" placeholder="Usuario"></input></td>
				</tr>
				<tr>
					<td><input class="logInput" type="password" name="txtPass" id="txtPass" placeholder="Contraseña"></input></td>
				</tr>
				<tr>
					<td><button onclick="$('#frmLogin').submit();" class="btnA">Iniciar Sesi&oacute;n</button></td>
				</tr>
			</table>
		</form>
	</div>
	<div id="supportBrowsersLog">
		<img src="images/scopesilogo.png" width="142" />
	</div>

	<!-- Global Message -->
	<div id="globalMess">
		<div class="img"></div>
		<div id="globalMessImgClose" class="imgClose"></div>
		<div id="tituloMess" class="msgTitulo">TITULO</div>
		<p id="textoMess" class="msgTexto">MENSAJE</p>
	</div>

	<script type='text/javascript'>
		$(document).ready(
		function() {
					setTimeout(function(){focus_login()}, 100);
					<?php if (!empty($_POST['txtUser']) && $rsUser->EOF) { ?>
						$_showMessage('ERR', 'ERROR', 'Combinaci&oacute;n usuario/clave incorrecto');
					<?php } ?>

					//Check if the Database is available
					$.ajax({
					    type: 'POST',
					    url: 'usuariosAction.php',
					    data: {actionOfForm: 'checkAvailableDB'},
					    dataType: 'json',
					    success: function(jsonObj) {
							if(jsonObj.status > 1) {
								$.blockUI({
									message: '<h2>' + jsonObj.title + '.<br /></h2><span id="msgAdicional">' + jsonObj.msg + '</span>',
							    	css: {
							            border: 'none',
							            padding: '15px',
							            backgroundColor: '#000',
							            '-webkit-border-radius': '10px',
							            '-moz-border-radius': '10px',
							            opacity: .4,
							            color: '#fff'
							    		}
							    });
							}
					    }
					});
		});
	</script>

</body>
</html>
