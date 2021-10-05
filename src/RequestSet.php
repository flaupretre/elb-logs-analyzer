<?php

class RequestSet {

public $reqs;
public $min_tstamp = 0;
public $max_tstamp = 0;
public $host;

#------------

public function __construct($host)
{
  $this->reqs = array();
  $this->host = $host;
}

#---

public function insert_from_log_line($line)
{
  try {
    $req = new Request($line, $this->host);
  } catch (Exception $e) {
    return;
  }
  if (count($this->reqs)) {
    $this->min_tstamp = min($this->min_tstamp, $req->tstamp);
    $this->max_tstamp = max($this->max_tstamp, $req->tstamp);
  } else {
    $this->max_tstamp = $this->min_tstamp = $req->tstamp;
  }
  $this->reqs[] = $req;
}

#---

public function insert_from_log_stdin()
{
  while (($line=fgets(STDIN))!==false) {
    $this->insert_from_log_line($line);
  }
}

#---

public function csv()
{
  $ret = Request::csv_header()."\n";
  foreach($this->reqs as $req) {
    $ret .= $req->csv_line()."\n";
  }
return $ret;
}

#---

public function duration()
{
  return $this->max_tstamp - $this->min_tstamp;
}

//---
}