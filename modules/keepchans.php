<?php
/*no need for this module since it is now included in core

$irc->registerModule(
	"keepchans",
	"xnite <xnite@xnite.org>",
	array(),
	array()
);
$irc->hook_join(keepchans_join);

function keepchans_join($x = array()) {
	global $irc; global $db; global $me;
	if($x['nick'] == $me) {
		$db->channel_add($x['chan']);
		echo '[INFO] Adding '.$x['chan']."to database so we will auto-join it next time\n";
	}
}
*/