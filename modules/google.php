<?php
$irc->registerModule(
	"Google",
	"xnite <xnite@xnite.org>",
	array('google'),
	array('google' => 'Do a Google search from IRC. \002USAGE:\002 google <search query>')
);
$irc->hook_command('google', 'google_dosearch');
function google_dosearch($x = array()) {
	global $irc;
	$send2=$irc->target($x['chan'], $x['nick']);
	if(!$x['arguments']) { $irc->privmsg($send2, $x['nick'].": You did not provide a search query. Please come back when you have one!"); }
	else {
		$search_query=$x['arguments'];
		$google=json_decode(file_get_contents("http://ajax.googleapis.com/ajax/services/search/web?v=1.0&rsz=large&q=".urlencode($search_query)."&ql=rn&lr=lang_en"));
		$url=html_entity_decode($google->responseData->results[0]->url);
		$title=html_entity_decode($google->responseData->results[0]->titleNoFormatting);
		$irc->privmsg($send2, $x['nick'].': "'.$title.'" -> '.$url.' @ '.$google->responseData->results[0]->visibleUrl);
	}
}

?>