<?php

//Converted to a module by Zach Lawson, with the addition of settings

/*
Version History:
Version 1.0 - Original public release
Version 2.0 - Added a feature that allows the cost of the potions to vary by day
Version 2.1 - Fixed some broken stuff
Version 2.2 - Really fixed it this time
Version 2.3 - fixed random cost.
*/

function cedrikspotions_getmoduleinfo(){
	$info = array(
		"name"=>"Cedrik's Potion Shop",
		"version"=>"2.6",
		"author"=>"Eric Stevens<br>Modifications by: Chris Vorndran",
		"category"=>"Inn",
		"download"=>"core_module",
		"settings"=>array(
			"Cedrik's Potion Shop - Potion Availability,title",
			"ischarm"=>"Is Charm potion available,bool|1",
			"ismax"=>"Is Vitality potion available,bool|1",
			"istemp"=>"Is Health potion available,bool|1",
			"isforget"=>"Is Forgetfulness potion available,bool|1",
			"istrans"=>"Is Transmutation potion available,bool|1",
			"Cedrik's Potion Shop - Costs,title",
			"charmcost"=>"Cost for Charm Potion,range,1,10,1|2",
			"maxcost"=>"Cost for Vitality Potion,range,1,10,1|2",
			"tempcost"=>"Cost for Health Potion,range,1,10,1|2",
			"forgcost"=>"Cost for Forgetfulness Potion,range,1,10,1|2",
			"transcost"=>"Cost for Transmutation Potion,range,1,10,1|2",
			"random"=>"Is the cost per point of potion random,bool|0",
			"minrand"=>"Minimum cost per point of effect,range,1,9,1|2",
			"maxrand"=>"Maximum cost per point of effect,range,2,10,1|5",
			"randcost"=>"Current random cost,rang,1,10,1|2",
			"Note: Each <x> amount of gems spent will give the effect the potion.  The actual effects can vary based on configuration.,note",
			"Cedrik's Potion Shop - Effects,title",
			"transmuteturns"=>"How many turns will the transmutation sickness last?,range,1,20,1|10",
			"defmod"=>"How much is the multiplier for Transmutation Sickness (defense)?,floatrange,.1,2,.05|.75",
			"atkmod"=>"How much is the multiplier for Transmutation Sickness (attack)?,floatrange,.1,2,.05|.75",
			"survive"=>"Will transmutation sickness carry over new days?,bool|1",
			"charmgain"=>"How much charm do you get per potion,int|1",
			"vitalgain"=>"How many maxhp do you get per potion,int|1",
			"tempgain"=>"How many current hp do you get per potion,int|20",
			"carrydk"=>"Do MaxHitpoint (vitality) potions carry across DKs?,bool|1",
		),
		"prefs"=>array(
			"Cedrik's Potion Shop User Preferences,title",
			"extrahps"=>"How many extra hitpoints has the user bought?,int",
		),
	);
	return $info;
}

function cedrikspotions_install(){
	module_addhook("header-inn");
	module_addhook("newday-runonce");
	module_addhook("hprecalc");
	return true;
}

function cedrikspotions_uninstall(){
	return true;
}

function cedrikspotions_dohook($hookname,$args){
	switch($hookname){
	case "header-inn":
		$op = Http::httpget("op");
		$act = Http::httpget("act");
		if($op=="bartender" && $act == "") {
			addnav_notl(SanitizeClass::sanitize(Settings::getsetting("barkeep","`tCedrik")));
			OutputClass::addnav("Gems","runmodule.php?module=cedrikspotions&op=gems");
		}
		break;
	case "newday-runonce":
		if (get_module_setting("random")){
			$min = get_module_setting("minrand");
			$max = get_module_setting("maxrand");
			$randcost = Erand::e_rand($min,$max);
			set_module_setting("randcost",$randcost);
		}
		break;
	case "hprecalc":
		$args['total'] -= get_module_pref("extrahps");
		if (!get_module_setting("carrydk")) {
			$args['extra'] -= get_module_pref("extrahps");
			Modules::set_module_pref("extrahps", 0);
		}
	}
	return $args;
}

