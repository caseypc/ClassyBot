<?php
$irc->registerModule(
	"greet",
	"xnite <xnite@xnite.org>",
	array(),
	array()
);
$irc->hook_join(greet_others_join);

function greet_others_join($x = array()) {
	global $irc; global $me;
	if($x['nick'] != $me) {
		$irc->privmsg($x['chan'], "Hello ".$x['nick'].", welcome to ".$x['channel']."!");
	} else {
		$irc->privmsg($x['chan'], "Hello everybody! I am ".$me.", and some might say I'm rather classy!");
	}
}