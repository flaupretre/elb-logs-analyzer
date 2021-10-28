<?php

class Request {

public $set;
public $date;
public $time;
public $tstamp;
public $src;
public $duration;
public $code;
public $rcvd;
public $sent;
public $method;
public $svc;
public $cmd;
public $args;
public $browser;
public $platform;
  
#-----------------------

public static function csv_header()
{
  return "date;time;tstamp;src;dur;code;rcvd;sent;method;svc;cmd;args;browser;platform;";
}  
  
#---

public function csv_line()
{
  return $this->date.';'.
    $this->time.';'.
    $this->tstamp.';'.
    $this->client.';'.
    $this->duration.';'.
    $this->code.';'.
    (($this->rcvd == 0) ? '' : $this->rcvd).';'.
    (($this->sent == 0) ? '' : $this->sent).';'.
    $this->method.';'.
    $this->svc.';'.
    $this->cmd.';'.
    $this->args.';'.
    $this->browser.';'.
    $this->platform.';';
}  
  
#---

public function __construct($set, $line, $host)  
{
  $this->set = $set;

  preg_match_all('/"(?:\\\\.|[^\\\\"])*"|\S+/', $line, $matches); 
  $a=$matches[0];
  //var_dump($a);

  $this->date=preg_replace('/T.*$/', '', $a[0]);
  $this->time=preg_replace('/^.*T(.*)\..*$/', '${1}', $a[0]);

  $d=new \DateTime($a[0]);
  $this->tstamp=$d->getTimestamp();

  $this->client=preg_replace('/:.*$/', '', $a[2]);

  $this->duration = intval($a[5] * 1000.);

  $this->code = $a[7];
  if (substr($this->code,0,1) != '2') {
    throw new Exception("Wrong return code: ".$this->code." (should be 2xx)");
  }

  $this->rcvd=round($a[9]/1000);
  $this->sent=round($a[10]/1000);

  $ra=explode(' ', trim($a[11], '"'));
  $this->method=$ra[0];
  if ($this->method == 'OPTIONS') {
    throw new Exception("Filtering out OPTIONS calls");
  }
  
  $url=$ra[1];
  $h=preg_replace('/^.*:\/\/([^\:\/]+)[\:\/].*$/', '${1}', $url);
  if (($host != '')&&($h != $host)) {
    throw new Exception("Wrong host: $h (should be $host)");
  }

  $p=preg_replace('/^.*:\/\/([^\/]+)\//', '', $url);
  $pa=explode('?', $p);
  $path=$pa[0];
  $this->args=((strpos($p, '?') != false) ? $pa[1] : '');

  $this->svc=preg_replace('/\/.*$/', '', $path);
  $this->cmd=((strpos($p, '/') != false) ? preg_replace('/^[^\/]+\//', '', $path) : '');
  $this->cmd=preg_replace('/[\/0-9]+$/', '/xxx', $this->cmd);
  $this->cmd=preg_replace('/\/[0-9]+\//', '/xxx/', $this->cmd);

  $b=new Browser($a[12]);
  $this->browser=$b->getBrowser();
  $this->platform=$b->getPlatform();
}

//---
}