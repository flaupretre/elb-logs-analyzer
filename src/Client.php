<?php

class Client {

public $reqs;
public $ip;
public $min_tstamp;
public $max_tstamp;

#------------

public function __construct($ip)
{
  $this->ip = $ip;
  $this->reqs=array();
}

#---

public function add_request($req)
{
  if (count($this->reqs)) {
    $this->min_tstamp = min($this->min_tstamp, $req->tstamp);
    $this->max_tstamp = max($this->max_tstamp, $req->tstamp);
  } else {
    $this->max_tstamp = $this->min_tstamp = $req->tstamp;
  }
  $this->reqs[] = $req;
}

#---

public static function csv_header()
{
  return "ip;reqs;min_tstamp;max_tstamp;dur;rate;";
}  
  
#---

public function csv_line()
{
  return $this->ip.';'.
    count($this->reqs).';'.
    $this->min_tstamp.';'.
    $this->max_tstamp.';'.
    $this->duration().';'.
    $this->rate().';';
}  

#---

public function duration()
{
  return $this->max_tstamp - $this->min_tstamp + 1;
}

#---

public function rate()
{
  $mn = round($this->duration() / 60);
  if ($mn == 0) $mn = 1;
  return round(count($this->reqs)/$mn);
}

//----
}
