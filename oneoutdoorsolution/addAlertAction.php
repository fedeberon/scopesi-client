
<?php
function fotosAlertShow()
{


	$dia = $_REQUEST['dia'];
	$includedExtensions = array ('jpg', 'gif', 'png');

	$dirImages = 'images/adalerts/'.$dia;
	if(file_exists($dirImages)) {
		if ($filesFotosAlert = opendir($dirImages)) {
			while (false !== ($image = readdir($filesFotosAlert))) {
				if($image != "." && $image != "..") {
					$extn = explode('.', $image);
		 		    $extn = array_pop($extn);
					if (in_array(strtolower($extn),$includedExtensions)) {
						$arrImages[] = array("image" => $dirImages."/".$image);
						// $i++;
					}
				}
	    	}
			return json_encode($arrImages);
		}
		else {
			return json_encode(array());
		}
	}
	else {
		return json_encode(array());
	}
}





switch($_REQUEST['actionOfForm'])
{

	case "fotosAlertShow":
		echo fotosAlertShow();
		break;

}
?>
