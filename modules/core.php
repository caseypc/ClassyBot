<?php
global $trigger;

$irc->registerModule(
	"core",
	"xnite <xnite@xnite.org>",
	array('mem'),
	array('mem' => 'Returns system memory usage.', 'uptime' => 'Returns system uptime.')
);
$irc->hook('/^:(?<server>.*) 376 (?<me>.*) :(?<line>.*)$/i', 'core_init');

$irc->hook('/^:(?<nick>.*)!(?<ident>.*)@(?<host>.*) PRIVMSG (?<chan>.*) :'.$trigger.'uptime$/i', 'core_uptime');
$irc->hook('/^:(?<nick>.*)!(?<ident>.*)@(?<host>.*) PRIVMSG (?<chan>.*) :'.$trigger.'mem$/i', 'core_memory');
$irc->hook('/^:(?<nick>.*)!(?<ident>.*)@(?<host>.*) PRIVMSG (?<chan>.*) :'.$trigger.'version$/i', 'core_version');
$irc->hook('/^:(?<nick>.*)!(?<ident>.*)@(?<host>.*) PRIVMSG (?<chan>.*) :'.$trigger.'join (?<channel_to_join>.*)$/i', 'core_join');


function core_init($x = array()) {
	global $irc;
	global $me;
	global $config;
	$me=$x['me'];
	$irc->join($config->channels);
}

function core_uptime($x = array()) {
	global $irc;
	global $me;
	if($x['chan'] == $me) { $target=$x['nick']; } else { $target=$x['chan']; }
	$irc->privmsg($target, $x['nick'].": ".exec(uptime));
}

function core_memory($x = array()) {
	global $irc;
	global $me;
	if($x['chan'] == $me) { $target=$x['nick']; } else { $target=$x['chan']; }
	$mem=memory_get_usage();
	$mem=$mem/1024;
	$irc->privmsg($target, $x['nick'].": ".$mem."Mb");
}
function core_version($x = array()) {
	global $irc;
	global $me;
	global $VERSION;
	if($x['chan'] == $me) { $target=$x['nick']; } else { $target=$x['chan']; }
	$irc->privmsg($target, $x['nick'].": \002ClassyBot Version\002 ".$VERSION." https://github.com/xnite/ClassyBot based on \002PHP IRCBot Class version\002 ".$irc->version()." https://github.com/xnite/PHPIRCBotClass");
}
function core_join($x = array()) {
	global $irc;
	global $me;
	global $config;
	$irc->join($x['channel_to_join']);
}
?>