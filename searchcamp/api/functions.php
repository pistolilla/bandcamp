<?php

/* For catching "Notice" errors */
set_error_handler('exceptions_error_handler');

function exceptions_error_handler($severity, $message, $filename, $lineno) {
  if (error_reporting() == 0) {
    return;
  }
  if (error_reporting() & $severity) {
    throw new ErrorException($message, 0, $severity, $filename, $lineno);
  }
}

/* Use internal libxml errors -- turn on in production, off for debugging */
libxml_use_internal_errors(false);

/* Global classes and functions */
class Tag {
    public $Tag;
    public $c;
}
class Artist {
    public $Url;
    public $Artist;
    public $Album;
    public $Tags;
    public $c;
}

// escape quotes for SQL calls
$escapeQuotes = function($value) {
    return preg_replace("/'/", "''", $value);
};

/* SQLite global and init */
// Construction class
class MyDB extends SQLite3 {
  function __construct() {
     $this->open(dirname(__FILE__) . "/searchcamp.sqlite");
  }
}

?>