<?php
/**
 *  Por Federico Pisarello - SisDev Software (c) 2012 - Buenos Aires, Argentina.
 * 	fpisarello@gmail.com
 */ 
function random_pic($dir = 'images/icons')
{
    $files = glob($dir . '/*.*');
    $file = array_rand($files);
    return $files[$file];
}

function fnEncrypt($sValue) 
{ 
	include("constants.php");
	
    return trim( 
        base64_encode( 
            mcrypt_encrypt( 
                MCRYPT_RIJNDAEL_256, 
                $sSecretKey, $sValue,  
                MCRYPT_MODE_ECB,  
                mcrypt_create_iv( 
                    mcrypt_get_iv_size( 
                        MCRYPT_RIJNDAEL_256,  
                        MCRYPT_MODE_ECB 
                    ),  
                    MCRYPT_RAND) 
                ) 
            ) 
        ); 
} 
 
function fnDecrypt($sValue) 
{ 
	include("constants.php");

    return trim( 
        mcrypt_decrypt( 
            MCRYPT_RIJNDAEL_256,  
            $sSecretKey,  
            base64_decode($sValue),  
            MCRYPT_MODE_ECB, 
            mcrypt_create_iv( 
                mcrypt_get_iv_size( 
                    MCRYPT_RIJNDAEL_256, 
                    MCRYPT_MODE_ECB 
                ),  
                MCRYPT_RAND 
            ) 
        ) 
    ); 
}
 
function open_url($url)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);
    curl_setopt($ch, CURLOPT_PORT, 80);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_URL, $url);
    $result = curl_exec($ch);
    if( !$result )
    	return curl_getinfo($ch);
    return $result;
}

function Zip($source, $destination)
{
	if (!extension_loaded('zip') || !file_exists($source)) {
		return false;
	}

	$zip = new ZipArchive();
	if (!$zip->open($destination, ZIPARCHIVE::CREATE)) {
		return false;
	}

	$source = str_replace('\\', '/', realpath($source));

	if (is_dir($source) === true)
	{
		$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source), RecursiveIteratorIterator::SELF_FIRST);

		foreach ($files as $file)
		{
			$file = str_replace('\\', '/', $file);

			// Ignore "." and ".." folders
			if( in_array(substr($file, strrpos($file, '/')+1), array('.', '..')) )
				continue;

			$file = realpath($file);

			if (is_dir($file) === true)
			{
				$zip->addEmptyDir(str_replace($source . '/', '', $file . '/'));
			}
			else if (is_file($file) === true)
			{
				$zip->addFromString(str_replace($source . '/', '', $file), file_get_contents($file));
			}
		}
	}
	else if (is_file($source) === true)
	{
		$zip->addFromString(basename($source), file_get_contents($source));
	}

	return $zip->close();
}

function create_zip($files = array(), $destination = '', $overwrite = false, $fromString = false) {
	//if the zip file already exists and overwrite is false, return false
	
	if(!file_exists($destination) && $overwrite) { $overwrite = false; }
	//vars
	$valid_files = array();
	//if files were passed in...
	if(is_array($files)) {
		//cycle through each file
		foreach($files as $file) {
			//make sure the file exists
			if(file_exists($file[0])) {
				$valid_files[] = $file[0];
			}
		}
	}
	//if we have good files...
	if(count($valid_files)) {
		//create the archive
		$zip = new ZipArchive();
		if($zip->open($destination, $overwrite ? ZIPARCHIVE::OVERWRITE : ZIPARCHIVE::CREATE) !== true) {
			return false;
		}
		
		//add the files
		foreach($valid_files as $file) {
			$new_filename = substr($file,strrpos($file,'/') + 1);
			if(!$fromString)
				$zip->addFile($file, $new_filename);
			else
				$zip->addFromString($file, $new_filename);
		}
		//debug
		//echo 'The zip archive contains ',$zip->numFiles,' files with a status of ',$zip->status;
		

		//close the zip -- done!
		$zip->close();

		//check to make sure the file exists
		return file_exists($destination);
	}
	else
	{
		return false;
	}
}

