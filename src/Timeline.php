<?php

class Timeline {

private $itv; // Array of 'Interval' elements
private $min_mn;
private $max_mn;

const REQUESTS_MA_SIZE = 5; // Moving average subset size
const RATE_MA_SIZE = 10; // Moving average subset size

#----------------------

#---

private static function to_mn($tstamp)
{
  return intval($tstamp / 60.);
}

#---

public function __construct($reqset, $clientset)
{
  $this->min_mn = $this->to_mn($reqset->min_tstamp);
  $this->max_mn = $this->to_mn($reqset->max_tstamp);
  $this->itv = array();

  for ($mn=$this->min_mn; $mn <= $this->max_mn; $mn++) {
    $this->itv[$mn] = new Interval($mn);
  }

  foreach($clientset->clients as $client) {
    for($mn = self::to_mn($client->min_tstamp) ; $mn <= self::to_mn($client->max_tstamp); $mn++) {
      $this->itv[$mn]->inc_clients();
    }
  }

  foreach($reqset->reqs as $req) {
    $this->itv[$this->to_mn($req->tstamp)]->inc_reqs();
  }
}

#---
// Get CSV for each interval and add moving average

public function csv()
{
  $reqsma=new MovingAverage(self::REQUESTS_MA_SIZE);
  $ratema=new MovingAverage(self::RATE_MA_SIZE);

  $ret = Interval::csv_header()."RequestsMA;RateMA;\n";

  foreach($this->itv as $mn => $i) {
    $line=$i->csv_line();

    $reqsma->add($i->reqs);
    $line .= $reqsma->value().";";

    $ratema->add($i->rate());
    $line .= $ratema->value().";";

    $ret .= $line."\n";
  }
return $ret;
}

//---
}
?>
