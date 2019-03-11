<?php

if ( ! function_exists('todays_date'))
{
	function todays_date()
	{
		$timezone = "Asia/Manila";
		if(function_exists('date_default_timezone_set')){ date_default_timezone_set($timezone);}

		$todays_date = date("Y-m-d");

		return $todays_date;

	}
}

if ( ! function_exists('todays_time'))
{
	function todays_time()
	{
		$timezone = "Asia/Manila";
		if(function_exists('date_default_timezone_set')){ date_default_timezone_set($timezone);}

		$todays_time = date("G:i:s");

		return $todays_time;
	}
}

if ( ! function_exists('_dateFormat'))
{
	function _dateFormat($date)
	{
		$date = date_create($date);
		return date_format($date, 'F d, Y');	
	}
}

if ( ! function_exists('_timeFormat'))
{
	function _timeFormat($datetime)
	{
		$convertingtime = strtotime($datetime);
		return date("g:i a", $convertingtime); 	
	}
}


if ( ! function_exists('_getTimestamp'))
{
	function _getTimestamp()
	{
		$date = new DateTime();
		$currentTime = $date->getTimestamp();
		return $currentTime;
	}
}

if ( ! function_exists('_dateFormatoSql'))
{
	function _dateFormatoSql($date_to_format){
		$date_to_format = date_create($date_to_format);
		return date_format($date_to_format, 'Y-m-d');
	}
}

if ( ! function_exists('_datefolder'))
{
	function _datefolder(){
        $date = date('Y-m-d H:i:s');
        $date = str_replace( ':', '', $date);
        $date = str_replace(' ','-',$date);
		return $date;
	}
}


