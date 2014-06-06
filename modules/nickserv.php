<?php
$irc->registerModule(
	"nickserv",
	"xnite <xnite@xnite.org>",
	array(),
	array('_' => 'Handles communications with nickserv automatically.')
);

$irc->hook_connect('nickserv_init');
$irc->hook('/^:'.$config->ns_nick.'!(?<ident>.*)@(?<host>.*) NOTICE (?<chan>.*) :(.*)is not a registered nickname.$/i', 'nickserv_register');
$irc->hook('/^:(?<server>.*) 433 (?<me>.*) (?<nick_in_use>.*) :(?<human_readable_msg>.*)$/i', 'nickserv_inuse');

function nickserv_inuse($x = array()) {
	global $config;
	global $irc;
	global $me;
	$me=$x['nick_in_use'].rand(100,999);
	$irc->nick($me);
	$irc->privmsg($config->ns_nick, $config->ns_ghost);
	sleep(1);
	$irc->nick($config->nick);
	$irc->privmsg($config->ns_nick, $config->ns_identify);
}
function nickserv_register($x = array()) {
	global $irc;
	global $config;
	$irc->privmsg($config->ns_nick, $config->ns_register);
}
function nickserv_init($x = array()) {
	global $irc;
	global $config;
	$irc->privmsg($config->ns_nick, $config->ns_identify);
}