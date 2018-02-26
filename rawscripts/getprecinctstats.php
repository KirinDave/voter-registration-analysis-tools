<?php
/*
Gets the Total, Dem, Rep, Libertarian and Other registrations per precinct. Writes summary to a file called precinctregstats.csv in the relevant date directory



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
php getprecinctstats.php

*/

$outputfilename = 'precinctregstats.csv';
$outputfile = fopen( $outputfilename, 'w');

$notfoundfilename = 'precinctsnotfound.csv';
$notfoundfile = fopen( $notfoundfilename, 'w');
fwrite ( $outputfile, "County\tPrecinct\tTotal Registered\tDems\tReps\tLibs\tOthers\tLibs and Others\tWomen\tMen\tUngendered\n" );

$tsvfiles = shell_exec('ls -1 *FVE*');
$codefiles = shell_exec('ls -1 *Zone_Codes*.csv');
/*
zone code format
CRAWFORD	1	10	ATHENS Twp
CRAWFORD	1	20	BEAVER Twp
CRAWFORD	1	30	BLOOMFIELD Twp
CRAWFORD	1	40	BLOOMING VALLEY Boro
CRAWFORD	1	50	CAMBRIDGE Twp
CRAWFORD	1	60	CAMBRIDGE SPRINGS Boro
CRAWFORD	1	70	CENTERVILLE Boro
CRAWFORD	1	80	COCHRANTON Boro
CRAWFORD	1	90	CONNEAUT Twp
CRAWFORD	1	100	CONNEAUT LAKE Boro
CRAWFORD	1	110	CONNEAUTVILLE Boro
CRAWFORD	1	120	CUSSEWAGO Twp
CRAWFORD	1	130	EAST FAIRFIELD Twp
CRAWFORD	1	140	EAST FALLOWFIELD Twp
CRAWFORD	1	150	EAST MEAD Twp
CRAWFORD	1	160	FAIRFIELD Twp
CRAWFORD	1	170	GREENWOOD Twp
CRAWFORD	1	180	HAYFIELD Twp
CRAWFORD	1	190	HYDETOWN Boro
*/
if( $tsvfiles )
{
	$tsvarray = explode( "\n", $tsvfiles );
	$codefilearray = explode( "\n", $codefiles );
	// precinct array is an array with precinct ids as keys and arrays with precinct name and registration counts as values
	
	//$datestring = 
	foreach( $tsvarray as $key=>$tsvfilename )
	{
		$precinctarray = array();
		if( $tsvfilename )
		{
			echo 'starting '.$tsvfilename."\n";
			$codefile = $codefilearray[ $key ];
			$codelines = file( $codefile );
			foreach( $codelines as $codeline )
			{
				$codeparts = explode( "\t", $codeline );
				if( count( $codeparts ) > 4  && $codeparts[4] ) $name = $codeparts[4];
				else $name = $codeparts[3];
				$name = trim( str_replace( '"', '', $name ) );
				if( is_numeric( $name ) ) $name = (int) $name;
				if( $codeparts[1] == 1 )
				{
					$precinctdata = array(
						'name' => $name,
						'dems' => 0,
						'reps' => 0,
						'libs' => 0,
						'others' => 0,
						'libsandothers' => 0,
						'women' => 0,
						'men' => 0,
						'ungendered' => 0,
						'total' => 0
					);
					$precinctarray[ trim( $codeparts[2] ) ] = $precinctdata;
				}
			}
			print_r( $precinctarray );

			$countyname = str_replace( '_FVE_20170731.txt', '', str_replace( '_FVE_20160404.txt', '', str_replace( '_FVE_20161107.txt', '', $tsvfilename ) ) );
			shell_exec( 'cp '.$tsvfilename.' tail.tsv' );
			$i = 0;
			clearstatcache ();
			while( filesize ( 'tail.tsv' ) > 0 )
			{
				echo filesize ( 'tail.tsv' )."\n";
				shell_exec( 'mv tail.tsv tmp.tsv' );
				shell_exec( 'head -n 500000 tmp.tsv > head.tsv' );
				shell_exec( 'tail -n +500001 tmp.tsv > tail.tsv' );
				clearstatcache ();
				$i++;
				echo $countyname.' - '.$i."\n";
				$tsvlines = file( 'head.tsv' );
				foreach ($tsvlines as $tsvline )
				{
					//echo $tsvline."\n";
					$lineparts = explode( "\t", $tsvline );
					$gender = trim( str_replace( '"', '', $lineparts[6] ) );
					$party = trim( str_replace( '"', '', $lineparts[11] ) );
					$precinctcode = trim( str_replace( '"', '', $lineparts[26] ) );
					if( is_numeric( $precinctcode ) ) $precinctcode = (int) $precinctcode;
					//echo $party."\n";
					if( isset( $precinctarray[ $precinctcode ] ) )
					{
						if( $party == 'D' ) $precinctarray[ $precinctcode ]['dems'] = $precinctarray[ $precinctcode ]['dems'] + 1;
						elseif( $party == 'R' ) $precinctarray[ $precinctcode ]['reps'] = $precinctarray[ $precinctcode ]['reps'] + 1;
						elseif( $party == 'LN' ) $precinctarray[ $precinctcode ]['libs'] = $precinctarray[ $precinctcode ]['libs'] + 1;
						else $precinctarray[ $precinctcode ]['others'] = $precinctarray[ $precinctcode ]['others'] + 1;
						if( $gender == 'F' ) $precinctarray[ $precinctcode ]['women'] = $precinctarray[ $precinctcode ]['women'] + 1;
						elseif( $gender == 'M' ) $precinctarray[ $precinctcode ]['men'] = $precinctarray[ $precinctcode ]['men'] + 1;
						else $precinctarray[ $precinctcode ]['ungendered'] = $precinctarray[ $precinctcode ]['ungendered'] + 1;
						$precinctarray[ $precinctcode ]['total'] = $precinctarray[ $precinctcode ]['total'] + 1;
					}
					else
					{	$notfound[] = $countyname.' - '.$precinctcode."\n";
						echo 'Precinct ID not found: '.$countyname.' - '.$precinctcode."\n";
					}
				}
			}
		}
		foreach( $precinctarray as $precinct )
		{
			$libsandothers = $precinct['libs'] + $precinct['others'];
			echo $countyname."\t".$precinct['name']."\t".$precinct['total']."\t".$precinct['dems']."\t".$precinct['reps']."\t".$precinct['libs']."\t".$precinct['others']."\t".$libsandothers."\n";
			fwrite ( $outputfile, $countyname."\t".$precinct['name']."\t".$precinct['total']."\t".$precinct['dems']."\t".$precinct['reps']."\t".$precinct['libs']."\t".$precinct['others']."\t".$libsandothers."\t".$precinct['women']."\t".$precinct['men']."\t".$precinct['ungendered']."\n" );
		}
	}
}
$uniquenotfound = array_unique( $notfound );
echo 'NOT FOUND: '.count( $uniquenotfound )."\n";
foreach( $uniquenotfound as $nf )
{
	fwrite( $notfoundfile, $nf );
}
fclose( $outputfile );
fclose( $notfoundfile );

?>