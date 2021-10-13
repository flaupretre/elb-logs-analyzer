<?php

class SvcSet {

public $svcs; // Array

#-------------------

public function __construct()
{
  $this->svcs=array();
}

#---

public function add_req($req)
{
  if (!array_key_exists($req->svc, $this->svcs)) {
    $this->svcs[$req->svc] = new Svc($req->svc);
  }
  $this->svcs[$req->svc]->add_request($req);
}

#---

public function csv()
{
  $ret = Svc::csv_header()."\n";
  ksort($this->svcs);
  foreach($this->svcs as $svc) {
    $ret .= $svc->csv_lines();
  }
return $ret;
}

//---
}
