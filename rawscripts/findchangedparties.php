<?php
/*
Counts the number of changed parties for each county.
Arguments: 
$argv[1] = date1 (like 2016-11-07)
$argv[2] = date2 (like 2017-02-27)

1. Gets list of county names from files in first subdir
2. Walks through county names
3. Writes ids, and parties from file in first dir into an array
4. Checks ids and parties in second file
5. Writes a "changed" file with all ids where the party code is different from the earlier party code
6. Counts changed parties per county and writes to a changed_summary.csv file

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
php findchangedparties.php 2017-02-27 2016-11-07 

*/

$outputfilename = 'changed/changed_summary_'.$argv[2].'-'.$argv[1].'.csv';

$outputfile = fopen( $outputfilename, 'w');
fwrite ( $outputfile, "County\tTotal Registered\tTotal Changed\tPercent Changed\tNew Dems\tNew Reps\tNew Others\tDem to Rep\tDem to Other\tRep to Dem\tRep to Other\tDem to Other\tRep to Other\n" );

$tsvfiles = shell_exec('ls -1 '.$argv[1].'/*FVE*');

if( $tsvfiles )
{
	$tsvarray = explode( "\n", $tsvfiles );
	foreach( $tsvarray as $tsvfilename )
	{

		// write data from first (NEWER) file into arrays
		$idarray = array();
		$countyname = str_replace( $argv[1].'/', '', $tsvfilename );
		$datecode = str_replace( '-', '', $argv[1] );
		$filenamesuffix = '_FVE_'.$datecode.'.txt';
		$countyname = str_replace( $filenamesuffix, '', $countyname );
		echo $countyname."\n";
		$idarray = array();
		$partyarray = array();

		//if( $countyname == 'PHILADELPHIA' || $countyname == 'PIKE' || $countyname == 'POTTER' || $countyname == 'SCHUYLKILL' || $countyname == 'SNYDER' || $countyname == 'SOMERSET' || $countyname == 'SULLIVAN' || $countyname == 'SUSQUEHANNA' || $countyname == 'TIOGA' || $countyname == 'UNION' || $countyname == 'VENANGO' || $countyname == 'WARREN' || $countyname == 'WASHINGTON' || $countyname == 'WAYNE' || $countyname == 'WESTMORELAND' || $countyname == 'WYOMING' || $countyname == 'YORK' )
		if( true )
		{
			$countyoutputfilename = 'changed/'.$countyname.'_'.$argv[2].'_'.$argv[1].'.csv';
			$countyoutputfile = fopen( $countyoutputfilename, 'w');
			fwrite( $countyoutputfile, "ID	Status	Old Party	New Party	Registration Date	Status Change Date	Date Last Changed	Last Vote Date\n" );
			shell_exec( 'cp '.$tsvfilename.' tail.tsv' );
			$i = 0;
			while( filesize ( 'tail.tsv' ) > 0 )
			{
				clearstatcache ();
				shell_exec( 'mv tail.tsv tmp.tsv' );
				shell_exec( 'head -n 10000 tmp.tsv > head.tsv' );
				shell_exec( 'tail -n +10001 tmp.tsv > tail.tsv' );
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
					$party = str_replace( '"', '', $lineparts[11] );
					$idarray[] = $id;
					$partyarray[] = $party;
				}
			}
			echo "found ".count( $idarray )." ids\n";
			//open second (OLDER) file
			$datecode2 = str_replace( '-', '', $argv[2] );
			$tsvfilename2 = str_replace( $datecode, $datecode2, str_replace( $argv[1], $argv[2], $tsvfilename ) );
			echo $tsvfilename2."\n";
			$changedcount = 0;
			$demtorep = 0;
			$demtoother = 0;
			$reptodem = 0;
			$reptoother = 0;
			$othertodem = 0;
			$othertorep = 0;
			$newdems = 0;
			$newreps = 0;
			$newothers = 0;
			shell_exec( 'cp '.$tsvfilename2.' tail2.tsv' );
			while( filesize ( 'tail2.tsv' ) > 0 )
			{
				clearstatcache ();
				shell_exec( 'mv tail2.tsv tmp2.tsv' );
				shell_exec( 'head -n 10000 tmp2.tsv > head2.tsv' );
				shell_exec( 'tail -n +10001 tmp2.tsv > tail2.tsv' );
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
					$partycode = str_replace( '"', '', $lineparts[11] );
					if( in_array( $id, $idarray ) )
					{
						$key = array_search( $id, $idarray );
						$newpartycode = $partyarray[ $key ];
						if( $partycode != $newpartycode )
						{
							echo "changed party: ".$id." old party: ".$partycode." new party: ".$newpartycode."\n";
							$status = str_replace( '"', '', $lineparts[9] );
							$registrationdate = str_replace( '"', '', $lineparts[8] );
							$statuschangedate = str_replace( '"', '', $lineparts[10] );
							$lastvotedate = str_replace( '"', '', $lineparts[25] );
							$datelastchanged = str_replace( '"', '', $lineparts[28] );
							$changedcount++;
							
							if( $partycode == 'D' && $newpartycode == 'R' )
							{
								$demtorep++;
								$newreps++;
								$newdems--;
							}
							elseif( $partycode == 'D' && $newpartycode != 'R' )
							{
								$demtoother++;
								$newothers++;
								$newdems --;
							}
							elseif( $partycode == 'R' && $newpartycode == 'D'  )
							{
								$reptodem++;
								$newdems++;
								$newreps--;
							}
							elseif( $partycode == 'R' && $newpartycode != 'D'  )
							{
								$reptoother++;
								$newothers++;
								$newreps--;
							}
							elseif( ( $partycode != 'R'&& $partycode != 'D' ) && $newpartycode == 'D'  )
							{
								$othertodem++;
								$newdems++;
								$newothers--;
							}
							elseif( ( $partycode != 'R'&& $partycode != 'D' ) && $newpartycode == 'R'  )
							{
								$othertorep++;
								$newreps++;
								$newothers--;
							}

							//echo $id."	".$status."	".$partycode."	".$registrationdate."	".$statuschangedate."	".$datelastchanged."	".$lastvotedate."	".$changedcount."\n";
							fwrite( $countyoutputfile, $id."	".$status."	".$partycode."	".$newpartycode."	".$registrationdate."	".$statuschangedate."	".$datelastchanged."	".$lastvotedate."\n" );
						}
					}
				}
			}
			$changedpercent = round( ( 100 * ( $changedcount / count( $idarray ) ) ), 2 ).'%';
			fwrite( $outputfile, $countyname."	".count( $idarray )."	".$changedcount."	".$changedpercent."	".$newdems."	".$newreps."	".$newothers."	".$demtorep."	".$demtoother."	".$reptodem."	".$reptoother."	".$othertodem."	".$othertorep."\n" );
			fclose( $countyoutputfile );
		}
	}
	fclose( $outputfile );
}


?>