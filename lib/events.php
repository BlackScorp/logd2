<?php
// addnews ready
// translator ready
// mail ready
require_once("lib/constants.php");
require_once("lib/http.php");

// This file encapsulates all the special event handling for most locations
class Events
{
// Returns whether or not the description should be skipped
    public static function handle_event($location, $baseLink = false, $needHeader = false)
    {
        if ($baseLink === false) {
            global $PHP_SELF;
            $baseLink = substr($PHP_SELF, strrpos($PHP_SELF, "/") + 1) . "?";
        } else {
            //OutputClass::debug("Base link was specified as $baseLink");
            //OutputClass::debug(debug_backtrace());
        }
        global $session, $playermount, $badguy;
        $skipdesc = false;

        Translator::tlschema("events");
        $allowinactive = false;
        $eventhandler = Http::httpget('eventhandler');
        if (($session['user']['superuser'] & SU_DEVELOPER) && $eventhandler != "") {
            $allowinactive = true;
            $array = preg_split("/[:-]/", $eventhandler);
            if ($array[0] == "module") {
                $session['user']['specialinc'] = "module:" . $array[1];
            } else {
                $session['user']['specialinc'] = "";
            }
        }

        $_POST['i_am_a_hack'] = 'true';

        if ($session['user']['specialinc'] != "") {
            $specialinc = $session['user']['specialinc'];
            $session['user']['specialinc'] = "";
            if ($needHeader !== false) {
                PageParts::page_header($needHeader);
            }

            OutputClass::output("`^`c`bSomething Special!`c`b`0");
            if (strchr($specialinc, ":")) {
                $array = split(":", $specialinc);
                $starttime = getmicrotime();
                module_do_event($location, $array[1], $allowinactive, $baseLink);
                $endtime = getmicrotime();
                if (($endtime - $starttime >= 1.00 && ($session['user']['superuser'] & SU_DEBUG_OUTPUT))) {
                    OutputClass::debug("Slow Event (" . round($endtime - $starttime, 2) . "s): $hookname - {$row['modulename']}`n");
                }
            }
            if (OutputClass::checknavs()) {
                // The page rendered some linkage, so we just want to exit.
                PageParts::page_footer();
            } else {
                $skipdesc = true;
                $session['user']['specialinc'] = "";
                $session['user']['specialmisc'] = "";
                Http::httpset("op", "");
            }
        }
        Translator::tlschema();
        return $skipdesc;
    }
}

?>
