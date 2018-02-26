<?php
/*
Checks a set of "added data" input files and counts people who have ever voted and people who voted on Nov. 08, 2016
Writes county name and these numbers to output file

Last Vote Date is field 6

Example:
php getvotedfromadded.php 2016-11-07 2017-07-31



*/



$outputfile = 'added/addedvoterlist_'.$argv[1].'_'.$argv[1].'.csv';
fwrite ( $outputfile, "County	Voted Before	Voted in November\n" );

$tsvfiles = shell_exec( 'ls -1 added/*'.$argv[1].'.csv' );

if( $tsvfiles )
{
	$tsvarray = explode( "\n", $tsvfiles );
	foreach( $tsvarray as $tsvfilename )
	{
	$filenameparts = explode( '_', $tsvfilename );
	$countyname = $filenameparts[0];
	if ( $countyname != 'added' )
	{
	$tsvlines = file( $tsvfilename );
	foreach ($tsvlines as $tsvline )
	{
		//echo $tsvline."\n";
		$lineparts = explode( "\t", $tsvline );
		$lastvoted = str_replace( '"', '', $lineparts[6] );
		
	}
}

fclose( $outputfile );

?>