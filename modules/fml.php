<?php
/* THIS MODULE REQUIRES YOUR OWN FML API KEY: http://www.fmylife.com/api/home */
$irc->registerModule(
	"fml",
	"xnite <xnite@xnite.org>",
	array('fml'),
	array('fml' => 'Gets a random FML quote')
);

//$irc->hook('/^:(?<nick>.*)!(?<ident>.*)@(?<host>.*) PRIVMSG (?<chan>.*) :'.$trigger.'fml$/i', 'fml_random');
$irc->hook_command("fml", "fml_random");
global $fmlKey;
$fmlKey=""; //CONFIGURE YOUR API KEY!

function fml_random($x = array()) {
	global $fmlKey;
	global $irc;
	global $me;
	echo "[DEBUG] Getting quote from FML API\n[DEBUG]Key:\t".$fmlKey."\n";
	$api=simplexml_load_file("http://api.fmylife.com/view/random?key=".$fmlKey."&language=en");
	foreach($api->items as $xml) {
		$category=$xml->item->category;
		$deserved=$xml->item->deserved;
		$agree=$xml->item->agree;
		$story=str_replace("\n", " ", $xml->item->text)."\n";
		$msg="\002[Category:\002 ".$category." \002| Agree:\002 ".$agree." \002| Deserved:\002 ".$deserved."\002]\002 ".$story;
		$target=$irc->target($x['chan'], $x['nick']);
		$irc->privmsg($target, $msg);
	}
}
?>