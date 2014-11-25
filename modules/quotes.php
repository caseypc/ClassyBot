<?php
require_once("IRCQuotes.Class.php");

$irc->registerModule(
	"Quotes",
	"xnite <xnite@AfraidIRC.net>",
	array('quote','quote-add','quote-del','quote-search','quote-import'),
	array(
		'quote-add' => 'Add a quote to the database. \002Usage:\002 quote-add <quote>',
		'quote-del' => 'Remove a quote from the database. \002Usage:\002 quote-del <quote id>',
		'quote'		=> 'Retrieve a specified or random quote from the database. \002Usage:\002 quote [<quote id>]',
		'quote-search' => 'Searches quotes author, channel, and text for matching quotes and sends first 5 results via NOTICE. \002Usage:\002 quote-search <search term(s)>',
		'quote-import' => 'Imports quotes from a file, specify the file path and the bot will do the rest. See README before issuing this command! \002Usage:\002 quote-import [<path to file>]'
		//NOTICE: Only bot masters can perform imports, choose your masters wisely. A master would have the ability to import files like /etc/passwd (for example) into the quotes database where they could then read the files. Needless to say, but also don't run as root. If everybody MUST for w/e reason be a master, then comment out the command hook below.
	)
);
$irc->hook_command('quote-add', 'quotes_add');
$irc->hook_command('quote-del', 'quotes_del');
$irc->hook_command('quote', 'quotes_get');
//$irc->hook_command('quote-search', 'quotes_search');
$irc->hook_command('quote-import', 'quotes_import'); //comment out if you don't trust all of your bot masters, or if you don't trust the opers on the network which your bot operates on.

function quotes_add($x = array()) {
	global $irc;
	$send2=$irc->target($x['chan'], $x['nick']);
	if(strlen($x['arguments']) <= 9) {
		$irc->privmsg($send2, $x['nick'].": That quote could not be added, because it is too short.");
		return false;
	}
	if(strlen($x['arguments']) >= 510) {
		$irc->privmsg($send2, $x['nick'].": That quote is too long, it would never fit!");
		return false;
	}
	$quotes = new IRCQuotes(realpath("quotes.db"));
	if($quotes->add_quote($x['chan'], $x['nick'], $x['arguments']) == false) {
		echo "Error: ".$quotes->error_msg."\n";
		$irc->privmsg($send2, "Failed to add quote to database.");
		return false;
	}
	$irc->privmsg($send2, "Successfully added quote to the database. It is quote #".$quotes->last_insert_id);
	return true;
}
function quotes_import($x = array()) {
	global $irc;
	$send2=$irc->target($x['chan'], $x['nick']);
	if($irc->cm->check_user_perms("M", $x['nick'], $x['host']) == false) {
		$irc->privmsg($send2, $x['nick'].": You do not have permission to execute this command!");
		return false;
	}
	$arg=explode(" ", $x['arguments']);
	$file = $arg[0];
	if(!file_exists($file)) {
		$irc->notice($x['nick'], "'".$file."' no such file!");
		return false;
	}
	foreach(file($file) as $line) {
		$quotes = new IRCQuotes(realpath("quotes.db"));
		if($quotes->add_quote('!Imported', '!Imported', $line) == false) {
			echo $quotes->error_msg."\n";
		}
	}
	return true;
}

function quotes_del($x = array()) {
	global $irc;
	$quotes = new IRCQuotes(realpath("quotes.db"));
	$send2=$irc->target($x['chan'], $x['nick']);
	if($irc->cm->check_user_perms("Q", $x['nick'], $x['host']) == false) {
		$irc->privmsg($send2, $x['nick'].": You do not have permission to execute this command!");
		return false;
	}
	$arg=explode(" ",$x['arguments']);
	$quote_id=$arg[0];
	if($quotes->del_quote($quote_id) == false) {
		$irc->privmsg($send2, $x['nick'].": Could not delete quote");
		return false;
	}
	$irc->privmsg($send2, $x['nick'].": Quote #".$quote_id." deleted.");
	return true;
}
function quotes_get($x = array()) {
	global $irc;
	$send2=$irc->target($x['chan'], $x['nick']);
	$arg=explode(" ",$x['arguments']);
	if(isset($arg[0])) { $quote_id=$arg[0]; } else { $quote_id = false; }
	if($quote_id == false) {
		$quotes = new IRCQuotes(realpath("quotes.db"));
		$r = $quotes->random_quote();
		if($r == false) {
			$irc->privmsg($send2, $x['nick'].": I was unable to fetch any quotes.");
			return false;
		}
		foreach($r as $q) {
			$irc->privmsg($send2, '[#'.$q['quote_id'].'][Author:'.$q['quote_author'].']: '.$q['quote_text']);
		}
		return true;
	} else {
		$quotes = new IRCQuotes(realpath("quotes.db"));
		if(!is_numeric($quote_id)) {
			$irc->privmsg($send2, $x['nick'].": Quote ID must be numeric.");
			return false;
		}
		$r = $quotes->view_quote($quote_id);
		if($r == false) {
			$irc->privmsg($send2, $x['nick'].": I was unable to fetch quote #".$quote_id);
			return false; 
		}
		foreach($r as $q) {
			$irc->privmsg($send2, '[#'.$q['quote_id'].'][Author:'.$q['quote_author'].']: '.$q['quote_text']);
		}
		return true;
	}
}
?>