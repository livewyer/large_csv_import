<?php

require_once './Database.class.php';

$csv_file = 'FOIA_3_5_2013.csv';
$test_write_file = 'sample_output.csv';
$write_to_file = false;

$handle = fopen($csv_file, 'r') or die('Cannot open: '.$csv_file); // Open the enormous csv file.
$op_handle = fopen($test_write_file, 'w') or die('Cannot open: '.$test_write_file); // Create a file for writing output.

$i = 0;
$i_max = 5; // max number of lines to read

// Loop through specified number of lines and write them to new output file.
while(!feof($handle) && $i++ <= $i_max) {
  $line = fgets($handle);
  if($write_to_file) {
    fwrite($op_handle, $line);
  } else {
    $data = array();
    $data = explode(',',$line);
//     fwrite($op_handle, $i . ' = ' . count($data) . ', ');
    if($i > 3) {
      $empty_vals = Database::create_vals(10);
      Database::run_query(
	"INSERT INTO csv_import (permit, facility_name, sample_point_id, analyte, result_numeric, units, sample_dat, coord_type_code, x, y) VALUES ($empty_vals)",
	$data
      );
    }
  }
}

// Close the files
fclose($handle);
fclose($op_handle);