function getDirContents($dir, &$results = array())
{
	$files = scandir($dir);

	foreach($files as $key => $value){
		$path = realpath($dir.DIRECTORY_SEPARATOR.$value);
		if(!is_dir($path)) {
			$results[] = array($path);
		} else if(is_dir($path) && $value != "." && $value != "..") {
			getDirContents($path, $results);
			$results[] = array($path);
		}
	}

	return $results;
}

function dateAdd($interval,$number,$dateTime) {
        
    $dateTime = (strtotime($dateTime) != -1) ? strtotime($dateTime) : $dateTime;       
    $dateTimeArr=getdate($dateTime);
                
    $yr=$dateTimeArr[year];
    $mon=$dateTimeArr[mon];
    $day=$dateTimeArr[mday];
    $hr=$dateTimeArr[hours];
    $min=$dateTimeArr[minutes];
    $sec=$dateTimeArr[seconds];

    switch($interval) {
        case "s"://seconds
            $sec += $number; 
            break;

        case "n"://minutes
            $min += $number; 
            break;

        case "h"://hours
            $hr += $number; 
            break;

        case "d"://days
            $day += $number; 
            break;

        case "ww"://Week
            $day += ($number * 7); 
            break;

        case "m": //similar result "m" dateDiff Microsoft
            $mon += $number; 
            break;

        case "yyyy": //similar result "yyyy" dateDiff Microsoft
            $yr += $number; 
            break;

        default:
            $day += $number; 
         }       
                
        $dateTime = mktime($hr,$min,$sec,$mon,$day,$yr);
        $dateTimeArr=getdate($dateTime);
        
        $nosecmin = 0;
        $min=$dateTimeArr[minutes];
        $sec=$dateTimeArr[seconds];

        if ($hr==0){$nosecmin += 1;}
        if ($min==0){$nosecmin += 1;}
        if ($sec==0){$nosecmin += 1;}
        
        if ($nosecmin>2){     return(date("Ymd",$dateTime));} else {     return(date("Ymd G:i:s",$dateTime));}
}

 function datediff($interval, $datefrom, $dateto, $using_timestamps = false) {
	 /*
	 $interval can be:
	 yyyy - Number of full years
	 q - Number of full quarters
	 m - Number of full months
	 y - Difference between day numbers
	 (eg 1st Jan 2004 is "1", the first day. 2nd Feb 2003 is "33". The datediff is "-32".)
	 d - Number of full days
	 w - Number of full weekdays
	 ww - Number of full weeks
	 h - Number of full hours
	 n - Number of full minutes
	 s - Number of full seconds (default)
	 */

	if (!$using_timestamps) {
		$datefrom = strtotime($datefrom, 0);
		$dateto = strtotime($dateto, 0);
	}
	$difference = $dateto - $datefrom; // Difference in seconds

	switch($interval) {
 		case 'yyyy': // Number of full years
 			$years_difference = floor($difference / 31536000);
 			if (mktime(date("H", $datefrom), date("i", $datefrom), date("s", $datefrom), date("n", $datefrom), date("j", $datefrom), date("Y", $datefrom)+$years_difference) > $dateto) {
 				$years_difference--;
 			}
 			if (mktime(date("H", $dateto), date("i", $dateto), date("s", $dateto), date("n", $dateto), date("j", $dateto), date("Y", $dateto)-($years_difference+1)) > $datefrom) {
 				$years_difference++;
 			}
 			$datediff = $years_difference;
 			break;
  
 		case "q": // Number of full quarters
 			$quarters_difference = floor($difference / 8035200);
 			while (mktime(date("H", $datefrom), date("i", $datefrom), date("s", $datefrom), date("n", $datefrom)+($quarters_difference*3), date("j", $dateto), date("Y", $datefrom)) < $dateto) {
 				$months_difference++;
			}
 			$quarters_difference--;
 			$datediff = $quarters_difference;
 			break;
  
 		case "m": // Number of full months
 			$months_difference = floor($difference / 2678400);
 			while (mktime(date("H", $datefrom), date("i", $datefrom), date("s", $datefrom), date("n", $datefrom)+($months_difference), date("j", $dateto), date("Y", $datefrom)) < $dateto) {
 				$months_difference++;
 			}
 			$months_difference--;
 			$datediff = $months_difference;
 			break;
  
		case 'y': // Difference between day numbers
 			$datediff = date("z", $dateto) - date("z", $datefrom);
 			break;
  
 		case "d": // Number of full days  
 			$datediff = floor($difference / 86400);
 			break;
 			
 		case "w": // Number of full weekdays  
 			$days_difference = floor($difference / 86400);
 			$weeks_difference = floor($days_difference / 7); // Complete weeks
 			$first_day = date("w", $datefrom);
 			$days_remainder = floor($days_difference % 7);
 			$odd_days = $first_day + $days_remainder; // Do we have a Saturday or Sunday in the remainder?
 			if ($odd_days > 7) { // Sunday
 				$days_remainder--;
 			}
 			if ($odd_days > 6) { // Saturday
 				$days_remainder--;
 			}
 			$datediff = ($weeks_difference * 5) + $days_remainder;
 			break;
  
		case "ww": // Number of full weeks  
 			$datediff = floor($difference / 604800);
 			break;
 			  
 		case "h": // Number of full hours  
 			$datediff = floor($difference / 3600);
 			break;
  
 		case "n": // Number of full minutes
 			$datediff = floor($difference / 60);
 			break;
 		
 		default: // Number of full seconds (default)
 			$datediff = $difference;
 			break;
	}

	return $datediff;
	  
}

