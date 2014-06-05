<?php
$CONFIG=array(
	'server'		=> 'irc.afraidirc.net',				//The address of the irc server
	'port'			=> 6667,							//The port of the irc server (usually 6667)
	'channels'		=> '#phpbot',						//Channels you would like your bot to join after connecting separated by a comma (no spaces!)
	'nick'			=> 'ClassyBot',						//The nickname of your bot
	'ident'			=> 'ClassyBot',						//The ident of your bot
	'realname'		=> 'ClassyBot version'.$VERSION,	//The real name of your bot
	'trigger'		=> '@',								//The command trigger for your bot
	'timeout'		=> '15',							//Seconds before declairing connection timed out. Should not be set above 30 or below 5. 15 is a fair number here.
	'ns_identify'	=> 'IDENTIFY ClassyBot password',
	'ns_ghost'		=> 'GHOST ClassyBot password',
	'ns_register'	=> 'REGISTER password me@domain.tld',
	'ns_nick'		=> 'NickServ'
	);
?>
