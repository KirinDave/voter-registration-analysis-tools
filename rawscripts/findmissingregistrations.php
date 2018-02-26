<?php
/*
Counts the number of missing registrations for each county.
Arguments: 
$argv[1] = date1 (like 2016-11-07)
$argv[2] = date2 (like 2017-02-27)

1. Gets list of county names from files in first subdir
2. Walks through county names
3. Writes ids and status codes from file in first dir into an array
4. Checks ids in second file
5. Writes a "missing" file with all ids and status codes from first file that are not found in second file
6. Counts missing lines per county and writes to a missing_summary.csv file

Summary File Format:
County Name	Total Missing	Active Missing	Inactive Missing Republican Missing	Democrat Missing	Other Missing

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
php findmissingregistrations.php 2017-07-31 2016-11-07 

*/

$outputfilename = 'missing/missing_summary_'.$argv[2].'-'.$argv[1].'.csv';

$outputfile = fopen( $outputfilename, 'w');
fwrite ( $outputfile, "County\tTotal Missing\tActive\tInactive Recent Voter\tInactive\tDems\tReps\tOther Party\n" );

$tsvfiles = shell_exec('ls -1 '.$argv[1].'/*FVE*');

if( $tsvfiles )
{
	$tsvarray = explode( "\n", $tsvfiles );
	foreach( $tsvarray as $tsvfilename )
	{

		// write data from first (later) file into arrays
		$idarray = array();
		$countyname = str_replace( $argv[1].'/', '', $tsvfilename );
		$datecode = str_replace( '-', '', $argv[1] );
		$filenamesuffix = '_FVE_'.$datecode.'.txt';
		$countyname = str_replace( $filenamesuffix, '', $countyname );
		echo $countyname."\n";
		$idarray = array();

		if(  $countyname == 'MONTGOMERY' || $countyname == 'MONTOUR' || $countyname == 'NORTHAMPTON' || $countyname == 'NORTHUMBERLAND' || $countyname == 'PERRY' || $countyname == 'PHILADELPHIA' || $countyname == 'PIKE' || $countyname == 'POTTER' || $countyname == 'SCHUYLKILL' || $countyname == 'SNYDER' || $countyname == 'SOMERSET' || $countyname == 'SULLIVAN' || $countyname == 'SUSQUEHANNA' || $countyname == 'TIOGA' || $countyname == 'UNION' || $countyname == 'VENANGO' || $countyname == 'WARREN' || $countyname == 'WASHINGTON' || $countyname == 'WAYNE' || $countyname == 'WESTMORELAND' || $countyname == 'WYOMING' || $countyname == 'YORK' )
		{
			$countyoutputfilename = 'missing/'.$countyname.'_'.$argv[2].'_'.$argv[1].'.csv';
			$countyoutputfile = fopen( $countyoutputfilename, 'w');
			fwrite( $countyoutputfile, "ID	Status	Party	Registration Date	Status Change Date	Date Last Changed	Last Vote Date\n" );
			shell_exec( 'cp '.$tsvfilename.' tail.tsv' );
			$i = 0;
			while( filesize ( 'tail.tsv' ) > 0 )
			{
				clearstatcache ();
				shell_exec( 'mv tail.tsv tmp.tsv' );
				shell_exec( 'head -n 500000 tmp.tsv > head.tsv' );
				shell_exec( 'tail -n +500001 tmp.tsv > tail.tsv' );
				$i++;
				echo $i."\n";
				unset( $tsvlines );
				unset( $tsvline );
				unset( $tsvlines2 );
				unset( $tsvline2 );
				unset( $lineparts );
				$tsvlines = file( 'head.tsv' );
				foreach ($tsvlines as $tsvline )
				{
					//echo $tsvline."\n";
					$lineparts = explode( "\t", $tsvline );
					$id = str_replace( '"', '', $lineparts[0] );
					$idarray[] = $id;
				}
			}
			echo "found ".count( $idarray )." ids\n";
			//open second (earlier) file
			$datecode2 = str_replace( '-', '', $argv[2] );
			$tsvfilename2 = str_replace( $datecode, $datecode2, str_replace( $argv[1], $argv[2], $tsvfilename ) );
			echo $tsvfilename2."\n";
			$missingcount = 0;
			$missingactive = 0;
			$missinginactivevoted = 0;
			$missingdem = 0;
			$missingrep = 0;
			shell_exec( 'cp '.$tsvfilename2.' tail2.tsv' );
			while( filesize ( 'tail2.tsv' ) > 0 )
			{
				clearstatcache ();
				shell_exec( 'mv tail2.tsv tmp2.tsv' );
				shell_exec( 'head -n 500000 tmp2.tsv > head2.tsv' );
				shell_exec( 'tail -n +500001 tmp2.tsv > tail2.tsv' );
				unset( $tsvlines );
				unset( $tsvline );
				unset( $tsvlines2 );
				unset( $tsvline2 );
				unset( $lineparts );
				$tsvlines2 = file( 'head2.tsv' );
				foreach ($tsvlines2 as $tsvline2 )
				{
					$lineparts = explode( "\t", $tsvline2 );
					$id = str_replace( '"', '', $lineparts[0] );
					if( ! in_array( $id, $idarray ) ) //MISSING FROM FIRST (LATER) FILE!
					{
						echo "missing: ".$id."\n";
						$status = str_replace( '"', '', $lineparts[9] );
						$registrationdate = str_replace( '"', '', $lineparts[8] );
						$statuschangedate = str_replace( '"', '', $lineparts[10] );
						$partycode = str_replace( '"', '', $lineparts[11] );
						$lastvotedate = str_replace( '"', '', $lineparts[25] );
						$datelastchanged = str_replace( '"', '', $lineparts[28] );
						$lastvotedatejulian = strtotime( $lastvotedate );
						$nov2009 = strtotime( '2009-11-01' );
						$missingcount++;
						if( $status == 'A' ) $missingactive++;
						if( $status == 'I' && $lastvotedatejulian > $nov2009 ) $missinginactivevoted++;
						if( $partycode == 'D' ) $missingdem++;
						if( $partycode == 'R' ) $missingrep++;
						$missingids[] = $id;
						//echo $id."	".$status."	".$partycode."	".$registrationdate."	".$statuschangedate."	".$datelastchanged."	".$lastvotedate."	".$missingcount."\n";
						fwrite( $countyoutputfile, $id."	".$status."	".$partycode."	".$registrationdate."	".$statuschangedate."	".$datelastchanged."	".$lastvotedate."\n" );
					}
				}
			}

			$missinginactive = $missingcount - $missingactive - $missinginactivevoted;
			$missingother = $missingcount - $missingdem - $missingrep;
			fwrite( $outputfile, $countyname."	".$missingcount."	".$missingactive."	".$missinginactivevoted."	".$missinginactive."	".$missingdem."	".$missingrep."	".$missingother."\n" );
			fclose( $countyoutputfile );
		}
	}
	fclose( $outputfile );
}



?>