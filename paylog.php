<?php
// mail ready
// addnews ready
// translator ready
require_once("common.php");
require_once("lib/http.php");

Translator::tlschema("paylog");

SuAccess::check_su_access(SU_EDIT_PAYLOG);
/*
+-----------+---------------------+------+-----+---------+----------------+
| Field     | Type                | Null | Key | Default | Extra          |
+-----------+---------------------+------+-----+---------+----------------+
| payid     | int(11)             |      | PRI | NULL    | auto_increment |
| info      | text                |      |     |         |                |
| response  | text                |      |     |         |                |
| txnid     | varchar(32)         |      | MUL |         |                |
| amount    | float(9,2)          |      |     | 0.00    |                |
| name      | varchar(50)         |      |     |         |                |
| acctid    | int(11) unsigned    |      |     | 0       |                |
| processed | tinyint(4) unsigned |      |     | 0       |                |
| filed     | tinyint(4) unsigned |      |     | 0       |                |
| txfee     | float(9,2)          |      |     | 0.00    |                |
+-----------+---------------------+------+-----+---------+----------------+
*/
PageParts::page_header("Payment Log");
require_once("lib/superusernav.php");
SuperUserNavClass::superusernav();

$op = Http::httpget('op');
if ($op==""){
	$sql = "SELECT info,txnid FROM ".db_prefix("paylog")." WHERE processdate='0000-00-00'";
	$result = db_query($sql);
	while ($row = db_fetch_assoc($result)){
		$info = unserialize($row['info']);
		$sql = "UPDATE ".db_prefix('paylog')." SET processdate='".date("Y-m-d H:i:s",strtotime($info['payment_date']))."' WHERE txnid='".addslashes($row['txnid'])."'";
		db_query($sql);
	}
	$sql = "SELECT substring(processdate,1,7) AS month, sum(amount)-sum(txfee) AS profit FROM ".db_prefix('paylog')." GROUP BY month DESC";
	$result = db_query($sql);
	OutputClass::addnav("Months");
	while ($row = db_fetch_assoc($result)){
		OutputClass::addnav(array("%s %s %s", date("M Y",strtotime($row['month']."-01")), Settings::getsetting("paypalcurrency", "USD"), $row['profit']),"paylog.php?month={$row['month']}");
	}
	$month = Http::httpget('month');
	if ($month=="") $month = date("Y-m");
	$startdate = $month."-01 00:00:00";
	$enddate = date("Y-m-d H:i:s",strtotime("+1 month",strtotime($startdate)));
	$sql = "SELECT " . db_prefix("paylog") . ".*," . db_prefix("accounts") . ".name," . db_prefix("accounts") . ".donation," . db_prefix("accounts") . ".donationspent FROM " . db_prefix("paylog") . " LEFT JOIN " . db_prefix("accounts") . " ON " . db_prefix("paylog") . ".acctid = " . db_prefix("accounts") . ".acctid WHERE processdate>='$startdate' AND processdate < '$enddate' ORDER BY payid DESC";
	$result = db_query($sql);
	OutputClass::rawoutput("<table border='0' cellpadding='2' cellspacing='1' bgcolor='#999999'>");
	$type = Translator::translate_inline("Type");
	$gross = Translator::translate_inline("Gross");
	$fee = Translator::translate_inline("Fee");
	$net = Translator::translate_inline("Net");
	$processed = Translator::translate_inline("Processed");
	$id = Translator::translate_inline("Transaction ID");
	$who = Translator::translate_inline("Who");
	OutputClass::rawoutput("<tr class='trhead'><td>Date</td><td>$id</td><td>$type</td><td>$gross</td><td>$fee</td><td>$net</td><td>$processed</td><td>$who</td></tr>");
	$number=db_num_rows($result);
	for ($i=0;$i<$number;$i++){
		$row = db_fetch_assoc($result);
		$info = unserialize($row['info']);
		OutputClass::rawoutput("<tr class='".($i%2?"trlight":"trdark")."'><td nowrap>");
		OutputClass::output_notl(date("m/d H:i",strtotime($info['payment_date'])));
		OutputClass::rawoutput("</td><td>");
		OutputClass::output_notl("%s",$row['txnid']);
		OutputClass::rawoutput("</td><td>");
		OutputClass::output_notl("%s", $info['txn_type']);
		OutputClass::rawoutput("</td><td nowrap>");
		OutputClass::output_notl("%.2f %s", $info['mc_gross'], $info['mc_currency']);
		OutputClass::rawoutput("</td><td>");
		OutputClass::output_notl("%s", $info['mc_fee']);
		OutputClass::rawoutput("</td><td>");
		OutputClass::output_notl("%.2f", (float)$info['mc_gross'] - (float)$info['mc_fee']);
		OutputClass::rawoutput("</td><td>");
		OutputClass::output_notl("%s", Translator::translate_inline($row['processed']?"`@Yes`0":"`\$No`0"));
		OutputClass::rawoutput("</td><td nowrap>");
		if ($row['name']>"") {
			OutputClass::rawoutput("<a href='user.php?op=edit&userid={$row['acctid']}'>");
			OutputClass::output_notl("`&%s`0 (%d/%d)", $row['name'],  $row['donationspent'],
					$row['donation']);
			OutputClass::rawoutput("</a>");
			OutputClass::addnav("","user.php?op=edit&userid={$row['acctid']}");
		}else{
			$amt = round((float)$info['mc_gross'] * 100,0);
			$memo = "";
			if (isset($info['memo'])) {
				$memo = $info['memo'];
			}
			$link = "donators.php?op=add1&name=".rawurlencode($memo)."&amt=$amt&txnid={$row['txnid']}";
			OutputClass::rawoutput("-=( <a href='$link' title=\"".htmlentities($info['item_number'], ENT_COMPAT, Settings::getsetting("charset", "ISO-8859-1"))."\" alt=\"".htmlentities($info['item_number'], ENT_COMPAT, Settings::getsetting("charset", "ISO-8859-1"))."\">[".htmlentities($memo, ENT_COMPAT, Settings::getsetting("charset", "ISO-8859-1"))."]</a> )=-");
			OutputClass::addnav("",$link);
		}
		OutputClass::rawoutput("</td></tr>");
	}
	OutputClass::rawoutput("</table>");
	OutputClass::addnav("Refresh","paylog.php");
}
PageParts::page_footer();
?>