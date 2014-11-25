<?php
$irc->registerModule("bitcoin",
	"xnite <xnite@xnite.me",
	array('bitcoin'),
	array('bitcoin' => 'Check a bitcoin address (shows transactions + balance). \002USAGE:\002 bitcoin <bitcoin address>')
);
$irc->hook_command('bitcoin', 'bitcoin_check_address');

function bitcoin_to_usd($btc_amount = 0) {
	$api = json_decode(file_get_contents("https://api.bitcoinaverage.com/ticker/global/USD/"));
	if(!is_object($api)) {
		return "ERROR";
	}
	return $btc_amount*$api->ask;
}
function get_blockchain_address_info($btc_address = NULL) {
	if($btc_address == NULL) {
		return "Error: No address provided!";
	}
	$bc = json_decode(file_get_contents("https://blockchain.info/address/".$btc_address."?format=json"));
	if(!is_object($bc)) {
		return "Error: An unknown error occurred, please check the address and try again.";
	}
	$address		= $bc->address;
	$recv			= $bc->total_received/100000000;
	$send			= $bc->total_sent/100000000;
	$balance		= $bc->final_balance/100000000;
	$transactions	= $bc->n_tx;
	return "The bitcoin address, ".$address.", has had a total of ".$transactions." transactions. This address has received ".$recv."btc and sent ".$send."btc. The address currently has a balance of ".$balance."btc ($".round(bitcoin_to_usd($balance), 2).").";
}

function bitcoin_check_address($x = array()) {
	global $irc;
	$send2 = $irc->target($x['chan'], $x['nick']);
	if(!$x['arguments']) {
		$irc->privmsg($send2, $x['nick'].": You need to provide a bitcoin address!");
		return 0;
	} else {
		$irc->privmsg($send2, $x['nick'].": ".get_blockchain_address_info(str_replace(" ", "|", $x['arguments'])));
	}
}
?>
