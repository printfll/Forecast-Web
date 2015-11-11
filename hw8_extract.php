
<?php
	 
error_reporting(E_ERROR);
define(FORECAST_API, "64fc585d500232717d682f3b5126505e");

		header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
	
		$req=$_GET["req"];
		$address=$_GET['address'];
		$degree=$_GET['degree'];
		$result= extractXML($address,$degree);
		echo "$result";
		
		
	
 
	function extractXML($url,$degree){
		
		//$api="https://maps.google.com/maps/api/geocode/xml?";
		$api="https://maps.googleapis.com/maps/api/geocode/xml?";
		
		$query=array(
			'address'=>$url,
			'key'=>'AIzaSyDQueJ2wi4jkvVpg0DoKyNh6SweoOlbeo0'
		);

		$link=$api.http_build_query($query);
		$xml=simplexml_load_file($link);
		//$xml = str_replace(array("\n", "\r", "\t"), '', $xml);
		$status=$xml->status;
		if ($status=='OK') {
			$location=$xml->result->geometry->location;
			$lat=$location->lat;
			$lng=$location->lng;

			
			return extractJson($lat,$lng,$degree);
		}else if ($status=='ZERO_RESULTS'&&isset($_POST['submit'])) {

				$result=array("status"=>"ZERO_RESULTS");
				return json_encode($result);
		}
		
	}

	function extractJson($lat, $lng, $degree){
		$api="https://api.forecast.io/forecast/";
		if ($degree=="Celsius") {
			$units="si";
		}else{
			$units="us";
		}
		$query=array(
			'units'=>$units,
			'exclude'=>'flags',
		);
		$link=$api.FORECAST_API.'/'.$lat.','.$lng.'?'.http_build_query($query);
		
		$content=file_get_contents($link);
		$result = json_decode($content,true);
		
		$current_result=parseCurrentJson($result,$units);
		$hourly_result=parseHourlyJson($result,$units);
		$days_result=parseDaysJson($result,$units);
		$result=array(
			"status"=>"OK",
			"lat"=>"".$lat,
			"lng"=>"".$lng,
			"link"=>$link,
			"current_result"=>$current_result,
			"hour_result"=>$hourly_result,
			"day_result"=>$days_result
			
		);
		return json_encode($result);
	}


	function dealWithImg($icon){
		$index="http://cs-server.usc.edu:42766/hw8/";
		switch ($icon) {
			case 'clear-day':
				$icon_img.="clear";
				break;
			case 'clear-night':
				$icon_img.="clear_night";
				break;
			case 'partly-cloudy-day':
				$icon_img.="cloud_day";
				break;
			case 'partly-cloudy-night':
				$icon_img.="cloud_night";
				break;
			default:
				$icon_img.=$icon;

				break;
		}
		return $index.$icon_img.".png";
	}

	function dealWithHumidity($humidity){
		$humidity_display=intval($humidity*100);
		$humidity_display.="%";
		return $humidity_display;
	}

	function dealWithIntensity($precipIntensity){
		if($precipIntensity>=0&&$precipIntensity<0.002){
			$precipIntensity_display="None";
		}else if($precipIntensity>=0.002&&$precipIntensity<0.017){
			$precipIntensity_display="Very Light";
		}else if($precipIntensity>=0.017&&$precipIntensity<0.1){
			$precipIntensity_display="Light";
		}else if($precipIntensity>=0.1&&$precipIntensity<0.4){
			$precipIntensity_display="Moderate";
		}else if($precipIntensity>=0.4){
			$precipIntensity_display="Heavy";
		}
		return $precipIntensity_display;
	}

	function dealWithDecimal($var){
		return number_format($var, 2, '.', '');
	}

	function parseCurrentJson($info,$units){
		//icon,precipIntensity{0, 0.002, 0.0017, 0.1, 0.4},precipProbability*100%,windSpeed(integer) mph,dewPoint(integer)
		//humidity*100%, visibility(integer$offset=$info['offset'];
		
		$timezone=$info['timezone'];
		
		$currently=$info['currently'];
		
		$summary=$currently['summary'];
		$temperature=intval($currently['temperature']);

		$icon=$currently['icon'];
		
		$precipIntensity=$currently['precipIntensity'];
		$precipIntensity_display=dealWithIntensity($precipIntensity);
		

		$precipProbability=$currently['precipProbability']*100;
		$precipProbability_display=$precipProbability."%";

		$windSpeed=dealWithDecimal($currently['windSpeed']);
		$dewPoint=dealWithDecimal($currently['dewPoint']);
		$visibility=dealWithDecimal($currently['visibility']);


		$daily=$info['daily'];
		$data=$daily['data'];
		foreach ($data as $day) {
			$temperatureMin=intval($day['temperatureMin']);
			$temperatureMax=intval($day['temperatureMax']);
			break;
		}
		
		if($units=="us"){
			$temperature.="°F";
			$temperatureMin.="°F";
			$temperatureMax.="°F";
			$windSpeed_display=$windSpeed."mph";
			$dewPoint_display=$dewPoint."°F";
			$visibility_display=$visibility."mi";

		}else{
			$temperature.="°C";
			$temperatureMin.="°C";
			$temperatureMax.="°C";
			$windSpeed_display=$windSpeed."m/s";
			$dewPoint_display=$dewPoint."°C";
			$visibility_display=$visibility."km";

		}
		
		$humidity=$currently['humidity'];
		$humidity_display=dealWithHumidity($humidity);

		$daily=$info['daily'];$data=$daily['data'];$time=$data['0'];
		date_default_timezone_set($timezone);
		$sunriseTime=$time['sunriseTime']; $risetime=date('h:i A', $sunriseTime);
		$sunsetTime=$time['sunsetTime'];$settime=date('h:i A', $sunsetTime);
		$icon_img=dealWithImg($icon);
		
		
		
		$result=array(
			"summary"=>$summary,
			"icon"=>$icon,
			"icon_img"=>$icon_img,
		 	"temperature" =>$temperature,
		 	"temperatureMin"=>$temperatureMin,
		 	"temperatureMax"=>$temperatureMax,
			"precipIntensity" => $precipIntensity_display,
			"precipProbability" => $precipProbability_display,
			"windSpeed"=>$windSpeed_display,
			"dewPoint"=>$dewPoint_display,
			"humidity"=>$humidity_display,
			"visibility"=>$visibility_display,
			"risetime"=>$risetime,
			"settime"=>$settime,
		);
		return $result;
	}

	function parseHourlyJson($info,$units){
		//Hourly: Time,Summary,Cloud Cover(%),Temp(0.00),
		//Detail:Wind, Humidity, Visibility, Pressure(F:mb, C:hPa)
		$timezone=$info['timezone'];
		date_default_timezone_set($timezone);
		$hourly=$info['hourly'];
		$data=$hourly['data'];
		$i=0;$array=array();
		foreach ($data as $hour) {
			if ($i==0) {
				$i++;
				continue;
			}
			$time=date('h:i A',$hour['time']);
			$icon=$hour['icon'];
			$summary=dealWithImg($icon);
			$cloudCover=intval($hour['cloudCover']*100);
			$cloudCover.="%";
			$temperature=dealWithDecimal($hour['temperature']); 
			$wind=$hour['windSpeed'];
			$humidity=dealWithHumidity($hour['humidity']);
			$visibility=dealWithDecimal($hour['visibility']);
			$pressure=$hour['pressure'];
			$windSpeed=dealWithDecimal($hour['windSpeed']);

			if($units=="us"){
				$temperature.="°F";
				$windSpeed_display=$windSpeed."mph";
				$pressure.="mb";
				$visibility_display=$visibility."mi";

			}else{
				$temperature.="°C";
				$windSpeed_display=$windSpeed."m/s";
				$pressure.="hPa";
				$visibility_display=$visibility."km";
			}
			$result=array(
				"tag"=>"".$i,
				"time"=>$time,
				"summary"=>$summary,
				"icon"=>$icon,
				"cloudCover"=>$cloudCover,
				"temperature"=>$temperature,
				"windspeed"=>$windSpeed_display,
				"humidity"=>$humidity,
				"visibility"=>$visibility_display,
				"pressure"=>$pressure
			);
			array_push($array, $result);
			$i++;
			if($i==25){
				return $array;
			}
		}
		
	}

	function parseDaysJson($info, $units){
		//7 day: Day(e.g:Wednesday), Month Date(Nov 10), Icon image, Min Temp&Max Temp(temperatureMin,integer), 
		//Detail:Wind, Humidity, Visibility, Pressure(F:mb, C:hPa)
		$timezone=$info['timezone'];
		date_default_timezone_set($timezone);
		$daily=$info['daily'];
		$data=$daily['data'];
		$i=0;$array=array();
		foreach ($data as $day) {
			if ($i==0) {
				$i++;
				continue;
			}
			$dayweek=date('l',$day['time']);
			$month=date('M d',$day['time']);
			$icon_img=dealWithImg($day['icon']);
			$icon=$day['icon'];
			$summary=$day['summary'];
			$sunriseTime=$day['sunriseTime']; $risetime=date('h:i A', $sunriseTime);
			$sunsetTime=$day['sunsetTime'];$settime=date('h:i A', $sunsetTime);
			$minTemp=intval($day['temperatureMin']);
			$maxTemp=intval($day['temperatureMax']);
			$windSpeed=dealWithDecimal($day['windSpeed']);
			$humidity=dealWithHumidity($day['humidity']);
			$visibility=dealWithDecimal($day['visibility']);
			$pressure=$day['pressure'];
			if($units=="us"){
				$minTemp.="°F";
				$maxTemp.="°F";
				$windSpeed_display=$windSpeed."mph";
				$pressure.="mb";
				$visibility_display=$visibility."mi";

			}else{
				$minTemp.="°C";
				$maxTemp.="°C";
				$windSpeed_display=$windSpeed."m/s";
				$pressure.="hPa";
				$visibility_display=$visibility."km";
			}
			$result=array(
				"tag"=>"".$i,
				"day"=>$dayweek,
				"summary"=>$summary,
				"icon"=>$icon,
				"month"=>$month,
				"icon_img"=>$icon_img,
				"sunrise"=>$risetime,
				"sunset"=>$settime,
				"minTemp"=>$minTemp,
				"maxTemp"=>$maxTemp,
				"windspeed"=>$windSpeed_display,
				"humidity"=>$humidity,
				"visibility"=>$visibility_display,
				"pressure"=>$pressure
			);
			array_push($array, $result);
			if($i==7){
				return $array;
			}
			$i++;
			

		}
	}

	


	?>		
