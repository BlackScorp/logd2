<?php
function lovers_violet(){
	global $session;
	$seenlover = get_module_pref("seenlover");
	$partner = get_partner();

	if ($seenlover==0){
		if ($session['user']['marriedto']==INT_MAX){
			if (Erand::e_rand(1, 4)==1){
				switch(Erand::e_rand(1,4)){
				case 1:
					$msg = Translator::translate_inline("being too busy serving these pigs,");
					break;
				case 2:
					$msg = Translator::translate_inline("\"that time of month,\"");
					break;
				case 3:
					$msg = Translator::translate_inline("\"a little cold...  *cough cough* see?\"");
					break;
				case 4:
					$msg = Translator::translate_inline("men all being pigs,");
					break;
				}
				OutputClass::output("You head over to cuddle %s`0 and kiss her about the face and neck, but she grumbles something about %s and with a comment like that, you storm away from her!`n`n",$partner,$msg);
				$session['user']['charm']--;
				OutputClass::output("`^You LOSE a charm point!");
			}else{
				OutputClass::output("You and %s`0 take some time to yourselves, and you leave the inn, positively glowing!",$partner);
				apply_buff('lover',lovers_getbuff());
				$session['user']['charm']++;
				OutputClass::output("`n`n`^You gain a charm point!");
			}
			$seenlover = 1;
		}elseif (Http::httpget('flirt')==""){
			OutputClass::output("You stare dreamily across the room at %s`0, who leans across a table to serve a patron a drink.",$partner);
			OutputClass::output("In doing so, she shows perhaps a bit more skin than is necessary, but you don't feel the need to object.");
			OutputClass::addnav("Flirt");
			OutputClass::addnav("Wink","runmodule.php?module=lovers&op=flirt&flirt=1");
			OutputClass::addnav("Kiss her hand","runmodule.php?module=lovers&op=flirt&flirt=2");
			OutputClass::addnav("Peck her on the lips","runmodule.php?module=lovers&op=flirt&flirt=3");
			OutputClass::addnav("Sit her on your lap","runmodule.php?module=lovers&op=flirt&flirt=4");
			OutputClass::addnav("Grab her backside","runmodule.php?module=lovers&op=flirt&flirt=5");
			OutputClass::addnav("Carry her upstairs","runmodule.php?module=lovers&op=flirt&flirt=6");
			OutputClass::addnav("Marry her","runmodule.php?module=lovers&op=flirt&flirt=7");
		}else{
			$c = $session['user']['charm'];
			$seenlover = 1;
			switch(Http::httpget('flirt')){
				case 1:
					if (Erand::e_rand($c,2)>=2){
						OutputClass::output("You wink at %s`0, and she gives you a warm smile in return.",$partner);
						if ($c<4) $c++;
					}else{
						OutputClass::output("You wink at %s`0, but she pretends not to notice.",$partner);
					}
					break;
				case 2:
					OutputClass::output("You stroll confidently across the room toward %s`0.",$partner);
					if (Erand::e_rand($c,4)>=4){
						OutputClass::output("Taking hold of her hand, you kiss it gently, your lips remaining for only a few seconds.");
						OutputClass::output("%s`0 blushes and tucks a strand of hair behind her ear as you walk away, then presses the back side of her hand longingly against her cheek while watching your retreat.",$partner);
						if ($c<7) $c++;
					}else{
						OutputClass::output("You reach out to grab her hand, but %s`0 takes her hand back and asks if perhaps you'd like a drink.",$partner);
					}
					break;
				case 3:
					OutputClass::output("Standing with your back against a wooden column, you wait for %s`0 to wander your way when you call her name.",$partner);
					if (Erand::e_rand($c,7)>=7){
						OutputClass::output("She approaches, a hint of a smile on her face.");
						OutputClass::output("You grab her chin, lift it slightly, and place a firm but quick kiss on her plump lips.");
						if ($c<11) $c++;
					}else{
						OutputClass::output("She smiles and apologizes, insisting that she is simply too busy to take a moment from her work.");
					}
					break;
				case 4:
					OutputClass::output("Sitting at a table, you wait for %s`0 to come your way.",$partner);
					if (Erand::e_rand($c,11)>=11){
						OutputClass::output("When she does so, you reach up and grab her firmly by the waist, pulling her down on to your lap.");
						OutputClass::output("She laughs and throws her arms around your neck in a warm hug before thumping you on the chest, standing up, and insisting that she really must get back to work.");
						if ($c<14) $c++;
					}else{
						OutputClass::output("When she does so, you reach up to grab her by the waist, but she deftly dodges, careful not to spill the drink that she's carrying.");
						if ($c>0 && $c<10) $c--;
					}
					break;
				case 5:
					OutputClass::output("Waiting for %s`0 to brush by you, you firmly palm her backside.",$partner);
					if (Erand::e_rand($c,14)>=14){
						OutputClass::output("She turns and gives you a warm, knowing smile.");
						if ($c<18) $c++;
					}else{
						OutputClass::output("She turns and slaps you across the face. Hard.");
						OutputClass::output("Perhaps you should go a little slower.");
						if ($c>0 && $c<13) $c--;
					}
					break;
				case 6:
					if (Erand::e_rand($c,18)>=18){
						OutputClass::output("Like a whirlwind, you sweep through the inn, grabbing %s`0, who throws her arms around your neck, and whisk her upstairs to her room there.",$partner);
						OutputClass::output("Not more than 10 minutes later you stroll down the stairs, smoking a pipe, and grinning from ear to ear.");
						if ($session['user']['turns']>0){
							OutputClass::output("You feel exhausted!  ");
							$session['user']['turns']-=2;
							if ($session['user']['turns']<0) $session['user']['turns']=0;
						}
						AddNewsClass::addnews("`@%s`@ and %s`@ were seen heading up the stairs in the inn together.`0",$session['user']['name'],$partner);
						if ($c<25) $c++;
					}else{
						OutputClass::output("Like a whirlwind, you sweep through the inn, and grab for %s`0.",$partner);
						OutputClass::output("She turns and slaps your face!");
						OutputClass::output("\"`%What sort of girl do you think I am, anyhow?`0\" she demands! ");
						if ($c>0) $c--;
					}
					break;
				case 7:
					OutputClass::output("%s`0 is working feverishly to serve patrons of the inn.",$partner);
					OutputClass::output("You stroll up to her and take the mugs out of her hand, placing them on a nearby table.");
					OutputClass::output("Amidst her protests you kneel down on one knee, taking her hand in yours.");
					OutputClass::output("She quiets as you stare up at her and utter the question that you never thought you'd utter.");
					OutputClass::output("She stares at you and you immediately know the answer by the look on her face.`n`n");
					if ($c>=22){
						OutputClass::output("It is a look of exceeding happiness.");
						OutputClass::output("\"`%Yes!`0\" she says, \"`%Yes, yes yes!!!`0\"");
						OutputClass::output("Her final confirmations are buried in a flurry of kisses about your face and neck.`n`n");
						OutputClass::output("The next days are a blur; you and %s`0 are married in the abbey down the street, in a gorgeous ceremony with many frilly girly things.",$partner);
						AddNewsClass::addnews("`&%s`& and %s`& are joined today in joyous matrimony!!!",$session['user']['name'],$partner);
						$session['user']['marriedto']=INT_MAX;
						apply_buff('lover',lovers_getbuff());
					}else{
						OutputClass::output("It is a look of sadness.");
						OutputClass::output("\"`%No`0,\" she says, \"`%I'm not yet ready to settle down`0.\"`n`n");
						OutputClass::output("Disheartened, you no longer possess the will to pursue any more forest adventures today.");
						$session['user']['turns']=0;
						DebugLogClass::debuglog("lost all turns after being rejected for marriage.");
					}
			}
			if ($c > $session['user']['charm'])
				OutputClass::output("`n`n`^You gain a charm point!");
			if ($c < $session['user']['charm'])
				OutputClass::output("`n`n`\$You LOSE a charm point!");
			$session['user']['charm']=$c;
		}
	}else{
		OutputClass::output("You think you had better not push your luck with %s`0 today.",$partner);
	}
	set_module_pref("seenlover",$seenlover);
}
?>