function armarPaginador($countReg, $pageNum, $rowsPerPage, $strUrlLink)
{
	$strHtmlFoot = "";
	$strHtmlespacio = "&nbsp;";
	
	$cantPaginas = ceil($countReg/$rowsPerPage);

	if ($cantPaginas == 0) $cantPaginas++;

	if($cantPaginas == 1){
		$strHtmlFoot .= "<a href='javascript:;'>&lt;</a>" . $strHtmlespacio;
		$strHtmlFoot .= "<a class='selected' href='javascript:;'>1</a>" . $strHtmlespacio;
		$strHtmlFoot .= "<a href='javascript:;'>&gt;</a>" . $strHtmlespacio;
	}
	else{

		if ($pageNum != 1)
			$strHtmlFoot .= "<a href='javascript:;' onclick='return " . $strUrlLink . "(" . ($pageNum - 1) . ");'>&lt;</a>" . $strHtmlespacio;
		else
			$strHtmlFoot .= "<a href='javascript:;'>&lt;</a>" . $strHtmlespacio;

		for($iLoop = 1; $iLoop <= $cantPaginas; $iLoop++)
        {
		    if ($iLoop == $pageNum)
		    	$strHtmlFoot .= "<a class='selected' href='javascript:;'>" . $iLoop . "</a>" . $strHtmlespacio;
		    else
		    	$strHtmlFoot .= "<a href='javascript:;' onClick='return " . $strUrlLink . "(" . $iLoop . ");'>" . $iLoop . "</a>" . $strHtmlespacio;
        }
		
		if ($pageNum != $cantPaginas)
			$strHtmlFoot .= "<a href='javascript:;' onclick='return " . $strUrlLink . "(" . ($pageNum + 1) . ");'>&gt;</a>" . $strHtmlespacio;
		else
			$strHtmlFoot .= "<a href='javascript:;'>&gt;</a>" . $strHtmlespacio;
	}
	
	return "<div id='pager'>P&aacute;gina" . $strHtmlespacio . $strHtmlFoot . "</div>";
}

function empaquetarFecha($strFecha)
{
	list($day, $month, $year) = split('[/.-]', $strFecha);
	
	return $year . $month . $day;
}

function desempaquetarFecha($strFecha)
{
	if($strFecha=="")
		return "";
	else
		return substr($strFecha, 6) . "/" . substr($strFecha, 4, 2) . "/" . substr($strFecha, 0, 4);
}

function guionBarraReves($strFecha)
{
	list($year, $month, $day) = split('-', $strFecha);
	
	return $day . "/" . $month . "/" . $year;
}

function html_encode($var)
{
	return htmlentities($var, ENT_QUOTES, 'UTF-8') ;
}

function random_color(){
    mt_srand((double)microtime()*1000000);
    $c = '';
    while(strlen($c)<6){
        $c .= sprintf("%02X", mt_rand(0, 255));
    }
    return $c;
}

