<?php

class Interval {

public $mn;
public $clients;
public $req_count=0;

#----------------------

# Convert to Excel timestamp (Unit = day)

private static function mn_to_estamp($mn)
{
  return str_replace('.', ',', strval(($mn+60.)/1440.+25569.));
}

#---

private static function mn_to_dstring($mn)
{
  return gmdate('d-M-Y_H-i',60*$mn);
}

#---

public function __construct($mn)
{
  $this->mn = $mn;
  $this->clients = array();
}

#---

public function add_client($client)
{
$this->clients[$client]=true;
}

#---

public function inc_req_count()
{
$this->req_count++;
}

#---

public static function csv_header()
{
  return "Etstamp;Time;Clients;Requests;Rate;";
}  
  
#---

public function rate()
{
  return (($this->req_count==0) ? 0 : round($this->req_count/count($this->clients)));
}

#---

public function csv_line()
{
  $ret = self::mn_to_estamp($this->mn)
     .";".self::mn_to_dstring($this->mn)
      .";".count($this->clients)
      .";".$this->req_count
      .";".$this->rate()
      .";";
  return $ret;
}
//---
}

?>
