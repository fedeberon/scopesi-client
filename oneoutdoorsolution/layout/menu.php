<div id="headNav">
	<div id="contHeadNav">
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

		while(!$rsMenu->EOF){
			if($rsMenu->fields('Hab') == "E") {
				echo '<a href="principal.php?pantalla='.$rsMenu->fields('pantalla').'" id="'.$rsMenu->fields('btnMenu').'" class="btnNav '.$rsMenu->fields('btnMenu').'"></a>';
				array_push($arrMenu, $rsMenu->fields('idMenu'));
			}
			else {
				if (!in_array($rsMenu->fields('idMenu'), $arrMenu))
					echo '<div class="btnNav '.$rsMenu->fields('btnMenu').'D"></div>';
			}
			$rsMenu->MoveNext();
		}

		?>
		<div class="clear"></div>
	</div>
</div>
