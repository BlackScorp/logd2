<?php
// translator ready
// addnews ready
// mail ready
function fightnav($allowspecial=true, $allowflee=true,$script=false){
	global $PHP_SELF,$session,$newenemies,$companions;
	Translator::tlschema("fightnav");
	if ($script===false){
		$script = substr($PHP_SELF,strrpos($PHP_SELF,"/")+1)."?";
	}else{
		if (!strpos($script,"?")) {
			$script.="?";
//		}elseif (substr($script,strlen($script)-1)!="&" && !substr($script,strlen($script)-1)=="?"){
		}elseif (substr($script,strlen($script)-1)!="&"){
			$script.="&";
		}
	}
	$fight = "Fight";
	$run = "Run";
	if (!$session['user']['alive']) {
		$fight = "F?Torment";
		$run = "R?Flee";
	}
	OutputClass::addnav($fight,$script."op=fight");
	if ($allowflee) {
		OutputClass::addnav($run,$script."op=run");
	}
	if ($session['user']['superuser'] & SU_DEVELOPER) {
		OutputClass::addnav("Abort", $script);
	}

	if (Settings::getsetting("autofight",0)) {
		OutputClass::addnav("Automatic Fighting");
		OutputClass::addnav("5?For 5 Rounds", $script."op=fight&auto=five");
		OutputClass::addnav("1?For 10 Rounds", $script."op=fight&auto=ten");
		$auto = Settings::getsetting("autofightfull",0);
		if (($auto == 1 || ($auto == 2 && !$allowflee)) && count($newenemies)==1) {
			OutputClass::addnav("U?Until End", $script."op=fight&auto=full");
		} elseif ($auto == 1 || ($auto == 2 && !$allowflee)) {
			OutputClass::addnav("U?Until current enemy dies", $script."op=fight&auto=full");
		}
	}

	if ($allowspecial) {
		OutputClass::addnav("Special Abilities");
		Modules::modulehook("fightnav-specialties", array("script"=>$script));

		if ($session['user']['superuser'] & SU_DEVELOPER) {
			OutputClass::addnav("`&Super user`0","");
			OutputClass::addnav("!?`&&#149; __GOD MODE",$script."op=fight&skill=godmode",true);
		}
		Modules::modulehook("fightnav", array("script"=>$script));
	}

	if (count($newenemies) > 1) {
		OutputClass::addnav("Targets");
		foreach ($newenemies as $index=>$badguy){
			if ($badguy['creaturehealth'] <= 0 || (isset($badguy['dead']) && $badguy['dead'] == true)) continue;
			OutputClass::addnav(array("%s%s`0",(isset($badguy['istarget'])&&$badguy['istarget'])?"`#*`0":"", $badguy['creaturename']), $script."op=fight&newtarget=$index");
		}
	}
	Translator::tlschema();
}
?>