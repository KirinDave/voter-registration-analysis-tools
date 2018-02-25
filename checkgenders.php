<?php
/*

Runs through a list of first names and genders and checks gender whenever a name matches a name on the common male name list or the common female name list

This script is run in the same directory that the firstname_gender_set.csv file is in
Syntax:
php checkgenders.php

Output: commonmalenamereport.csv, commonfemalenamereport.csv

The name lists are from https://www.ssa.gov/oact/babynames/decades/century.html

We used the top 16 names, since #17 for girls is Ashley, which is also a not-uncommon boy's name

*/
$malenames = array(
'James',
'John',
'Robert',
'Michael',
'William',
'David',
'Richard',
'Joseph',
'Thomas',
'Charles',
'Christopher',
'Daniel',
'Matthew',
'Anthony',
'Donald',
'Mark',
'Jared'
);
$femalenames = array(
'Mary',
'Patricia',
'Jennifer',
'Elizabeth',
'Linda',
'Barbara',
'Susan',
'Jessica',
'Margaret',
'Sarah',
'Karen',
'Nancy',
'Betty',
'Lisa',
'Dorothy',
'Sandra',
'Ivanka'
);


$outputfilename1 = 'commonmalenamereport.csv';
$outputfile1 = fopen( $outputfilename1, 'w' );
fwrite( $outputfile1, "Name	Total	Male	Female	Unknown	Blank\n" );

$outputfilename2 = 'commonfemalenamereport.csv';
$outputfile2 = fopen( $outputfilename2, 'w' );
fwrite( $outputfile2, "Name	Total	Male	Female	Unknown	Blank\n" );

$namesetc = file( 'firstname_gender_set.csv' );

$malecount = array();
$malecountmale = array();
$malecountfemale = array();
$malecountunknown = array();
$malecountnone = array();
$femalecount = array();
$femalecountmale = array();
$femalecountfemale = array();
$femalecountunknown = array();
$femalecountnone = array();
foreach( $namesetc as $key=>$nameetc )
{
	$parts = explode( '|', trim( $nameetc ) );

	@$firstname = ucfirst( strtolower( trim( $parts[0] ) ) );
	@$gender = $parts[1];
	
	//echo $firstname."\n";
	$key = array_search( $firstname, $malenames );
	if( $key !== false )
	{
		//echo $firstname.' - '.$gender."\n";
		if( ! isset( $malecount[ $key ] ) ) $malecount[ $key ] = 1; else $malecount[ $key ] = $malecount[ $key ] + 1;
		if( $gender == 'M' )
		{
			if( ! isset( $malecountmale[ $key ] ) ) $malecountmale[ $key ] = 1; else $malecountmale[ $key ] = $malecountmale[ $key ] + 1;
		}
		if( $gender == 'F' )
		{
			if( ! isset( $malecountfemale[ $key ] ) ) $malecountfemale[ $key ] = 1; else $malecountfemale[ $key ] = $malecountfemale[ $key ] + 1;
		}
		if( $gender == 'U' )
		{
			if( ! isset( $malecountunknown[ $key ] ) ) $malecountunknown[ $key ] = 1; else $malecountunknown[ $key ] = $malecountunknown[ $key ] + 1;
		}
		if( $gender == '' )
		{
			if( ! isset( $malecountnone[ $key ] ) ) $malecountnone[ $key ] = 1; else $malecountnone[ $key ] = $malecountnone[ $key ] + 1;
		}
	}
	$key = array_search( $firstname, $femalenames );
	if( $key !== false )
	{
		if( ! isset( $femalecount[ $key ] ) ) $femalecount[ $key ] = 1; else $femalecount[ $key ] = $femalecount[ $key ] + 1;
		if( $gender == 'M' )
		{
			if( ! isset( $femalecountmale[ $key ] ) ) $femalecountmale[ $key ] = 1; else $femalecountmale[ $key ] = $femalecountmale[ $key ] + 1;
		}
		if( $gender == 'F' )
		{
			if( ! isset( $femalecountfemale[ $key ] ) ) $femalecountfemale[ $key ] = 1; else $femalecountfemale[ $key ] = $femalecountfemale[ $key ] + 1;
		}
		if( $gender == 'U' )
		{
			if( ! isset( $femalecountunknown[ $key ] ) ) $femalecountunknown[ $key ] = 1; else $femalecountunknown[ $key ] = $femalecountunknown[ $key ] + 1;
		}
		if( $gender == '' )
		{
			if( ! isset( $femalecountnone[ $key ] ) ) $femalecountnone[ $key ] = 1; else $femalecountnone[ $key ] = $femalecountnone[ $key ] + 1;
		}
	}
}
$total = 0;
$totalmale = 0;
$totalfemale = 0;
$totalunknown = 0;
$totalnone = 0;
foreach( $malenames as $key=>$name )
{
	$total += $malecount[ $key ];
	$totalmale += $malecountmale[ $key ];
	$totalfemale += $malecountfemale[ $key ];
	$totalunknown += $malecountunknown[ $key ];
	$totalnone += $malecountnone[ $key ];
	fwrite( $outputfile1, $name."	".$malecount[ $key ]."	".$malecountmale[ $key ]."	".$malecountfemale[ $key ]."	".$malecountunknown[ $key ]."	".$malecountnone[ $key ]."\n" );
}
fwrite( $outputfile1, "TOTAL	".$total."	".$totalmale."	".$totalfemale."	".$totalunknown."	".$totalnone."\n" );

$total = 0;
$totalmale = 0;
$totalfemale = 0;
$totalunknown = 0;
$totalnone = 0;
foreach( $femalenames as $key=>$name )
{
	$total += $femalecount[ $key ];
	$totalmale += $femalecountmale[ $key ];
	$totalfemale += $femalecountfemale[ $key ];
	$totalunknown += $femalecountunknown[ $key ];
	$totalnone += $femalecountnone[ $key ];
	fwrite( $outputfile2, $name."	".$femalecount[ $key ]."	".$femalecountmale[ $key ]."	".$femalecountfemale[ $key ]."	".$femalecountunknown[ $key ]."	".$femalecountnone[ $key ]."\n" );
}
fwrite( $outputfile2, "TOTAL	".$total."	".$totalmale."	".$totalfemale."	".$totalunknown."	".$totalnone."\n" );
?>