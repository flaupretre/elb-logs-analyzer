<?php

class APICmd {

public $reqs;
public $cmd;

#------------

public function __construct($svc, $cmd)
{
  $this->cmd = $cmd;
  $this->reqs=array();
}

#---

public function add_request($req)
{
  $this->reqs[] = $req;
}

#---

public static function csv_header()
{
  return "cmd;count;countpc;dmean;dmin;dmax;dpct25;dpct50;dpct75;dpct95;";
}  
  
#---
# $a must be sorted

public function pct($a, $pc)
{
  if (count($a) == 0) return 0;
  if (count($a) == 1) return $a[0];
  
  $i = round(count($a) * $pc / 100.)-1;
  if ($i >= count($a)) $i = count($a) -1;
  if ($i < 0) $i = 0;
  $res = $a[$i];
  return $res;
}

#---

public function csv_line()
{
  $empty = (count($this->reqs) == 0);

  if (! $empty) {
    $d=array();
    foreach($this->reqs as $req) $d[]=$req->duration;
    sort($d);
    $c = count($d);
    $cpc = round(($c * 100)/$this->reqs[0]->set->size()).'%';
    $dmean = round(array_sum($d) / $c);
    $dmin = min($d);
    $dmax = max($d);
    $dpct25 = self::pct($d, 25);
    $dpct50 = self::pct($d, 50);
    $dpct75 = self::pct($d, 75);
    $dpct95 = self::pct($d, 95);
  } else {
    $c = $dmean = $dmin = $dmax = $dpct25 = $dpct50 = $dpct75 = $dpct95 = 0;
  }
    
  return $this->cmd.';'.
    $c.';'.
    $cpc.';'.
    $dmean.';'.
    $dmin.';'.
    $dmax.';'.
    $dpct25.';'.
    $dpct50.';'.
    $dpct75.';'.
    $dpct95.';'
    ;
}  

//----
}
