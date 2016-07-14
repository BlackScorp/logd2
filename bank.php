<?php
// translator ready
// addnews ready
// mail ready
require_once("common.php");
require_once("lib/systemmail.php");
require_once("lib/sanitize.php");
require_once("lib/http.php");
require_once("lib/villagenav.php");

Translator::tlschema("bank");

PageParts::page_header("Ye Olde Bank");
OutputClass::output("`^`c`bYe Olde Bank`b`c");
$op = Http::httpget('op');
if ($op==""){
  GameDateTime::checkday();
  OutputClass::output("`6As you approach the pair of impressive carved rock crystal doors, they part to allow you entrance into the bank.");
  OutputClass::output("You find yourself standing in a room of exquisitely vaulted ceilings of carved stone.");
  OutputClass::output("Light filters through tall windows in shafts of soft radiance.");
  OutputClass::output("About you, clerks are bustling back and forth.");
  OutputClass::output("The sounds of gold being counted can be heard, though the treasure is nowhere to be seen.`n`n");
  OutputClass::output("You walk up to a counter of jet black marble.`n`n");
  OutputClass::output("`@Elessa`6, a petite woman in an immaculately tailored business dress, greets you from behind reading spectacles with polished silver frames.`n`n");
  OutputClass::output("`6\"`5Greetings, my good lady,`6\" you greet her, \"`5Might I inquire as to my balance this fine day?`6\"`n`n");
  OutputClass::output("`@Elessa`6 blinks for a moment and then smiles, \"`@Hmm, `&%s`@, let's see.....`6\" she mutters as she scans down a page in her ledger.",$session['user']['name']);
	if ($session['user']['goldinbank']>=0){
		OutputClass::output("`6\"`@Aah, yes, here we are.  You have `^%s gold`@ in our prestigious bank.  Is there anything else I can do for you?`6\"",$session['user']['goldinbank']);
	}else{
		OutputClass::output("`6\"`@Aah, yes, here we are.  You have a `&debt`@ of `^%s gold`@ in our prestigious bank.  Is there anything else I can do for you?`6\"",abs($session['user']['goldinbank']));
	}
}elseif($op=="transfer"){
	OutputClass::output("`6`bTransfer Money`b:`n");
	if ($session['user']['goldinbank']>=0){
		OutputClass::output("`@Elessa`6 tells you, \"`@Just so that you are fully aware of our policies, you may only transfer `^%s`@ gold per the recipient's level.",Settings::getsetting("transferperlevel",25));
		$maxout = $session['user']['level']*Settings::getsetting("maxtransferout",25);
		OutputClass::output("Similarly, you may transfer no more than `^%s`@ gold total during the day.`6\"`n",$maxout);
		if ($session['user']['amountouttoday'] > 0) {
			OutputClass::output("`6She scans her ledgers briefly, \"`@For your knowledge, you have already transferred `^%s`@ gold today.`6\"`n",$session['user']['amountouttoday']);
		}
		OutputClass::output_notl("`n");
		$preview = Translator::translate_inline("Preview Transfer");
		OutputClass::rawoutput("<form action='bank.php?op=transfer2' method='POST'>");
		OutputClass::output("Transfer how much: ");
		OutputClass::rawoutput("<input name='amount' id='amount' width='5'>");
		OutputClass::output_notl("`n");
		OutputClass::output("To: ");
		OutputClass::rawoutput("<input name='to'>");
		OutputClass::output(" (partial names are ok, you will be asked to confirm the transaction before it occurs).`n");
		OutputClass::rawoutput("<input type='submit' class='button' value='$preview'></form>");
		OutputClass::rawoutput("<script language='javascript'>document.getElementById('amount').focus();</script>");
		OutputClass::addnav("","bank.php?op=transfer2");
	}else{
		OutputClass::output("`@Elessa`6 tells you that she refuses to transfer money for someone who is in debt.");
	}
}elseif($op=="transfer2"){
	OutputClass::output("`6`bConfirm Transfer`b:`n");
	$string="%";
	$to = Http::httppost('to');
	for ($x=0;$x<strlen($to);$x++){
		$string .= substr($to,$x,1)."%";
	}
	$sql = "SELECT name,login FROM " . db_prefix("accounts") . " WHERE name LIKE '".addslashes($string)."' AND locked=0 ORDER by login='$to' DESC, name='$to' DESC, login";
	$result = db_query($sql);
	$amt = abs((int)Http::httppost('amount'));
	if (db_num_rows($result)==1){
		$row = db_fetch_assoc($result);
		$msg = Translator::translate_inline("Complete Transfer");
		OutputClass::rawoutput("<form action='bank.php?op=transfer3' method='POST'>");
		OutputClass::output("`6Transfer `^%s`6 to `&%s`6.",$amt,$row['name']);
		OutputClass::rawoutput("<input type='hidden' name='to' value='".HTMLEntities($row['login'], ENT_COMPAT, Settings::getsetting("charset", "ISO-8859-1"))."'><input type='hidden' name='amount' value='$amt'><input type='submit' class='button' value='$msg'></form>",true);
		OutputClass::addnav("","bank.php?op=transfer3");
	}elseif(db_num_rows($result)>100){
		OutputClass::output("`@Elessa`6 looks at you disdainfully and coldly, but politely, suggests you try narrowing down the field of who you want to send money to just a little bit!`n`n");
		$msg = Translator::translate_inline("Preview Transfer");
		OutputClass::rawoutput("<form action='bank.php?op=transfer2' method='POST'>");
		OutputClass::output("Transfer how much: ");
		OutputClass::rawoutput("<input name='amount' id='amount' width='5' value='$amt'><br>");
		OutputClass::output("To: ");
		OutputClass::rawoutput("<input name='to' value='$to'>");
		OutputClass::output(" (partial names are ok, you will be asked to confirm the transaction before it occurs).`n");
		OutputClass::rawoutput("<input type='submit' class='button' value='$msg'></form>");
		OutputClass::rawoutput("<script language='javascript'>document.getElementById('amount').focus();</script>",true);
		OutputClass::addnav("","bank.php?op=transfer2");
	}elseif(db_num_rows($result)>1){
		OutputClass::rawoutput("<form action='bank.php?op=transfer3' method='POST'>");
		OutputClass::output("`6Transfer `^%s`6 to ",$amt);
		OutputClass::rawoutput("<select name='to' class='input'>");
		$number=db_num_rows($result);
		for ($i=0;$i<$number;$i++){
			$row = db_fetch_assoc($result);
			OutputClass::rawoutput("<option value=\"".HTMLEntities($row['login'], ENT_COMPAT, Settings::getsetting("charset", "ISO-8859-1"))."\">".full_sanitize($row['name'])."</option>");
		}
		$msg = Translator::translate_inline("Complete Transfer");
		OutputClass::rawoutput("</select><input type='hidden' name='amount' value='$amt'><input type='submit' class='button' value='$msg'></form>",true);
		OutputClass::addnav("","bank.php?op=transfer3");
	}else{
		OutputClass::output("`@Elessa`6 blinks at you from behind her spectacles, \"`@I'm sorry, but I can find no one matching that name who does business with our bank!  Please try again.`6\"");
	}
}elseif($op=="transfer3"){
	$amt = abs((int)Http::httppost('amount'));
	$to = Http::httppost('to');
	OutputClass::output("`6`bTransfer Completion`b`n");
	if ($session['user']['gold']+$session['user']['goldinbank']<$amt){
		OutputClass::output("`@Elessa`6 stands up to her full, but still diminutive height and glares at you, \"`@How can you transfer `^%s`@ gold when you only possess `^%s`@?`6\"",$amt,$session['user']['gold']+$session['user']['goldinbank']);
	}else{
		$sql = "SELECT name,acctid,level,transferredtoday FROM " . db_prefix("accounts") . " WHERE login='$to'";
		$result = db_query($sql);
		if (db_num_rows($result)==1){
			$row = db_fetch_assoc($result);
			$maxout = $session['user']['level']*Settings::getsetting("maxtransferout",25);
			$maxtfer = $row['level']*Settings::getsetting("transferperlevel",25);
			if ($session['user']['amountouttoday']+$amt > $maxout) {
				OutputClass::output("`@Elessa`6 shakes her head, \"`@I'm sorry, but I cannot complete that transfer; you are not allowed to transfer more than `^%s`@ gold total per day.`6\"",$maxout);
			}else if ($maxtfer<$amt){
				OutputClass::output("`@Elessa`6 shakes her head, \"`@I'm sorry, but I cannot complete that transfer; `&%s`@ may only receive up to `^%s`@ gold per day.`6\"",$row['name'],$maxtfer);
			}else if($row['transferredtoday']>=Settings::getsetting("transferreceive",3)){
				OutputClass::output("`@Elessa`6 shakes her head, \"`@I'm sorry, but I cannot complete that transfer; `&%s`@ has received too many transfers today, you will have to wait until tomorrow.`6\"",$row['name']);
			}else if($amt<(int)$session['user']['level']){
				OutputClass::output("`@Elessa`6 shakes her head, \"`@I'm sorry, but I cannot complete that transfer; you might want to send a worthwhile transfer, at least as much as your level.`6\"");
			}else if($row['acctid']==$session['user']['acctid']){
				OutputClass::output("`@Elessa`6 glares at you, her eyes flashing dangerously, \"`@You may not transfer money to yourself!  That makes no sense!`6\"");
			}else{
				debuglog("transferred $amt gold to", $row['acctid']);
				$session['user']['gold']-=$amt;
				if ($session['user']['gold']<0){
					//withdraw in case they don't have enough on hand.
					$session['user']['goldinbank']+=$session['user']['gold'];
					$session['user']['gold']=0;
				}
				$session['user']['amountouttoday']+= $amt;
				$sql = "UPDATE ". db_prefix("accounts") . " SET goldinbank=goldinbank+$amt,transferredtoday=transferredtoday+1 WHERE acctid='{$row['acctid']}'";
				db_query($sql);
				OutputClass::output("`@Elessa`6 smiles, \"`@The transfer has been completed!`6\"");
				$subj = array("`^You have received a money transfer!`0");
				$body = array("`&%s`6 has transferred `^%s`6 gold to your bank account!",$session['user']['name'],$amt);
				systemmail($row['acctid'],$subj,$body);
			}
		}else{
			OutputClass::output("`@Elessa`6 looks up from her ledger with a bit of surprise on her face, \"`@I'm terribly sorry, but I seem to have run into an accounting error, would you please try telling me what you wish to transfer again?`6\"");
		}
	}
}elseif($op=="deposit"){
	OutputClass::output("`0");
	OutputClass::rawoutput("<form action='bank.php?op=depositfinish' method='POST'>");
	$balance = Translator::translate_inline("`@Elessa`6 says, \"`@You have a balance of `^%s`@ gold in the bank.`6\"`n");
	$debt = Translator::translate_inline("`@Elessa`6 says, \"`@You have a `\$debt`@ of `^%s`@ gold to the bank.`6\"`n");
	OutputClass::output_notl($session['user']['goldinbank']>=0?$balance:$debt,abs($session['user']['goldinbank']));
	OutputClass::output("`6Searching through all your pockets and pouches, you calculate that you currently have `^%s`6 gold on hand.`n`n", $session['user']['gold']);
	$dep = Translator::translate_inline("`^Deposit how much?");
	$pay = Translator::translate_inline("`^Pay off how much?");
	OutputClass::output_notl($session['user']['goldinbank']>=0?$dep:$pay);
	$dep = Translator::translate_inline("Deposit");
	OutputClass::rawoutput(" <input id='input' name='amount' width=5 > <input type='submit' class='button' value='$dep'>");
	OutputClass::output("`n`iEnter 0 or nothing to deposit it all`i");
	OutputClass::rawoutput("</form>");
	OutputClass::rawoutput("<script language='javascript'>document.getElementById('input').focus();</script>",true);
  OutputClass::addnav("","bank.php?op=depositfinish");
}elseif($op=="depositfinish"){
	$amount = abs((int)Http::httppost('amount'));
	if ($amount==0){
		$amount=$session['user']['gold'];
	}
	$notenough = Translator::translate_inline("`\$ERROR: Not enough gold in hand to deposit.`n`n`^You plunk your `&%s`^ gold on the counter and declare that you would like to deposit all `&%s`^ gold of it.`n`n`@Elessa`6 stares blandly at you for a few seconds until you become self conscious and recount your money, realizing your mistake.");
	$depositdebt = Translator::translate_inline("`@Elessa`6 records your deposit of `^%s `6gold in her ledger. \"`@Thank you, `&%s`@.  You now have a debt of `\$%s`@ gold to the bank and `^%s`@ gold in hand.`6\"");
	$depositbalance= Translator::translate_inline("`@Elessa`6 records your deposit of `^%s `6gold in her ledger. \"`@Thank you, `&%s`@.  You now have a balance of `^%s`@ gold in the bank and `^%s`@ gold in hand.`6\"");
	if ($amount>$session['user']['gold']){
		OutputClass::output_notl($notenough,$session['user']['gold'],$amount);
	}else{
		debuglog("deposited " . $amount . " gold in the bank");
		$session['user']['goldinbank']+=$amount;
		$session['user']['gold']-=$amount;
		OutputClass::output_notl($session['user']['goldinbank']>=0?$depositbalance:$depositdebt,$amount,$session['user']['name'], abs($session['user']['goldinbank']),$session['user']['gold']);
	}
}elseif($op=="borrow"){
	$maxborrow = $session['user']['level']*Settings::getsetting("borrowperlevel",20);
	$borrow = Translator::translate_inline("Borrow");
	$balance = Translator::translate_inline("`@Elessa`6 scans through her ledger, \"`@You have a balance of `^%s`@ gold in the bank.`6\"`n");
	$debt = Translator::translate_inline("`@Elessa`6 scans through her ledger, \"`@You have a `\$debt`@ of `^%s`@ gold to the bank.`6\"`n");
	OutputClass::rawoutput("<form action='bank.php?op=withdrawfinish' method='POST'>");
	OutputClass::output_notl($session['user']['goldinbank']>=0?$balance:$debt,abs($session['user']['goldinbank']));
	OutputClass::output("`6\"`@How much would you like to borrow `&%s`@?  At your level, you may borrow up to a total of `^%s`@ from the bank.`6\"`n`n",$session['user']['name'], $maxborrow);
	OutputClass::rawoutput(" <input id='input' name='amount' width=5 > <input type='hidden' name='borrow' value='x'><input type='submit' class='button' value='$borrow'>");
	OutputClass::output("`n(Money will be withdrawn until you have none left, the remainder will be borrowed)");
	OutputClass::rawoutput("</form>");
	OutputClass::rawoutput("<script language='javascript'>document.getElementById('input').focus();</script>");
	OutputClass::addnav("","bank.php?op=withdrawfinish");
}elseif($op=="withdraw"){
	$withdraw = Translator::translate_inline("Withdraw");
	$balance = Translator::translate_inline("`@Elessa`6 scans through her ledger, \"`@You have a balance of `^%s`@ gold in the bank.`6\"`n");
	$debt = Translator::translate_inline("`@Elessa`6 scans through her ledger, \"`@You have a `\$debt`@ of `^%s`@ gold in the bank.`6\"`n");
	OutputClass::rawoutput("<form action='bank.php?op=withdrawfinish' method='POST'>");
	OutputClass::output_notl($session['user']['goldinbank']>=0?$balance:$debt,abs($session['user']['goldinbank']));
	OutputClass::output("`6\"`@How much would you like to withdraw `&%s`@?`6\"`n`n",$session['user']['name']);
	OutputClass::rawoutput("<input id='input' name='amount' width=5 > <input type='submit' class='button' value='$withdraw'>");
	OutputClass::output("`n`iEnter 0 or nothing to withdraw it all`i");
	OutputClass::rawoutput("</form>");
	OutputClass::rawoutput("<script language='javascript'>document.getElementById('input').focus();</script>");
	OutputClass::addnav("","bank.php?op=withdrawfinish");
}elseif($op=="withdrawfinish"){
	$amount=abs((int)Http::httppost('amount'));
	if ($amount==0){
		$amount=abs($session['user']['goldinbank']);
	}
	if ($amount>$session['user']['goldinbank'] && Http::httppost('borrow')=="") {
		OutputClass::output("`\$ERROR: Not enough gold in the bank to withdraw.`^`n`n");
		OutputClass::output("`6Having been informed that you have `^%s`6 gold in your account, you declare that you would like to withdraw all `^%s`6 of it.`n`n", $session['user']['goldinbank'], $amount);
		OutputClass::output("`@Elessa`6 looks at you for a few moments without blinking, then advises you to take basic arithmetic.  You realize your folly and think you should try again.");
	}else if($amount>$session['user']['goldinbank']){
		$lefttoborrow = $amount;
		$didwithdraw = 0;
		$maxborrow = $session['user']['level']*Settings::getsetting("borrowperlevel",20);
		if ($lefttoborrow<=$session['user']['goldinbank']+$maxborrow){
			if ($session['user']['goldinbank']>0){
				OutputClass::output("`6You withdraw your remaining `^%s`6 gold.", $session['user']['goldinbank']);
				$lefttoborrow-=$session['user']['goldinbank'];
				$session['user']['gold']+=$session['user']['goldinbank'];
				$session['user']['goldinbank']=0;
				debuglog("withdrew $amount gold from the bank");
				$didwithdraw = 1;
			}
			if ($lefttoborrow-$session['user']['goldinbank'] > $maxborrow){
				if ($didwithdraw) {
					OutputClass::output("`6Additionally, you ask to borrow `^%s`6 gold.", $leftoborrow);
				} else {
					OutputClass::output("`6You ask to borrow `^%s`6 gold.", $lefttoborrow);
				}
				OutputClass::output("`@Elessa`6 looks up your account and informs you that you may only borrow up to `^%s`6 gold.", $maxborrow);
			}else{
				if ($didwithdraw) {
					OutputClass::output("`6Additionally, you borrow `^%s`6 gold.", $lefttoborrow);
				} else {
					OutputClass::output("`6You borrow `^%s`6 gold.", $lefttoborrow);
				}
				$session['user']['goldinbank']-=$lefttoborrow;
				$session['user']['gold']+=$lefttoborrow;
				debuglog("borrows $lefttoborrow gold from the bank");
				OutputClass::output("`@Elessa`6 records your withdrawal of `^%s `6gold in her ledger. \"`@Thank you, `&%s`@.  You now have a debt of `\$%s`@ gold to the bank and `^%s`@ gold in hand.`6\"", $amount,$session['user']['name'], abs($session['user']['goldinbank']),$session['user']['gold']);
			}
		}else{
			OutputClass::output("`6Considering the `^%s`6 gold in your account, you ask to borrow `^%s`6. `@Elessa`6 peers through her ledger, runs a few calculations and then informs you that, at your level, you may only borrow up to a total of `^%s`6 gold.", $session['user']['goldinbank'], $lefttoborrow-$session['user']['goldinbank'], $maxborrow);
		}
	}else{
		$session['user']['goldinbank']-=$amount;
		$session['user']['gold']+=$amount;
		debuglog("withdrew $amount gold from the bank");
		OutputClass::output("`@Elessa`6 records your withdrawal of `^%s `6gold in her ledger. \"`@Thank you, `&%s`@.  You now have a balance of `^%s`@ gold in the bank and `^%s`@ gold in hand.`6\"", $amount,$session['user']['name'], abs($session['user']['goldinbank']),$session['user']['gold']);
	}
}
villagenav();
OutputClass::addnav("Money");
if ($session['user']['goldinbank']>=0){
	OutputClass::addnav("W?Withdraw","bank.php?op=withdraw");
	OutputClass::addnav("D?Deposit","bank.php?op=deposit");
	if (Settings::getsetting("borrowperlevel",20)) OutputClass::addnav("L?Take out a Loan","bank.php?op=borrow");
}else{
	OutputClass::addnav("D?Pay off Debt","bank.php?op=deposit");
	if (Settings::getsetting("borrowperlevel",20)) OutputClass::addnav("L?Borrow More","bank.php?op=borrow");
}
if (Settings::getsetting("allowgoldtransfer",1)){
	if ($session['user']['level']>=Settings::getsetting("mintransferlev",3) || $session['user']['dragonkills']>0){
		OutputClass::addnav("M?Transfer Money","bank.php?op=transfer");
	}
}

PageParts::page_footer();

?>