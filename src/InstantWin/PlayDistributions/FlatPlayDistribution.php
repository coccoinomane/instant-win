<?php

namespace InstantWin\PlayDistributions;

use InstantWin\TimePeriod;

/**
 * A flat distribution in time.
 *
 * @author Guido W. Pettinari <guido.pettinari@gmail.com>
 */
class FlatPlayDistribution extends AbstractCDFPlayDistribution
{
	
    /**
     * The flat distribution function.
     *
     * @return float Number from 0 to 1
     */
    public function getOdds()
    {
        // the odds of having a play do not depend on the time of the day
        $start = $this->getTimePeriod()->getStartTimestamp();
        $end = $this->getTimePeriod()->getEndTimestamp();
		$odds = 1.0/($end - $start);

		// debug
		// printf ("odds = %12.4f ", $odds);
		
        return $odds;
    }


    /**
	 * Compute the CDF at the current time
	 *
     * @return float A CDF value between 0 and 1
     */
    public function getCumulative()
    {
        // the odds of having a play do not depend on the time of the day
        $start = $this->getTimePeriod()->getStartTimestamp();
        $end = $this->getTimePeriod()->getEndTimestamp();
        $current = $this->getTimePeriod()->getCurrentTimestamp();
		$odds = ((float)($current - $start))/($end - $start);

		// debug
		// printf ("odds = %12.4f ", $odds);
		
        return $odds;
    }


    /**
	 * Return the time corresponding to a given CDF value
	 *
	 * @throws \Exception 
     * @return float A time in the considered time period
	 * @param float A CDF value between 0 and 1
     */
    public function getInverseCumulative($cdf_value)
    {
		if (($cdf_value < 0) || ($cdf_value > 1))
			throw new \Exception("CDF value out of bounds");
		
        $start = $this->getTimePeriod()->getStartTimestamp();
        $end = $this->getTimePeriod()->getEndTimestamp();
		$time = $start + $cdf_value*($end-$start);
		
		// debug
		// printf ("%12.4f %12.4f\n", $cdf_value, $time);
		
        return $time;
    }

}
