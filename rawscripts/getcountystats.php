<?php
/*
Gets the Total, Dem, Rep, Libertarian and Other registrations per county. Writes summary to a file called countystats.csv in the relevant date directory
Arguments: 
$argv[1] = date (like 2016-11-07)


Fields
0 ID Number
6 Gender
7 DOB
8 Registration Date
9 Voter Status
10 Status Change Date
11 Party Code
17 City
25 Last Vote Date
26 Precinct Code
27 Precinct Split ID
28 Date Last Changed

Syntax:
php getcountystats.php 2016-11-07 

*/

$outputfilename = $argv[1].'/countystats.csv';

$outputfile = fopen( $outputfilename, 'w');
fwrite ( $outputfile, "County\tTotal Registered\tDems\tReps\tLibs\tOthers\n" );

$tsvfiles = shell_exec('ls -1 '.$argv[1].'/*FVE*');

if( $tsvfiles )
{
	$tsvarray = explode( "\n", $tsvfiles );
	foreach( $tsvarray as $tsvfilename )
	{
		if( $tsvfilename )
		{
			echo 'starting '.$tsvfilename."\n";
			$countyname = str_replace( $argv[1].'/', '', $tsvfilename );
			$datecode = str_replace( '-', '', $argv[1] );
			$filenamesuffix = '_FVE_'.$datecode.'.txt';
			$countyname = str_replace( $filenamesuffix, '', $countyname );
			shell_exec( 'cp '.$tsvfilename.' tail.tsv' );
			$dems = 0;
			$reps = 0;
			$libs = 0;
			$others = 0;
			$total = 0;
			$i = 0;
			clearstatcache ();
			while( filesize ( 'tail.tsv' ) > 0 )
			{
				echo filesize ( 'tail.tsv' )."\n";
				shell_exec( 'mv tail.tsv tmp.tsv' );
				shell_exec( 'head -n 50000 tmp.tsv > head.tsv' );
				shell_exec( 'tail -n +50001 tmp.tsv > tail.tsv' );
				clearstatcache ();
				$i++;
				echo $countyname.' - '.$i."\n";
				$tsvlines = file( 'head.tsv' );
				foreach ($tsvlines as $tsvline )
				{
					//echo $tsvline."\n";
					$lineparts = explode( "\t", $tsvline );
					$party = str_replace( '"', '', $lineparts[11] );
					//echo $party."\n";
					if( $party == 'D' ) $dems++;
					elseif( $party == 'R' ) $reps++;
					elseif( $party == 'LN' ) $libs++;
					else $others++;
					$total++;
				}
			}
		}
		echo $countyname."\t".$total."\t".$dems."\t".$reps."\t".$libs."\t".$others."\n";
		fwrite ( $outputfile, $countyname."\t".$total."\t".$dems."\t".$reps."\t".$libs."\t".$others."\n" );
	}
}
fclose( $outputfile );


?>