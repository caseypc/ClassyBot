<?php
ini_set('error_reporting', 'E_ALL');
ini_set('display_errors', 1);
$irc->registerModule(
	"nickserv",
	"xnite <xnite@xnite.org>",
	array(),
	array('_' => 'Handles communications with nickserv automatically.')
);

$irc->hook_connect('nickserv_init');
$irc->hook('/^:'.$irc->cm->config->modules->nickserv->services_nick.'!(?<ident>.*)@(?<host>.*) NOTICE (?<chan>.*) :(.*)is not a registered nickname.$/i', 'nickserv_register');
$irc->hook('/^:(?<server>.*) 433 (?<me>.*) (?<nick_in_use>.*) :(?<human_readable_msg>.*)$/i', 'nickserv_inuse');

function nickserv_inuse($x = array()) {
	global $irc;
	global $me;
	$me=$x['nick_in_use'].rand(100,999);
	$irc->nick($me);
	$irc->privmsg($irc->cm->config->modules->nickserv->services_nick, $irc->cm->config->modules->nickserv->ghost_string);
	sleep(3);
	$irc->nick($config->nick);
	$irc->privmsg($irc->cm->config->modules->nickserv->services_nick, $irc->cm->config->modules->nickserv->identify_string);
	sleep(1);
}
function nickserv_register($x = array()) {
	global $irc;
	$irc->privmsg($irc->cm->config->modules->nickserv->services_nick, $irc->cm->config->modules->nickserv->register_string);
}
function nickserv_init($x = array()) {
	global $irc;
	$irc->privmsg($irc->cm->config->modules->nickserv->services_nick, $irc->cm->config->modules->nickserv->identify_string);
}