function formatoFecha($strFecha)
{
	if($strFecha=="")
		return "";
	else {
		$fecha = date_create($strFecha);
		return date_format($fecha, 'd/m/Y');
	}
}

function genPWD($length = 8)
{

  // espezamos con una clave en blanco
  $password = "";

  // definimos posibles caracteres
  $possible = "0123456789bcdfghjkmnpqrstvwxyz"; 
    
  // seteamos un contador
  $i = 0; 
    
  // agregamos caracteres aleatorios a $password hasta que alcanzamos a $length
  while ($i < $length) { 
    // agarramos un caracter aleatorio desde los posibles
    $char = substr($possible, mt_rand(0, strlen($possible)-1), 1);
        
    // si el caracter ya lo tenemos en la clave no lo queremos
    if (!strstr($password, $char)) { 
      $password .= $char;
      $i++;
    }
  }

  // listo!
  return $password;
}

function mailValido($email) 
{
	// Primero, checamos que solo haya un s’mbolo @, y que los largos sean correctos
  if (!ereg("^[^@]{1,64}@[^@]{1,255}$", $email)) 
	{
		// correo inv‡lido por nœmero incorrecto de caracteres en una parte, o nœmero incorrecto de s’mbolos @
    return false;
  }
  // se divide en partes para hacerlo m‡s sencillo
  $email_array = explode("@", $email);
  $local_array = explode(".", $email_array[0]);
  for ($i = 0; $i < sizeof($local_array); $i++) 
	{
    if (!ereg("^(([A-Za-z0-9!#$%&'*+/=?^_`{|}~-][A-Za-z0-9!#$%&'*+/=?^_`{|}~\.-]{0,63})|(\"[^(\\|\")]{0,62}\"))$", $local_array[$i])) 
		{
      return false;
    }
  } 
  // se revisa si el dominio es una IP. Si no, debe ser un nombre de dominio v‡lido
	if (!ereg("^\[?[0-9\.]+\]?$", $email_array[1])) 
	{ 
     $domain_array = explode(".", $email_array[1]);
     if (sizeof($domain_array) < 2) 
		 {
        return false; // No son suficientes partes o secciones para se un dominio
     }
     for ($i = 0; $i < sizeof($domain_array); $i++) 
		 {
        if (!ereg("^(([A-Za-z0-9][A-Za-z0-9-]{0,61}[A-Za-z0-9])|([A-Za-z0-9]+))$", $domain_array[$i])) 
				{
           return false;
        }
     }
  }
  return true;
}

function randomPrefix($length) 
{ 
	$random= "";
	srand((double)microtime()*1000000);
	
	$data  = "AbcDE123IJKLMN67QRSTUVWXYZ"; 
	$data .= "aBCdefghijklmn123opq45rs67tuv89wxyz"; 
	$data .= "0FGH45OP89";
	
	for($i = 0; $i < $length; $i++) 
	{ 
		$random .= substr($data, (rand()%(strlen($data))), 1); 
	}
return $random; 
}

function findexts($filename) 
{ 
	$filename = strtolower($filename) ; 
	$exts = split("[/\\.]", $filename) ; 
	$n = count($exts)-1; 
	$exts = $exts[$n]; 

	return $exts; 
}

function getDescripcionEstado($idEstado)
{
	require("selectLanguaje.php");
    
    $DB = NewADOConnection('mysqlt');
	$DB->Connect();
    
    $rs = $DB->Execute("select descripcion from estados_incidentes where idEstado = '" . $idEstado . "'");
    
    if(!$rs->EOF)
    	return $a_languages[$rs->fields("descripcion")];
    
    return "";
}

