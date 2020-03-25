<style>
.title{
  margin-top:0px;
  margin-bottom:0px;
  color: #ffffff;
}

.subtitle{
  font-size:10px;
  margin-top:0px;
  margin-bottom:0px;
  color: #ffffff;
}

.icon-logout{
  margin-top:10px;
  margin-left:10px;
}

.icon-user{
  height: 120px;
  margin-top: -30px;
  margin-bottom: -40px;
}

.image-upload > input
{
    display: none;
}


</style>

<nav class="navbar navbar-expand-lg navbar-light bg-light bg-dark">

  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarTogglerDemo01" aria-controls="navbarTogglerDemo01" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navbarTogglerDemo01">
   
    <a class="navbar-brand" href="#">
        <img src="images/scopesilogo.png">
    </a>
   
    <a class="navbar-brand" href="#">
        <img src="images/icons/OOS.png" style="height:30px">
    </a>

    <ul class="navbar-nav mr-auto mt-2 mt-lg-0">
      <li class="nav-item active">
        <a id="inicio" class="nav-link" href="principal.php" title="Inicio">
          <i id="icon-principal" class="fas fa-home"></i>
        </a>
      </li>
      
      <li class="nav-item">
        <a id="manager" class="nav-link" href="manager.php" title="Manager">
          <i class="fas fa-cog"></i>
        </a>  
      </li>

      <li class="nav-item dropdown" style="margin-top: -4px">
        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <i class="fas fa-bars"></i>
        </a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
          <a class="dropdown-item" href="#">Servicio 1</a>
          <a class="dropdown-item" href="#">Servicio 2</a>
          <a class="dropdown-item disabled" href="#">Servicio 3 (deshabilitado) </a>
        </div>
      </li>


   
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
 				$i = $rsMenu->fields('pantalla');
 				switch ($i) {
 			    case 'auditoria':
 			        echo ' <li class="nav-item"><a id="auditorias" class="nav-link" href="principal.php?pantalla=auditoria" title="Auditorías"><i id="icon-auditoria" class="fas fa-glasses"></i></a></li>';
 			        break;
 					case 'mapping':
 							echo '<li class="nav-item"><a id="geoplanning" class="nav-link" href="principal.php?pantalla=mapping" title="GeoPlanning+"><i id="icon-mapping" class="fas fa-map-marker-alt"></i></a></li>';
 							break;
 					case 'contratos':
 							echo '<li class="nav-item"><a id="contratos" class="nav-link" href="principal.php?pantalla=contratos" title="Contratos"><i id="icon-contratos" class="fas fa-file-alt"></i></a></li>';
 							break;
 					case 'inversion':
 							echo '<li class="nav-item"><a id="competencia" class="nav-link" href="principal.php?pantalla=inversion" title="Competencia"><i id="icon-inversion" class="fas fa-dollar-sign"></i></a></li>';
 							break;
 					case 'usuarios':
 							echo '<li class="nav-item"><a id="usuarios" class="nav-link" href="principal.php?pantalla=usuarios" title="Usuarios"><i class="fas fa-user-cog"></i></a></li>';
 							break;
 					case 'addAlert':
 							echo '<li class="nav-item"><a id="adalert" class="nav-link" href="principal.php?pantalla=addAlert" title="Ad Alerts"><i id="icon-addAlert" class="fas fa-exclamation-circle"></i></a></li>';
 							break;
 						}
 				array_push($arrMenu, $rsMenu->fields('idMenu'));
 			}
 			$rsMenu->MoveNext();
 		}

 		?>
    
    </ul>
    
    <ul class="nav navbar-nav navbar-right">

        <li class="nav-item">
          <div class="image-upload">
          <label for="file-input">
            <img src="images/icons/iconfinder_1.png" class="icon-user"/>
          </label>
          <input id="file-input" type="file"/>
          </div>
        </li>

        <li class="nav-item">
           <p class="title"><?=$_SESSION['userCompleteName']?></p>
           <p class="subtitle"><?=$_SESSION['empresa']?></p>
           <p class="subtitle"><?=$_SESSION['eMail']?></p>
        </li>

        <li class="nav-item icon-logout">
          <a class="nav-link active" href="logout.php">
            <i class="fas fa-sign-out-alt"></i>
          </a>
        </li>

    </ul>

  </div>
</nav>