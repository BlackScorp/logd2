<?php
if ($session['user']['gravefights']<=0){
	OutputClass::output("`\$`bYour soul can bear no more torment in this afterlife.`b`0");
	$op="";
	Http::httpset('op', "");
}else{
	require_once("lib/extended-battle.php");
	ExtendedBattle::suspend_companions("allowinshades", true);
	if (Modules::module_events("graveyard", Settings::getsetting("gravechance", 0)) != 0) {
		if (!OutputClass::checknavs()) {
			// If we're going back to the graveyard, make sure to reset
			// the special and the specialmisc
			$session['user']['specialinc'] = "";
			$session['user']['specialmisc'] = "";
			$skipgraveyardtext=true;
			$op = "";
			Http::httpset("op", "");
		} else {
			PageParts::page_footer();
		}
	} else {
		$session['user']['gravefights']--;
 			$battle=true;
 			$sql = "SELECT * FROM " . db_prefix("creatures") . " WHERE graveyard=1 ORDER BY rand(".Erand::e_rand().") LIMIT 1";
		$result = db_query($sql);
		$badguy = db_fetch_assoc($result);
		$level = $session['user']['level'];
		$shift = 0;
		if ($level < 5) $shift = -1;
		$badguy['creatureattack'] = 9 + $shift + (int)(($level-1) * 1.5);
		// Make graveyard creatures easier.
		$badguy['creaturedefense'] = (int)((9 + $shift + (($level-1) * 1.5)));
		$badguy['creaturedefense'] *= .7;
		$badguy['creaturehealth'] = $level * 5 + 50;
		$badguy['creatureexp'] = Erand::e_rand(10 + round($level/3),20 + round($level/3));
		$badguy['creaturelevel'] = $level;
		$attackstack['enemies'][0] = $badguy;
		$attackstack['options']['type'] = 'graveyard';
		$session['user']['badguy']=ArrayUtil::createstring($attackstack);
	}
}
?>