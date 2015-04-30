#!/usr/bin/php
<?php

/**
 * Simulate a single play at an instant-win lottery.
 *
 * The script keeps track of the number of plays and wins
 * by updating two files. Therefore, it can be called multiple
 * times.
 *
 * @author Konr Ness <konrness@gmail.com>
 */

require __DIR__ . '/../src/autoload.php';

use InstantWin\Player;
use InstantWin\WinDistributions\EvenOverTimeDistribution;
use InstantWin\TimePeriod;

date_default_timezone_set('UTC');
$midnightToday = strtotime("today midnight");
$midnightTomorrow = strtotime("tomorrow midnight");
$winsPerDay = 10;

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
    file_put_contents($todayPlayCountFile, "100");
}

$curPlays = (int) file_get_contents($todayPlayCountFile);

/**
 * Setup the distribution, time period and player
 */
$player = new Player();

$player->setMaxWins($winsPerDay);
$player->setCurWins($curWins);
$player->setPlayCount($curPlays);

$timePeriod = new TimePeriod();
$timePeriod->setStartTimestamp($midnightToday);
$timePeriod->setEndTimestamp($midnightTomorrow);
$timePeriod->setCurrentTimestamp(time());
$player->setTimePeriod($timePeriod);

$player->setDistribution(new EvenOverTimeDistribution());

/**
 * Execute a single instant-win play attempt
 */

$win = $player->isWinner();

$curPlays++;
file_put_contents($todayPlayCountFile, $curPlays);

if ($win) {
    echo "You Won!!!\n";
    $curWins++;
    file_put_contents($todayWinCountFile, $curWins);

} else {
    echo "Sorry, you did not win.\n";
}
