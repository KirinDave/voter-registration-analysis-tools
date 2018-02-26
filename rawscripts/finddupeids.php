<?php
/*
Counts the number of duplicate voter IDs in a dataset, writes them to output file
*/

$outputfilename = 'dupefullids.txt';
$outputfile = fopen( $outputfilename, 'w');
$allfullids = file( 'fullids.txt' );
$fullids = array_unique( $allfullids );
echo 'count fullids: '.count( $fullids )."\n";
//compare each id to the one before
$dupeids = file( 'dupevoterids.txt' );
echo 'count dupe voter ids: '.count( $dupeids )."\n";
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