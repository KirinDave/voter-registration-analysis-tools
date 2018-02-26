<?php
/*
Checks the "added" voters for a county against the missing voters from all counties, given a time period If one is found write the voter ID and the county name to output file.
Arguments: 
$argv[1] = county name
$argv[2] = date1 (like 2016-11-07)
$argv[3] = date2 (like 2017-07-31 )

Syntax:
php checkmissingforadded.php ALLEGHENY 2016-11-07 2017-07-31

*/
clearstatcache ();
$outputfilename = 'added/'.$argv[1].'_fromelsewhere_'.$argv[2].'-'.$argv[3].'.csv';

$outputfile = fopen( $outputfilename, 'w');
fwrite ( $outputfile, "County\tVoterID\n" );

//added/ALLEGHENY_2017-07-31_2016-11-07.csv
$addedfilename = 'added/'.$argv[1].'_'.$argv[3].'_'.$argv[2].'.csv';
$addedlines = file( $addedfilename );

$idarray = array();
foreach( $addedlines as $addedline )
{
	$lineparts = explode( "\t", $addedline );
	$fullid = str_replace( '"', '', $lineparts[0] );
	$idparts = explode( '-', $fullid );
	$id = $idparts[0];
	$idarray[] = $id;
}

// write data from first (earlier) file into arrays
print_r( $idarray );
//missing/FRANKLIN_2016-11-07_2017-07-31.csv
$missingfiles = shell_exec('ls -1 missing/*'.$argv[2].'_'.$argv[3].'.csv');

//$missingfiles = shell_exec('ls -1 '.$argv[4].'/*FVE*');

if( $missingfiles )
{
	echo $missingfiles;
	$missingarray = explode( "\n", $missingfiles );
	print_r( $missingarray );
	foreach( $missingarray as $missingfilename )
	{
		clearstatcache ();
		if( $missingfilename )
		{
			echo $missingfilename."\n";
			$filenameparts = explode( '_', $missingfilename );
			//$missingfromcountyname = str_replace( $argv[4].'/', '', $filenameparts[0] );
			$missingfromcountyname = str_replace( 'missing/', '', $filenameparts[0] );
			shell_exec( 'cp '.$missingfilename.' tail.tsv' );
			while( filesize ( 'tail.tsv' ) > 0 )
			{
				clearstatcache ();
				shell_exec( 'mv tail.tsv tmp.tsv' );
				shell_exec( 'head -n 500000 tmp.tsv > head.tsv' );
				shell_exec( 'tail -n +500001 tmp.tsv > tail.tsv' );
				$datalines = file( 'head.tsv' );
				foreach ($datalines as $dataline )
				{
					$lineparts = explode( "\t", $dataline );
					$fullid = str_replace( '"', '', $lineparts[0] );
					$idparts = explode( '-', $fullid );
					$id = $idparts[0];
					if( in_array( $id, $idarray )  && $id != 'ID' )
					{
						echo $missingfromcountyname."	".$id."\n";
						fwrite( $outputfile, $missingfromcountyname."	".$id."\n" );
					}
				}
			}
		}
	}
}

fclose( $outputfile );

?>