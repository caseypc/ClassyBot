<?php
//echo "Loaded ping-pong module\n";
$irc->registerModule(
	"ping-pong",
	"xnite <xnite@xnite.org>",
	array(),
	array('_' => 'Sends ping replies to the server')
);

$irc->hook('/^:(?<server>.*) 376 (?<me>.*) :(?<line>.*)$/i', 'pingpong_init');
$irc->hook('/^PING :(?<data>.*)$/i', 'pingpong_reply');

function pingpong_init($x = array()) {
	global $irc;
	echo "[INFO] Me=".$x['me']."\n";
	$irc->raw("PING :".date('U'));
}

function pingpong_reply($x = array()) {
	global $irc;
	if(isset($x['data'])) {
		$irc->raw("PONG ".$x['data']);
	}
}
?>