function cedrikspotions_run(){
	global $session;
	$wish = Http::httppost('wish');
	$op = Http::httpget("op");
	$iname = Settings::getsetting("innname", LOCATION_INN);
	Translator::tlschema("inn");
	PageParts::page_header($iname);
	OutputClass::rawoutput("<span style='color: #9900FF'>");
	OutputClass::output_notl("`c`b");
	OutputClass::output($iname);
	OutputClass::output_notl("`b`c");
	Translator::tlschema();
	$barkeep = Settings::getsetting("barkeep","`tCedrik");
	$mincost = 0;
	$maxcost = 0;
	$cost = 0;
	$gemcount = Http::httppost('gemcount');
	if ($gemcount == "") {
		if(get_module_setting("random")) {
			$cost =get_module_setting("randcost");
		} else {
			if (get_module_setting("ischarm")) {
				$cm = get_module_setting("charmcost");
				if ($mincost==0 || $cm < $mincost) $mincost = $cm;
				if ($maxcost==0 || $cm > $maxcost) $maxcost = $cm;
			}
			if (get_module_setting("ismax")) {
				$cm = get_module_setting("maxcost");
				if ($mincost==0 || $cm < $mincost) $mincost = $cm;
				if ($maxcost==0 || $cm > $maxcost) $maxcost = $cm;
			}
			if (get_module_setting("istemp")) {
				$cm = get_module_setting("tempcost");
				if ($mincost==0 || $cm < $mincost) $mincost = $cm;
				if ($maxcost==0 || $cm > $maxcost) $maxcost = $cm;
			}
			if (get_module_setting("isforget")) {
				$cm = get_module_setting("forgcost");
				if ($mincost==0 || $cm < $mincost) $mincost = $cm;
				if ($maxcost==0 || $cm > $maxcost) $maxcost = $cm;
			}
			if (get_module_setting("istrans")) {
				$cm = get_module_setting("transcost");
				if ($mincost==0 || $cm < $mincost) $mincost = $cm;
				if ($maxcost==0 || $cm > $maxcost) $maxcost = $cm;
			}
			if ($mincost == $maxcost) $cost = $mincost;
		}
	}

	if (!get_module_setting("random")){
		switch ($wish){
		case 1:
			$cost = get_module_setting("charmcost");
			break;
		case 2:
			$cost = get_module_setting("maxcost");
			break;
		case 3:
			$cost = get_module_setting("tempcost");
			break;
		case 4:
			$cost = get_module_setting("forgcost");
			break;
		case 5:
			$cost = get_module_setting("transcost");
			break;
		}
	}else{
		$cost = get_module_setting("randcost");
	}

	if($op=="gems"){
		if ($gemcount==""){
			if (get_module_setting("random") || $mincost == $maxcost) {
				OutputClass::output("\"`%You have gems, do ya?`0\" %s`0 asks.  \"`%Well, I'll make you a magic elixir for `^ %s %s`%!`0\"",$barkeep,$cost, Translator::translate_inline($cost == 1?"gem" : "gems"));
			} else {
				OutputClass::output("\"`%You have gems, do ya?`0\" %s`0 asks.  \"`%Well, I'll make you a magic elixir for between `^%s and %s gems`%, depending on which one you want!`0\"",$barkeep,$mincost, $maxcost);
			}
			OutputClass::output("`n`nGive him how many gems?");
			$give = Translator::translate_inline("Give");
			$link = appendcount("runmodule.php?module=cedrikspotions&op=gems");
			OutputClass::addnav("", $link);
			OutputClass::rawoutput("<form action='$link' method='POST'>");
			OutputClass::rawoutput("<input name='gemcount' value='0'>");
			OutputClass::rawoutput("<input type='submit' class='button' value='$give'>");
			OutputClass::output("`nAnd what do you wish for?`n");
			if (get_module_setting("ischarm") == 1) {
				OutputClass::rawoutput("<input type='radio' name='wish' value='1' checked>");
				OutputClass::output("Charm");
				if ($mincost != $maxcost) {
					$cm = get_module_setting("charmcost");
					OutputClass::output("(%s %s for %s charm)", $cm, Translator::translate_inline($cm==1?"gem":"gems"), get_module_setting("charmgain"));
				}
				OutputClass::output_notl("`n");
			}
			if (get_module_setting("ismax") == 1) {
				OutputClass::rawoutput("<input type='radio' name='wish' value='2'>");
				OutputClass::output("Vitality");
				if ($mincost != $maxcost) {
					$cm = get_module_setting("maxcost");
					$hm = get_module_setting("vitalgain");
					$hptype = "permanent";
					if (!get_module_setting("carrydk") ||
							(Modules::is_module_active("globalhp") &&
							 !get_module_setting("carrydk", "globalhp")))
						$hptype = "temporary";
					$hptype = Translator::translate_inline($hptype);

					OutputClass::output("(%s %s for %s %s max %s)", $cm,
							Translator::translate_inline($cm==1?"gem":"gems"), $hm,
							$hptype,
							Translator::translate_inline($hm==1?"hitpoint":"hitpoints"));
				}
				OutputClass::output_notl("`n");
			}
			if (get_module_setting("istemp") == 1) {
				OutputClass::rawoutput("<input type='radio' name='wish' value='3'>");
				OutputClass::output("Health");
				if ($mincost != $maxcost) {
					$cm = get_module_setting("tempcost");
					$hm = get_module_setting("tempgain");
					OutputClass::output("(%s %s for %s %s)", $cm, Translator::translate_inline($cm==1?"gem":"gems"), $hm, Translator::translate_inline($hm == 1? "hitpoint":"hitpoints"));
				}
				OutputClass::output_notl("`n");
			}
			if (get_module_setting("isforget") == 1) {
				OutputClass::rawoutput("<input type='radio' name='wish' value='4'>");
				OutputClass::output("Forgetfulness");
				if ($mincost != $maxcost) {
					$cm = get_module_setting("forgcost");
					OutputClass::output_notl("(%s %s)", $cm, Translator::translate_inline($cm==1?"gem":"gems"));
				}
				OutputClass::output_notl("`n");
			}
			if (get_module_setting("istrans") == 1) {
				OutputClass::rawoutput("<input type='radio' name='wish' value='5'>");
				OutputClass::output("Transmutation");
				if ($mincost != $maxcost) {
					$cm = get_module_setting("transcost");
					OutputClass::output_notl("(%s %s)", $cm, Translator::translate_inline($cm==1?"gem":"gems"));
				}
				OutputClass::output_notl("`n");
			}
			OutputClass::rawoutput("</form>");
		}else{
			$gemcount = abs((int)$gemcount);
			if ($gemcount>$session['user']['gems']){
				OutputClass::output("%s`0 stares at you blankly.",$barkeep);
				OutputClass::output("\"`%You don't have that many gems, `bgo get some more gems!`b`0\" he says.");
			}else{
				OutputClass::output("`#You place %s %s on the counter.", $gemcount, Translator::translate_inline($gemcount==1?"gem":"gems"));
				if (($wish == 4 || $wish == 5) && $gemcount > $cost) {
					OutputClass::output("%s`0, feeling sorry for you, prevents you from paying for multiple doses of a potion that only needs a single dose.",$barkeep);
					$gemcount = $cost;
				}
				$strength = ($gemcount/$cost);
				if(!is_integer($strength)){
					OutputClass::output("%s`0, knowing about your fundamental misunderstanding of math, hands some of them back to you.",$barkeep);
					$strength = floor($strength);
					$gemcount=($strength * $cost);
				}
				if ($gemcount>0) {
					OutputClass::output("You drink the potion %s`0 hands you in exchange for your %s, and.....`n`n",$barkeep, Translator::translate_inline($gemcount==1?"gem":"gems"));
					$session['user']['gems']-=$gemcount;
					switch($wish){
					case 1:
						$session['user']['charm'] += ($strength *
								get_module_setting("charmgain"));
						OutputClass::output("`&You feel charming!");
						OutputClass::output("`^(You gain %s charm %s.)",
								$strength*get_module_setting("charmgain"),
								Translator::translate_inline($strength *
									get_module_setting("charmgain")==1 ?
									"point" : "points"));
						$potiontype = "charm";
						break;
					case 2:
						$session['user']['maxhitpoints'] +=
							($strength*get_module_setting("vitalgain"));
						$session['user']['hitpoints'] +=
							($strength*get_module_setting("vitalgain"));
						OutputClass::output("`&You feel vigorous!");
						$hptype = "permanently";
						if (!get_module_setting("carrydk") ||
								(Modules::is_module_active("globalhp") &&
								 !get_module_setting("carrydk", "globalhp")))
							$hptype = "temporarily";
						$hptype = Translator::translate_inline($hptype);

						OutputClass::output("`^(You %s gain %s max %s.)", $hptype,
								$strength * get_module_setting("vitalgain"),
								Translator::translate_inline($strength *
									get_module_setting("vitalgain") == 1 ?
									"hitpoint" : "hitpoints"));
						$potiontype = "vitality";
						Modules::set_module_pref("extrahps",
								get_module_pref("extrahps") +
								($strength * get_module_setting("vitalgain")));
						break;
					case 3:
						if ($session['user']['hitpoints'] <
								$session['user']['maxhitpoints'])
							$session['user']['hitpoints'] =
								$session['user']['maxhitpoints'];
						$session['user']['hitpoints'] +=
							($strength * get_module_setting("tempgain"));
						OutputClass::output("`&You feel healthy!");
						OutputClass::output("`^(You gain %s temporary %s.)",
								$strength * get_module_setting("tempgain"),
								Translator::translate_inline($strength *
									get_module_setting("tempgain") == 1 ?
									"hitpoint" : "hitpoints"));
						$potiontype = "health";
						break;
					case 4:
						$session['user']['specialty']="";
						OutputClass::output("`&You feel completely directionless in life.");
						OutputClass::output("You should rest and make some important decisions about your life!");
						OutputClass::output("`^(Your specialty has been reset.)");
						$potiontype = "forgetfulness";
						break;
					case 5:
						$session['user']['race']=RACE_UNKNOWN;
						OutputClass::output("`@You double over retching from the effects of transformation potion as your bones turn to gelatin!`n");
						OutputClass::output("`^(Your race has been reset and you will be able to chose a new one tomorrow.)");
						Buffs::strip_buff('racialbenefit');
						$potiontype = "transmutation";
						if (isset($session['bufflist']['transmute'])) {
							$session['bufflist']['transmute']['rounds'] += get_module_setting("transmuteturns");
						} else {
							Buffs::apply_buff('transmute',
								array("name"=>"`6Transmutation Sickness",
									"rounds"=>get_module_setting("transmuteturns"),
									"wearoff"=>"You stop puking your guts up.  Literally.",
									"atkmod"=>get_module_setting("atkmod"),
									"defmod"=>get_module_setting("defmod"),
									"roundmsg"=>"Bits of skin and bone reshape themselves like wax.",
									"survivenewday"=>get_module_setting("survive"),
									"newdaymessage"=>"`6Due to the effects of the Transmutation Potion, you still feel `2ill`6.",
									"schema"=>"module-cedrikspotions"
								)
							);
						}
						break;
					}
					DebugLogClass::debuglog("used $gemcount gems on $potiontype potions");
				}else{
					OutputClass::output("`n`nYou feel as though your gems would be better used elsewhere, not on some smelly potion.");
				}
			}
		}
		OutputClass::addnav("I?Return to the Inn","inn.php");
		VillageNavClass::villagenav();
	}
	OutputClass::rawoutput("</span>");
	PageParts::page_footer();
}
?>
