#!/usr/bin/php
<?php
$loader = require __DIR__ . "/../vendor/autoload.php";

use InstantWin\PlayDistributions\FlatPlayDistribution;
use InstantWin\TimePeriod;


// ===========================================================
// =                  User defined variables                 =
// ===========================================================

// time period of the lottery
date_default_timezone_set('UTC');
$start = strtotime("now");
$end = strtotime("+10 seconds");

// how many plays in the time period?
$num_plays = 100000;


// ==========================================================
// =                       Actual code                      =
// ==========================================================

// set the time period
$timePeriod = new TimePeriod();
$timePeriod->setStartTimestamp($start);
$timePeriod->setEndTimestamp($end);

// create the distribution and let it know about the time period
$flatPlayDistribution = new FlatPlayDistribution();
$flatPlayDistribution->setTimePeriod($timePeriod);
$flatPlayDistribution->setMaxOdds(1.01*$flatPlayDistribution->getOdds($start));

// name of file that will contain the times corresponding to plays
parse_str(implode('&', array_slice($argv, 1)), $_GET);
if (empty($argv[1]))
	$histFile = 'play_hist.' . date('Ymd') . '.txt';
else
	$histFile = $argv[1];
fopen($histFile, 'w'); 

// generate random numbers
for ($i=0; $i < $num_plays; $i++) { 
	$flatArray[$i] = $flatPlayDistribution->draw() - $start;
	// echo $flatArray[$i]. "<br />\n";
}

// write to file the histogram of plays
file_put_contents($histFile, implode("\n", $flatArray));




?>
