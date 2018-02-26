<?php
/*
Looks at the list of voters from the February 27 data set and grab those who last voted on April 26, 2016.

Write names, addresses, etc to output file.

This runs from within the February data directory

Syntax:
php getvotedinprimarynotingeneral.php


Output: 
votedinprimarynotingeneral.csv
*/

$counties = array(
1 => 'ADAMS',
2 => 'ALLEGHENY',
3 => 'ARMSTRONG',
4 => 'BEAVER',
5 => 'BEDFORD',
6 => 'BERKS',
7 => 'BLAIR',
8 => 'BRADFORD',
9 => 'BUCKS',
10 => 'BUTLER',
11 => 'CAMBRIA',
12 => 'CAMERON',
13 => 'CARBON',
14 => 'CENTRE',
15 => 'CHESTER',
16 => 'CLARION',
17 => 'CLEARFIELD',
18 => 'CLINTON',
19 => 'COLUMBIA',
20 => 'CRAWFORD',
21 => 'CUMBERLAND',
22 => 'DAUPHIN',
23 => 'DELAWARE',
24 => 'ELK',
25 => 'ERIE',
26 => 'FAYETTE',
27 => 'FOREST',
28 => 'FRANKLIN',
29 => 'FULTON',
30 => 'GREENE',
31 => 'HUNTINGDON',
32 => 'INDIANA',
33 => 'JEFFERSON',
34 => 'JUNIATA',
35 => 'LACKAWANNA',
36 => 'LANCASTER',
37 => 'LAWRENCE',
38 => 'LEBANON',
39 => 'LEHIGH',
40 => 'LUZERNE',
41 => 'LYCOMING',
42 => 'McKEAN',
43 => 'MERCER',
44 => 'MIFFLIN',
45 => 'MONROE',
46 => 'MONTGOMERY',
47 => 'MONTOUR',
48 => 'NORTHAMPTON',
49 => 'NORTHUMBERLAND',
50 => 'PERRY',
51 => 'PHILADELPHIA',
52 => 'PIKE',
53 => 'POTTER',
54 => 'SCHUYLKILL',
55 => 'SNYDER',
56 => 'SOMERSET',
57 => 'SULLIVAN',
58 => 'SUSQUEHANNA',
59 => 'TIOGA',
60 => 'UNION',
61 => 'VENANGO',
62 => 'WARREN',
63 => 'WASHINGTON',
64 => 'WAYNE',
65 => 'WESTMORELAND',
66 => 'WYOMING',
67 => 'YORK'
);


$outputfilename = 'votedinprimarynotingeneral.csv';
$outputfile = fopen( $outputfilename, 'w' );
fwrite( $outputfile, "ID	Name	DOB	Gender	Phone	Address	County	Party	Status	Registered	Last Voted	Record Changed	Grep Code\n" );

$partysummaryfilename = 'votedinprimarynotingeneralsummary.csv';
$partysummaryfile = fopen( $partysummaryfilename, 'w' );
fwrite( $partysummaryfile, "County	Voted - General	Dems	Reps	Other	Voted - Primary Only	Dems	Reps	Others  \n" );

$voterids1 = file( 'voterids.txt' );
$fullids1 = file( 'fullids.txt' );
$addressesetc1 = file( 'addressesetc.txt' );
$names1 = file('firstnamelastnamedobs.txt' );

$primarycount = array();
$primarydemcount = array();
$primaryrepcount = array();
$primaryothercount = array();
$generalcount = array();
$generaldemcount = array();
$generalrepcount = array();
$generalothercount = array();


