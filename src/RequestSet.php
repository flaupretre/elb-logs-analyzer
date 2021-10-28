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

public function size()
{
  return count($this->reqs);
}

#---

public function insert_from_log_line($line)
{
  if ((count($this->reqs) % 50000) == 0) echo 'Requests: '.count($this->reqs)."...\n";
  try {
    $req = new Request($this, $line, $this->host);
  } catch (Exception $e) {
    return;
  }

  $this->client_set->add_req($req);
  $this->svc_set->add_req($req);

  if ($this->size()) {
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

public function csv(&$restart, $split=0)
{
  $start = $num = $restart;
  $ret = "";
  if ($start == 0) $ret .= Request::csv_header()."\n";
  
  while($num < $this->size()) {
    if ($split && (($num - $start) > $split)) {
      $restart = $num;
      echo "Requests: returning partial results ($start - ".($num - 1).")\n";
      return $ret;
    }
    $ret .= $this->reqs[$num++]->csv_line()."\n";
  }
$restart = 0;
return $ret;
}

#---

public function duration()
{
  return $this->max_tstamp - $this->min_tstamp;
}

//---
}
