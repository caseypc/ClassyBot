<?php
/*******************************************************************************************************************************************\
** ChaosBot v2.x by Robert Whitney is licensed under a Creative Commons Attribution-NonCommercial-NoDerivatives 4.0 International License. **
** Based on a work at https://github.com/xnite/chaosbotv2.                                                                                 **
** Permissions beyond the scope of this license may be available at http://xnite.org/copyright.                                            **
\*******************************************************************************************************************************************/
ini_set('error_reporting', 'E_ALL');
ini_set('display_errors', 1);
if(!file_exists("config.json")) {
	die("There was an error reading the configuration file. Please be sure that the file config.json exists in the working directory\n");
}
require_once("configmanager.php");
$c = new ircConfigurationManager("config.json");
if(!is_object($c->config)) {
	die("Configuration did not pass inspection.\n");
}
global $c;
global $clean_shutdown;
global $VERSION;
$VERSION='1.6';
$clean_shutdown=false;
$config=json_decode(json_encode($CONFIG));

require_once("IRCBot.class.php");
global $irc;
global $trigger;
$trigger=$config->trigger;
$irc = new IRCBot($c->config->irc_server, $c->config->irc_port, $c->config->bot_nick, $c->config->bot_ident, $c->config->bot_realname, $c->config->use_ssl);
echo "Initiating ClassyBot version ".$VERSION." by xnite <xnite@xnite.org>\n";
echo "Using IRCBot Class version ".$irc->version()." by xnite <xnite@xnite.org>\n";
echo "Configuring modules\n";
foreach(file('modlist.conf') as $modPath) {
	require_once(str_replace("\n", "", str_replace("\r", "", $modPath)));
	echo "Loaded ".$modPath;
}
echo "\n";

$irc->hook_command('help', 'do_help');
function do_help($x = array()) {
	global $irc;
	global $c;
	if(!isset($x['arguments'])) {
		$irc->notice($x['nick'], "Please provide a command to look up help text.");
	} else {
		$args=explode(" ", $x['arguments']);
		$irc->notice($x['nick'], $irc->cmdHelp($args[0]));
	}
}


while($clean_shutdown == false) {
	$irc->connect($irc->cm->config->timeout);
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
