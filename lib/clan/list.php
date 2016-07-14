<?php
	PageParts::page_header("Clan Listing");
	$registrar=Settings::getsetting('clanregistrar','`%Karissa');
	OutputClass::addnav("Clan Options");
	$sql = "SELECT MAX(" . db_prefix("clans") . ".clanid) AS clanid, MAX(clanshort) AS clanshort, MAX(clanname) AS clanname,count(" . db_prefix("accounts") . ".acctid) AS c FROM " . db_prefix("clans") . " LEFT JOIN " . db_prefix("accounts") . " ON " . db_prefix("clans") . ".clanid=" . db_prefix("accounts") . ".clanid AND clanrank>".CLAN_APPLICANT." GROUP BY " . db_prefix("clans") . ".clanid ORDER BY c DESC";
	$result = db_query($sql);
	if (db_num_rows($result)>0){
		OutputClass::output("`7You ask %s`7 for the clan listings.  She points you toward a marquee board near the entrance of the lobby that lists the clans.`0`n`n",$registrar);
		$v = 0;
		$memb_n = Translator::translate_inline("(%s members)");
		$memb_1 = Translator::translate_inline("(%s member)");
		OutputClass::rawoutput('<table cellspacing="0" cellpadding="2" align="left">');
		while ($row = db_fetch_assoc($result)){
			if ($row['c']==0){
				$sql = "DELETE FROM " . db_prefix("clans") . " WHERE clanid={$row['clanid']}";
				db_query($sql);
			}else{
				OutputClass::rawoutput('<tr class="' . ($v%2?"trlight":"trdark").'"><td>', true);
				if ($row['c'] == 1) {
					$memb = sprintf($memb_1, $row['c']);
				} else {
					$memb = sprintf($memb_n, $row['c']);
				}
				OutputClass::output_notl("&#149; &#60;%s&#62; <a href='clan.php?detail=%s'>%s</a> %s`n",
						$row['clanshort'],
						$row['clanid'],
						full_sanitize(htmlentities($row['clanname']), ENT_COMPAT, Settings::getsetting("charset", "ISO-8859-1")),
						$memb, true);
				OutputClass::rawoutput('</td></tr>');
				OutputClass::addnav("","clan.php?detail={$row['clanid']}");
				$v++;
			}
		}
		OutputClass::rawoutput("</table>", true);
		OutputClass::addnav("Return to the Lobby","clan.php");
	}else{
		OutputClass::output("`7You ask %s`7 for the clan listings.  She stares at you blankly for a few moments, then says, \"`5Sorry pal, no one has had enough gumption to start up a clan yet.  Maybe that should be you, eh?`7\"",$registrar);
		OutputClass::addnav("Apply for a New Clan","clan.php?op=new");
		OutputClass::addnav("Return to the Lobby","clan.php");
	}

	page_footer();
?>