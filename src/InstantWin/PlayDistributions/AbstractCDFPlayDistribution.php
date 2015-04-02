<?php

namespace InstantWin\PlayDistributions;

/**
 * 
 * Class for distribution functions with an invertible & analitical
 * cumulative distribution function (CDF), which allows for quicker
 * simulations using the inversion method.
 *
 * Using this class requires specifying the inverse CDF explicitly,
 * but the computation will be faster than the default rejection method
 * implemented in the parent class. If you don't know what a CDF is,
 * just stick with the parent class: the speed gain is for most applications
 * negligible.
 *
 * @author Guido W. Pettinari <guido.pettinari@gmail.com>
 */

abstract class AbstractCDFPlayDistribution extends AbstractPlayDistribution
{

    /**
	 * Compute the CDF at the current time
	 *
     * @return float A CDF value between 0 and 1
     */
    abstract public function getCumulative();


    /**
	 * Return the time corresponding to a given CDF value
	 *
     * @return float A time in the considered time period
	 * @param float A CDF value between 0 and 1
     */
    abstract public function getInverseCumulative($cdf_value);


    /**
     * Return an evenly-distributed time between the start and the end of
	 * the considered time period using the inverse CDF, using the inversion
     * method.
     *
     * Comment this function to use the default rejection method, instead.
     *
     * @return float Number in the time period
     */
	public function draw()
	{
		$random_float = (float)(mt_rand(0, self::SMOOTHNESS)) / self::SMOOTHNESS;
		$random_time = $this->getInverseCumulative($random_float);
		
		// debug
		// printf ("%12.4f %12.4f\n", $random_float, $random_time);
		
		return $random_time;
	}

}

?>