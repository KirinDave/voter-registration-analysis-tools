<?php
/*
Gets the Total, Dem, Rep and Others who voted on 11-08-2016 per county, looking at the "last voted" date in a later data file. Writes summary to a file called centenarianswhovoted.csv in the directory containing the data subsets for this date

Pulls information out of subset files, taking advantage of the fact that all subset of a given set are indentically indexed.

The "Grep Code" is a unique indicator for county. It allows one to easily extract records for a desired county

Syntax:
php getcentenarianswhovoted.php

*/

$outputfilename = 'centenarianswhovoted.csv';
$outputfile = fopen( $outputfilename, 'w');
fwrite( $outputfile, "ID	Name	DOB	Gender	Phone	Address	County	Party	Status	Changed	Last Voted	Grep Code\n" );

$fullids = file( 'id_set.csv' );
$addressesetc = file( 'address_plus_set.csv' );
$namesetc = file( 'firstname_middlename_lastname_dob_gender_set.csv' );

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
		// find everyone born before Nov 8, 1916 
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