<?php
// addnews ready
// translator ready
// mail ready
require_once("lib/villagenav.php");

class ForestClass
{
    public static function forest($noshowmessage = false)
    {
        global $session, $playermount;
        Translator::tlschema("forest");
//	mass_module_prepare(array("forest", "validforestloc"));
        OutputClass::addnav("Heal");
        OutputClass::addnav("H?Healer's Hut", "healer.php");
        OutputClass::addnav("Fight");
        OutputClass::addnav("L?Look for Something to Kill", "forest.php?op=search");
        if ($session['user']['level'] > 1) {
            OutputClass::addnav("S?Go Slumming", "forest.php?op=search&type=slum");
        }
        OutputClass::addnav("T?Go Thrillseeking", "forest.php?op=search&type=thrill");
        if (Settings::getsetting("suicide", 0)) {
            if (Settings::getsetting("suicidedk", 10) <= $session['user']['dragonkills']) {
                OutputClass::addnav("*?Search `\$Suicidally`0", "forest.php?op=search&type=suicide");
            }
        }
        if ($session['user']['level'] >= 15 && $session['user']['seendragon'] == 0) {
            // Only put the green dragon link if we are a location which
            // should have a forest.   Don't even ask how we got into a forest()
            // call if we shouldn't have one.   There is at least one way via
            // a superuser link, but it shouldn't happen otherwise.. We just
            // want to make sure however.
            $isforest = 0;
            $vloc = Modules::modulehook('validforestloc', array());
            foreach ($vloc as $i => $l) {
                if ($session['user']['location'] == $i) {
                    $isforest = 1;
                    break;
                }
            }
            if ($isforest || count($vloc) == 0) {
                OutputClass::addnav("G?`@Seek Out the Green Dragon", "forest.php?op=dragon");
            }
        }
        OutputClass::addnav("Other");
        VillageNavClass::villagenav();
        if ($noshowmessage != true) {
            OutputClass::output("`c`7`bThe Forest`b`0`c");
            OutputClass::output("The Forest, home to evil creatures and evildoers of all sorts.`n`n");
            OutputClass::output("The thick foliage of the forest restricts your view to only a few yards in most places.");
            OutputClass::output("The paths would be imperceptible except for your trained eye.");
            OutputClass::output("You move as silently as a soft breeze across the thick moss covering the ground, wary to avoid stepping on a twig or any of the numerous pieces of bleached bone that populate the forest floor, lest you betray your presence to one of the vile beasts that wander the forest.`n");
            Modules::modulehook("forest-desc");
        }
        Modules::modulehook("forest", array());
        Modules::module_display_events("forest", "forest.php");
        Translator::tlschema();
    }
}

?>
