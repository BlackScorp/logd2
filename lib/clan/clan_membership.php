<?php
		OutputClass::addnav("Clan Hall","clan.php");
		OutputClass::addnav("Clan Options");
		OutputClass::output("This is your current clan membership:`n");
		$setrank = Http::httpget('setrank');
		$whoacctid = (int)Http::httpget('whoacctid');
		if ($setrank>"") {
			$sql="SELECT name,login,clanrank FROM ".db_prefix("accounts")." WHERE acctid=$whoacctid LIMIT 1";
			$result=db_query($sql);
			$row=db_fetch_assoc($result);
			$who = $row['login'];
			$whoname = $row['name'];
			if ($setrank>""){
				$args = Modules::modulehook("clan-setrank", array("setrank"=>$setrank, "login"=>$who, "name"=>$whoname, "acctid"=>$whoacctid, "clanid"=>$session['user']['clanid'], "oldrank"=>$row['clanrank']));
				if (!(isset($args['handled']) && $args['handled'])) {
					$sql = "UPDATE " . db_prefix("accounts") . " SET clanrank=GREATEST(0,least({$session['user']['clanrank']},$setrank)) WHERE login='$who'";
					db_query($sql);
					DebugLogClass::debuglog("Player {$session['user']['name']} changed rank of {$whoname} to {$setrank}.", $whoacctid);
				}
			}
		}
		$remove = Http::httpget('remove');
		if ($remove>""){
			$sql = "SELECT name,login,clanrank FROM " . db_prefix("accounts") . " WHERE acctid='$remove'";
			$row = db_fetch_assoc(db_query($sql));
			$args = Modules::modulehook("clan-setrank", array("setrank"=>0, "login"=>$row['login'], "name"=>$row['name'], "acctid"=>$remove, "clanid"=>$session['user']['clanid'], "oldrank"=>$row['clanrank']));
			$sql = "UPDATE " . db_prefix("accounts") . " SET clanrank=".CLAN_APPLICANT.",clanid=0,clanjoindate='0000-00-00 00:00:00' WHERE acctid='$remove' AND clanrank<={$session['user']['clanrank']}";
			db_query($sql);
			DebugLogClass::debuglog("Player {$session['user']['name']} removed player {$row['login']} from {$claninfo['clanname']}.", $remove);
			//delete unread application emails from this user.
			//breaks if the applicant has had their name changed via
			//dragon kill, superuser edit, or lodge color change
			require_once("lib/safeescape.php");
			$subj = safeescape(serialize(array($apply_short, $row['name'])));
			$sql = "DELETE FROM " . db_prefix("mail") . " WHERE msgfrom=0 AND seen=0 AND subject='$subj'";
			db_query($sql);
		}
		$sql = "SELECT name,login,acctid,clanrank,laston,clanjoindate,dragonkills,level FROM " . db_prefix("accounts") . " WHERE clanid={$claninfo['clanid']} ORDER BY clanrank DESC ,dragonkills DESC,level DESC,clanjoindate";
		$result = db_query($sql);
		OutputClass::rawoutput("<table border='0' cellpadding='2' cellspacing='0'>");
		$rank = Translator::translate_inline("Rank");
		$name = Translator::translate_inline("Name");
		$lev = Translator::translate_inline("Level");
		$dk = Translator::translate_inline("Dragon Kills");
		$jd = Translator::translate_inline("Join Date");
		$lo = Translator::translate_inline("Last On");
		$ops = Translator::translate_inline("Operations");
		$promote = Translator::translate_inline("Promote");
		$demote = Translator::translate_inline("Demote");
		$stepdown = Translator::translate_inline("`\$Step down as founder");
		$remove = Translator::translate_inline("Remove From Clan");
		$confirm = Translator::translate_inline("Are you sure you wish to remove this member from your clan?");
		OutputClass::rawoutput("<tr class='trhead'><td>$rank</td><td>$name</td><td>$lev</td><td>$dk</td><td>$jd</td><td>$lo</td>".($session['user']['clanrank']>CLAN_MEMBER?"<td>$ops</td>":"")."</tr>",true);
		$i=0;
		$tot = 0;
		require_once("lib/clan/func.php");
		while ($row=db_fetch_assoc($result)){
			$i++;
			$tot += $row['dragonkills'];
			OutputClass::rawoutput("<tr class='".($i%2?"trlight":"trdark")."'>");
			OutputClass::rawoutput("<td>");
			OutputClass::output_notl($ranks[$row['clanrank']]);
			OutputClass::rawoutput("</td><td>");
			$link = "bio.php?char=".$row['acctid']."&ret=".urlencode($_SERVER['REQUEST_URI']);
			OutputClass::rawoutput("<a href='$link'>", true);
			OutputClass::addnav("", $link);
			OutputClass::output_notl("`&%s`0", $row['name']);
			OutputClass::rawoutput("</a>");
			OutputClass::rawoutput("</td><td align='center'>");
			OutputClass::output_notl("`^%s`0",$row['level']);
			OutputClass::rawoutput("</td><td align='center'>");
			OutputClass::output_notl("`\$%s`0",$row['dragonkills']);
			OutputClass::rawoutput("</td><td>");
			OutputClass::output_notl("`3%s`0",$row['clanjoindate']);
			OutputClass::rawoutput("</td><td>");
			OutputClass::output_notl("`#%s`0",GameDateTime::reltime(strtotime($row['laston'])));
			OutputClass::rawoutput("</td>");
			if ($session['user']['clanrank']>CLAN_MEMBER){
				OutputClass::rawoutput("<td>");
				if ($row['clanrank']<$session['user']['clanrank'] && $row['clanrank']<CLAN_FOUNDER){
					OutputClass::rawoutput("[ <a href='clan.php?op=membership&setrank=".clan_nextrank($ranks,$row['clanrank'])."&who=".rawurlencode($row['login'])."&whoname=".rawurlencode($row['name'])."&whoacctid=".$row['acctid']."'>$promote</a> | ");
					OutputClass::addnav("","clan.php?op=membership&setrank=".clan_nextrank($ranks,$row['clanrank'])."&who=".rawurlencode($row['login'])."&whoname=".rawurlencode($row['name'])."&whoacctid=".$row['acctid']);
				}else{
					OutputClass::output_notl("[ `)%s`0 | ", $promote);
				}
				if ($row['clanrank']<=$session['user']['clanrank'] && $row['clanrank']>CLAN_APPLICANT && $row['login']!=$session['user']['login'] && clan_previousrank($ranks,$row['clanrank']) > 0){
					OutputClass::rawoutput("<a href='clan.php?op=membership&setrank=".clan_previousrank($ranks,$row['clanrank'])."&whoacctid=".$row['acctid']."'>$demote</a> | ");
					OutputClass::addnav("","clan.php?op=membership&setrank=".clan_previousrank($ranks,$row['clanrank'])."&whoacctid=".$row['acctid']);
				}elseif ($row['clanrank']==CLAN_FOUNDER && $row['clanrank']>CLAN_APPLICANT && $row['login']==$session['user']['login']){
					OutputClass::output_notl("<a href='clan.php?op=membership&setrank=".clan_previousrank($ranks,$row['clanrank'])."&whoacctid=".$row['acctid']."'>$stepdown</a> | ",true);
					OutputClass::addnav("","clan.php?op=membership&setrank=".clan_previousrank($ranks,$row['clanrank'])."&whoacctid=".$row['acctid']);
				} else {
					OutputClass::output_notl("`)%s`0 | ", $demote);
				}
				if ($row['clanrank'] <= $session['user']['clanrank'] && $row['login']!=$session['user']['login']){
					OutputClass::rawoutput("<a href='clan.php?op=membership&remove=".$row['acctid']."' onClick=\"return confirm('$confirm');\">$remove</a> ]");
					OutputClass::addnav("","clan.php?op=membership&remove=".$row['acctid']);
				}else{
					OutputClass::output_notl("`)%s`0 ]", $remove);
				}
				OutputClass::rawoutput("</td>");
			}
			OutputClass::rawoutput("</tr>");
		}
		OutputClass::rawoutput("</table>");
		OutputClass::output("`n`n`^This clan has a total of `\$%s`^ dragon kills.",$tot);
?>