foreach( $voterids1 as $key1=>$voterid1 )
{
	$addressesandstuff = $addressesetc1[ $key1 ];
	$addressparts = explode( "\t", $addressesandstuff);
	$lastvoted = $addressparts[4];
	$party = trim( $addressparts[2] );
	if( $lastvoted == '04/26/2016' )
	{
		echo $voterid1.' voted in primary not in general'."\n";
		$name = trim( $names1[ $key1 ] );
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
		$fullid = trim( $fullids1[ $key1 ] );
		{
			$parts = explode( '-', $fullid );
			$voterid = $parts[0];
			$countycode = $parts[1] * 1;
			if( strlen( $countycode ) == 1 ) $paddedcountycode = '0'.$countycode;
			else $paddedcountycode = $countycode;
		}
		$namestring = $justname."	".$dob."	".$gender;
		$address = $addressparts[0];
		$addressbits = explode( ' ', $address );
		$county = trim( $addressbits[0] );
		$realaddress = trim( str_replace( $county, '', $address ) );
		$phone = $addressparts[1];
		if( isset( $addressparts[3] ) ) $status = $addressparts[3]; else $status = '';
		if( isset( $addressparts[4] ) ) $lastvoteddate = $addressparts[4]; else $lastvoteddate = '';
		if( isset( $addressparts[5] ) ) $registereddate = $addressparts[5]; else $registereddate = '';
		if( isset( $addressparts[7] ) ) $recordchangeddate = str_replace("\n", '', $addressparts[7] ); else $recordchangeddate = '';
		
		if( ! isset( $primarycount[ $countycode ] ) ) $primarycount[ $countycode ] = 1; else $primarycount[ $countycode ] = $primarycount[ $countycode ] + 1;
		if( $party == 'D' )
		{
			if( ! isset( $primarydemcount[ $countycode ] ) ) $primarydemcount[ $countycode ] = 1; else $primarydemcount[ $countycode ] = $primarydemcount[ $countycode ] + 1;
		}
		elseif( $party == 'R' )
		{
			if( ! isset( $primaryrepcount[ $countycode ] ) ) $primaryrepcount[ $countycode ] = 1; else $primaryrepcount[ $countycode ] = $primaryrepcount[ $countycode ] + 1;
		}
		else
		{
			if( ! isset( $primaryothercount[ $countycode ] ) ) $primaryothercount[ $countycode ] = 1; else $primaryothercount[ $countycode ] = $primaryothercount[ $countycode ] + 1;
		}
		
		fwrite( $outputfile, trim( $voterid )."\t".$namestring."\t".$phone."\t".$realaddress."\t".$county."\t".$party."\t".$status."\t".$registereddate."\t".$lastvoteddate."\t".$recordchangeddate."\t".$paddedcountycode.'xxxxxxx'."\n" );
	}
	elseif( $lastvoted == '11/08/2016' )
	{
		if( ! isset( $generalcount[ $countycode ] ) ) $generalcount[ $countycode ] = 1; else $generalcount[ $countycode ] = $generalcount[ $countycode ] + 1;
		if( $party == 'D' )
		{
			if( ! isset( $generaldemcount[ $countycode ] ) ) $generaldemcount[ $countycode ] = 1; else $generaldemcount[ $countycode ] = $generaldemcount[ $countycode ] + 1;
		}
		elseif( $party == 'R' )
		{
			if( ! isset( $generalrepcount[ $countycode ] ) ) $generalrepcount[ $countycode ] = 1; else $generalrepcount[ $countycode ] = $generalrepcount[ $countycode ] + 1;
		}
		else
		{
			if( ! isset( $generalothercount[ $countycode ] ) ) $generalothercount[ $countycode ] = 1; else $generalothercount[ $countycode ] = $generalothercount[ $countycode ] + 1;
		}
	}
}

foreach( $counties as $countycode=>$county )
{
	if( ! isset( $generalcount[ $countycode ] ) ) $generalcount[ $countycode ] = 0;
	if( ! isset( $generaldemcount[ $countycode ] ) ) $generaldemcount[ $countycode ] = 0;
	if( ! isset( $generalrepcount[ $countycode ] ) ) $generalrepcount[ $countycode ] = 0;
	if( ! isset( $generalothercount[ $countycode ] ) ) $generalothercount[ $countycode ] = 0;
	if( ! isset( $primarycount[ $countycode ] ) ) $primarycount[ $countycode ] = 0;
	if( ! isset( $primarydemcount[ $countycode ] ) ) $primarydemcount[ $countycode ] = 0;
	if( ! isset( $primaryrepcount[ $countycode ] ) ) $primaryrepcount[ $countycode ] = 0;
	if( ! isset( $primaryothercount[ $countycode ] ) ) $primaryothercount[ $countycode ] = 0;
	fwrite( $partysummaryfile, $county."\t".$generalcount[ $countycode ]."	".$generaldemcount[ $countycode ]."	".$generalrepcount[ $countycode ]."	".$generalothercount[ $countycode ]."	".$primarycount[ $countycode ]."	".$primarydemcount[ $countycode ]."	".$primaryrepcount[ $countycode ]."	".$primaryothercount[ $countycode ]."\n" );
}
fclose( $outputfile );
fclose( $partysummaryfile );

?>