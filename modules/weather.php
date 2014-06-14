<?php
$irc->registerModule(
	"weather",
	"xnite <xnite@xnite.org>",
	array('weather'),
	array('weather' => 'Get the weather in the specified zip code. (US ONLY) \002USAGE:\002 weather <zip>')
);
$irc->hook_command('weather', 'weather_get');

function weather_get($x) {
	global $irc;
	$send2=$irc->target($x['chan'], $x['nick']);
	$args=explode(" ", $x['arguments']);
	$zip=$args[0];
	$args=NULL;
	if(!is_numeric($zip) || strlen($zip) != 5) {
		$irc->privmsg($send2, $x['nick'].": The zip code you have provided is not a valid American zip code");
	} else {
		$xml=file_get_contents("http://weather.yahooapis.com/forecastrss?p=".$zip."&u=f");
		$xml=explode("\n", $xml);
		foreach($xml as $x) {
			if(preg_match('/^<yweather:location city="(?<city>.*)" region="(?<region>.*)"   country="(?<country>.*)"\/>$/i', $x, $y)) {
				$city=$y['city'];
				$region=$y['region'];
				$country=$y['country'];
			} elseif(preg_match('/^<yweather:wind chill="(?<windchill>.*)"   direction="(?<direction>.*)"   speed="(?<windspeed>.*)" \/>$/i', $x, $y)) {
				$windchill=$y['windchill'];
				$windspeed=$y['windspeed'];
			} elseif(preg_match('/^<yweather:condition  text="(?<condition>.*)"  code="(?<code>.*)"  temp="(?<temp>.*)"  date="(?<last_update>.*)" \/>$/i', $x, $y)) {
				$temp=$y['temp'];
				$condition=$y['condition'];
				$updated=$y['last_update'];
			}
		}
		$irc->privmsg($send2, $x['nick'].": Weather for ".$city.", ".$region." - ".$country.": Condition is ".$condition." and temperature is ".$temp."F; Wind Speed: ".$windspeed."MPh [".$updated."]");
		
	}
}