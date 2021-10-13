<?php

class RequestSet {

public $reqs;
public $client_set;
public $svc_set;

public $min_tstamp = 0;
public $max_tstamp = 0;
public $host;

#------------

public function __construct($host)
{
  $this->reqs = array();
  $this->host = $host;
  $this->client_set = new ClientSet();
  $this->svc_set = new SvcSet();
}

#---

public function insert_from_log_line($line)
{
  try {
    $req = new Request($line, $this->host);
  } catch (Exception $e) {
    return;
  }

  $this->client_set->add_req($req);
  $this->svc_set->add_req($req);

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