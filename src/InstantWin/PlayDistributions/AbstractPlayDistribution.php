<?php

namespace InstantWin\PlayDistributions;

/**
 * Allows to extract a number distributed randomly over a time period,
 * according to some distribution function f(t), using the rejection
 * method.
 *
 * @author Guido W. Pettinari <guido.pettinari@gmail.com>
 */

abstract class AbstractPlayDistribution
{

	/**
	 * Period of time over which we allow players to play; also contains current time.
	 *
     * @var TimePeriod
     */
    protected $timePeriod;

	/**
	 * Maximum value of the distribution function f(t), required to optimise the 
	 * rejection method.
	 *
     * @var maxOdds
     */
	protected $maxOdds;

	/**
	 * To generate random numbers, we use the int mt_rand(int $min, int $max) function,
     * which returns a randomly distributed integer between $min and $max. We then
     * convert it to a randomly distributed float between 0.0 and 1.0 by scaling
     * the integer result of mt_rand() in the following way:
     * <php? random_float = (float)(mt_rand(0, self::SMOOTHNESS)) / self::SMOOTHNESS; ?>
     *
     * Here we set the SMOOTHNESS constant; the larger it is, the more continuous is
     * the randomly distributed number. In order to avoid random duplicates, make sure
     * that SMOOTHNESS is always much larger than the number of random draws you need.
     * Note however that it cannot be larger of the largest representable integer,
     * usually ~2e9.
     */
    const SMOOTHNESS = 1000000;

    /**
     * Find the value of the distribution function at the current time. When multiplied
	 * by a small time interval, it gives the probability of a play happening during that
	 * interval.
     *
     * @return float Number between 0 and 1
     */
    abstract public function getOdds();


    /**
     * @param float $maxOdds
     * @return $this
     */
    public function setMaxOdds($maxOdds)
    {
        $this->maxOdds = $maxOdds;
        return $this;
    }

	/**
	 * @throws \Exception
     * @return float
     */
    public function getMaxOdds()
    {
        if (empty($this->maxOdds)) {
            throw new \Exception("maxOdds not set");
        }
        return $this->maxOdds;
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
     * Draw a timestamp from the distribution function using the rejection method.
     *
     * @return float A randomly distributed timestamp between the start & end times
     */
    public function draw()
	{

		if (empty($this->maxOdds))
			throw new \Exception("Cannot use rejection method if you don't specify an upper limit for the PDF");

		$start = $this->getTimePeriod()->getStartTimestamp();
		$end = $this->getTimePeriod()->getEndTimestamp();

		do {
			$random_time = $start + ((float)(mt_rand(0, self::SMOOTHNESS)) / self::SMOOTHNESS)*($end-$start);
			$odds_of_time = $this->getOdds($random_time);
			$random_odds = ((float)(mt_rand(0, self::SMOOTHNESS)) / self::SMOOTHNESS)*$this->maxOdds;
		} while ($random_odds > $odds_of_time);
		
		// debug
		// printf ("%12.4f %12.4f\n", $random_float, $random_time);
		
		return $random_time;
		
    }

}

?>