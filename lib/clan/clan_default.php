<?php
		Modules::modulehook("collapse{", array("name"=>"clanentry"));
		OutputClass::output("Having pressed the secret levers and turned the secret knobs on the lock of the door to your clan's hall, you gain entrance and chat with your clan mates.`n`n");
		Modules::modulehook("}collapse");

		$sql = "SELECT name FROM " . db_prefix("accounts")  . " WHERE acctid={$claninfo['motdauthor']}";
		$result = db_query($sql);
		$row = db_fetch_assoc($result);
		$motdauthname = $row['name'];

		$sql = "SELECT name FROM " . db_prefix("accounts") . " WHERE acctid={$claninfo['descauthor']}";
		$result = db_query($sql);
		$row = db_fetch_assoc($result);
		$descauthname = $row['name'];

		if ($claninfo['clanmotd'] != '') {
			OutputClass::rawoutput("<div style='margin-left: 15px; padding-left: 15px;'>");
			OutputClass::output("`&`bCurrent MoTD:`b `#by %s`2`n",$motdauthname);
			OutputClass::output_notl(nltoappon($claninfo['clanmotd'])."`n");
			OutputClass::rawoutput("</div>");
			OutputClass::output_notl("`n");
		}

		Commentary::commentdisplay("", "clan-{$claninfo['clanid']}","Speak",25,($claninfo['customsay']>''?$claninfo['customsay']:"says"));

		Modules::modulehook("clanhall");

		if ($claninfo['clandesc'] != '') {
			Modules::modulehook("collapse{", array("name"=>"collapsedesc"));
			OutputClass::output("`n`n`&`bCurrent Description:`b `#by %s`2`n",$descauthname);
			OutputClass::output_notl(nltoappon($claninfo['clandesc']));
			Modules::modulehook("}collapse");
		}
		$sql = "SELECT count(*) AS c, clanrank FROM " . db_prefix("accounts") . " WHERE clanid={$claninfo['clanid']} GROUP BY clanrank DESC";
		$result = db_query($sql);
		// begin collapse
		Modules::modulehook("collapse{", array("name"=>"clanmemberdet"));
		OutputClass::output("`n`n`bMembership Details:`b`n");
		$leaders = 0;
		while ($row = db_fetch_assoc($result)){
			OutputClass::output_notl($ranks[$row['clanrank']].": `0".$row['c']."`n");
			if ($row['clanrank']>CLAN_OFFICER) $leaders += $row['c'];
		}
		OutputClass::output("`n");
		$noleader = Translator::translate_inline("`^There is currently no leader!  Promoting %s`^ to leader as they are the highest ranking member (or oldest member in the event of a tie).`n`n");
		if ($leaders==0){
			//There's no leader here, probably because the leader's account
			//expired.
			$sql = "SELECT name,acctid,clanrank FROM " . db_prefix("accounts") . " WHERE clanid={$session['user']['clanid']} AND clanrank > " . CLAN_APPLICANT . " ORDER BY clanrank DESC, clanjoindate";
			$result = db_query($sql);
			if (db_num_rows($result)) {
				$row = db_fetch_assoc($result);
				$sql = "UPDATE " . db_prefix("accounts") . " SET clanrank=".CLAN_LEADER." WHERE acctid={$row['acctid']}";
				db_query($sql);
				OutputClass::output_notl($noleader,$row['name']);
				if ($row['acctid']==$session['user']['acctid']){
					//if it's the current user, we'll need to update their
					//session in order for the db write to take effect.
					$session['user']['clanrank']=CLAN_LEADER;
				}
			} else {
				// There are no viable leaders.  But we cannot disband the clan
				// here.
			}
		}
		// end collapse
		Modules::modulehook("}collapse");

		if ($session['user']['clanrank']>CLAN_MEMBER){
			OutputClass::addnav("Update MoTD / Clan Desc","clan.php?op=motd");
		}
		OutputClass::addnav("M?View Membership","clan.php?op=membership");
		OutputClass::addnav("Online Members","list.php?op=clan");
		OutputClass::addnav("Your Clan's Waiting Area","clan.php?op=waiting");
		OutputClass::addnav("Withdraw From Your Clan","clan.php?op=withdrawconfirm");
?>