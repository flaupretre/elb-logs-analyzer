<?php

class Interval {

public $mn;
public $clients=0;
public $reqs=0;

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
}

#---

public function inc_clients()
{
$this->clients++;
}

#---

public function inc_reqs()
{
$this->reqs++;
}

#---

public static function csv_header()
{
  return "Etstamp;Time;Clients;Requests;Rate;";
}  
  
#---

public function rate()
{
  return (($this->reqs==0) ? 0 : round($this->reqs/$this->clients));
}

#---

public function csv_line()
{
  $ret = self::mn_to_estamp($this->mn)
     .";".self::mn_to_dstring($this->mn)
      .";".$this->clients
      .";".$this->reqs
      .";".$this->rate()
      .";";
  return $ret;
}
//---
}

?>
