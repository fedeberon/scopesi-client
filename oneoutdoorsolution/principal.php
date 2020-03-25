<?php
if (session_status() == PHP_SESSION_NONE) {
		session_start();
}
require("includes/funciones.inc.php");

if(!isset($_SESSION['idUser'])) $_SESSION['idUser'] = '';

if($_SESSION['idUser'] == "" && isset($_REQUEST['uniqueID']))
{
	$DB = NewADOConnection('mysqli');
	$DB->Connect();
	$DB->Execute("SET NAMES utf8;");
	//$DB->debug=true;

	$rsUser = $DB->Execute("SELECT * FROM usuarios WHERE usuario = ? ", array("COMPARTIR"));
	if (!$rsUser->EOF)
	{
		$_SESSION['idUser'] = $rsUser->fields('idUsuario');
		$_SESSION['userName'] = $rsUser->fields("usuario");
		$_SESSION['userCompleteName'] = $rsUser->fields("nombreCompleto");
		$_SESSION['userType'] = $rsUser->fields("idTipoUsuario");
		$_SESSION['idContratoInv'] = $rsUser->fields("idContratoInv");
		$_SESSION['idContratoAud'] = $rsUser->fields("idContratoAud");
		$_SESSION['idContratoMap'] = $rsUser->fields("idContratoMap");

	}
	else
		Header("Location: index.html");
}

if(!isset($_SESSION['userName']))
	Header("Location: index.html");
?>
<html>
<head>
	<title>Scopesi - ONE Outdoor Solution</title>
	<link href='http://fonts.googleapis.com/css?family=Roboto' rel='stylesheet' type='text/css' />
	<link rel="shortcut icon" type="image/png" href="images/favicon.png"/>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css" integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS" crossorigin="anonymous">
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css" integrity="sha384-UHRtZLI+pbxtHCWp1t77Bi1L4ZtiqrqD80Kn4Z8NTSRyMA2Fd33n5dQ8lWUE00s/" crossorigin="anonymous">
	<style type="text/css" title="currentStyle">
		@import "js/datatables/css/demo_table_jui.css";
		/* @import "//cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css"; */
		@import "//cdn.datatables.net/1.9.4/css/jquery.dataTables.css";
		/*@import "js/datatables/themes/smoothness/jquery-ui-1.8.4.custom.css";*/
		@import "js/jquery-ui-notooltip/jquery-ui.css";
		@import "css/one.css";
		@import "css/geoplanning.css";
	</style>


	<!-- JQuery Script -->
	<script src="js/jquery-3.4.1.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
	<script src="js/jquery-ui-notooltip/jquery-ui.min.js"></script>

	<!--
	<script type="text/javascript" src="js/jquery-1.7.1.js"></script>
	<script type="text/javascript" src="js/jquery/jquery-ui-1.8.16.custom.min.js"></script>
	<script type="text/javascript" src="js/jquery/jquery.ui.core.js"></script>
	<script type="text/javascript" src="js/jquery/jquery.ui.widget.js"></script>
	<script type="text/javascript" src="js/jquery/jquery.ui.tabs.js"></script>
	<script type="text/javascript" src="js/jquery/jquery.ui.dialog.js"></script>
	<script type="text/javascript" src="js/jquery/jquery.ui.mouse.js"></script>
	<script type="text/javascript" src="js/jquery/jquery.ui.draggable.js"></script>
	<script type="text/javascript" src="js/jquery/jquery.ui.position.js"></script>
	<script type="text/javascript" src="js/jquery/jquery.ui.resizable.js"></script>
	<script type="text/javascript" src="js/jquery/jquery.effects.core.js"></script>
	<script type="text/javascript" src="js/jquery/jquery.effects.blind.js"></script>
	<script type="text/javascript" src="js/jquery/jquery.effects.explode.js"></script>
	<script type="text/javascript" src="js/jquery/jquery.ui.datepicker.js"></script>
	<script type="text/javascript" src="js/jquery/jquery.maskedinput-1.2.2.min.js"></script>

	<script type="text/javascript" src="js/jquery/jquery.validate.js"></script>
	<script type="text/javascript" src="js/jquery/jquery.numeric.js"></script>
	<script type="text/javascript" src="js/jquery/jquery.ui.tooltip.js"></script>
	<script type="text/javascript" src="js/jquery/NumberFormat154.js"></script>
	<script type="text/javascript" src="js/jquery/html2canvas.js"></script>
	<script type="text/javascript" src="js/jquery/canvas-toBlob.js"></script>
	<script type="text/javascript" src="js/jquery/FileSaver.js"></script>
	-->

	<script type="text/javascript" src="js/jquery/jquery.validate.js"></script>
	<script type="text/javascript" src="js/jquery/jquery.numeric.js"></script>
	<script type="text/javascript" src="js/jquery/NumberFormat154.js"></script>
	<script type="text/javascript" src="js/jquery/html2canvas.js"></script>
	<script type="text/javascript" src="js/jquery/canvas-toBlob.js"></script>
	<script type="text/javascript" src="js/jquery/FileSaver.js"></script>

	<!-- Data Tables -->
	<!-- <script src="//cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js" type="text/javascript" ></script> -->
	<script src="//cdn.datatables.net/1.9.4/js/jquery.dataTables.min.js" type="text/javascript" ></script>


	<!--
	<script src="js/datatables/js/jquery.dataTables.js" type="text/javascript" ></script>
	<script src="js/datatables/media/js/jquery.jeditable.js" type="text/javascript"></script>
    <script src="js/datatables/media/js/jquery-ui.js" type="text/javascript"></script>
    <script src="js/datatables/media/js/jquery.validate.js" type="text/javascript"></script>
    <script src="js/datatables/media/js/jquery.dataTables.editable.js" type="text/javascript"></script>
	-->

    <!-- Tools Script -->
    <script src="js/utils.js" type="text/javascript"></script>

	<!-- Global site tag (gtag.js) - Google Analytics -->
	<script async src="https://www.googletagmanager.com/gtag/js?id=UA-116228558-1"></script>
	<script>
		window.dataLayer = window.dataLayer || [];
		function gtag(){dataLayer.push(arguments);}
		gtag('js', new Date());
		gtag('config', 'UA-116228558-1');
	</script>

</head>

<body>
<div id="mainWindow" class="d-flex flex-column h-100">
	<!-- Header -->
	<?php include_once('layout/header-1.php');?>

	<!-- Menu
	<?php //include_once('layout/menu.php');?>
	-->

	<!-- Main Boby -->
	<?php
	include_once('layout/body.php');
	//echo '<iframe class="d-flex flex-column flex-grow" src="mapping.php" scrolling="no" frameBorder="0"></iframe>';

	?>


	<!-- Footer -->
	<?php //include_once('layout/footer.php');?>
</div>

<script>
	$('#icon-principal').addClass('active');
</script>
</body>
</html>
