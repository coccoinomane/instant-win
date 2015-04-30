#!/usr/bin/env php
<?php

/**
 * Quickly simulate an instant-win lottery over a custom time period, assuming
 * a certain number of players and prizes. The simulation is instantaneous,
 * meaning that you can simulate a 60-day lottery in less than a second.
 * 
 * The timing of the plays is determined with a custom time probability distribution
 * (using the PlayDistribution classes). Which of these players win is
 * determined via a second probability distribution (implemented in the
 * WinDistributions classes).
 * 
 * This script outputs two files with the play timings and the win timings,
 * respectively:
 * ./example-simulation.php plays.dat wins.dat
 * Each file can be plotted as an histogram using, for example, GNU Octave
 * (https://www.gnu.org/software/octave/):
 * octave> plays=load("plays.dat"); wins=load("wins.dat");
 * octave> subplot(1,2,1); hist(plays,20);
 * octave> subplot(1,2,2); hist(wins,60);
 *
 *
 * @author Guido W. Pettinari <guido.pettinari@gmail.com>
 */

require __DIR__ . '/../src/autoload.php';

use InstantWin\Player;
use InstantWin\PlayDistributions\FlatPlayDistribution;
use InstantWin\PlayDistributions\PowerLawPlayDistribution;
use InstantWin\WinDistributions\EvenOverTimeDistribution;
use InstantWin\TimePeriod;

// ===========================================================
// =                  User defined variables                 =
// ===========================================================

// time period of the lottery
date_default_timezone_set('UTC');
$start = strtotime("now");
$end = strtotime("+60 days");

// number of prizes available
$maxWins = 40;

// how many players in the full time interval? Set to 1 for a quick test run
$num_plays = 10000;

// how should be the players distributed over time?
$playDistribution = new FlatPlayDistribution(); // flat distribution
// $playDistribution = new PowerLawPlayDistribution(/*exponent*/2.0); // power law distribution

// Amplitude of the wins probability distribution. A large value means
// that prizes will be awarded more evenly in the time period, but also
// that, on small time intervals, they will be more clustered. Experiment
// with the parameter until you find the value that suits your needs.
$sparsityFactor = 10;


// ==========================================================
// =                       Actual code                      =
// ==========================================================

// set the time period
$timePeriod = new TimePeriod();
$timePeriod->setStartTimestamp($start);
$timePeriod->setEndTimestamp($end);

// create player and distribution of wins
$player = new Player();
$player->setMaxWins($maxWins);
$player->setDistribution(new EvenOverTimeDistribution());
$player->getDistribution()->setSparsityFactor($sparsityFactor);

// let the distribution know about the time period
$playDistribution->setTimePeriod($timePeriod);

// open file that will contain the timings of plays
parse_str(implode('&', array_slice($argv, 1)), $_GET);
if (empty($argv[1]))
    $playHistFile = 'play_hist.' . date('Ymd') . '.txt';
else
    $playHistFile = $argv[1];
fopen($playHistFile, 'w');

// open file that will contain the timings of wins
if (empty($argv[2]))
    $winHistFile = 'win_hist.' . date('Ymd') . '.txt';
else
    $winHistFile = $argv[2];
fopen($winHistFile, 'w');

// generate timings of plays and sort them from first to last
for ($i=0; $i < $num_plays; $i++)
    $playsArray[$i] = $playDistribution->draw();
sort($playsArray);

// write to file the timings of the plays (in day units)
foreach ($playsArray as $play)
    file_put_contents($playHistFile, (($play-$start)/86400)."\n", FILE_APPEND);

// initialise the number of plays and wins
$curPlays = 0; // consider incrementing this
$curWins = 0;

// loop over the timing of plays, from first to last.
foreach ($playsArray as $play) {

    // update time period & player
    $timePeriod->setCurrentTimestamp($play);
    $player->setTimePeriod($timePeriod);
    $player->setCurWins($curWins);
    $player->setPlayCount($curPlays);

    // execute a single instant-win play attempt
    $win = $player->isWinner();
    $curPlays++;

    if ($win) {

        // update the number of wins and record to file the winning time
        $curWins++;
        $winString = sprintf("%12.4f\n", ($play-$start)/86400);
        file_put_contents($winHistFile, $winString, FILE_APPEND);

        // debug
        // echo "Prize awarded at t=" . (microtime(TRUE)-$start) . "\n";
    }
}

echo $curWins . " prizes were awarded\n";

