<?php
//addnews ready
// mail ready
// translator ready

function specialtythiefskills_getmoduleinfo(){
	$info = array(
		"name" => "Specialty - Thieving Skills",
		"author" => "Eric Stevens",
		"version" => "1.0",
		"download" => "core_module",
		"category" => "Specialties",
		"prefs" => array(
			"Specialty - Thieving Skills User Prefs,title",
			"skill"=>"Skill points in Thieving Skills,int|0",
			"uses"=>"Uses of Thieving Skills allowed,int|0",
		),
	);
	return $info;
}

function specialtythiefskills_install(){
	$sql = "DESCRIBE " . db_prefix("accounts");
	$result = db_query($sql);
	$specialty="TS";
	while($row = db_fetch_assoc($result)) {
		// Convert the user over
		if ($row['Field'] == "thievery") {
			OutputClass::debug("Migrating thieving skills field");
			$sql = "INSERT INTO " . db_prefix("module_userprefs") . " (modulename,setting,userid,value) SELECT 'specialtythiefskills', 'skill', acctid, thievery FROM " . db_prefix("accounts");
			db_query($sql);
			OutputClass::debug("Dropping thievery field from accounts table");
			$sql = "ALTER TABLE " . db_prefix("accounts") . " DROP thievery";
			db_query($sql);
		} elseif ($row['Field']=="thieveryuses") {
			OutputClass::debug("Migrating thieving skills uses field");
			$sql = "INSERT INTO " . db_prefix("module_userprefs") . " (modulename,setting,userid,value) SELECT 'specialtythiefskills', 'uses', acctid, thieveryuses FROM " . db_prefix("accounts");
			db_query($sql);
			OutputClass::debug("Dropping thieveryuses field from accounts table");
			$sql = "ALTER TABLE " . db_prefix("accounts") . " DROP thieveryuses";
			db_query($sql);
		}
	}
	OutputClass::debug("Migrating Thieving Skills Specialty");
	$sql = "UPDATE " . db_prefix("accounts") . " SET specialty='$specialty' WHERE specialty='3'";
	db_query($sql);

	module_addhook("choose-specialty");
	module_addhook("set-specialty");
	module_addhook("fightnav-specialties");
	module_addhook("apply-specialties");
	module_addhook("newday");
	module_addhook("incrementspecialty");
	module_addhook("specialtynames");
	module_addhook("specialtymodules");
	module_addhook("specialtycolor");
	module_addhook("dragonkill");
	return true;
}

function specialtythiefskills_uninstall(){
	// Reset the specialty of anyone who had this specialty so they get to
	// rechoose at new day
	$sql = "UPDATE " . db_prefix("accounts") . " SET specialty='' WHERE specialty='TS'";
	db_query($sql);
	return true;
}

