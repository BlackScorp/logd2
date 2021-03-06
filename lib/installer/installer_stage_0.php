<?php
OutputClass::output("`@`c`bWelcome to Legend of the Green Dragon`b`c`0");
OutputClass::output("`2This is the installer script for Legend of the Green Dragon, by Eric Stevens & JT Traub.`n");
OutputClass::output("`nIn order to install and use Legend of the Green Dragon (LoGD), you must agree to the license under which it is deployed.`n");
OutputClass::output("`n`&This game is a small project into which we have invested a tremendous amount of personal effort, and we provide this to you absolutely free of charge.`2");
OutputClass::output("Please understand that if you modify our copyright, or otherwise violate the license, you are not only breaking international copyright law (which includes penalties which are defined in whichever country you live), but you're also defeating the spirit of open source, and ruining any good faith which we have demonstrated by providing our blood, sweat, and tears to you free of charge.  You should also know that by breaking the license even one time, it is within our rights to require you to permanently cease running LoGD forever.`n");
OutputClass::output("`nPlease note that in order to use the installer, you must have cookies enabled in your browser.`n");
if (DB_CHOSEN){
	$sql = "SELECT count(*) AS c FROM accounts WHERE superuser & ".SU_MEGAUSER;
	$result = db_query($sql);
	$row = db_fetch_assoc($result);
	if ($row['c'] == 0){
		$needsauthentication = false;
	}
	if (Http::httppost("username")>""){
		OutputClass::debug(md5(md5(stripslashes(Http::httppost("password")))), true);
		$version = Settings::getsetting("installer_version","-1");
		if ($version == "-1") {
			// Passwords weren't encrypted in these versions
			$sql = "SELECT * FROM ".db_prefix("accounts")." WHERE login='".mysql_real_escape_string(Http::httppost("username"))."' AND password='".mysql_real_escape_string(Http::httppost("password"))."' AND superuser & ".SU_MEGAUSER;
		}else $sql = "SELECT * FROM ".db_prefix("accounts")." WHERE login='".mysql_real_escape_string(Http::httppost("username"))."' AND password='".md5(md5(stripslashes(Http::httppost("password"))))."' AND superuser & ".SU_MEGAUSER;
		$result = db_query($sql);
		if (db_num_rows($result) > 0){
			$row = db_fetch_assoc($result);
			OutputClass::debug($row['password'], true);
			OutputClass::debug(Http::httppost('password'), true);
			// Okay, we have a username with megauser, now we need to do
			// some hackery with the password.
			$needsauthentication=true;
			$p = stripslashes(Http::httppost("password"));
			$p1 = md5($p);
			$p2 = md5($p1);
			OutputClass::debug($p2, true);

			if (Settings::getsetting("installer_version", "-1") == "-1") {
				OutputClass::debug("HERE I AM", true);
				// Okay, they are upgrading from 0.9.7  they will have
				// either a non-encrypted password, or an encrypted singly
				// password.
				if (strlen($row['password']) == 32 &&
				$row['password'] == $p1) {
					$needsauthentication = false;
				} elseif ($row['password'] == $p) {
					$needsauthentication = false;
				}
			} elseif ($row['password'] == $p2) {
				$needsauthentication = false;
			}
			if ($needsauthentication === false) {
				RedirectClass::redirect("installer.php?stage=1");
			}
			OutputClass::output("`\$That username / password was not found, or is not an account with sufficient privileges to perform the upgrade.`n");
		}else{
			$needsauthentication=true;
			OutputClass::output("`\$That username / password was not found, or is not an account with sufficient privileges to perform the upgrade.`n");
		}
	}else{
		$sql = "SELECT count(*) AS c FROM ".db_prefix("accounts")." WHERE superuser & ".SU_MEGAUSER;
		$result = db_query($sql);
		$row = db_fetch_assoc($result);
		if ($row['c']>0){
			$needsauthentication=true;
		}else{
			$needsauthentication=false;
		}
	}
}else{
	$needsauthentication=false;
}
//if a user with appropriate privs is already logged in, let's let them past.
if ($session['user']['superuser'] & SU_MEGAUSER) $needsauthentication=false;
if ($needsauthentication){
	$session['stagecompleted']=-1;
	OutputClass::rawoutput("<form action='installer.php?stage=0' method='POST'>");
	OutputClass::output("`%In order to upgrade this LoGD installation, you will need to provide the username and password of a superuser account with the MEGAUSER privilege`n");
	OutputClass::output("`^Username: `0");
	OutputClass::rawoutput("<input name='username'><br>");
	OutputClass::output("`^Password: `0");
	OutputClass::rawoutput("<input type='password' name='password'><br>");
	$submit = Translator::translate_inline("Submit");
	OutputClass::rawoutput("<input type='submit' value='$submit' class='button'>");
	OutputClass::rawoutput("</form>");
}else{
	OutputClass::output("`nPlease continue on to the next page, \"License Agreement.\"");
}
?>