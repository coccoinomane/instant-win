#!/usr/bin/env php
<?php

$loader = require __DIR__ . "/../vendor/autoload.php";

use InstantWin\Player;
use InstantWin\WinDistributions\EvenOverTimeDistribution;
use InstantWin\TimePeriod;

// ===========================================================
// =                  User defined variables                 =
// ===========================================================

// time period of the lottery
date_default_timezone_set('UTC');
$start = strtotime("now");
$end = strtotime("+10 seconds");

// number of prizes available
$maxWins = 100;

// how many players in the full time interval? Set to 1 for a quick test run
$num_plays = 10000;


// ==========================================================
// =                       Actual code                      =
// ==========================================================

// set the time period
$timePeriod = new TimePeriod();
$timePeriod->setStartTimestamp($start);
$timePeriod->setEndTimestamp($end);

// create player and define distribution to use for wins
$player = new Player();
$player->setMaxWins($maxWins);
$player->setDistribution(new EvenOverTimeDistribution());

// name of file that will contain the times corresponding to wins
parse_str(implode('&', array_slice($argv, 1)), $_GET);
if (empty($argv[1]))
	$histFile = 'hist.' . date('Ymd') . '.txt';
else
	$histFile = $argv[1];
fopen($histFile, 'w');


// main loop over number of plays
for ($i=0; $i < $num_plays; $i++) { 
	
	// evenly distribute the plays
	if ($num_plays > 1)
		usleep (($end-$start)/$num_plays*1e6);
	
	/**
	 * Load the current wins
	 */
	$todayWinCountFile = 'win-count.' . date('Ymd') . '.txt';
	if (! file_exists($todayWinCountFile)) {
	    file_put_contents($todayWinCountFile, "0");
	}

	$curWins = (int) file_get_contents($todayWinCountFile);

	/**
	 * Load the current # of plays (either winning or losing)
	 */
	$todayPlayCountFile = 'play-count.' . date('Ymd') . '.txt';
	if (! file_exists($todayPlayCountFile)) {
	    // charge the play counts with 100 plays so the EvenOverTimeDistribution
	    // doesn't think a lot of time has passed in the day with no plays, which
	    // would cause a lot of wins to be given out all at once
	    // file_put_contents($todayPlayCountFile, "100");  /* ARBITRARY */
	    file_put_contents($todayPlayCountFile, "0");
	}

	$curPlays = (int) file_get_contents($todayPlayCountFile);

	/**
	 * Update time period & player
	 */
	$timePeriod->setCurrentTimestamp(time());
	$player->setTimePeriod($timePeriod);
	$player->setCurWins($curWins);
	$player->setPlayCount($curPlays);


	/**
	 * Execute a single instant-win play attempt
	 */

	$win = $player->isWinner();

	$curPlays++;
	file_put_contents($todayPlayCountFile, $curPlays);

	if ($win) {
	    echo "You Won!!! (" . (microtime(TRUE)-$start) . ")\n";
	    $curWins++;
	    file_put_contents($todayWinCountFile, $curWins);
		$winString = sprintf("%12.4f\n", microtime(TRUE)-$start);
		file_put_contents($histFile, $winString, FILE_APPEND);

	} else {
	    // echo "Sorry, you did not win.\n";
	}
}

echo "So far, " . $curWins . " prizes were awarded\n";

?>