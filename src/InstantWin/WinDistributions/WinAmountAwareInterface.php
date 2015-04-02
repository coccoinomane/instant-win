<?php

namespace InstantWin\WinDistributions;


interface WinAmountAwareInterface
{
    /**
     * @param int $currentWinCount
     */
    public function setCurrentWinCount($currentWinCount);

    /**
     * @param int $playCount
     */
    public function setPlayCount($playCount);

    /**
     * @param int $maxWinCount
     */
    public function setMaxWinCount($maxWinCount);
}
