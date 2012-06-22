<?php

/**
 * Generates random number and returns it.
 * @return the random number
 */
function send111(){

	$cur_time = time();

	$odd1 = 11;
	$odd2 = 7;
	$odd3 = 9;
	$odd4 = 13;

	$random_no1 = $cur_time%$odd1;
	$random_no2 = $cur_time%$odd2;
	$random_no3 = $cur_time%$odd3;
	$random_no4 = $cur_time%$odd4;

	$random_no = "$random_no1"."$random_no2"."$random_no3"."$random_no4";
	return $random_no;

}
?>