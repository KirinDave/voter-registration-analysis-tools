<?php
/*
Counts the number of duplicate full IDs in a dataset, writes them to output file
*/

$outputfilename = 'dupefullids.txt';
$outputfile = fopen( $outputfilename, 'w');
$allfullids = file( 'fullids.txt' );
$fullids = array_unique( $allfullids );
echo 'all fullids: '.count( $allfullids )."\n";
echo 'unique fullids: '.count( $fullids )."\n";
$dupecount = count( $allfullids ) - count( $fullids );
echo 'dupe fullids: '.$dupecount."\n";

//compare each id to the one before
$dupeids = file( 'dupevoterids.txt' );
echo 'dupe voter ids: '.count( $dupeids )."\n";
foreach( $dupeids as $key=>$dupeid )
{
	$dupeid = substr( $dupeid, 0, -1 ); //remove line break
	foreach( $fullids as $fullid )
	{
		$voterid = substr( $fullid, 0, -4 );
		//if( $key < 5 ) echo $dupeid.', '.$voterid."\n";
		if( $voterid == $dupeid )
		{
			echo $fullid;
			fwrite( $outputfile, $fullid );
		}
	}
}

fclose( $outputfile );

?>