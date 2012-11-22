<?php

define("WIDTH", 21);
define("HEIGHT", 11);

define("START_X", 10);
define("START_Y", 5);

define("MAX_HP", 100);

session_start();
init();
$event = checkForInput();
outputStyle();
outputTitle();
outputGameWorld();
outputUserInterface();
outputStats();

if (!playerAlive()) {
    outputObituary();// Dead msg
} else if ($event) {
    $eventText = doEvent();
    
    if (isset($eventText)) {
        outputEvent($eventText);
    }
}

function init() {
	if (!isset($_SESSION['herox']) || !isset($_SESSION['heroy']) || !isset($_SESSION['herohp']) || !isset($_SESSION['heroxp']) || !isset($_SESSION['map'])) {
		resetSessionData();
	}
}

function changeHP($value) {
    if ($_SESSION['herohp'] + $value > MAX_HP) {
        $_SESSION['herohp'] = MAX_HP;
    } else {
        $_SESSION['herohp'] += $value;
    }
}

function changeXP($value) {
    $_SESSION['heroxp'] += $value;
}

function checkForInput() {
	if (isset($_GET['reset'])) {
		resetSessionData();
	}
    
    if (!playerAlive()) {// Page refresh fix
        return false;
    }

	if (isset($_GET['dir'])) {
	   
		switch ($_GET['dir']) {
			case "up":
                $_SESSION['heroy']--;
                if (!heroOnPassableTerrain()) {
                    $_SESSION['heroy']++;
                    return false;
                }
				return true;

			case "down":
				$_SESSION['heroy']++;
                if (!heroOnPassableTerrain()) {
                    $_SESSION['heroy']--;
                    return false;
                }
				return true;

			case "left":
				$_SESSION['herox']--;
                if (!heroOnPassableTerrain()) {
                    $_SESSION['herox']++;
                    return false;
                }
				return true;

			case "right":
				$_SESSION['herox']++;
                if (!heroOnPassableTerrain()) {
                    $_SESSION['herox']--;
                    return false;
                }
				return true;
            default:
                return false;
		}
	}
}

function doEvent() {
    $randomIndex = rand(1, 3);// improved :)
    
    switch($randomIndex) {
        default:
                return null;
                break;
                
        case 1:
                changeHP(-10);
                changeXP(100);
                return "You fight an evil wizard and defeat him! HP -10 XP +100";
                break;
    }
}

function heroOnPassableTerrain() {
    $y = $_SESSION['heroy'];
    $x = $_SESSION['herox'];
    $block = $_SESSION['map'][$y][$x];
    return $block != "M" && $block != "W";
}

function outputEvent($eventText) {
    echo "<br /><br /><b>$eventText</b>";
}

function outputGameWorld() {
	$space = "&nbsp;";
    
	for ($y = 0; $y < HEIGHT; $y++) {
		for ($x = 0; $x < WIDTH; $x++) {
			if ($x == $_SESSION['herox'] && $y == $_SESSION['heroy']) {
			 
			    if (playerAlive()) {
				   echo "<span class=H>H</span>";
                } else {
				   echo "<span class=H>+</span>";
                }
                
			} else {
				$map = $_SESSION['map'][$y][$x];
                if ($map == " ") {
                	echo "<span class=G>$space</span>";
                } else {
                	echo "<span class=$map>$map</span>";
                }
			}
		}
		echo "<br/>";
	}
}

// Dead msg
function outputObituary() {
    echo "<br /><br /><b>It seems like ... you died!</b>";
}

function outputStats() {
    echo "HP: " . $_SESSION['herohp'];
    echo "<br />";
    echo "XP: " . $_SESSION['heroxp'];
}

function outputStyle() {
	echo "<style>
	body
	{
		font-family: courier;
		font-size: 32px;
	}
	.H
	{
		color: white;
		background-color: green;
		font-weight: bold;
	}
	.G
	{
		background-color: green;
	}
	.F
	{
		color: lawngreen;
		background-color: green;
	}
	.W
	{
		color: lightblue;
		background-color: blue;
	}
	.M
	{
		background-color: gray;
		font-weight: bold;
	}
	</style>";
}

function outputTitle() {
	echo "<h2>Turn Based Game<br/>Using \$_SESSION<br/><small>Part 04: Adding the Map</small></h2>";
}

function outputUserInterface() {
	echo "<p>";
        if (playerAlive()) {
        	echo "<a href='?dir=up'>up</a> ";
        	echo "<a href='?dir=down'>down</a> ";
        	echo "<a href='?dir=left'>left</a> ";
        	echo "<a href='?dir=right'>right</a> ";
        }
    	echo "<a href='?reset'>reset</a>";
	echo "</p>";
}

function playerAlive() {
    return $_SESSION['herohp'] > 0;
}

function randomMapRow() {
    $grass_chance = 5;
    $forest_chance = 8;
    $water_chance = 9;
    $mountain_chance = 10;
    $returnString = "";
    
    for ($x = 0; $x < WIDTH; $x++) {
        $randomChance = rand(1, 10);
        
        if ($x == 0 || $x == WIDTH - 1) {
            $returnString .= "M";
        } else if ($randomChance <= $grass_chance) {
            $returnString .= " ";
        } else if ($randomChance <= $forest_chance) {
            $returnString .= "F";
        } else if ($randomChance <= $water_chance) {
            $returnString .= "W";
        } else if ($randomChance <= $mountain_chance) {
            $returnString .= "M";
        }
    }
    return $returnString;
}

function resetSessionData() {
    $_SESSION['herox'] = START_X;
	$_SESSION['heroy'] = START_Y;
	$_SESSION['herohp'] = MAX_HP;
	$_SESSION['heroxp'] = 0;
    
    $_SESSION['map'][0] = topBottomMapRow();

    for ($y = 1; $y < HEIGHT - 1; $y++) {
    	$_SESSION['map'][$y] = randomMapRow();
    }
    
    $_SESSION['map'][HEIGHT - 1] = topBottomMapRow();
    
    if (!heroOnPassableTerrain()) {
        $_SESSION['map'][START_Y][START_X] = " ";
    }
}

function topBottomMapRow() {
    $returnString = "";
    for ($column = 0; $column < WIDTH; $column++) {
        $returnString .= "M";
    }
    
    return $returnString;
}

?>