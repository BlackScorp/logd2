<?php
// addnews ready
// mail ready
// translator ready
function game_stones_getmoduleinfo(){
	$info = array(
		"name"=>"Stones Game for DarkHorse",
		"author"=>"Eric Stevens",
		"version"=>"1.1",
		"category"=>"Darkhorse Game",
		"download"=>"core_module",
	);
	return $info;
}

function game_stones_install(){
	global $session;
	debug("Adding Hooks");
	module_addhook("darkhorsegame");
	return true;
}

function game_stones_uninstall(){
	OutputClass::output("Uninstalling this module.`n");
	return true;
}

function game_stones_dohook($hookname, $args){
	if ($hookname=="darkhorsegame"){
		$ret = urlencode($args['return']);
		OutputClass::addnav("S?Play Stones Game",
				"runmodule.php?module=game_stones&ret=$ret");
	}
	return $args;
}

function game_stones_run(){
	global $session;
	$ret = urlencode(Http::httpget("ret"));
	PageParts::page_header("A Game of Stones");
	$stones = unserialize($session['user']['specialmisc']);
	if (!is_array($stones)) $stones = array();
	$side = Http::httpget('side');
	if ($side=="likepair") $stones['side']="likepair";
	if ($side=="unlikepair") $stones['side']="unlikepair";
	$bet = httppost('bet');
	if ($bet != "")
		$stones['bet'] = min($session['user']['gold'], abs((int)$bet));
	if (!isset($stones['side']) || $stones['side']==""){
		OutputClass::output("`3The old man explains his game, \"`7I have a bag with 6 red stones, and 10 blue stones in it.  You can choose between 'like pair' or 'unlike pair.'  I will then draw out pairs of stones two at a time.  If they are the same color as each other, they go to which ever of us is 'like pair,' and otherwise they go to which ever of us is 'unlike pair.'  Whoever has the most stones at the end will win.  If we have the same number, then it is a draw, and no one wins.`3\"");
		OutputClass::addnav("Never Mind", appendlink(urldecode($ret), "op=oldman"));
		OutputClass::addnav("Like Pair",
				"runmodule.php?module=game_stones&side=likepair&ret=$ret");
		OutputClass::addnav("Unlike Pair",
				"runmodule.php?module=game_stones&side=unlikepair&ret=$ret");
		$stones['red']=6;
		$stones['blue']=10;
		$stones['player']=0;
		$stones['oldman']=0;
	}elseif (!isset($stones['bet']) || $stones['bet']==0){
		$s1 = Translator::translate_inline($stones['side']=="likepair"?"Like":"Unlike");
		$s2 = Translator::translate_inline($stones['side']=="likepair"?"unlike":"like");
		OutputClass::output("`3\"`7%s pair for you, and %s pair for me it is then!  How much do you bet?`3\"", $s1, $s2);
		OutputClass::rawoutput("<form action='runmodule.php?module=game_stones&ret=$ret' method='POST'>");
		OutputClass::rawoutput("<input name='bet' id='bet'>");
		$b = Translator::translate_inline("Bet");
		OutputClass::rawoutput("<input type='submit' class='button' value='$b'>");
		OutputClass::rawoutput("</form>");
		OutputClass::rawoutput("<script language='JavaScript'>document.getElementById('bet').focus();</script>");
		OutputClass::addnav("","runmodule.php?module=game_stones&ret=$ret");
		OutputClass::addnav("Never Mind", appendlink(urldecode($ret), "op=oldman"));
	}elseif ($stones['red']+$stones['blue'] > 0 &&
			$stones['oldman']<=8 && $stones['player']<=8){
		$s1="";
		$s2="";
		$rstone = Translator::translate_inline("`\$red`3");
		$bstone = Translator::translate_inline("`!blue`3");
		while ($s1=="" || $s2==""){
			$s1 = e_rand(1,($stones['red']+$stones['blue']));
			if ($s1<=$stones['red']) {
				$s1=$rstone;
				$stones['red']--;
			}else{
				$s1=$bstone;
				$stones['blue']--;
			}
			if ($s2=="") {
				$s2=$s1;
				$s1="";
			}
		}
		OutputClass::output("`3The old man reaches into his bag and withdraws two stones.");
		OutputClass::output("They are %s and %s.  Your bet is `^%s`3.`n`n", $s1, $s2, $stones['bet']);

		if ($stones['side']=="likepair" && $s1==$s2) {
			$winner="your";
			$stones['player']+=2;
		} elseif ($stones['side']!="likepair" && $s1!=$s2) {
			$winner="your";
			$stones['player']+=2;
		} else {
			$stones['oldman']+=2;
			$winner = "his";
		}
		$winner = Translator::translate_inline($winner);

		OutputClass::output("Since you are %s pairs, the old man places the stones in %s pile.`n`n", Translator::translate_inline($stones['side']=="likepair"?"like":"unlike"), $winner);

		OutputClass::output("You currently have `^%s`3 stones in your pile, and the old man has `^%s`3 stones in his.`n`n", $stones['player'], $stones['oldman']);
		OutputClass::output("There are %s %s stones and %s %s stones in the bag yet.", $stones['red'], $rstone, $stones['blue'], $bstone);
		OutputClass::addnav("Continue","runmodule.php?module=game_stones&ret=$ret");
	}else{
		if ($stones['player']>$stones['oldman']){
			OutputClass::output("`3Having defeated the old man at his game, you claim your `^%s`3 gold.", $stones['bet']);
			$session['user']['gold']+=$stones['bet'];
			debuglog("won {$stones['bet']} gold in the stones game");
		}elseif ($stones['player']<$stones['oldman']){
			OutputClass::output("`3Having defeated you at his game, the old man claims your `^%s`3 gold.", $stones['bet']);
			$session['user']['gold']-=$stones['bet'];
			debuglog("lost {$stones['bet']} gold in the stones game");
		}else{
			OutputClass::output("`3Having tied the old man, you call it a draw.");
		}
		$stones=array();
		OutputClass::addnav("Play again?","runmodule.php?module=game_stones&ret=$ret");
		OutputClass::addnav("Other Games",appendlink(urldecode($ret), "op=oldman"));
		OutputClass::addnav("Return to Main Room", appendlink(urldecode($ret), "op=tavern"));
	}
	$session['user']['specialmisc']=serialize($stones);
	page_footer();
}
?>