function specialtythiefskills_dohook($hookname,$args){
	global $session,$resline;

	$spec = "TS";
	$name = "Thieving Skills";
	$ccode = "`^";

	switch ($hookname) {
	case "dragonkill":
		Modules::set_module_pref("uses", 0);
		Modules::set_module_pref("skill", 0);
		break;
	case "choose-specialty":
		if ($session['user']['specialty'] == "" ||
				$session['user']['specialty'] == '0') {
			OutputClass::addnav("$ccode$name`0","newday.php?setspecialty=".$spec."$resline");
			$t1 = Translator::translate_inline("Stealing from the rich and giving to yourself");
			$t2 = OutputClass::appoencode(Translator::translate_inline("$ccode$name`0"));
			OutputClass::rawoutput("<a href='newday.php?setspecialty=$spec$resline'>$t1 ($t2)</a><br>");
			OutputClass::addnav("","newday.php?setspecialty=$spec$resline");
		}
		break;
	case "set-specialty":
		if($session['user']['specialty'] == $spec) {
			PageParts::page_header($name);
			OutputClass::output("`6Growing up, you recall discovering that a casual bump in a crowded room could earn you the coin purse of someone otherwise more fortunate than you.");
			OutputClass::output("You also discovered that the back side of your enemies were considerably more prone to a narrow blade than the front side was to even a powerful weapon.");
		}
		break;
	case "specialtycolor":
		$args[$spec] = $ccode;
		break;
	case "specialtynames":
		$args[$spec] = Translator::translate_inline($name);
		break;
	case "specialtymodules":
		$args[$spec] = "specialtythiefskills";
		break;
	case "incrementspecialty":
		if($session['user']['specialty'] == $spec) {
			$new = get_module_pref("skill") + 1;
			Modules::set_module_pref("skill", $new);
			$name = Translator::translate_inline($name);
			$c = $args['color'];
			OutputClass::output("`n%sYou gain a level in `&%s%s to `#%s%s!",
					$c, $name, $c, $new, $c);
			$x = $new % 3;
			if ($x == 0){
				OutputClass::output("`n`^You gain an extra use point!`n");
				Modules::set_module_pref("uses", get_module_pref("uses") + 1);
			}else{
				if (3-$x == 1) {
					OutputClass::output("`n`^Only 1 more skill level until you gain an extra use point!`n");
				} else {
					OutputClass::output("`n`^Only %s more skill levels until you gain an extra use point!`n", (3-$x));
				}
			}
			OutputClass::output_notl("`0");
		}
		break;
	case "newday":
		$bonus = Settings::getsetting("specialtybonus", 1);
		if($session['user']['specialty'] == $spec) {
			$name = Translator::translate_inline($name);
			if ($bonus == 1) {
				OutputClass::output("`n`2For being interested in %s%s`2, you receive `^1`2 extra `&%s%s`2 use for today.`n",$ccode,$name,$ccode,$name);
			} else {
				OutputClass::output("`n`2For being interested in %s%s`2, you receive `^%s`2 extra `&%s%s`2 uses for today.`n",$ccode,$name,$bonus,$ccode,$name);
			}
		}
		$amt = (int)(get_module_pref("skill") / 3);
		if ($session['user']['specialty'] == $spec) $amt = $amt + $bonus;
		Modules::set_module_pref("uses", $amt);
		break;
	case "fightnav-specialties":
		$uses = get_module_pref("uses");
		$script = $args['script'];
		if ($uses > 0) {
			OutputClass::addnav(array("$ccode$name (%s points)`0", $uses), "");
			OutputClass::addnav(array("$ccode &#149; Insult`7 (%s)`0", 1), 
					$script."op=fight&skill=$spec&l=1", true);
		}
		if ($uses > 1) {
			OutputClass::addnav(array("$ccode &#149; Poison Blade`7 (%s)`0", 2),
					$script."op=fight&skill=$spec&l=2",true);
		}
		if ($uses > 2) {
			OutputClass::addnav(array("$ccode &#149; Hidden Attack`7 (%s)`0", 3),
					$script."op=fight&skill=$spec&l=3",true);
		}
		if ($uses > 4) {
			OutputClass::addnav(array("$ccode &#149; Backstab`7 (%s)`0", 5),
					$script."op=fight&skill=$spec&l=5",true);
		}
		break;
	case "apply-specialties":
		$skill = Http::httpget('skill');
		$l = Http::httpget('l');
		if ($skill==$spec){
			if (get_module_pref("uses") >= $l){
				switch($l){
				case 1:
					Buffs::apply_buff('ts1',array(
						"startmsg"=>"`^You call {badguy} a bad name, making it cry.",
						"name"=>"`^Insult",
						"rounds"=>5,
						"wearoff"=>"Your victim stops crying and wipes its nose.",
						"roundmsg"=>"{badguy} feels dejected and cannot attack as well.",
						"badguyatkmod"=>0.5,
						"schema"=>"module-specialtythiefskills"
					));
					break;
				case 2:
					Buffs::apply_buff('ts2',array(
						"startmsg"=>"`^You apply some poison to your {weapon}.",
						"name"=>"`^Poison Attack",
						"rounds"=>5,
						"wearoff"=>"Your victim's blood has washed the poison from your {weapon}.",
						"atkmod"=>2,
						"roundmsg"=>"Your attack is multiplied!", 
						"schema"=>"module-specialtythiefskills"
					));
					break;
				case 3:
					Buffs::apply_buff('ts3', array(
						"startmsg"=>"`^With the skill of an expert thief, you virtually disappear, and attack {badguy} from a safer vantage point.",
						"name"=>"`^Hidden Attack",
						"rounds"=>5,
						"wearoff"=>"Your victim has located you.",
						"roundmsg"=>"{badguy} cannot locate you, and swings wildly!",
						"badguyatkmod"=>0,
						"schema"=>"module-specialtythiefskills"
					));
					break;
				case 5:
					Buffs::apply_buff('ts5',array(
						"startmsg"=>"`^Using your skills as a thief, you disappear behind {badguy} and slide a thin blade between its vertebrae!",
						"name"=>"`^Backstab",
						"rounds"=>5,
						"wearoff"=>"Your victim won't be so likely to let you get behind it again!",
						"atkmod"=>3,
						"defmod"=>3,
						"roundmsg"=>"Your attack is multiplied, as is your defense!",
						"schema"=>"module-specialtythiefskills"
					));
					break;
				}
				Modules::set_module_pref("uses", get_module_pref("uses") - $l);
			}else{
				Buffs::apply_buff('ts0', array(
					"startmsg"=>"You try to attack {badguy} by putting your best thievery skills into practice, but instead, you trip over your feet.",
					"rounds"=>1,
					"schema"=>"module-specialtythiefskills"
				));
			}
		}
		break;
	}
	return $args;
}

function specialtythiefskills_run(){
}
?>
