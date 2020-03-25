<?php
/**
 *  Por Federico Pisarello - SisDev Software (c) 2012 - Buenos Aires, Argentina.
 * 	fpisarello@sisdevsoft.com
 */

require("includes/funciones.inc.php");

session_start();
if(!isset($_SESSION['userName']))
	exit();

function setDBBuses()
{
	require("includes/constants.php");
	
	$DB = NewADOConnection('mysqlt');
	$DB->Connect();
	$DB->Execute("SET NAMES utf8;");
	//$DB->debug=true;
	
	$prov = $_REQUEST['provincia'];
	$lineaNro = $_REQUEST['linea'];
	
	if($prov == "")
	{
		$arrJSON->status = "ERROR";
		$arrJSON->msg = 'Error en la Selecci&oacute;n de la Provincia';
		return json_encode($arrJSON);
	}
	
	if($lineaNro == "")
	{
		$arrJSON->status = "ERROR";
		$arrJSON->msg = 'Linea de Colectivo Incorrecta';
		return json_encode($arrJSON);
	}
	
	$url = "http://movil.omnilineas.com.ar/$prov/colectivo/linea-$lineaNro/";
	$pagina_inicio = file_get_contents($url);
	
	$arrLinea = array();
	foreach(preg_split("/((\r?\n)|(\r\n?))/", $pagina_inicio) as $line){
		if(strpos($line, "myarr") !== false) {
			$arr = explode("myarr", $line);
			foreach ($arr as $geo){
				if(strpos($geo, "str2garr") !== false) {
					$initPos = strpos($geo, "'")+1;
					$finishPos = strrpos($geo, "'")-1;
	
					array_push($arrLinea, str2garr(substr($geo, $initPos, $finishPos-$initPos)));
				}
			}
		}
	}
	
	$DB->StartTrans();
	
	$strSQL = "INSERT INTO map_buses ( ";
	$strSQL .= "	linea, ";
	$strSQL .= "	color ";
	$strSQL .= "	) VALUES ( ";
	$strSQL .= "	'".$lineaNro."', ";
	$strSQL .= "	'#".random_color()."' ";
	$strSQL .= "	)";
	
	$DB->Execute($strSQL);
	
	$ID = $DB->Insert_ID();

	$nroRecorrido = 1;
	foreach ($arrLinea as $aLinea) {
		$encoded = Polyline::Encode($aLinea);
		
		$strSQL = "INSERT INTO map_buses_recorridos ( ";
		$strSQL .= "	idMapBuses, ";
		$strSQL .= "	idRecorrido, ";
		$strSQL .= "	recorrido ";
		$strSQL .= "	) VALUES ( ";
		$strSQL .= "	'".$ID."', ";
		$strSQL .= "	'".($nroRecorrido++)."', ";
		$strSQL .= "	'".mysql_real_escape_string($encoded)."' ";
		$strSQL .= "	)";
		
		
		
		$DB->Execute($strSQL);
	}
	
	if (!$DB->CompleteTrans())
	{
		$arrJSON->status = "ERROR";
		$arrJSON->msg = "Ocurri&oacute; un error al grabar el/los Recorrido/s de la Linea $lineaNro";
	}
	else
	{
		$arrJSON->status = "OK";
		$arrJSON->msg = "El/Los recorrido/s de la Linea $lineaNro se guard&oacute; correctamente";
	}
	return json_encode($arrJSON);
}

function str2garr($recostr) {
	$myres = array();
	while (strlen($recostr) >= 18) {
		$mylatlng = dcod(substr($recostr,0,18));
		$myres[] = $mylatlng;
		$recostr = substr($recostr, 18);
	}
	return $myres;
}

