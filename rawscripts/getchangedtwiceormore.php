<?php
/*
Looks at lists of changed names, dobs or genders for n-1 different data set comparisons (n data snapshots)
TODO: abstract this out to allow an arbitrary number of sets

Example:
php getchangedtwiceormore.php changeddobs

Output:
changedtwiceormoredobs.txt

*/
// read input files - each line contains old and new data staggered in each row
$diffs = array();
$diffs[1] = file( $argv[1].'_2016-04-04_2016-08-15.txt' );
$diffs[2] = file( $argv[1].'_2016-08-15_2016-11-07.txt' );
$diffs[3] = file( $argv[1].'_2016-11-07_2016-11-28.txt' );
$diffs[4] = file( $argv[1].'_2016-11-28_2017-02-27.txt' );
$diffs[5] = file( $argv[1].'_2017-02-27_2017-07-31.txt' );

$root = $argv[1];
$newroot = str_replace( 'changed', 'changedtwiceormore', $root );
$dates[1] = '2016-04-04';
$dates[2] = '2016-08-15';
$dates[3] = '2016-11-07';
$dates[4] = '2016-11-28';
$dates[5] = '2017-02-27';
$dates[6] = '2017-07-31';

$outputfilename = $newroot.'.csv';
$outputfile = fopen( $outputfilename, 'w' );
fwrite( $outputfile, "ID	Snapshot Date	Name	DOB	Gender	Phone	Address	County	Party	Status	Changed Date	Last Voted\n" );

$voterids[1] = array();
$voterids[2] = array();
$voterids[3] = array();
$voterids[4] = array();
$voterids[5] = array();

//read each "diff" file, write voter ids to their own arrays

$i = 1;
while( $i <= 5 )
{
	foreach( $diffs[ $i ] as $key=>$diff )
	{
		$parts = explode( "\t", $diff );
		$fullid = $parts[0];
		$idparts = explode( '-', $fullid );
		$voterids[ $i ][ $key ] = $idparts[0];
	}
	$i++;
}

$changedtwiceids = array();
$changedtwiceidsandkeys = array();
// find the voter ids that appear in more than one of the "changed" files
// compare each file with all later ones
$i = 1;
while( $i <= 5 )
{
	$j = 1;
	while( $j <= 5 )
	{
		if( $j != $i )
		{
			$index = 1;
			$key1 = -99;
			$key2 = -99;
			while( $index <= 5 )
			{
				$k[ $index ] = -99;
				$index++;
			}
			echo 'COMPARING #'.$i.' and #'.$j."\n";
			if( $i == 1 )
			{
				foreach( $voterids[ $i ] as $key1=>$voterid )
				{
					//skip header row
					if( $key1 > 0 )
					{
						$key2 = array_search( $voterid, $voterids[ $j ] );
						if( $key2 > 0 )
						{
							$k[ $i ] = $key1;
							$k[ $j ] = $key2;
							echo $voterid." dupe found\n";
							$changedtwiceids[] = $voterid;
							$changedtwiceidsandkeys[] = array( $voterid, $k[1], $k[2], $k[3], $k[4], $k[5] );
						}
					}
				}
			}
			else
			{
				foreach( $voterids[ $i ] as $key1=>$voterid )
				{
					//skip header row
					if( $key1 > 0 )
					{
						$key2 = array_search( $voterid, $voterids[ $j ] );
						if( $key2 > 0 )
						{
							//if the voter id has already been found, add the key for the new array
							$changedtwicekey = array_search( $voterid, $changedtwiceids );
							if( $changedtwicekey )
							{
								echo $voterid." already added\n";
								$idandkeys = $changedtwiceidsandkeys[ $changedtwicekey ];
								$idandkeys[ $i ] = $key1;
								$idandkeys[ $j ] = $key2;
								$changedtwiceidsandkeys[ $changedtwicekey ] = $idandkeys;
							}
							else
							{
								$k[ $i ] = $key1;
								$k[ $j ] = $key2;
								echo $voterid." dupe found\n";
								$changedtwiceids[] = $voterid;
								$changedtwiceidsandkeys[] = array( $voterid, $k[1], $k[2], $k[3], $k[4], $k[5] );
							}
						}
					}
				}
			}
		}
		$j++;
	}
	$i++;
}


print_r( $changedtwiceidsandkeys );
echo "\n\n";
echo 'Changed Twice or More: '.count( $changedtwiceids )." - ".count( $changedtwiceidsandkeys )."\n";
//return;

//now walk through changedtwiceids, pulling records from each diff file whenever available

/*extract the "previous" and "new" fields from the diff files
FORMAT:
fwrite( $outputfile, "Previous ID	ID	Previous Name	Name	Previous DOB	DOB	Previous Gender	Gender	Previous Phone	Phone	Previous Address	Previous County	Address	County	Previous Party	Party	Previous Status	Status	Previous Changed	Changed	Previous Last Voted	Last Voted	Grep Code\n" );
*/


