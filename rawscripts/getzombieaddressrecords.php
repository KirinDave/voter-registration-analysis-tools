<?php
/*
Looks at list of all addresses for three data snapshots, the one in the local directory and two control sets in separate directories

Does array_count_values for both sets, then runs through local address set looking for records where the valuecount is greater than in the control set

We write these values and keys to a new array, then collect records from local data files.

This is done efficiently by noting the key of each duplicate and pulling the corresponding record from the appropriate county file

Syntax:
php getzombieaddressrecords.php thisdate earlierdate laterdate
Example:
php getzombieaddressrecords.php 2016-11-07 2016-08-15 2017-02-27
*/

$outputfilename = 'suspectedzombierecords_'.$argv[2].'_'.$argv[3].'.txt';
$outputfile = fopen( $outputfilename, 'w' );
$outputfilename2 = 'popupzombierecords_'.$argv[2].'_'.$argv[3].'.txt';
$outputfile2 = fopen( $outputfilename2, 'w' );

$addresses = file( 'addresses.txt' );
$compareaddresses = file( '../'.$argv[2].'/addresses.txt' );
$compareaddresses2 = file( '../'.$argv[3].'/addresses.txt' );
$fullids = file( 'fullids.txt' );
$valuecounts = array_count_values( $addresses );
$comparevaluecounts = array_count_values( $compareaddresses );
$comparevaluecounts2 = array_count_values( $compareaddresses2 );

$currentcount = array();
$earliercount  = array();
$latercount = array();
$zombieaddressids = array();
$zombiecount1 = array();
$zombiecount2 = array();
foreach( $addresses as $key=>$address )
{
	if( array_key_exists( $address, $comparevaluecounts ) && array_key_exists( $address, $comparevaluecounts2 ) )
	{
		if( $valuecounts[ $address ] > $comparevaluecounts[ $address ] && $valuecounts[ $address ] > $comparevaluecounts2[ $address ] )
		{
			$currentcount[ $key ] = $valuecounts[ $address ];
			$earliercount[ $key ] = $comparevaluecounts[ $address ];
			$latercount[ $key ] = $comparevaluecounts2[ $address ];
			$zombiecount1[ $key ] = $valuecounts[ $address ] - $comparevaluecounts[ $address ];
			$zombiecount2[ $key ] = $valuecounts[ $address ] - $comparevaluecounts2[ $address ];
			$zombieaddressids[ $key ] = $fullids[ $key ];
			//if( $zombiecount1 > 20 && $zombiecount2 > 20 ) echo $address."\n";
		}
		elseif( $comparevaluecounts[ $address ] > $valuecounts[ $address ] && $comparevaluecounts[ $address ] > $comparevaluecounts2[ $address ] )
		{
			$augusthasmostaddressids[ $key ] = $fullids[ $key ];
		}
		elseif( $comparevaluecounts2[ $address ] > $valuecounts[ $address ] && $comparevaluecounts2[ $address ] > $comparevaluecounts[ $address ] )
		{
			$februaryhasmostaddressids[ $key ] = $fullids[ $key ];
		}
	}
	elseif( ! array_key_exists( $address, $comparevaluecounts ) && ! array_key_exists( $address, $comparevaluecounts2 ) )
	{
		$newids[ $key ] = $fullids[ $key ];
		$newcurrentcount[ $key ] = $valuecounts[ $address ];
	}
}
unset( $addresses );
unset( $compareaddresses );
unset( $compareaddresses2 );
unset( $fullids );

$zombiedistribution = array_count_values( $zombiecount1 );
ksort( $zombiedistribution );
print_r( $zombiedistribution );

$zombiedistribution2 = array_count_values( $zombiecount2 );
ksort( $zombiedistribution2 );
print_r( $zombiedistribution2 );

echo 'count zombie addresses = '.count( $zombieaddressids )."\n";
echo 'count zombie pop-up addresses = '.count( $newids )."\n";


