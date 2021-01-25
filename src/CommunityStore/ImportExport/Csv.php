<?php
namespace Concrete\Package\CommunityStoreImportExport\Src\CommunityStore\ImportExport;

use Concrete\Core\Controller\Controller;

defined('C5_EXECUTE') or die("Access Denied.");

class Csv extends Controller {

  public static function getFileLines($file, $lines = null) {
    foreach (file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
      foreach (explode(";", iconv(mb_detect_encoding($line, mb_detect_order(), true), "UTF-8", $line)) as $key => $value) {
        $productLine[$key] = self::clean($value);
      }
      $productLines[] = $productLine;

      // Break if line count is set and we reached the number
      if ($lines && ++$count == $lines){ break; }
    }
    return $productLines;
  }


  public static function clean($value) {

    // Strip "" if present around the value
    if (substr($value, 0, 1) == '"' && substr($value, -1) == '"') { $value = substr($value, 1, -1); }

    return $value;
  }

}