// walk through the arrays collecting relevant records
// start with 1 because record zero is headers
foreach( $changedtwiceidsandkeys as $changedtwice )
{
	$id = $changedtwice[0];
	$records = array();
	$fullid = array();
	$name = array();
	$dob = array();
	$gender = array();
	$phone = array();
	$address = array();
	$county = array();
	$party = array();
	$status = array();
	$changed = array();
	$lastvoted = array();
	$dateline = array();
	//walk through each of the $diff arrays, pulling records
	foreach( $diffs as $diffkey=>$diff )
	{
		$dateline[ $diffkey ] = '';
		if( $changedtwice[ $diffkey ] >= 0 ) $records[ $diffkey ] = $diff[ $changedtwice[ $diffkey] ];
		else $records[ $diffkey ] = false;
		echo 'diffkey = '.$diffkey."\n";
		if( $records[ $diffkey ] )
		{
			echo $records[ $diffkey ]."\n";
			$parts = explode( "\t", $records[ $diffkey ]  );
			
			// check to see if data was already written
			if( ! isset( $name[ $diffkey ] ) )
			{
				$fullid[ $diffkey ] = $parts[0];
				$name[ $diffkey ] = $parts[2];
				if( $parts[3] == '' ) $dob[ $diffkey ] = '';
				else $dob[ $diffkey ] = date( 'd M. Y', strtotime( trim( $parts[4] ) ) );
				echo 'dob '.$diffkey.': '.$dob[ $diffkey ]."\n";
				$gender[ $diffkey ] = $parts[6];
				$phone[ $diffkey ] = $parts[8];
				$address[ $diffkey ] = $parts[10];
				$county[ $diffkey ] = $parts[11];
				$party[ $diffkey ] = $parts[14];
				$status[ $diffkey ] = $parts[16];
				if( $parts[18] == '' ) $changed[ $diffkey ] = '';
				else $changed[ $diffkey ] = date( 'd M. Y', strtotime( trim( $parts[18] ) ) );
				if( $parts[20] == '' ) $lastvoted[ $diffkey ] = '';
				$lastvoted[ $diffkey ] = date( 'd M. Y', strtotime( trim( $parts[20] ) ) );
			}
			$fullid[ $diffkey + 1 ] = $parts[1];
			$name[ $diffkey + 1 ] = $parts[3];
			if( $parts[4] == '' ) $dob[ $diffkey + 1 ] = '';
			else $dob[ $diffkey + 1 ] = date( 'd M. Y', strtotime( trim( $parts[5] ) ) );
			echo 'later dob '.': '.$dob[ $diffkey + 1 ]."\n";
			$gender[ $diffkey + 1 ] = $parts[7];
			$phone[ $diffkey + 1 ] = $parts[9];
			$address[ $diffkey + 1 ] = $parts[12];
			$county[ $diffkey + 1 ] = $parts[13];
			$party[ $diffkey + 1 ] = $parts[15];
			$status[ $diffkey + 1 ] = $parts[17];
			if( $parts[19] == '' ) $changed[ $diffkey + 1 ] = '';
			else $changed[ $diffkey + 1 ] = date( 'd M. Y', strtotime( trim( $parts[19] ) ) );
			if( $parts[21] == '' ) $lastvoted[ $diffkey + 1 ] = '';
			$lastvoted[ $diffkey + 1 ] = date( 'd M. Y', strtotime( trim( $parts[21] ) ) );
		}
	}
	/*
	// fill in missing data with data from the first key that exists
	$i = 1;
	while( $i <= 6 )
	{
		if( ! isset( $name[ $i ] ) )
		{
			$found = false;
			$j = 1;
			while( $j <= 6 && ! $found )
			{
				if( $name[ $j ] )
				{
					$name[ $i ] = $name[ $j ];
					$dob[ $i ] = $dob[ $j ];
					$gender[ $i ] = $gender[ $j ];
					$phone[ $i ] = $phone[ $j ];
					$address[ $i ] = $address[ $j ];
					$county[ $i ] = $county[ $j ] ;
					$party[ $i ] = $party[ $j ];
					$status[ $i ] = $status[ $j ];
					$changed[ $i ] = $changed[ $j ];
					$lastvoted[ $i ] = $lastvoted[ $j ];
					$found = true;
				}
				$j++;
			}
		}
		$i++;
	}
	*/
	foreach( $name as $key=>$thisname )
	{
		$line = $fullid[ $key ]."	".$dates[ $key ]."	".$name[ $key ]."	".$dob[ $key ]."	".$gender[ $key ]."	".$phone[ $key ]."	".$address[ $key ]."	".$county[ $key ]."	".$party[ $key ]."	".$status[ $key ]."	".$changed[ $key ]."	".$lastvoted[ $key ]."\n";
		echo $line;
		$dateline[ $key ] = $line;
	}
	$break = "												\n";
	fwrite( $outputfile, $dateline[1].$dateline[2].$dateline[3].$dateline[4].$dateline[5].$dateline[6].$break );
}

?>