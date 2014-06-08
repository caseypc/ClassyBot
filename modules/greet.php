<?php
$irc->registerModule(
	"greet",
	"xnite <xnite@xnite.org>",
	array(),
	array()
);
$irc->hook_me_join(greet_me_join);
$irc->hook_join(greet_others_join);

function greet_me_join($x = array()) {
	global $me;
	$irc->privmsg($x['chan'], "Hello everybody! I am ".$me.", and some might say I'm rather classy!");
}
function greet_others_join($x = array()) {
	global $me;
	if($x['nick'] != $me) {
		$irc->privmsg($x['nick'], "Hello ".$x['nick'].", welcome to ".$x['channel']."!");
	}
}