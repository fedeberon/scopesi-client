<div id="header">
	<img src="images/scopesilogo.png" id="imgLogo" height="30" align="left">
	<img src="images/logo_oos_white.png" id="imgLogo" height="30" align="left">
	<div id="btnModules">
		<!--
		<a id="inicio" class="nav-link" href="index.html" title="Inicio"><i class="fas fa-home"></i></a>
		<a id="auditorias" class="nav-link" href="#" title="Auditorías"><span id="auditoria"><b>A</b><span></a>
		<a id="geoplanning" class="nav-link" href="#" title="GeoPlanning+"><i class="fas fa-map-marker-alt"></i></a>
		<a id="contratos" class="nav-link" href="#" title="Contratos"><i class="fas fa-file-alt"></i></a>
		<a id="competencia" class="nav-link" href="#" title="Competencia"><i class="fas fa-dollar-sign"></i></a>
		<a id="usuarios" class="nav-link" href="#" title="Usuarios"><i class="fas fa-user-cog"></i></a>
		<a id="adalert" class="nav-link" href="#" title="Ad Alerts"><i class="fas fa-exclamation-circle"></i></a>
		 -->
		 <?php
 		if (session_status() == PHP_SESSION_NONE) {
     		session_start();
 		}

 		require("includes/funciones.inc.php");

 		$DB = NewADOConnection("mysqli");
 		$DB->Connect();
 		$DB->Execute("SET NAMES utf8;");
 		//$DB->debug=true;

 		$idUser = $_SESSION['idUser'];
 		$idTypeUser = $_SESSION['userType'];

 		$arrMenu = array();

 		$strSQL = "SELECT m.*, 'E' Hab FROM menu m
 					INNER JOIN usuarios_menu um ON m.idMenu = um.idMenu WHERE um.idUsuario = $idUser
 					UNION
 					SELECT m.*, 'D' Hab FROM menu m
 					INNER JOIN tipo_usuarios_menu tum ON m.idMenu = tum.idMenu WHERE tum.idTipoUsuario = $idTypeUser";

 		$rsMenu = $DB->Execute($strSQL);

 		//var_dump($rsMenu);

 		echo '<a id="inicio" class="nav-link" href="principal.php" title="Inicio"><i id="icon-principal" class="fas fa-home"></i></a>';

		 echo '<a id="manager" class="nav-link" href="manager.php" title="Manager"><i class="fas fa-cog"></i></a>';


 		while(!$rsMenu->EOF){
 			if($rsMenu->fields('Hab') == "E") {
 				//echo '<a href="principal.php?pantalla='.$rsMenu->fields('pantalla').'" id="'.$rsMenu->fields('btnMenu').'" class="btnNav '.$rsMenu->fields('btnMenu').'"></a>';
 				$i = $rsMenu->fields('pantalla');
 				switch ($i) {
 			    case 'auditoria':
 			        echo '<a id="auditorias" class="nav-link" href="principal.php?pantalla=auditoria" title="Auditorías"><i id="icon-auditoria" class="fas fa-glasses"></i></a>';
 			        break;
 					case 'mapping':
 							echo '<a id="geoplanning" class="nav-link" href="principal.php?pantalla=mapping" title="GeoPlanning+"><i id="icon-mapping" class="fas fa-map-marker-alt"></i></a>';
 							break;
 					case 'contratos':
 							echo '<a id="contratos" class="nav-link" href="principal.php?pantalla=contratos" title="Contratos"><i id="icon-contratos" class="fas fa-file-alt"></i></a>';
 							break;
 					case 'inversion':
 							echo '<a id="competencia" class="nav-link" href="principal.php?pantalla=inversion" title="Competencia"><i id="icon-inversion" class="fas fa-dollar-sign"></i></a>';
 							break;
 					case 'usuarios':
 							echo '<a id="usuarios" class="nav-link" href="principal.php?pantalla=usuarios" title="Usuarios"><i class="fas fa-user-cog"></i></a>';
 							break;
 					case 'addAlert':
 							echo '<a id="adalert" class="nav-link" href="principal.php?pantalla=addAlert" title="Ad Alerts"><i id="icon-addAlert" class="fas fa-exclamation-circle"></i></a>';
 							break;
 						}
 				array_push($arrMenu, $rsMenu->fields('idMenu'));
 			}
 			else {
 				//if (!in_array($rsMenu->fields('idMenu'), $arrMenu))
 					//echo '<div class="btnNav '.$rsMenu->fields('btnMenu').'D"></div>';
 			}
 			$rsMenu->MoveNext();
 		}

 		?>
	</div>

	<div id="btnHeader">
		<img src="images/icons/iconfinder_1.png" style="margin-top">
		<button class="btnsHeader btnUsr"><?=$_SESSION['userCompleteName']?></button>
		<button class="btnsHeader btnCerSes" onclick="window.location='logout.php'" title="Cerrar Sesión"><i class="fas fa-sign-out-alt"></i></button>
	</div>
</div>

<div id="headNav">
	<div id="contHeadNav">

		<div class="clear"></div>
	</div>
</div>
