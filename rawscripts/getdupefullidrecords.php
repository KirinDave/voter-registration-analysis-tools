<?php
/*
Looks at list of all full ids and pulls records for each that has an exact duplicate.

This is done efficiently by noting the key of each duplicate and pulling the corresponding record from the appropriate county file

Note that the dupicates will both be in the same county file, so don't stop reading county file after finding one.

Usage:
php getdupefullidrecords 2016-11-07

*/

$outputfilename = 'dupefullidrecords.txt';
$outputfile = fopen( $outputfilename, 'w' );
$ids = file( 'fullids.txt' );
$valuecounts = array_count_values( $ids );
foreach( $ids as $key=>$id )
{
	if( $valuecounts[ $id ] > 1 )
	{
		$dupeids[ $key ] = $id;
	}
}
//sort( $dupeids );
foreach( $dupeids as $key=>$dupeid )
{
	echo $key.': '.$dupeid."\n";
}
echo 'count dupe records = '.count( $dupeids )."\n";


$counties = array(
1 =>'ADAMS',
2 =>'ALLEGHENY',
3 =>'ARMSTRONG',
4 =>'BEAVER',
5 =>'BEDFORD',
6 =>'BERKS',
7 =>'BLAIR',
8 =>'BRADFORD',
9 =>'BUCKS',
10 =>'BUTLER',
11 =>'CAMBRIA',
12 =>'CAMERON',
13 =>'CARBON',
14 =>'CENTRE',
15 =>'CHESTER',
16 =>'CLARION',
17 =>'CLEARFIELD',
18 =>'CLINTON',
19 =>'COLUMBIA',
20 =>'CRAWFORD',
21 =>'CUMBERLAND',
22 =>'DAUPHIN',
23 =>'DELAWARE',
24 =>'ELK',
25 =>'ERIE',
26 =>'FAYETTE',
27 =>'FOREST',
28 =>'FRANKLIN',
29 =>'FULTON',
30 =>'GREENE',
31 =>'HUNTINGDON',
32 =>'INDIANA',
33 =>'JEFFERSON',
34 =>'JUNIATA',
35 =>'LACKAWANNA',
36 =>'LANCASTER',
37 =>'LAWRENCE',
38 =>'LEBANON',
39 =>'LEHIGH',
40 =>'LUZERNE',
41 =>'LYCOMING',
42 =>'McKEAN',
43 =>'MERCER',
44 =>'MIFFLIN',
45 =>'MONROE',
46 =>'MONTGOMERY',
47 =>'MONTOUR',
48 =>'NORTHAMPTON',
49 =>'NORTHUMBERLAND',
50 =>'PERRY',
51 =>'PHILADELPHIA',
52 =>'PIKE',
53 =>'POTTER',
54 =>'SCHUYLKILL',
55 =>'SNYDER',
56 =>'SOMERSET',
57 =>'SULLIVAN',
58 =>'SUSQUEHANNA',
59 =>'TIOGA',
60 =>'UNION',
61 =>'VENANGO',
62 =>'WARREN',
63 =>'WASHINGTON',
64 =>'WAYNE',
65 =>'WESTMORELAND',
66 =>'WYOMING',
67 =>'YORK'
);

$firstrecords = array();
$totallines = 0;
foreach( $counties as $key=>$county )
{
	$firstrecords[ $key ] = $totallines;
	$lines = shell_exec( 'wc -l < '.$county.'_FVE_'.str_replace( '-', '',$argv[1] ).'.txt' );
	echo $totallines."\n";
	$totallines = $totallines + $lines;
}

foreach ( $dupeids as $key=>$dupeid )
{
	$parts = explode( '-', $dupeid );
	$voterid = $parts[0];
	$countycode = $parts[1] * 1;
	$county = $counties[ $countycode ];
	$firstrecord = $firstrecords[ $countycode ];
	echo 'dupe id = '.$dupeid."\n";

	$recordinfile = $key - $firstrecord + 1;
	$countyfile = $county.'_FVE_'.str_replace( '-', '',$argv[1] ).'.txt';
	$record = shell_exec( 'head -n '.$recordinfile.' '.$countyfile.' | tail -n +'.$recordinfile );
	$lineparts = explode( "\t", $record );
	$id_in_record = str_replace( '"', '', $lineparts[0] )."\n";
	echo 'id in record: '.$id_in_record;
	$i = 0;
	while( $id_in_record != $dupeid && $i < 100 )
	{
		$recordinfile = $recordinfile + 1;
		$record = shell_exec( 'head -n '.$recordinfile.' '.$countyfile.' | tail -n +'.$recordinfile );
		$lineparts = explode( "\t", $record );
		$id_in_record = str_replace( '"', '', $lineparts[0] )."\n";
		echo 'id in next record: '.$id_in_record;
		$i++;
	}
	$i = 0;
	while( $id_in_record != $dupeid && $i < 100 )
	{
		$recordinfile = $recordinfile - 1;
		$record = shell_exec( 'head -n '.$recordinfile.' '.$countyfile.' | tail -n +'.$recordinfile );
		$lineparts = explode( "\t", $record );
		$id_in_record = str_replace( '"', '', $lineparts[0] )."\n";
		echo 'id in previous record: '.$id_in_record;
		$i++;
	}
	fwrite( $outputfile, $record );
}

fclose( $outputfile );

?>