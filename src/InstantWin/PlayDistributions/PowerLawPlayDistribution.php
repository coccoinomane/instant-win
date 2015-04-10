<?php

namespace InstantWin\PlayDistributions;

use InstantWin\TimePeriod;

/**
 * Class implementing a power-law distribution function of the form
 * f(t) = c * (t-t0)^a, where:
 * - the slope parameter 'a' can be any positive number, including
 *   zero, in which case f(t) is a flat distribution
 * - t0 is the start of the time period
 * - the normalization constant 'c' is automatically computed so that
 *   the area below f(t) is equal to 1.
 *
 * @author Guido W. Pettinari <guido.pettinari@gmail.com>
 */
class PowerLawPlayDistribution extends AbstractCDFPlayDistribution
{

    /**
     * Slope parameter 'a'; it enters the distribution function as
     * f(t) = c * (t-t0)^a
     *
     * @var float
     */
    protected $a = NULL;


    /**
     * Normalization parameter 'c'; it enters the distribution function as
     * f(t) = c * (t-t0)^a
     *
     * @var float
     */
    protected $c = NULL;
    

    /**
     * @param float The slope parameter 'a' in f(t) = c * (t-t0)^a
     */
    function __construct($a = NULL)
    {
        $this->setSlope($a);
    }
    

    /**
     * @param float The slope parameter 'a' in f(t) = c * (t-t0)^a
     * @return $this
     */
    public function setSlope($a)
    {
        if ($a<0) {
            throw new \Exception("special case a<0 not implemented yet");
        }
        
        $this->a = $a;

        return $this;
    }

    /**
     * @throws \Exception
     * @return float
     */
    public function getSlope()
    {
        if (is_null($this->a)) {
            throw new \Exception("slope parameter 'a' not set");
        }
        return $this->a;
    }
    
    /**
     *
     * Compute the normalization parameter 'c' so that the integral of
     * f(t) = c * (t-t0)^a over the time period is equal to unity.
     * 
     * @return $this
     */
    public function setNormalization()
    {
        $start = $this->getTimePeriod()->getStartTimestamp();
        $end = $this->getTimePeriod()->getEndTimestamp();
        $a = $this->getSlope();
        
        $this->c = ($a+1)/pow($end-$start,$a+1);

        // debug
        // echo "c = " . $this->c . "<br />\n";;
        
        return $this;
    }


    /**
     * The power-law distribution function, f(t) = c * (t-t0)^a
     *
     * @return float Number from 0 to 1
     */
    public function getOdds()
    {
        $start = $this->getTimePeriod()->getStartTimestamp();
        $current = $this->getTimePeriod()->getCurrentTimestamp();
        $a = $this->getSlope();
        if (is_null($this->c))
            $this->setNormalization();
        
		$odds = $this->c * pow($current - $start, $a);
        
		// debug
		// printf ("odds = %12.4f ", $odds);
		
        return $odds;
    }


    /**
	 * Compute the CDF at the current time. The CDF is computed as
     * the integrand function of the distribution function.
	 *
     * @return float A CDF value between 0 and 1
     */
    public function getCumulative()
    {
        // the odds of having a play do not depend on the time of the day
        $start = $this->getTimePeriod()->getStartTimestamp();
        $current = $this->getTimePeriod()->getCurrentTimestamp();
        $a = $this->getSlope();
        
        if (is_null($this->c))
            $this->setNormalization();
        
		$cdf = $this->c * pow($current-$start,$a+1)/($a+1);

		// debug
		// printf ("cdf = %12.4f ", $cdf);
		
        return $cdf;
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
		
        if (is_null($this->c))
            $this->setNormalization();
        
        $start = $this->getTimePeriod()->getStartTimestamp();
        $a = $this->getSlope();
		$time = $start + pow($cdf_value*($a+1)/$this->c, 1/($a+1));
		
		// debug
		// printf ("%12.4f %12.4f\n", $cdf_value, $time);
		
        return $time;
    }
    

    /**
     * The maximum of the distribution function is easily computed
     * using its monotonicity.
     *
     * @return $this
     */
    public function setMaxOdds()
    {
        $a = $this->getSlope();        
        $end = $this->getTimePeriod()->getEndTimestamp();

        $this->maxOdds = $this->getOdds($end);

        return $this;
    }

}
