#!/usr/bin/env php
<?php

/**
 * Generate random numbers distributed according to a distribution function.
 * This is a test of the PlayDistribution classes.
 *
 * This script outputs a file with the play timings:
 * ./example-playdistribution.php plays.dat
 * The file can be plotted as an histogram using, for example, GNU Octave
 * (https://www.gnu.org/software/octave/):
 * octave> plays=load("plays.dat"); hist(plays,20);
 *
 *
 * @author Guido W. Pettinari <guido.pettinari@gmail.com>
 */

require __DIR__ . '/../src/autoload.php';

use InstantWin\PlayDistributions\FlatPlayDistribution;
use InstantWin\PlayDistributions\PowerLawPlayDistribution;
use InstantWin\TimePeriod;


// ===========================================================
// =                  User defined variables                 =
// ===========================================================

// time period of the lottery
date_default_timezone_set('UTC');
$start = strtotime("now");
$end = strtotime("+10 seconds");

// how many plays in the time period?
$num_plays = 10000;

// how should be the players distributed over time?
// $flatPlayDistribution = new FlatPlayDistribution(); // flat distribution
$playDistribution = new PowerLawPlayDistribution(/*exponent*/2.0); // power law distribution


// ==========================================================
// =                       Actual code                      =
// ==========================================================

// set the time period
$timePeriod = new TimePeriod();
$timePeriod->setStartTimestamp($start);
$timePeriod->setEndTimestamp($end);

// let the distribution know about the time period
$playDistribution->setTimePeriod($timePeriod);

// name of file that will contain the times corresponding to plays
parse_str(implode('&', array_slice($argv, 1)), $_GET);
if (empty($argv[1]))
	$histFile = 'play_hist.' . date('Ymd') . '.txt';
else
	$histFile = $argv[1];
fopen($histFile, 'w'); 

// generate random numbers
for ($i=0; $i < $num_plays; $i++) { 
	$flatArray[$i] = $playDistribution->draw() - $start;
	// echo $flatArray[$i]. "<br />\n";
}

// write to file the histogram of plays
file_put_contents($histFile, implode("\n", $flatArray));




?>
