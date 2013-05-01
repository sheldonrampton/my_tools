<?php
$startpath = $argv[1];
$purpose = isset($argv[2]) ? $argv[2] : 'html';
if ($startpath) {
  $ritit = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($startpath), RecursiveIteratorIterator::CHILD_FIRST); 
  $r = array();
  foreach ($ritit as $splFileInfo) {
     $path = $splFileInfo->isDir()
           ? array($splFileInfo->getFilename() => array())
           : array($splFileInfo->getFilename());

     for ($depth = $ritit->getDepth() - 1; $depth >= 0; $depth--) {
         $path = array($ritit->getSubIterator($depth)->current()->getFilename() => $path);
     }
     $r = array_merge_recursive($r, $path);
  }
//  print_r($r);
  global $counts;
  $counts = array();
  switch ($purpose) {
    case 'csv':
      traverse_folder_csv($r);
      break;
    case 'html':
    default:
      traverse_folder_html($r);
      echo "\n\n";
      print_r($counts);
      break;
  }
}

function traverse_folder_csv($element, $path="http:/", $element_key=NULL, $level=0) {
  global $counts;
  $padding = str_repeat(' ', $level);
  if (is_array($element)) {
    $counts['folder'] += 1;
    foreach ($element as $key => $item) {
      traverse_folder_csv($item, "$path/$element_key", $key, $level+2, $counts);
    }
  }
  else {
    $pieces = explode('.', $element);
    $suffix = array_pop($pieces);
    $counts[$suffix] += 1;
    if ($suffix != 'css' && $suffix != 'js') {
      echo "$suffix\t$path/$element\n";
    }
  }
}

function traverse_folder_html($element, $path="http:/", $element_key=NULL, $level=0) {
  global $counts;
  $padding = str_repeat(' ', $level);
  if (is_array($element)) {
    $counts['folder'] += 1;
    echo "$padding<li class=\"folder\"><a href=\"$path/$element_key\">$element_key</a>\n";
    echo "$padding<ul>\n";
    foreach ($element as $key => $item) {
      traverse_folder_html($item, "$path/$element_key", $key, $level+2, $counts);
    }
    echo "$padding</ul>\n";
    echo "$padding</li>\n";
  }
  else {
    $pieces = explode('.', $element);
    $suffix = array_pop($pieces);
    $counts[$suffix] += 1;
    echo "$padding<li class=\"$suffix\"><a href=\"$path/$element\">$element</a></li>\n";
  }
}
?>