<?php
/* Use modset to configure the api key for this module */
$irc->registerModule(
	"Klout",
	"xnite <xnite@xnite.org>",
	array('klout'),
	array('klout' => 'Get the Klout score for specified Twitter username. \002USAGE:\002 klout <twitter_handle>')
);
$irc->hook_command('klout', 'klout_score');
function klout_score($x = array()) {
	global $irc;
	$send2=$irc->target($x['chan'], $x['nick']);
	if(!isset($irc->cm->config->modules->klout->api_key)) {
		$irc->privmsg($send2, $x['nick'].': Klout API Key is not set.');
		return false;
	}
	if(isset($x['arguments'])) {
		$a=explode(" ", $x['arguments']);
		$username=$a['0'];
	}
	if(!$username) {
		$irc->privmsg($send2, $x['nick'].': You need to provide a Twitter username.');
	} else {
		$api=json_decode(file_get_contents('http://api.klout.com/v2/identity.json/twitter?screenName='.$username.'&key='.$irc->cm->config->modules->klout->api_key));
		if(isset($api)) { $klout_id=$api->id; }
		$api=json_decode(file_get_contents('http://api.klout.com/v2/user.json/'.$klout_id.'/score?key='.$irc->cm->config->modules->klout->api_key));
		if(isset($api)) { $score=$api->score; }
		if(isset($score)) {$score=round($score); }
		if(!isset($score)) {
			$irc->privmsg($send2, $x['nick'].': Communication error with the Klout API. Please try your request again later.');
		} elseif($score <= 9 | !$score) {
			$irc->privmsg($send2, $x['nick'].': Klout score for '.$username.' is under 10% or user does not exist.');
		} else {
			$irc->privmsg($send2, ': Klout Score for '.$username.' [KloutID: '.$klout_id.'] is '.$score.'%');
		}

	}

}