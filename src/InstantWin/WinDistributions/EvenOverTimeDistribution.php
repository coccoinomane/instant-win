<?php

namespace InstantWin\WinDistributions;

use InstantWin\TimePeriod;

/**
 * Defines distribution logic for spreading wins evenly over a time period when
 * the number of total plays in the time period can not be known.
 *
 * @author Konr Ness <konrness@gmail.com>
 */
class EvenOverTimeDistribution extends AbstractWinDistribution implements
    TimePeriodAwareInterface,
    WinAmountAwareInterface
{

    /**
     * @var TimePeriod
     */
    protected $timePeriod;

    /**
     * @var int
     */
    protected $currentWinCount;

    /**
     * @var int
     */
    protected $playCount;

    /**
     * @var int
     */
    protected $maxWinCount;

    /**
     * Arbitrary amplitude of the winning odds. If you lower it, wins will be more
     * clustered close to the end time; increasing it will make the wins more
     * evenly spaced but less random.
     *
     * @var float
     */
	protected $sparsityFactor = 5;


    /**
     * Get the odds for a single play at this moment in time
     *
     * @return float Number from 0.000 to 0.999
     */
    public function getOdds()
    {
        // determine fraction of time elapsed; this is the same as the
		// percentage of wins awarded so far, assuming that the prizes have been
		// awarded evenly in time
        $timePercentage = $this->getTimePeriod()->getCompletion();

		// number of wins we should have awarded so far if we were truly
		// following an even-over-time distribution
        $desiredWinCount = $timePercentage * $this->getMaxWinCount();

        // estimated number of players until the end of the lottery, based
		// on the current plays and assuming a constant time distribution
        $estimatedRemainingPlays = ($this->getPlayCount() / $timePercentage) - $this->getPlayCount();
        $estimatedRemainingPlays = max(1, $estimatedRemainingPlays);

		// the odds of winning are directly proportional to the prizes we haven't assigned so far,
		// and inversely proportional to our estimate of the remaining players.
		$odds = ($desiredWinCount - $this->getCurrentWinCount())
              / $estimatedRemainingPlays
              * $this->sparsityFactor;
		
        // Give each player a small chance to win, regardless of the above algorithm,
        // making sure 1) to never exceed the number of available prizes and 2) to gauge
        // the chance to win to the number of plays so far.
        if (($this->getCurrentWinCount() < $this->getMaxWinCount()) && $this->getPlayCount() > 0)
            $odds = max ($odds, min (self::MIN_ODDS, 1.0/$this->getPlayCount()));
        
		// debug
        printf ("odds=%16.6g,\tdesiredWinCount-wins=%16.6g\n",
            $odds, ($desiredWinCount - $this->getCurrentWinCount()));
		
        return $odds;

    }

    /**
     * @param TimePeriod $timePeriod
     * @return $this
     */
    public function setTimePeriod($timePeriod)
    {
        $this->timePeriod = $timePeriod;
        return $this;
    }

    /**
     * @throws \Exception
     * @return \InstantWin\TimePeriod
     */
    public function getTimePeriod()
    {
        if (!$this->timePeriod) {
            throw new \Exception("TimePeriod not set");
        }
        return $this->timePeriod;
    }

    /**
     * @param int $currentWinCount
     */
    public function setCurrentWinCount($currentWinCount)
    {
        $this->currentWinCount = $currentWinCount;
    }

    /**
     * @param int $maxWinCount
     */
    public function setMaxWinCount($maxWinCount)
    {
        $this->maxWinCount = $maxWinCount;
    }

    /**
     * @param float $sparsityFactor
     */
    public function setSparsityFactor($sparsityFactor)
    {
        $this->sparsityFactor = $sparsityFactor;
    }

    /**
     * @throws \Exception
     * @return int
     */
    public function getCurrentWinCount()
    {
        if (null === $this->currentWinCount) {
            throw new \Exception("CurrentWinCount not set");
        }
        return $this->currentWinCount;
    }

    /**
     * @throws \Exception
     * @return int
     */
    public function getMaxWinCount()
    {
        if (null === $this->maxWinCount) {
            throw new \Exception("MaxWinCount not set");
        }
        return $this->maxWinCount;
    }

    /**
     * @throws \Exception
     * @return float
     */
    public function getSparsityFactor()
    {
        if (null === $this->sparsityFactor) {
            throw new \Exception("SparsityFactor not set");
        }
        return $this->sparsityFactor;
    }

    /**
     * @param int $playCount
     * @return $this;
     */
    public function setPlayCount($playCount)
    {
        $this->playCount = $playCount;
        return $this;
    }

    /**
     * @throws \Exception
     * @return int
     */
    public function getPlayCount()
    {
        if (null === $this->playCount) {
            throw new \Exception("PlayCount not set");
        }
        return $this->playCount;
    }
}
