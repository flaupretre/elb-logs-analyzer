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

  foreach($reqset->reqs as $req) {
    $mn = $this->to_mn($req->tstamp);
    $this->itv[$mn]->inc_req_count();
    $this->itv[$mn]->set_client($req->client);
    # Extend client activity to previous and next interval
    if ($mn > $this->min_mn) $this->itv[$mn-1]->set_client($req->client);
    if ($mn < $this->max_mn) $this->itv[$mn+1]->set_client($req->client);
  }
}

#---
// Get CSV for each interval and add moving average

public function csv()
{
  $reqs_ma=new MovingAverage(self::REQUESTS_MA_SIZE);
  $rate_ma=new MovingAverage(self::RATE_MA_SIZE);

  $ret = Interval::csv_header()."RequestsMA;RateMA;\n";

  foreach($this->itv as $mn => $i) {
    $line=$i->csv_line();

    $reqs_ma->add($i->req_count);
    $line .= $reqs_ma->value().";";

    $rate_ma->add($i->rate());
    $line .= $rate_ma->value().";";

    $ret .= $line."\n";
  }
return $ret;
}

//---
}
?>
