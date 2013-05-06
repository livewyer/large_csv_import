<?php

$csv_file = 'enormous.csv';
$test_write_file = 'sample_output.csv';

$handle = fopen($csv_file, 'r') or die('Cannot open: '.$csv_file); // Open the enormous csv file.
$op_handle = fopen($test_write_file, 'w') or die('Cannot open: '.$test_write_file); // Create a file for writing output.

$i = 0;
$i_max = 5; // max number of lines to read

// Loop through specified number of lines and write them to new output file.
while(!feof($handle) && $i++ <= $i_max) {
  $line = fgets($handle);
  fwrite($op_handle, $line);
}

// Close the files
fclose($handle);
fclose($op_handle);