function exportarExcel($arrayTituloCampos, $arrayValoresCampos, $arrayTipoDatosCampos,  $nombreArchivo, $strSQL)
{
    /* funcion creada por Juan Pablo Ochoa De La Maza - 2008 - SisdevSoft -
	 * Permite crear un listado simple de excel
	 * ayuda:
	 * $arrayTituloCampos = array compuesto por los titulos de la tabla 
	 * $arrayValoresCampos = array compuesto por los campos de la tabla de la db de donde se leeran los datos 
	 * $arrayTipoDatosCampos = array compuesto por los tipos de dato para cada columna. string,date,date_completo, email  
	 * $nombreArchivo = nombre del archivo de salida (sin extension) 
	 * $strSQL = string con la consulta a ejecutar.
	 */

	require_once("excel/excel_write/class.writeexcel_workbook.inc.php");
	require_once("excel/excel_write/class.writeexcel_worksheet.inc.php");
	require("selectLanguaje.php");
  
    $fname = tempnam("tmp", $nombreArchivo . ".xls");
	$workbook = new writeexcel_workbook($fname);
	$workbook->set_tempdir("tmp");
    $worksheet =& $workbook->addworksheet();
  
    $DB = NewADOConnection('mysqlt');
	$DB->Connect();
	//$DB->debug=true;
    
    $rs = $DB->Execute($strSQL);
    
    $header =& $workbook->addformat();
    $header->set_bold();
    $header->set_size(10);
    $header->set_bg_color('silver');         
  
        $corte = count($arrayTituloCampos); 
        for($id = 0; $id < $corte ; $id++)
        {
              $worksheet->write(0, $id, $arrayTituloCampos[$id], $header);
        }
  
        $i=1;
  
        while (!$rs->EOF)
        {
        	for($ide=0 ; $ide < $corte; $ide++)
            {
            	if($arrayTipoDatosCampos[$ide] == 'stringMultiIdioma')
        	{
            	$worksheet->write($i, $ide, trim($a_languages[$rs->fields($arrayValoresCampos[$ide])]));
        	}
        	
        	if($arrayTipoDatosCampos[$ide] == 'string')
        	{
            	$worksheet->write($i, $ide, trim($rs->fields($arrayValoresCampos[$ide])));
        	}
        
        	if($arrayTipoDatosCampos[$ide] == 'date')
        	{
            	$fecha_armada='';
             
             	for($ww=0 ; $ww < 3; $ww++)
             	{
                	if($ww==0)
                         $fecha_armada .= $rs->fields($arrayValoresCampos[$ide]."Day")."/";
                   
               		if($ww==1)
                         $fecha_armada .= $rs->fields($arrayValoresCampos[$ide]."Month")."/";
                   
                   	if($ww==2)
                         $fecha_armada .= $rs->fields($arrayValoresCampos[$ide]."Year");
             	}
             	$worksheet->write($i, $ide, $fecha_armada);
        	}
        
        	if($arrayTipoDatosCampos[$ide] == 'email')
        	{
            	$email_='';
             
             	for($ww=0 ; $ww < 2; $ww++)
             	{
                	if($ww==0)
                    	$email_ .= $rs->fields($arrayValoresCampos[$ide]."User")."@";
                   
                   	if($ww==1)
                        $email_ .= $rs->fields($arrayValoresCampos[$ide]."Domain");

             	}
             	$worksheet->write($i, $ide, $email_);
        	}
        	
        	if($arrayTipoDatosCampos[$ide] == 'date_completo')
        	{
            	$fecha_armada='';
                         
                    $fecha_armada = desempaquetarFecha($rs->fields($arrayValoresCampos[$ide]));
                         
                    $worksheet->write($i, $ide, $fecha_armada);
              	}
        	}           
  
            $i++;
            $rs->MoveNext();
        }
        
        $workbook->close();
  
    if(isset($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'],'MSIE'))
	{
		header("Pragma: public");
	    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	}
	header("Content-Length: ".@filesize($fname));
	header("Content-Type: application/x-msexcel; name=\"".$nombreArchivo. ".xls\"");
	header("Content-Disposition: inline; filename=\"".$nombreArchivo. ".xls\"");
	$fh=fopen($fname, "rb");
    fpassthru($fh);
    unlink($fname);
}

function makeSQLQuerySearch($aColumns, $sIndexColumn, $sTable, $sJoinTable, $sJoinColumns, &$iFilteredTotal, &$iTotal, $sColumnState = "", $otherCondition, $groupBy)
{
	require("includes/constants.php");
	
	$DB = NewADOConnection('mysqlt');
	$DB->Connect();
	$DB->Execute("SET NAMES utf8;");
	//$DB->debug=true;
	
	/* 
	 * Paging
	 */
	$sLimit = "";
	if ( isset( $_GET['iDisplayStart'] ) && $_GET['iDisplayLength'] != '-1' )
	{
		$sLimit = "LIMIT ".mysql_real_escape_string( $_GET['iDisplayStart'] ).", ".
			mysql_real_escape_string( $_GET['iDisplayLength'] );
	}
	
	
	/*
	 * Ordering
	 */
	$sOrder = "";
	if ( isset( $_GET['iSortCol_0'] ) )
	{
		$sOrder = "ORDER BY  ";
		for ( $i=0 ; $i<intval( $_GET['iSortingCols'] ) ; $i++ )
		{
			if ( $_GET[ 'bSortable_'.intval($_GET['iSortCol_'.$i]) ] == "true" )
			{
				$sOrder .= $aColumns[ intval( $_GET['iSortCol_'.$i] ) ]." ".
				 	mysql_real_escape_string( $_GET['sSortDir_'.$i] ) .", ";
			}
		}
		
		$sOrder = substr_replace( $sOrder, "", -2 );
		if ( $sOrder == "ORDER BY" )
		{
			$sOrder = "";
		}
	}
	
	
	/* 
	 * Filtering
	 * NOTE this does not match the built-in DataTables filtering which does it
	 * word by word on any field. It's possible to do here, but concerned about efficiency
	 * on very large tables, and MySQL's regex functionality is very limited
	 */
	$sWhere = "";
	if ( isset($_GET['sSearch']) && $_GET['sSearch'] != "" )
	{
		$sWhere = "WHERE (";
		for ( $i=0 ; $i<count($aColumns) ; $i++ )
		{
			$sWhere .= " ".$aColumns[$i]." LIKE '%".mysql_real_escape_string( $_GET['sSearch'] )."%' OR ";
		}
		$sWhere = substr_replace( $sWhere, "", -3 );
		$sWhere .= ')';
	}
	
	/* Individual column filtering */
	for ( $i=0 ; $i<count($aColumns) ; $i++ )
	{
		if ( isset($_GET['bSearchable_'.$i]) && $_GET['bSearchable_'.$i] == "true" && $_GET['sSearch_'.$i] != '' )
		{
			if ( $sWhere == "" )
				$sWhere = "WHERE ";
			else
				$sWhere .= " AND ";
			$sWhere .= " ".$aColumns[$i]." LIKE '%".mysql_real_escape_string($_GET['sSearch_'.$i])."%' ";
		}
	}
	if($sColumnState!="")
	{
		if($sWhere == "")
			$sWhere .= "WHERE ".$sColumnState." <> '".$stateErase."'";
		else 
			$sWhere .= " AND ".$sColumnState." <> '".$stateErase."'";
	}
	if($otherCondition!="")
	{
		if($sWhere == "")
			$sWhere .= "WHERE ".$otherCondition." ";
		else 
			$sWhere .= " AND ".$otherCondition." ";
	}
	
	//Group By
	$groupBy = "GROUP BY ".$groupBy;
	
	/*
	 * SQL queries
	 * Get data to display
	 */
	$joinTableColumn = ($sJoinTable != "") ? "INNER JOIN $sJoinTable ON $sJoinColumns" : "";
	$sQuery = "
		SELECT SQL_CALC_FOUND_ROWS ".str_replace(" , ", " ", implode(", ", $aColumns))."
		FROM   $sTable
		$joinTableColumn
		$sWhere
		$groupBy
		$sOrder
		$sLimit
		";

	$rResult = $DB->Execute($sQuery);
	
	/* Data set length after filtering */
	$sQuery = "
		SELECT FOUND_ROWS()
	";
	$rResultFilterTotal = $DB->Execute($sQuery);
	$iFilteredTotal = $rResultFilterTotal->fields[0];
	
	/* Total data set length */
	$sQuery = "
		SELECT COUNT(`".$sIndexColumn."`)
		FROM   $sTable
	";
	
	$rResultTotal = $DB->Execute($sQuery);
	$iTotal = $rResultTotal->fields[0];
	
	return $rResult;
	
}
?>
