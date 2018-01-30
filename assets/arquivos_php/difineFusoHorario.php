<?php
session_start();

if(!isset($_SESSION['timezone']) and $_SERVER['REQUEST_METHOD'] == 'POST'){
	
	class Timezone extends DateTimeZone
	{
		public static function tzOffsetToName($offset, $isDst = null)
		{
			$offset *= 3600;
			$zone = timezone_name_from_abbr('', $offset, $isDst);

			if ($zone == false)
			{
				foreach(timezone_abbreviations_list() as $abbr)
				{
					foreach($abbr as $city)
					{
						if ((bool)$city['dst'] == (bool)$isDst && strlen($city['timezone_id']) > 0 && $city['offset'] == $offset)
						{
							$zone = $city['timezone_id'];
							break;
						}
					}
					if($zone != false)
						break;
				}
			}
			return $zone;
		}
	}
	
	$offset = $_POST['offset'];
	$dst = $_POST['dst'];
	$Dtz = new Timezone(Timezone::tzOffsetToName($offset,$dst));

	include 'todasFuncoes.php';
	$_SESSION['timezone'] = $Dtz->getName();
	echo 	$_SESSION['timezone'];
}
?>