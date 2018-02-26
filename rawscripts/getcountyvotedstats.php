<?php
/*
Gets the Total, Dem, Rep and Others who voted on 11-08-2016 per county, looking at the "last voted"  date. Writes summary to a file called countyvotedstats.csv 


Syntax:
php getcountyvotedstats.php

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

$totals = array();
$dems = array();
$reps = array();
$others = array();

foreach( $counties as $county )
{
	$totals[ $county ] = 0;
	$dems[ $county ] = 0;
	$reps[ $county ] = 0;
	$others[ $county ] = 0;
}
$outputfilename = 'countyvotedstats.csv';

$outputfile = fopen( $outputfilename, 'w');
fwrite ( $outputfile, "County\tTotal Voted\tDems\tReps\tOthers\n" );

$addressesetc = file( 'addressesetc.txt' );

foreach( $addressesetc as $addressetc )
{
	if( $addressetc )
	{
		$addressesandstuff = $addressetc;
		$addressparts = explode( "\t", $addressesandstuff);
		$address = $addressparts[0];
		$addressbits = explode( ' ', $address );
		$county = trim( $addressbits[0] );
		$party = $addressparts[2];
		if( isset( $addressparts[4] ) ) $lastvoteddate = trim( $addressparts[4] ); else $lastvoteddate = '';
		if( $lastvoteddate == '11/08/2016' )
		{
			$totals[ $county ] = $totals[ $county ] + 1;
			if( $party == 'D' )$dems[ $county ] = $dems[ $county ] + 1;
			elseif( $party == 'R' )$reps[ $county ] = $reps[ $county ] + 1;
			else $others[ $county ] = $others[ $county ] + 1;
		}
	}
}

foreach( $counties as $county )
{
	echo $county."\t".$totals[ $county ]."\t".$dems[ $county ]."\t".$reps[ $county ]."\t".$others[ $county ]."\n";
	fwrite ( $outputfile, $county."\t".$totals[ $county ]."\t".$dems[ $county ]."\t".$reps[ $county ]."\t".$others[ $county ]."\n" );
}

fclose( $outputfile );


?>