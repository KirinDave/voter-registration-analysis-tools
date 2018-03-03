<?php
/*
Gets the Total, Dem, Rep, Other registrations per county. Writes summary to a file called registrationsbyparty.csv

Syntax:
php countregistrationsbyparty.php %STATEABBR%

Example:
php countregistrationsbyparty.php pa

*/

$outputfilename = 'registrationsbyparty.csv';

$outputfile = fopen( $outputfilename, 'w');
fwrite ( $outputfile, "County\tTotal Registered\tDems\tReps\tOthers\n" );

$filelines = file( 'address_plus_set.csv' );

if( isset( $argv[1] ) ) $state = $argv[1];
else $state = '';

//make separate array for counties and multi-sort by county name
//skip for pa - records were read in from county files so they are already ordered that way
if( strtoupper( $state ) != 'PA' )
{
	$counties = array();
	foreach ($filelines as $fileline )
	{
		//$address.'|'.$countyname.'|'.$phone.'|'.$party.'|'.$status.'|'.$lastvotedate.'|'.$registrationdate.'|'.$statuschangedate.'|'.$lastchangedate
		$counties[] = trim( $lineparts[1] );
	}

	array_multisort( $counties, $filelines );
}

$lastcountyname = 'xxx';
$dems = 0;
$reps = 0;
$others = 0;
$total = 0;
$totaldems = 0;
$totalreps = 0;
$totalothers = 0;
$totaltotal = 0;
foreach ($filelines as $fileline )
{
	if( $fileline )
	{
		//$address.'|'.$countyname.'|'.$phone.'|'.$party.'|'.$status.'|'.$lastvotedate.'|'.$registrationdate.'|'.$statuschangedate.'|'.$lastchangedate
		$lineparts = explode( '|', $fileline );
		$countyname = trim( $lineparts[1] );
		$party = trim( $lineparts[3] );
		// if the countycode has changed print totals to file and reset
		if( $countyname != $lastcountyname && $lastcountyname != 'xxx' )
		{
			echo $lastcountyname."\t".$total."\t".$dems."\t".$reps."\t".$others."\n";
			fwrite ( $outputfile, $lastcountyname."\t".$total."\t".$dems."\t".$reps."\t".$others."\n" );
			$dems = 0;
			$reps = 0;
			$others = 0;
			$total = 0;
		}

		if( $party == 'D' )
		{
			$dems++;
			$totaldems++;
		}
		elseif( $party == 'R' )
		{
			$reps++;
			$totalreps++;
		}
		else
		{
			$others++;
			$totalothers++;
		}
		$total++;
		$totaltotal++;
		
		$lastcountyname = $countyname;
	}
}

// print the last line and totals
echo $lastcountyname."\t".$total."\t".$dems."\t".$reps."\t".$others."\n";
fwrite ( $outputfile, $lastcountyname."\t".$total."\t".$dems."\t".$reps."\t".$others."\n" );
// print the last line
echo "TOTAL\t".$totaltotal."\t".$totaldems."\t".$totalreps."\t".$totalothers."\n";
fwrite ( $outputfile, "TOTAL\t".$totaltotal."\t".$totaldems."\t".$totalreps."\t".$totalothers."\n" );

fclose( $outputfile );


?>