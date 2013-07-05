<?php
/**
 * @file
 * Field Counter
 *
 * This module iterates over a tab text file and counts the number
 * of unique instances of each column (field) value.
 */

global $debug;
$debug = FALSE;
$filepath = $argv[1];
if (isset($argv[2]) && $argv[2] == '--debug') {
  $debug = TRUE;
}
process_rows($filepath);

/**
 * Process a row of tab-text webform submission and add district lookups.
 *
 * @param String $filepath
 *   The path to a tab-text file exported from the Webform module.
 *
 * @return String
 *   A series of tab-text rows with Senate district information added.
 */
function process_rows($filepath) {
  global $debug;
  $endloop = FALSE;
  if ($debug) {
    $i = 0;
  }
  $handle = @fopen($filepath, "r");
  if ($handle) {
    $timestamps = $ips = $urls = array();
    $min = 22861569;
    $max = 22864708;
    while ($min <= $max) {
      $timestamps[$min] = 0;
      $min++;
    }
    while (!$endloop && (($buffer = fgets($handle, 65536)) !== false)) {
      // If debug is true, stop looping after the first 10 items
      if ($debug) {
        $i++;
        if ($i > 99) {
          $endloop = TRUE;
        }
      }
      $buffer = trim($buffer);
      list(
        $timestamp,
        $ip,
        $url
      ) = explode("\t", $buffer);
      $timestamps[$timestamp]++;
      $ips[$ip]++;
      $urls[$url]++;
    }
    if (!feof($handle) && !$debug) {
      echo "Error: unexpected fgets() fail\n";
    }
    fclose($handle);
  }
  foreach ($timestamps as $timestamp => $count) {
    echo "$timestamp\t$count\n";
  }
/*
  echo "\n";
  foreach ($ips as $ip => $count) {
    echo "$ip\t$count\n";
  }
  echo "\n";
  foreach ($urls as $url => $count) {
    echo "$url\t$count\n";
  }
// */
}
?>
