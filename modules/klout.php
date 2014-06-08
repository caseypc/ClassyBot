<?php
/*CONFIGURATION*/
$klout_api_key='';
/*END OF CONFIGURATION*/
global $klout_api_key;
$irc->registerModule(
	"Klout",
	"xnite <xnite@xnite.org>",
	array('klout'),
	array('klout' => 'Get the Klout score for specified Twitter username. \002USAGE:\002 klout <twitter_handle>')
);
$irc->hook_command('klout', klout_score);
function klout_score($x = array()) {
	global $klout_api_key;
	global $irc;
	$send2=$irc->target($x['chan'], $x['nick']);
	if(isset($x['arguments'])) {
		$a=explode(" ", $x['arguments']);
		$username=$a['0'];
	}
	if(!$username) {
		$irc->privmsg($send2, $x['nick'].': You need to provide a Twitter username.');
	} else {
		$api=json_decode(file_get_contents('http://api.klout.com/v2/identity.json/twitter?screenName='.$username.'&key='.$klout_api_key));
		$klout_id=$api->id;
		$api=json_decode(file_get_contents('http://api.klout.com/v2/user.json/'.$klout_id'./score?key='.$klout_api_key));
		$score=$api->score;
		$score=round($score);
		if($score <= 9 | !$score) {
			$irc->privmsg($send2, $x['nick'].': Klout score for '.$username.' is under 10% or user does not exist.');
		} else {
			$irc->privmsg($send2, ': Klout Score for '.$username.' [KloutID: '.$klout_id.'] is '.$score.'%');
		}

	}

}