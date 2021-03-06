<?php
// translator ready
// addnews ready
// mail ready
require_once("common.php");
require_once("lib/fightnav.php");
require_once("lib/pvpwarning.php");
require_once("lib/pvplist.php");
require_once("lib/pvpsupport.php");
require_once("lib/http.php");
require_once("lib/taunt.php");
require_once("lib/villagenav.php");

Translator::tlschema("pvp");

$iname = Settings::getsetting("innname", LOCATION_INN);
$battle = false;

PageParts::page_header("PvP Combat!");
$op = Http::httpget('op');
$act = Http::httpget('act');

if ($op=="" && $act!="attack"){
	GameDateTime::checkday();
	PvpWarningClass::pvpwarning();
	$args = array(
		'atkmsg'=> '`4You head out to the fields, where you know some unwitting warriors are sleeping.`n`nYou have `^%s`4 PvP fights left for today.`n`n',
		'schemas'=>array('atkmsg'=>'pvp')
	);
	$args = Modules::modulehook("pvpstart", $args);
	Translator::tlschema($args['schemas']['atkmsg']);
	OutputClass::output($args['atkmsg'], $session['user']['playerfights']);
	Translator::tlschema();
	OutputClass::addnav("L?Refresh List of Warriors","pvp.php");
	PvpListClass::pvplist();
	VillageNavClass::villagenav();
} else if ($act == "attack") {
	$name = Http::httpget('name');
	$badguy = PvpSupport::setup_target($name);
	$options['type'] = "pvp";
	$failedattack = false;
	if ($badguy === false) {
		$failedattack = true;
	} else {
		$battle=true;
		if ($badguy['location']==$iname) {
			$badguy['bodyguardlevel']=$badguy['boughtroomtoday'];
		}
		$attackstack['enemies'][0] = $badguy;
		$attackstack['options'] = $options;
		$session['user']['badguy']=ArrayUtil::createstring($attackstack);
		OutputClass::debug($session['user']['badguy']);
		$session['user']['playerfights']--;
	}

	if ($failedattack){
		if (Http::httpget('inn') > ""){
			OutputClass::addnav("Return to Listing","inn.php?op=bartender&act=listupstairs");
		}else{
			OutputClass::addnav("Return to Listing","pvp.php");
		}
	}
}

if ($op=="run"){
  OutputClass::output("Your pride prevents you from running");
  $op="fight";
  Http::httpset('op', $op);
}

$skill = Http::httpget('skill');
if ($skill!=""){
  OutputClass::output("Your honor prevents you from using any special ability");
  $skill="";
  Http::httpset('skill', $skill);
}
if ($op=="fight" || $op=="run"){
	$battle=true;
}
if ($battle){

	require_once("battle.php");

	if ($victory){
		$killedin = $badguy['location'];
		$handled = PvpSupport::pvpvictory($badguy, $killedin, $options);

		// Handled will be true if a module has already done the addnews or
		// whatever was needed.
		if (!$handled) {
			if ($killedin==$iname){
				AddNewsClass::addnews("`4%s`3 defeated `4%s`3 by sneaking into their room in the inn!",$session['user']['name'],$badguy['creaturename']);
			}else{
				AddNewsClass::addnews("`4%s`3 defeated `4%s`3 in fair combat in the fields of %s.", $session['user']['name'],$badguy['creaturename'], $killedin);
			}
		}

		$op = "";
		Http::httpset('op', $op);
		if ($killedin==$iname){
			OutputClass::addnav("Return to the inn","inn.php");
		} else {
			VillageNavClass::villagenav();
		}
		if ($session['user']['hitpoints'] <= 0) {
			OutputClass::output("`n`n`&Using a bit of cloth nearby, you manage to staunch your wounds so that you do not die as well.");
			$session['user']['hitpoints'] = 1;
		}
	}elseif($defeat){
		$killedin = $badguy['location'];
		$taunt = Taunt::select_taunt_array();
		// This is okay because system mail which is all it's used for is
		// not translated
		$handled = PvpSupport::pvpdefeat($badguy, $killedin, $taunt, $options);
		// Handled will be true if a module has already done the addnews or
		// whatever was needed.
		if (!$handled) {
			if ($killedin == $iname) {
				AddNewsClass::addnews("`%%s`5 has been slain while breaking into the inn room of `^%s`5 in order to attack them.`n%s`0", $session['user']['name'], $badguy['creaturename'], $taunt);
			}else {
				AddNewsClass::addnews("`%%s`5 has been slain while attacking `^%s`5 in the fields of `&%s`5.`n%s`0", $session['user']['name'], $badguy['creaturename'], $killedin, $taunt);
			}
		}
	}else{
		$extra = "";
		if (Http::httpget('inn')) $extra = "?inn=1";
		FightNavClass::fightnav(false,false, "pvp.php$extra");
	}
}
PageParts::page_footer();
?>