<?php

class Timeline {

private $itv; // Array of 'Interval' elements

const REQUESTS_MA_SIZE = 5; // Moving average subset size
const RATE_MA_SIZE = 10; // Moving average subset size
const CLIENT_ACTIVE_MINUTES = 3;

#----------------------

#---

private static function to_mn($tstamp)
{
  return intval($tstamp / 60.);
}

#---

public function __construct($reqset)
{
  $this->itv = array();

  foreach($reqset->reqs as $req) {
    $mn = $this->to_mn($req->tstamp);
    if (!array_key_exists($mn, $this->itv)) $this->itv[$mn] = new Interval($mn);
    $this->itv[$mn]->inc_req_count();
    $this->itv[$mn]->add_client($req->client);
    # Extend client activity to CLIENT_ACTIVE_MINUTES
    for ($offset=1;$offset <= self::CLIENT_ACTIVE_MINUTES;$offset++) {
      $mno = $mn + $offset;
      if (!array_key_exists($mno, $this->itv)) $this->itv[$mno] = new Interval($mno);
      $this->itv[$mno]->add_client($req->client);
    }
  }
  ksort($this->itv);
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

    $line .= $reqs_ma->append($mn, $i->req_count).";";
    $line .= $rate_ma->append($mn, $i->rate()).";";

    $ret .= $line."\n";
  }
return $ret;
}

//---
}
?>
