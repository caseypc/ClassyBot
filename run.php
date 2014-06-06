<?php
/*******************************************************************************************************************************************\
** ChaosBot v2.x by Robert Whitney is licensed under a Creative Commons Attribution-NonCommercial-NoDerivatives 4.0 International License. **
** Based on a work at https://github.com/xnite/chaosbotv2.                                                                                 **
** Permissions beyond the scope of this license may be available at http://xnite.org/copyright.                                            **
\*******************************************************************************************************************************************/

global $clean_shutdown;
global $VERSION;
$VERSION='1.0';
$clean_shutdown=false;
require_once('config.php');
$config=json_decode(json_encode($CONFIG));
require_once("IRCBot.class.php");
global $irc;
global $trigger;
$trigger=$config->trigger;
$irc = new IRCBot($config->server, $config->port, $config->nick, $config->ident, $config->realname);
echo "Configuring modules\n";
foreach(file('modlist.conf') as $modPath) {
	require_once(str_replace("\n", "", str_replace("\r", "", $modPath)));
	echo "Loaded ".$modPath."\n";
}

$irc->hook_command('help', 'do_help');
function do_help($x = array()) {
	global $irc;
	if(!isset($x['arguments'])) {
		$irc->privmsg($irc->target($x['chan'], $x['nick']), $x['nick'].": Please provide a command to look up help text.");
	} else {
		$args=explode(" ", $x['arguments']);
		$irc->privmsg($irc->target($x['chan'], $x['nick']), $irc->cmdHelp($args[0]));
	}
}

while($clean_shutdown == false) {
	$irc->connect($config->timeout);
	while($irc->heartbeat() == true) {
		while($raw = $irc->read()) {
			$raw=str_replace("\n", "", str_replace("\r", "", $raw));
			echo "[RECV] ".$raw."\n"; //echo raw lines from the server so we know what the bot is seeing.
			//Pass handling raw feed off to the IRCBot class.
			$irc->cmdHandle($raw);
		}
	}
}
?>