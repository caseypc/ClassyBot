<?php
$irc->registerModule(
	"Spotify",
	"xnite <xnite@xnite.org>",
	array('spotify'),
	array('spotify' => "Search for songs, albums & artists with Spotify. \002Usage:\002 spotify <search string>")
);
$irc->hook_command('spotify', 'spotify_search');


function spotify_search($x = array()) {
	global $irc;
	if(!isset($x['arguments'])) {
		$irc->privmsg($irc->target($x['chan'], $x['nick']), $x['nick'].": Please provide a query to search for");
	} else {
		$search_string=urlencode($x['arguments']);
		$r=json_decode(file_get_contents("https://ws.spotify.com/search/1/album.json?q=$search_string"));
		$count=0;
		foreach($r->albums as $album) {
			if($count >= 3) { break; }
			else {
				$search_link="http://tpb.derp.pw/search/".urlencode($album->artists[0]->name." ".$album->name)."/0/7/0";
				$irc->privmsg($irc->target($x['chan'], $x['nick']), $x['nick'].": ".$album->artists[0]->name." - ".$album->name." \002[Pirate:\002 ".$search_link."\002]\002");
			}
			$count=$count+1;
		}
	}
}
?>