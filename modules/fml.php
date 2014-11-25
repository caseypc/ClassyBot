<?php
/* THIS MODULE REQUIRES YOUR OWN FML API KEY: http://www.fmylife.com/api/home */
/* Developer notes: Set your API key with modset command (modset fml key yourkeyhere) */
$irc->registerModule(
	"fml",
	"xnite <xnite@xnite.org>",
	array('fml'),
	array('fml' => 'Gets a random FML quote')
);

$irc->hook_command("fml", "fml_random");

function fml_random($x = array()) {
	global $irc;
	global $me;
	if(!isset($irc->cm->config->modules->fml->key) || $irc->cm->config->modules->fml->key == NULL) { return false; }
	
	echo "[DEBUG] Getting quote from FML API\n[DEBUG]Key:\t".$irc->cm->config->modules->fml->key."\n";
	$api=simplexml_load_file("http://api.fmylife.com/view/random?key=".$irc->cm->config->modules->fml->key."&language=en");
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