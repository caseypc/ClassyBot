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
$irc->hook_command('part', 'core_part');
$irc->hook_command('modset', 'core_modset');
$irc->hook_command('chanset', 'core_chanset');
$irc->hook_command('userset', 'core_userset');
$irc->hook_command('set', 'core_set');
$irc->hook_ctcp('VERSION', 'core_ctcp_version');


function core_init($x = array()) {
	global $irc;
	global $me;
	$me=$x['me'];	
	foreach($irc->cm->config->autojoin_channels as $channel) {
		$irc->join($channel);
	}
}

function core_modset($x = array()) {
	global $irc;
	global $me;
	if(isset($irc->cm->config->users->$x['nick']->is_admin) && $irc->cm->config->users->$x['nick']->is_admin == true && $irc->cm->config->users->$x['nick']->hostname == $x['host']) {
	
	} else { $irc->notice($x['nick'], "Failed to modify configuration, do you have permission to edit it?"); return false; }
	$me=$x['me'];
	$arg=explode(" ", $x['arguments']);
	$module = $arg[0];
	array_shift($arg);
	$field = $arg[0];
	array_shift($arg);
	$value = implode(" ", $arg);
	if($irc->cm->write_mod_settings($module, $field, $value) == true) {
		$irc->notice($x['nick'], "Wrote configuration successfully");
	} else {
		$irc->notice($x['nick'], "Failed to modify configuration!");
	}
}

function core_chanset($x = array()) {
	global $irc;
	global $me;
	if(isset($irc->cm->config->users->$x['nick']->is_admin) && $irc->cm->config->users->$x['nick']->is_admin == true && $irc->cm->config->users->$x['nick']->hostname == $x['host']) {
	} else { $irc->notice($x['nick'], "Failed to modify configuration, do you have permission to edit it?"); return false; }
	$me=$x['me'];
	$arg=explode(" ", $x['arguments']);
	$channel = $arg[0];
	array_shift($arg);
	$field = $arg[0];
	array_shift($arg);
	$value = implode(" ", $arg);
	if($irc->cm->write_channel_settings($channel, $field, $value) == true) {
		$irc->notice($x['nick'], "Wrote configuration successfully");
	} else {
		$irc->notice($x['nick'], "Failed to modify configuration!");
	}
}
function core_userset($x = array()) {
	global $irc;
	global $me;
	if(isset($irc->cm->config->users->$x['nick']->is_admin) && $irc->cm->config->users->$x['nick']->is_admin == true && $irc->cm->config->users->$x['nick']->hostname == $x['host']) {
	} else { $irc->notice($x['nick'], "Failed to modify configuration, do you have permission to edit it?"); return false; }
	$me=$x['me'];
	$arg=explode(" ", $x['arguments']);
	$user = $arg[0];
	array_shift($arg);
	$field = $arg[0];
	array_shift($arg);
	$value = implode(" ", $arg);
	if($irc->cm->write_user_settings($user, $field, $value) == true) {
		$irc->notice($x['nick'], "Wrote configuration successfully");
	} else {
		$irc->notice($x['nick'], "Failed to modify configuration!");
	}
}
function core_set($x = array()) {
	global $irc;
	global $me;
	if(isset($irc->cm->config->users->$x['nick']->is_admin) && $irc->cm->config->users->$x['nick']->is_admin == true && $irc->cm->config->users->$x['nick']->hostname == $x['host']) {
	} else { $irc->notice($x['nick'], "Failed to modify configuration, do you have permission to edit it?"); return false; }
	$me=$x['me'];
	$arg=explode(" ", $x['arguments']);
	$field = $arg[0];
	array_shift($arg);
	$value = implode(" ", $arg);
	if($irc->cm->write_core_settings($field, $value) == true) {
		$irc->notice($x['nick'], "Wrote configuration successfully");
	} else {
		$irc->notice($x['nick'], "Failed to modify configuration!");
	}
}

function core_uptime($x = array()) {
	global $irc;
	global $me;
	$target=$irc->target($x['chan'], $x['nick']);
	if($irc->is_windows() == true) {
		$irc->privmsg($target, $x['nick'].": You should run me on Linux. I'm more fun that way.");
	} else {
		$irc->privmsg($target, $x['nick'].": ".exec(uptime));
	}
}

function core_memory($x = array()) {
	global $irc;
	global $me;
	$target=$irc->target($x['chan'], $x['nick']);
	$mem=memory_get_usage();
	$mem=$mem/1024;
	$mem=round($mem, 2);
	$irc->privmsg($target, $x['nick'].": ".$mem."KB");
}
function core_ctcp_version($x = array()) {
	global $irc;
	global $me;
	global $VERSION;
	$irc->ctcp_reply($x['nick'], 'VERSION', "ClassyBot v".$VERSION." https://github.com/xnite/ClassyBot based on PHP IRCBot Class v".$irc->version()." https://github.com/xnite/PHPIRCBotClass");
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
	if(isset($x['arguments'])) {
		$a=explode(" ", $x['arguments']);
		$irc->join($a[0]);
		$irc->cm->add_channel($a[0]);
	}
}
function core_part($x = array()) {
	global $irc;
	global $me;
	if(isset($x['arguments'])) {
		$a=explode(" ", $x['arguments']);
		$irc->part($a[0]);
		$irc->cm->del_channel($a[0]);
	}
}


?>