function dcod($pdat) {
	if (strlen($pdat) != 18) 
		return false;
	
	$datlat = substr($pdat,0,9);
	$datlng = substr($pdat,9,18);

	$slat = substr($datlat,0,1);
	$slng = substr($datlng,0,1);
	$ablat = substr($datlat,1,2);
	$ablng = substr($datlng,1,2);
	$cdelat = substr($datlat,3,3);
	$cdelng = substr($datlng,3,3);
	$fghlat = substr($datlat,6,3);
	$fghlng = substr($datlng,6,3);
			
	$rslat = 1;
	if ($slat == "k") $rslat = -1;
	$rslng = 1;
	if ($slng == "k") $rslng = -1;
			
	$rablat = intval($ablat,16);
	$rablng = intval($ablng,16);
	
	 

	$rcdelat = intval($cdelat,16);
	$rcdelng = intval($cdelng,16);

	$rfghlat = intval($fghlat,16);
	$rfghlng = intval($fghlng,16);

		
	$reslat = $rslat * ($rablat + $rcdelat/1000 + $rfghlat/1000000);
	$reslng = $rslng * ($rablng + $rcdelng/1000 + $rfghlng/1000000);

	return array($reslat, $reslng);
}

class Polyline
{
	/**
	 * @var array $polylines
	 * @deprecated
	 * @ignore
	 */
	private $polylines = array();

	/**
	 * Default precision level of 1e-5.
	 *
	 * Overwrite this property in extended class to adjust precision of numbers.
	 * !!!CAUTION!!!
	 * 1) Adjusting this value will not guarantee that third party
	 *    libraries will understand the change.
	 * 2) Float point arithmetic IS NOT real number arithmetic. PHP's internal
	 *    float precision may contribute to undesired rounding.
	 *
	 * @var int $precision
	*/
	protected static $precision = 5;

	/**
	 * @var Polyline $instance
	 * @deprecated
	 * @ignore
	 */
	private static $instance;

	public function __construct()
	{
		// Overloading bug #11
	}

	/**
	 * Static instance method
	 *
	 * @return Polyline
	 * @deprecated
	 * @codeCoverageIgnore
	 * @ignore
	 */
	public static function Singleton()
	{
		trigger_error('Polyline::Singleton deprecated.', E_USER_DEPRECATED);
		return self::$instance instanceof self ? self::$instance : self::$instance = new self;
	}

	/**
	 * Magic method for supporting wildcard getters
	 *
	 * @let {Node} be the name of the polyline
	 * @method get{Node}Points(   ) //=> array of points for polyline "Node"
	 * @method get{Node}Encoded(  ) //=> encoded string  for polyline "Node"
	 * @method getPoints( "{Node}") //=> array of points for polyline "Node"
	 * @method getEncoded("{Node}") //=> encoded string  for polyline "Node"
	 * @deprecated
	 * @codeCoverageIgnore
	 * @ignore
	 */
	public function __call($method,$arguments)
	{
		trigger_error('Polyline::__call('.$method.') deprecated.', E_USER_DEPRECATED);
		$return = null;
		if (preg_match('/^get(.+?)(points|encoded)$/i', $method, $matches)) {
			list($all,$node,$type) = $matches;
			return $this->getPolyline(strtolower($node), strtolower($type));
		} elseif (preg_match('/^get(points|encoded)$/i', $method, $matches)) {
			list($all,$type) = $matches;
			$node = array_shift($arguments);
			return $this->getPolyline(strtolower($node), strtolower($type));
		} else {
			throw new BadMethodCallException();
		}
		return $return;
	}

	/**
	 * Polyline getter
	 * @param string $node
	 * @param string $type
	 * @return mixed
	 * @deprecated
	 * @codeCoverageIgnore
	 * @ignore
	 */
	public function getPolyline($node, $type)
	{
		trigger_error('Polyline::getPolyline deprecated.', E_USER_DEPRECATED);
		$node = strtolower($node);
		$type = in_array($type, array('points','encoded')) ? $type : 'encoded';
		return isset($this->polylines[$node])
		? $this->polylines[$node][$type]
		: ($type =='points' ? array() : null);
	}

