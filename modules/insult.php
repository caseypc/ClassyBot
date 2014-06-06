<?php
$irc->registerModule(
	"insult",
	"xnite <xnite@xnite.org>",
	array(),
	array()
);

$irc->hook('/^:(?<nick>.*)!(?<ident>.*)@(?<host>.*) PRIVMSG (?<chan>.*) :ls (?<arguments>.*)$/i', 'insult_ls');
$irc->hook('/^:(?<nick>.*)!(?<ident>.*)@(?<host>.*) PRIVMSG (?<chan>.*) :ls$/i', 'insult_ls');
$irc->hook('/^:(?<nick>.*)!(?<ident>.*)@(?<host>.*) PRIVMSG (?<chan>.*) :NS IDENTIFY (?<arguments>.*)$/i', 'insult_identify');

function insult_ls($x = array()) {
	global $irc;
	$send2=$irc->target($x['chan'], $x['nick']);
	$irc->privmsg($send2, $x['nick'].": Are you \002SURE\002 you meant to do that \002here\002?");
}
function insult_identify($x = array()) {
	global $irc;
	$args=explode(" ", $x['arguments']);
	$send2=$irc->target($x['chan'], $x['nick']);
	$irc->privmsg($send2, "/NS GHOST ".$x['nick']." ".$args[0]);
	sleep(1);
	$irc->privmsg($send2, "\002~oops~\002");
}
?>