<?php
/*
Gets the Total, Dem, Rep and Others who voted on 11-08-2016 per county, looking at the "last voted" date in the February 27 2017 data file. Writes summary to a file called oldpeoplewhovoted.csv in 2017-02-27 file


Syntax:
php getoldpeoplewhovoted.php

*/

$outputfilename = 'oldpeoplewhovoted.csv';

$outputfile = fopen( $outputfilename, 'w');
fwrite ( $outputfile, "Name\tDOB\tGender\tAddress\tPhone\tParty\tStatus\tLast Voted\tRegistered\tStatus Change\tLast Change\n" );

$addressesetc = file( 'addressesetc.txt' );
$namesetc = file( 'firstnamelastnamedobs.txt' );

foreach( $addressesetc as $key=>$addressesandstuff )
{
	if( $addressesandstuff )
	{
		$name = str_replace( "\n", '', trim( $namesetc[ $key ] ) );
		$addressparts = explode( "\t", $addressesandstuff );
		//divide "name" into name, dob, gender
		//since we trimmed the data, if the last character is numeric there is no gender
		if( is_numeric( substr( $name, -1 ) )  )
		{
			$gender = '';
			$dob = substr( $name, -10 );
			$justname = substr( $name, 0, strlen( $name ) - 10 );

		}
		else
		{
			$gender = substr( $name, -1 );
			$dob = substr( $name, -12, -2 );
			$justname = substr( $name, 0, strlen( $name ) - 12  );
		}
		$dobtime = strtotime( $dob );
		// find everyone born before Nov 8, 2016 
		if( $dobtime < strtotime( '11/08/1916' ) )
		{
			$addressparts = explode( "\t", $addressesandstuff );
			if( isset( $addressparts[4] ) ) $lastvoteddate = trim( $addressparts[4] ); else $lastvoteddate = '';
			if( $lastvoteddate == '11/08/2016' )
			{
				$age = round( ( strtotime( '11/08/2016' ) - $dobtime ) / ( 365 * 24 * 60 * 60 ), 1 );
				fwrite ( $outputfile, $justname."\t".$dob."\t".$age."\t".$gender."\t".$addressesandstuff );
			}
		}
	}
}

fclose( $outputfile );


?>