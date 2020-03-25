<?php
session_start();
require("includes/funciones.inc.php");
require("includes/constants.php");

$DB = NewADOConnection('mysqlt');
$DB->Connect();
$DB->Execute("SET NAMES utf8;");
//$DB->debug=true;

//LOGIN
if(!empty($_POST['txtUser']))
{
	$strSQL = "SELECT * FROM usuarios WHERE usuario = '".$_REQUEST['txtUser']."' AND password = '".fnEncrypt($_REQUEST["txtPass"])."'";
	
	$rsUser = $DB->Execute($strSQL); 
	if (!$rsUser->EOF)
	{
		$_SESSION['idUser'] = $rsUser->fields('idUsuario');
		$_SESSION['userName'] = $rsUser->fields("usuario");
		$_SESSION['userCompleteName'] = $rsUser->fields("nombreCompleto");
		$_SESSION['userType'] = $rsUser->fields("idTipoUsuario");
		$_SESSION['idContratoInv'] = $rsUser->fields("idContratoInv");
		$_SESSION['idContratoAud'] = $rsUser->fields("idContratoAud");
		$_SESSION['idContratoMap'] = $rsUser->fields("idContratoMap");
			
		Header("Location: principal.php");
		exit();
	}
}

?>
<!DOCTYPE HTML>
<html>
<head>
	<title>--[:: SCOPESI.NET ::]--</title>
	<link href='http://fonts.googleapis.com/css?family=Numans' rel='stylesheet' type='text/css'>
	<link rel="stylesheet" type="text/css" href="css/css.css">
	
	<!-- General Scripts -->
	<script type="text/javascript" src="js/jquery/jquery-1.7.1.min.js"></script>
	<script type="text/javascript" src="js/jquery/jquery-ui-1.8.16.custom.min.js"></script>
	<script type="text/javascript" src="js/jquery/jquery.blockUI-2.53.js"></script>
	<!-- Form Script -->
	<script type="text/javascript" src="js/login.js"></script>
	<script src="js/datatables/js/jquery.dataTables.js" type="text/javascript" ></script>
	<script type="text/javascript" src="js/utils.js"></script>
</head>
<body>
	<div id="logoLog">
		<img src="images/logo.png">
	</div>
	<div id="cajaLog">
		<form id="frmLogin" name="frmLogin" method="post" action="login.php" onsubmit="return $_validaCamposMandatoriosForm();">
			<table>
				<tr>
					<td>Usuario</td>
					<td><input class="logInput" type="text" name="txtUser" id="txtUser"></input></td>
				</tr>
				<tr>
					<td>Contrase&ntilde;a</td>
					<td><input class="logInput" type="password" name="txtPass" id="txtPass"></input></td>
				</tr>
				<tr>
					<td></td>
					<td><button onclick="$('#frmLogin').submit();" class="btnA">Iniciar Sesi&oacute;n</button></td>
				</tr>
			</table>
		</form>
	</div>
</body>
	
<!-- Global Message -->
<div id="globalMess"> 
	<div class="img"></div>
	<div id="globalMessImgClose" class="imgClose"></div>
	<div id="tituloMess" class="msgTitulo">TITULO</div>
	<p id="textoMess" class="msgTexto">MENSAJE</p>
</div>

<script type="text/javascript">
	$(document).ready(
	function() {
				setTimeout(function(){focus_login()}, 100);
				<?php if (!empty($_POST['txtUser']) && $rsUser->EOF) { ?>
					$_showMessage('ERR', 'ERROR', 'Combinaci&oacute;n usuario/clave incorrecto');
				<?php } ?>
	});	
</script>

</html>
