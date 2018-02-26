<?php
/*
Counts the number of duplicate voter IDs in a dataset


1. Gets list of county names from files in first subdir
2. Walks through county names
3. Writes ids and status codes from file in first dir into an array
4. Checks ids in second file
5. Writes a "added" file with all ids and status codes from first file that are not found in second file
6. Counts added lines per county and writes to a added_summary.csv file

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
php finddupes.php 

*/



$tsvfiles = shell_exec('ls -1 *FVE*');

if( $tsvfiles )
{
	$tsvarray = explode( "\n", $tsvfiles );
	$othertsvarray = $tsvarray;
	foreach( $tsvarray as $tsvfilename )
	{
		// write data from file into array
		$fileparts = explode( '_FVE_', $tsvfilename );
		$countyname = $fileparts[0];
		echo $countyname."\n";
		$idarray = array();
		$fullidarray = array();
		//if( $countyname == 'LUZERNE' ||  $countyname == 'LYCOMING' ||  $countyname == 'McKEAN' ||  $countyname == 'MERCER' ||  $countyname == 'MIFFLIN' ||  $countyname == 'MONROE' ||  $countyname == 'MONTGOMERY' ||  $countyname == 'MONTOUR' ||  $countyname == 'NORTHAMPTON' ||  $countyname == 'NORTHUMBERLAND' ||  $countyname == 'PERRY' ||  $countyname == 'PHILADELPHIA' || $countyname == 'PIKE' || $countyname == 'POTTER' || $countyname == 'SCHUYLKILL' || $countyname == 'SNYDER' || $countyname == 'SOMERSET' || $countyname == 'SULLIVAN' || $countyname == 'SUSQUEHANNA' || $countyname == 'TIOGA' || $countyname == 'UNION' || $countyname == 'VENANGO' || $countyname == 'WARREN' || $countyname == 'WASHINGTON' || $countyname == 'WAYNE' || $countyname == 'WESTMORELAND' || $countyname == 'WYOMING' || $countyname == 'YORK' )
		if( true )
		{
			$countyoutputfilename = $countyname.'_dupes.csv';
			$countyoutputfile = fopen( $countyoutputfilename, 'w');
			fwrite( $countyoutputfile, "ID	Other County	Other ID\n" );
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
				$tsvlines = file( 'head.tsv' );
				foreach ($tsvlines as $tsvline )
				{
					//echo $tsvline."\n";
					$lineparts = explode( "\t", $tsvline );
					$fullid = str_replace( '"', '', $lineparts[0] );
					$idparts = explode( '-', $fullid );
					$id = $idparts[0];
					$idarray[] = $id;
					$fullidarray[] = $fullid;
				}
			}
			//print_r( $idarray );
			echo "found ".count( $idarray )." ids in ".$countyname."\n";
			// go through array, skipping current county
			foreach( $othertsvarray as $tsvfilename2 )
			{
				clearstatcache ();
				// write data from file into array
				$fileparts = explode( '_FVE_', $tsvfilename2 );
				$countyname2 = $fileparts[0];
				if( $countyname2 != $countyname && ! ( $countyname =='ADAMS' && ( $countyname2 == 'ALLEGHENY' || $countyname2 == 'ARMSTRONG' || $countyname2 == 'BEAVER' || $countyname2 == 'BEDFORD' || $countyname2 == 'BERKS' || $countyname2 == 'BLAIR' || $countyname2 == 'BRADFORD' || $countyname2 == 'BUCKS' ) ) )
				{
					echo 'checking '.$countyname2."\n";
					shell_exec( 'cp '.$tsvfilename2.' tail2.tsv' );
					$j = 0;
					while( filesize ( 'tail2.tsv' ) > 0 )
					{
						clearstatcache ();
						shell_exec( 'mv tail2.tsv tmp2.tsv' );
						shell_exec( 'head -n 500000 tmp2.tsv > head2.tsv' );
						shell_exec( 'tail -n +500001 tmp2.tsv > tail2.tsv' );
						$j++;
						echo $j."\n";
						$tsvlines2 = file( 'head2.tsv' );
						foreach ($tsvlines2 as $tsvline2 )
						{
							$lineparts = explode( "\t", $tsvline2 );
							$fullid2 = str_replace( '"', '', $lineparts[0] );
							$idparts = explode( '-', $fullid2 );
							$id = $idparts[0];
							//echo $id."\n";
							if( in_array( $id, $idarray ) ) //FOUND ID IN DIFFERENT COUNTy!!!
							{
								$key = array_search( $id, $idarray );
								$firstfullid = $fullidarray[ $key ];
								echo "found:  ".$countyname.' - '.$firstfullid."\n";
								echo "found:  ".$countyname2.' - '.$fullid2."\n";
								fwrite( $countyoutputfile, $countyname."	".$firstfullid."	".$countyname2."	".$fullid2."\n" );
							}
						}
					}
				}
			}
			fclose( $countyoutputfile );
		}
	}
}



?>