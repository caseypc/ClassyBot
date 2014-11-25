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
		if(isset($irc->cm->config->users->$x['nick']->greeting) && $irc->cm->config->users->$x['nick']->greeting != false && $irc->cm->config->channel_settings->$x['chan']->greetings != false && $irc->cm->config->channel_settings->$x['chan']->greetings != "false" ) {
			$irc->privmsg($x['chan'], "User Greeting for ".$x['nick'].": ".$irc->cm->config->users->$x['nick']->greeting);
		}
	}
}