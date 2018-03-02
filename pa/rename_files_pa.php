<?php
/*
Replaces spaces with underbars in all the PA *FVE* data files. A path to the raw file directory is an optional input.

Syntax:
php rename_files_pa.php 



if( isset( $argv[1] ) )
{
	$datapath = $argv[1];
	if( strpos( '/', $datapath ) === false ) $datapath .= '/';
}
else $datapath = '';

NOTE: 
*/
$datapath = '';

$fvefiles = shell_exec( 'ls -1 '.$datapath.'*FVE*' );
$fvearray = explode( "\n", $fvefiles );

foreach( $fvearray as $fvename )
{
	if( $fvename && strpos( $fvename, '_FVE_' ) === false )
	{
		echo 'renaming '.$fvename."\n";
		$newfvename = str_replace( ' ', '_', $fvename );
		$fvename = trim( str_replace( ' ', '\ ', $fvename ) );
		shell_exec( 'mv '.$datapath.$fvename.' '.$datapath.$newfvename );
	}
}