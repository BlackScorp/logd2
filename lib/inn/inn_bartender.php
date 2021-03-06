<?php
$act = Http::httpget('act');
if ($act==""){
	OutputClass::output("%s`0 looks at you sort-of sideways like.",$barkeep);
	OutputClass::output("He never was the sort who would trust a man any farther than he could throw them, which gave dwarves a decided advantage, except in provinces where dwarf tossing was made illegal.");
	OutputClass::output("%s`0 polishes a glass, holds it up to the light of the door as another patron opens it to stagger out into the street.",$barkeep);
	OutputClass::output("He then makes a face, spits on the glass and goes back to polishing it.");
	OutputClass::output("\"`%What d'ya want?`0\" he asks gruffly.");
	addnav_notl(SanitizeClass::sanitize($barkeep));
	OutputClass::addnav("Bribe","inn.php?op=bartender&act=bribe");
	OutputClass::addnav("Drinks");
	Modules::modulehook("ale", array());
}elseif ($act=="bribe"){
	$g1 = $session['user']['level']*10;
	$g2 = $session['user']['level']*50;
	$g3 = $session['user']['level']*100;
	$type = Http::httpget('type');
	if ($type==""){
		OutputClass::output("While you know that you won't always get what you want, sometimes the way to a man's information is through your purse.");
		OutputClass::output("It's also always been said that more is better.`n`n");
		OutputClass::output("How much would you like to offer him?");
		OutputClass::addnav("1 gem","inn.php?op=bartender&act=bribe&type=gem&amt=1");
		OutputClass::addnav("2 gems","inn.php?op=bartender&act=bribe&type=gem&amt=2");
		OutputClass::addnav("3 gems","inn.php?op=bartender&act=bribe&type=gem&amt=3");
		OutputClass::addnav(array("%s gold", $g1),"inn.php?op=bartender&act=bribe&type=gold&amt=$g1");
		OutputClass::addnav(array("%s gold", $g2),"inn.php?op=bartender&act=bribe&type=gold&amt=$g2");
		OutputClass::addnav(array("%s gold", $g3),"inn.php?op=bartender&act=bribe&type=gold&amt=$g3");
	}else{
		$amt = Http::httpget('amt');
		if ($type=="gem"){
			if ($session['user']['gems']<$amt){
				$try=false;
				OutputClass::output("You don't have %s gems!", $amt);
			}else{
				$chance = $amt*30;
				$session['user']['gems']-=$amt;
				DebugLogClass::debuglog("spent $amt gems on bribing $barkeep");
				$try=true;
			}
		}else{
			if ($session['user']['gold']<$amt){
				OutputClass::output("You don't have %s gold!", $amt);
				$try=false;
			}else{
				$try=true;
				$sfactor = 50/90;
				$fact = $amt/$session['user']['level'];
				$chance = ($fact - 10)*$sfactor + 25;
					$session['user']['gold']-=$amt;
				DebugLogClass::debuglog("spent $amt gold bribing $barkeep");
			}
		}
		if ($try){
			if (Erand::e_rand(0,100)<$chance){
				OutputClass::output("%s`0 leans over the counter toward you.  \"`%What can I do for you, kid?`0\" he asks.",$barkeep);
				OutputClass::addnav("What do you want?");
				if (Settings::getsetting("pvp",1)) {
					OutputClass::addnav("Who's upstairs?","inn.php?op=bartender&act=listupstairs");
				}
				OutputClass::addnav("Tell me about colors","inn.php?op=bartender&act=colors");
				if (Settings::getsetting("allowspecialswitch", true))
					OutputClass::addnav("Switch specialty","inn.php?op=bartender&act=specialty");
			}else{
				OutputClass::output("%s`0 begins to wipe down the counter top, an act that really needed doing a long time ago.",$barkeep);
				if ($type == "gem") {
					if ($amt == 1) {
						OutputClass::output("When he's finished, your gem is gone.");
					} else{
						OutputClass::output("When he's finished, your gems are gone.");
					}
				} else {
					OutputClass::output("When he's finished, your gold is gone.");
				}
				OutputClass::output("You inquire about the loss, and he stares blankly back at you.");
				OutputClass::addnav(array("B?Talk to %s`0 again",$barkeep),"inn.php?op=bartender");
			}
		}else{
			OutputClass::output("`n`n%s`0 stands there staring at you blankly.",$barkeep);
			OutputClass::addnav(array("B?Talk to %s`0 the Barkeep",$barkeep),"inn.php?op=bartender");
		}
	}
}else if ($act=="listupstairs"){
	OutputClass::addnav("Refresh the list","inn.php?op=bartender&act=listupstairs");
	OutputClass::output("%s`0 lays out a set of keys on the counter top, and tells you which key opens whose room.  The choice is yours, you may sneak in and attack any one of them.",$barkeep);
	PvpListClass::pvplist($iname,"pvp.php", "?act=attack&inn=1");
}else if($act=="colors"){
	OutputClass::output("%s`0 leans on the bar.  \"`%So you want to know about colors, do you?`0\" he asks.",$barkeep);
	OutputClass::output("You are about to answer when you realize the question was posed in the rhetoric.");
	OutputClass::output("%s`0 continues, \"`%To do colors, here's what you need to do.",$barkeep);
	OutputClass::output(" First, you use a &#0096; mark (found right above the tab key) followed by 1, 2, 3, 4, 5, 6, 7, !, @, #, $, %, ^, &.", true);
	OutputClass::output("Each of those corresponds with a color to look like this:");
	OutputClass::output_notl("`n`1&#0096;1 `2&#0096;2 `3&#0096;3 `4&#0096;4 `5&#0096;5 `6&#0096;6 `7&#0096;7 ",true);
	OutputClass::output_notl("`n`!&#0096;! `@&#0096;@ `#&#0096;# `\$&#0096;\$ `%&#0096;% `^&#0096;^ `&&#0096;& `n",true);
	OutputClass::output("`% Got it?`0\"  You can practice below:");
	OutputClass::rawoutput("<form action=\"$REQUEST_URI\" method='POST'>",true);
	$testtext = Http::httppost('testtext');
	OutputClass::output("You entered %s`n", prevent_colors(HTMLEntities($testtext, ENT_COMPAT, Settings::getsetting("charset", "ISO-8859-1"))), true);
	OutputClass::output("It looks like %s`n", $testtext);
	$try = Translator::translate_inline("Try");
	OutputClass::rawoutput("<input name='testtext' id='input'>");
	OutputClass::rawoutput("<input type='submit' class='button' value='$try'>");
	OutputClass::rawoutput("</form>");
	OutputClass::rawoutput("<script language='javascript'>document.getElementById('input').focus();</script>");
		OutputClass::output("`0`n`nThese colors can be used in your name, and in any conversations you have.");
	OutputClass::addnav("",$REQUEST_URI);
}else if($act=="specialty"){
	$specialty = Http::httpget('specialty');
	if ($specialty==""){
		OutputClass::output("\"`2I want to change my specialty,`0\" you announce to %s`0.`n`n",$barkeep);
		OutputClass::output("With out a word, %s`0 grabs you by the shirt, pulls you over the counter, and behind the barrels behind him.",$barkeep);
		OutputClass::output("There, he rotates the tap on a small keg labeled \"Fine Swill XXX\"`n`n");
		OutputClass::output("You look around for the secret door that you know must be opening nearby when %s`0 rotates the tap back, and lifts up a freshly filled foamy mug of what is apparently his fine swill, blue-green tint and all.`n`n",$barkeep);
		OutputClass::output("\"`3What?  Were you expecting a secret room?`0\" he asks.  \"`3Now then, you must be more careful about how loudly you say that you want to change your specialty, not everyone looks favorably on that sort of thing.`n`n");
		OutputClass::output("`0\"`3What new specialty did you have in mind?`0\"");
		$specialities = Modules::modulehook("specialtynames");
		foreach($specialities as $key=>$name) {
			OutputClass::addnav($name,SanitizeClass::cmd_sanitize($REQUEST_URI)."&specialty=$key");
		}
	}else{
		OutputClass::output("\"`3Ok then,`0\" %s`0 says, \"`3You're all set.`0\"`n`n\"`2That's it?`0\" you ask him.`n`n",$barkeep);
		OutputClass::output("\"`3Yep.  What'd you expect, some sort of fancy arcane ritual???`0\"  %s`0 begins laughing loudly.",$barkeep);
		OutputClass::output("\"`3You're all right, kid... just don't ever play poker, eh?`0`n`n");
		OutputClass::output("\"`3Oh, one more thing.  Your old use points and skill level still apply to that skill, you'll have to build up some points in this one to be very good at it.`0\"");
		$session['user']['specialty']=$specialty;
	}
}
?>