<?php
$irc->registerModule("shorten",
	"xnite <xnite@xnite.me",
	array('shorten'),
	array('shorten' => 'Shorten a URL \002USAGE:\002 shorten <long-url>')
);
$irc->hook_command('shorten', 'shorten_url');

function shorten_url($x = array()) {
	global $irc;
	$send2 = $irc->target($x['chan'], $x['nick']);
	if(!$x['arguments']) {
		$irc->privmsg($send2, $x['nick'].": You need to provide an address to shorten!");
		return 0;
	} else {
		$irc->privmsg($send2, $x['nick'].": ".file_get_contents("https://api-ssl.bitly.com/v3/shorten?access_token=".$irc->cm->config->modules->bitly->key."&uri=".urlencode($x['arguments'])."&format=txt"));
	}
}

?>