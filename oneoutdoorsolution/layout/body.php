<?php
	if(!isset($_REQUEST['pantalla'])) {
		echo '<div id="modCenter"></div>';
	}
	else {
		switch ($_REQUEST['pantalla']) {
			case 'usuarios':
				include_once('usuarios.php');
				break;
			case 'contratos':
				include_once('contratos.php');
				break;
			case 'auditoria':
				include_once('auditoria.php');
				break;
			case 'inversion':
				include_once('inversion.php');
				break;
			case 'mapping':
				//echo '<iframe class="d-flex flex-column flex-grow" src="mapping.php" scrolling="no" frameBorder="0"></iframe>';
				include_once('mapping.php');
				break;
			case 'addAlert':
				include_once('addAlert.php');
				break;
			default:
				echo '<div id="modCenter"></div>';
				break;
		}
	}
?>
