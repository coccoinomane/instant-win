<?php

namespace InstantWin\WinDistributions;

abstract class AbstractWinDistribution
{

    /**
     * Get the odds for winning a single play at this moment in time
     *
     * @return float Number from 0.000 to 0.999
     */
    abstract public function getOdds();
}

?>