<?php

	/*
	 *	Custom PHP last.fm Widget
	 *	Written by Peter Sobot (petersobot.com)
	 *	v2.0: November 24, 2010
	 *	http://github.com/psobot/phplatestsong
	 *	
	 *	Licensed under the MIT license.
	 *
	 */

	ini_set("allow_url_fopen", "On");
	define("LASTFMUSERNAME", "killercanuck");					//Set your username here.
	define("LASTFMAPIKEY", "your_key_goes_here");	//Set your API key here.

	class Date_Difference {
		/**
		 *	Converts a timestamp to pretty human-readable format.
		 * 
		 *	Original JavaScript Created By John Resig (jquery.com)  Copyright (c) 2008
		 *	Copyright (c) 2008 John Resig (jquery.com)
		 *	Licensed under the MIT license.
		 *	Ported to PHP >= 5.1 by Zach Leatherman (zachleat.com)
		 *	Heavily modified by Peter Sobot (petersobot.com)
		 *	
		 */
		public static function getString($date, DateTime $compareTo = NULL) { 
			if(is_null($compareTo))	$compareTo = new DateTime('now'); 
			$diff = $compareTo->format('U') - $date; 
			$dayDiff = floor($diff / 86400); 
	
			if(is_nan($dayDiff) || $dayDiff < 0) return ''; 
					 
			if($dayDiff == 0) { 
				if($diff < 60) return 'listening now'; 
				elseif($diff < 120)	return 'listened 1 minute ago'; 
				elseif($diff < 3600) return 'listened ' . floor($diff/60) . ' minutes ago'; 
				elseif($diff < 7200) return 'listened ' . '1 hour ago'; 
				elseif($diff < 86400) return 'listened ' . floor($diff/3600) . ' hours ago'; 
			} elseif($dayDiff == 1) return 'listened yesterday'; 
			elseif($dayDiff < 7) return 'listened ' . $dayDiff . ' days ago'; 
			elseif($dayDiff == 7) return 'listened 1 week ago'; 
			elseif($dayDiff < (7*6)) return 'listened ' . ceil($dayDiff/7) . ' weeks ago'; 
			elseif($dayDiff < 365) return 'listened ' . ceil($dayDiff/(365/12)) . ' months ago'; 
			else { 
				$years = round($dayDiff/365); 
				return 'listened ' . $years . ' year' . ($years != 1 ? 's' : '') . ' ago'; 
			} 
		} 
	}
	
	/*
	 *	input:		Array
	 *	returns:	first element of Array
	 */
	function firstOf($a)	{	return $a[0];						}

	/*
	 *	input: 	username to search for	(defaults to this file's LASTFMUSERNAME defined value)
	 *	output:	HTML string of latest song played, in following format
	 *				<style type="text/css">#lastfm{background: url(64_by_64_pixel_image.jpg)}</style>
	 *				<a href="link_to_track.com">Track Name</a><br />
	 *				by Artist
	 *				<div id="lastfmtime">time ago</div>
	 */
	function latestSong($username = LASTFMUSERNAME, $api_key = LASTFMAPIKEY){
		$data = simplexml_load_file("http://ws.audioscrobbler.com/2.0/?method=user.getrecenttracks&user=".$username."&api_key=".$api_key); // add ."&limit=1" for... different? behaviour
		if($data === false)	die("Something went wrong, last.fm's not responding... so insert a song here.");
		foreach($data->getNamespaces(true) as $prefix => $namespace)	$data->registerXPathNamespace($prefix, $namespace);

		$song = firstOf($data->recenttracks->track);
		$date = Date_Difference::getString($song->date->attributes()->uts);
		$pic = firstOf($song->xpath('image[@size="medium"]'));

		$r = "";

		if($pic != "") $r .= "<style type=\"text/css\">#lastfm{background: url(".$pic.")}</style>";
		else $r .= "<style type=\"text/css\">#lastfm{background: url(http://www.petersobot.com/img/music.jpg)}</style>";
		$r .= "<a href=\"" . $song->url . "\">" . $song->name . "</a><br />by " . $song->artist . "<div id=\"lastfmtime\">".$date."</div>";

		return $r;
	}
	echo latestSong();
?>
