<?php
global $trigger;

$irc->registerModule(
	"core",
	"xnite <xnite@xnite.org>",
	array('memory'),
	array('memory' => 'Returns system memory usage.', 'uptime' => 'Returns system uptime.')
);
$irc->hook_connect('core_init');

$irc->hook_command('uptime', 'core_uptime');
$irc->hook_command('memory', 'core_memory');
$irc->hook_command('version', 'core_version');
$irc->hook_command('join', 'core_join');



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
	$target=$irc->target($x['chan'], $x['nick']);
	$irc->privmsg($target, $x['nick'].": ".exec(uptime));
}

function core_memory($x = array()) {
	global $irc;
	global $me;
	$target=$irc->target($x['chan'], $x['nick']);
	$mem=memory_get_usage();
	$mem=$mem/1024/1024;
	$mem=$mem;
	$irc->privmsg($target, $x['nick'].": ".$mem."Mb");
}
function core_version($x = array()) {
	global $irc;
	global $me;
	global $VERSION;
	$target=$irc->target($x['chan'], $x['nick']);
	$irc->privmsg($target, $x['nick'].": \002ClassyBot Version\002 ".$VERSION." https://github.com/xnite/ClassyBot based on \002PHP IRCBot Class version\002 ".$irc->version()." https://github.com/xnite/PHPIRCBotClass");
}
function core_join($x = array()) {
	global $irc;
	global $me;
	global $config;
	if(isset($x['arguments'])) {
		$a=explode(" ", $x['arguments']);
		$irc->join($a[0]);
	}
}
?>