	/**
	 * General purpose data method
	 *
	 * @param string polyline name
	 * @param mixed [ string | array ] optional
	 * @return array
	 * @deprecated
	 * @codeCoverageIgnore
	 * @ignore
	 */
	public function polyline()
	{
		trigger_error('Polyline::polyline deprecated.', E_USER_DEPRECATED);
		$arguments = func_get_args();
		$return = null;
		switch (count($arguments)) {
			case 2:
				list($node,$value) = $arguments;
				$isArray = is_array($value);
				$return = $this->polylines[strtolower($node)] = array(
						'points'  => $isArray ? self::Flatten($value) : self::Decode($value),
						'encoded' => $isArray ? self::Encode($value) : $value
				);
				$return = $return[$isArray ? 'encoded' : 'points' ];
				break;
			case 1:
				$node = strtolower((string)array_shift($arguments));
				$return = isset($this->polylines[$node])
				? $this->polylines[$node]
				: array( 'points' => null, 'encoded' => null );
				break;
		}
		return $return;
	}

	/**
	 * Retrieve list of polyline within singleton
	 *
	 * @return array polylines
	 * @deprecated
	 * @codeCoverageIgnore
	 * @ignore
	 */
	public function listPolylines()
	{
		trigger_error('Polyline::listPolylines deprecated.', E_USER_DEPRECATED);
		return $return = array_keys($this->polylines);
	}

	/**
	 * Apply Google Polyline algorithm to list of points
	 *
	 * @param array $points
	 * @param integer $precision optional
	 * @return string encoded string
	 */
	final public static function Encode($points)
	{
		$points = self::Flatten($points);
		$encodedString = '';
		$index = 0;
		$previous = array(0,0);
		foreach ($points as $number) {
			$number = (float)($number);
			$number = (int)round($number * pow(10, static::$precision));
			$diff = $number - $previous[$index % 2];
			$previous[$index % 2] = $number;
			$number = $diff;
			$index++;
			$number = ($number < 0) ? ~($number << 1) : ($number << 1);
			$chunk = '';
			while ($number >= 0x20) {
				$chunk .= chr((0x20 | ($number & 0x1f)) + 63);
				$number >>= 5;
			}
			$chunk .= chr($number + 63);
			$encodedString .= $chunk;
		}
		return $encodedString;
	}

	/**
	 * Reverse Google Polyline algorithm on encoded string
	 *
	 * @param string $string
	 * @param integer $precision optional
	 * @return array points
	 */
	final public static function Decode($string)
	{
		$points = array();
		$index = $i = 0;
		$previous = array(0,0);
		while ($i < strlen($string)) {
			$shift = $result = 0x00;
			do {
				$bit = ord(substr($string, $i++)) - 63;
				$result |= ($bit & 0x1f) << $shift;
				$shift += 5;
			} while ($bit >= 0x20);

			$diff = ($result & 1) ? ~($result >> 1) : ($result >> 1);
			$number = $previous[$index % 2] + $diff;
			$previous[$index % 2] = $number;
			$index++;
			$points[] = $number * 1 / pow(10, static::$precision);
		}
		return $points;
	}

	/**
	 * Reduce multi-dimensional to single list
	 *
	 * @param array $array
	 * @return array flattened
	 */
	final public static function Flatten($array)
	{
		$flatten = array();
		array_walk_recursive(
		$array, // @codeCoverageIgnore
		function ($current) use (&$flatten) {
			$flatten[] = $current;
		}
		);
		return $flatten;
	}

	/**
	 * Concat list into pairs of points
	 *
	 * @param array $list
	 * @return array pairs
	 */
	final public static function Pair($list)
	{
		$pairs = array();
		if (!is_array($list)) {
			return $pairs;
		}
		do {
			$pairs[] = array(
					array_shift($list),
					array_shift($list)
			);
		} while (!empty($list));
		return $pairs;
	}
}

switch($_REQUEST['actionOfForm'])
{
	case "setDBBuses":
		echo setDBBuses();
		break;
}

?>