<?php
/*
Finds each piece of non-unique data and get its associated voter id, then pull the complete original record from the original set of Pennsylvania data files
This relies on the fact that the indexes of the data files match the indexes of the data when the raw data files are catted together

Syntax:
php getduperecords.php %FILENAME% %PATHTORAWDATAFILES%

Example:
php getduperecords.php address_set.csv ..
writes data to address_duperecords.csv

NOTE: It's a good idea to run printcounts.php on your record set of choice before running this script. The output file can easily become quite large.

*/
$countynames = file( 'countynames_pa.txt' );
$countykeys = array();

// re-index the county array to start at 1. PA uses this index padded to two digits to indicate county in voter id numbers
$i = 1;
while ( $i <= count( $countynames ) )
{
	$countykeys[] = $i;
	$i++;
}

$counties = array_combine( $countykeys, $countynames );


$outputfilename = str_replace( '_set.csv', '_duperecords.csv', $argv[1] );
$outputfile = fopen( $outputfilename, 'w' );

$records = file( $argv[1] );
if( isset( $argv[2] ) )
{
	$datapath = $argv[2];
	if( strpos( $datapath, '/' ) === false ) $datapath .= '/';
}
else $datapath = '';

$fullids = file( 'id_set.csv' );

$uniquerecords = array_unique( $records );
// this set is only ONE of the instances of each dupe. we need all of them.
$duperecords = array_diff_key( $records,  $uniquerecords );

// if we add in the records that intersect we will be all _set
$matchingduperecords = array_intersect( $uniquerecords, $duperecords );

echo 'dupes = '.count( $duperecords )."\n";
echo 'matching dupes = '.count( $matchingduperecords )."\n";

// now get full voter id and original array key for each duplicate record
$allduperecords = array();
$duperecordids = array();
$dupekeys= array();
foreach( $duperecords as $key=>$record )
{
	$allduperecords[] = trim( $record );
	$dupekeys[] = $key;
	$duperecordids[] = trim( $fullids[ $key ] );
}
foreach( $matchingduperecords as $key=>$record )
{
	$allduperecords[] = trim( $record );
	$dupekeys[] = $key;
	$duperecordids[] = trim( $fullids[ $key ] );
}

// sort arrays so the duplicate values are next to each other in the outputfile
array_multisort( $allduperecords, $dupekeys, $duperecordids );


// get the date from one of the data filenames
$datafiles = shell_exec( 'ls -1 '.$datapath.'*FVE*' );
$dataarray = explode( "\n", $datafiles );
$nameparts = explode( '_', $dataarray[1] );
$datestring = str_replace( '.txt', '', $nameparts[2] );

$firstrecords = array();
$totallines = 0;
foreach( $counties as $key=>$county )
{
	$firstrecords[ $key ] = $totallines;
	$lines = shell_exec( 'wc -l < '.$datapath.trim( $county ).'_FVE_'.$datestring.'.txt' );
	$totallines = $totallines + $lines;
}


foreach ( $duperecordids as $key=>$dupeid )
{
	$parts = explode( '-', $dupeid );
	$voterid = $parts[0];
	$countycode = $parts[1] * 1;
	$county = trim( $counties[ $countycode ] );
	$firstrecord = $firstrecords[ $countycode ];

	$recordinfile = $dupekeys[ $key ] - $firstrecord + 1;
	$countyfile = $datapath.$county.'_FVE_'.$datestring.'.txt';
	
	//slice the file based on record position
	$record = shell_exec( 'head -n '.$recordinfile.' '.$countyfile.' | tail -n +'.$recordinfile );
	$lineparts = explode( "\t", $record );
	$id_in_record = str_replace( '"', '', $lineparts[0] )."\n";
	if( $dupeid == trim( $id_in_record ) )
	{
		echo 'found id: '.$id_in_record;
		fwrite( $outputfile, $record );
	}
}

echo $argv[1].': total duplicates found = '.count( $allduperecords ) ."\n";

fclose( $outputfile );
?>