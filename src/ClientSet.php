<?php

class ClientSet {

public $clients; // Array: IP -> Client

#-------------------

public function __construct()
{
  $this->clients=array();
}

#---

public function add_req($req)
{
  if (!array_key_exists($req->client, $this->clients)) {
    $this->clients[$req->client] = new Client($req->client);
  }
  $this->clients[$req->client]->add_request($req);
}

#---

public function csv()
{
  $ret = Client::csv_header()."\n";
  ksort($this->clients);
  foreach($this->clients as $client) {
    $ret .= $client->csv_line()."\n";
  }
return $ret;
}

//---
}
