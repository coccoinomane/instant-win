<?php

namespace InstantWin\WinDistributions;

abstract class AbstractWinDistribution
{

    /**
     * The instant-win probability is a function of time that, in principle and in particular
     * conditions, can be zero. Here we set a minimum value for the winning probability in order
     * to give each player at least a small chance to win.
     */    
    const MIN_ODDS = 0.001;


    /**
     * Get the odds for winning a single play at this moment in time
     *
     * @return float Number from 0.000 to 0.999
     */
    abstract public function getOdds();
}

?>