echo 'count addresses with most occupants on '.$argv[2].' = '.count( $augusthasmostaddressids )."\n";
echo 'count addresses with most occupants on '.$argv[3].' = '.count( $februaryhasmostaddressids )."\n";
//return;


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
	echo $lines."\n";
	$totallines = $totallines + $lines;
}
$count = 0;
foreach ( $zombieaddressids as $key=>$zombieaddressid )
{
	$count++;
	if( $count > 0 )
	{
		$parts = explode( '-', $zombieaddressid );
		$voterid = $parts[0];
		$countycode = $parts[1] * 1;
		$county = $counties[ $countycode ];
		$firstrecord = $firstrecords[ $countycode ];
		echo 'zombie address id: '.$zombieaddressid;

		$recordinfile = $key - $firstrecord + 1;
		$countyfile = $county.'_FVE_'.str_replace( '-', '',$argv[1] ).'.txt';
		$record = shell_exec( 'head -n '.$recordinfile.' '.$countyfile.' | tail -n +'.$recordinfile );
		$lineparts = explode( "\t", $record );
		$id_in_record = str_replace( '"', '', $lineparts[0] )."\n";
		echo 'id in record: '.$id_in_record."\n";
		$i = 0;
		while( $id_in_record != $zombieaddressid && $i < 10 )
		{
			$recordinfile = $recordinfile + 1;
			$record = shell_exec( 'head -n '.$recordinfile.' '.$countyfile.' | tail -n +'.$recordinfile );
			$lineparts = explode( "\t", $record );
			$id_in_record = str_replace( '"', '', $lineparts[0] )."\n";
			echo 'id in next record: '.$id_in_record;
			$i++;
		}
		$i = 0;
		while( $id_in_record != $zombieaddressid && $i < 10 )
		{
			$recordinfile = $recordinfile - 1;
			$record = shell_exec( 'head -n '.$recordinfile.' '.$countyfile.' | tail -n +'.$recordinfile );
			$lineparts = explode( "\t", $record );
			$id_in_record = str_replace( '"', '', $lineparts[0] )."\n";
			echo 'id in previous record: '.$id_in_record;
			$i++;
		}
		$recordpluscounts = trim( $record )."	".$earliercount[ $key ]."	".$latercount[ $key ]."	".$currentcount[ $key ]."\n";
		fwrite( $outputfile, $recordpluscounts );
	}
}

fclose( $outputfile );

foreach ( $newids as $key=>$newid )
{
	$parts = explode( '-', $newid );
	$voterid = $parts[0];
	$countycode = $parts[1] * 1;
	$county = $counties[ $countycode ];
	$firstrecord = $firstrecords[ $countycode ];
	echo 'zombie new address id: '.$newid;

	$recordinfile = $key - $firstrecord + 1;
	$countyfile = $county.'_FVE_'.str_replace( '-', '',$argv[1] ).'.txt';
	$record = shell_exec( 'head -n '.$recordinfile.' '.$countyfile.' | tail -n +'.$recordinfile );
	$lineparts = explode( "\t", $record );
	$id_in_record = str_replace( '"', '', $lineparts[0] )."\n";
	echo 'id in record: '.$id_in_record."\n";
	$i = 0;
	while( $id_in_record != $newid && $i < 10 )
	{
		$recordinfile = $recordinfile + 1;
		$record = shell_exec( 'head -n '.$recordinfile.' '.$countyfile.' | tail -n +'.$recordinfile );
		$lineparts = explode( "\t", $record );
		$id_in_record = str_replace( '"', '', $lineparts[0] )."\n";
		echo 'id in next record: '.$id_in_record;
		$i++;
	}
	$i = 0;
	while( $id_in_record != $newid && $i < 10 )
	{
		$recordinfile = $recordinfile - 1;
		$record = shell_exec( 'head -n '.$recordinfile.' '.$countyfile.' | tail -n +'.$recordinfile );
		$lineparts = explode( "\t", $record );
		$id_in_record = str_replace( '"', '', $lineparts[0] )."\n";
		echo 'id in previous record: '.$id_in_record;
		$i++;
	}
	$recordpluscounts = trim( $record )."	0	0	".$newcurrentcount[ $key ]."\n";
	fwrite( $outputfile2, $recordpluscounts );
}

fclose( $outputfile2 );
?>