<?php

class Svc {

public $reqs;
public $cmds;
public $svc;

#------------

public function __construct($svc)
{
  $this->svc = $svc;
  $this->reqs=array();
  $this->cmds=array();
}

#---

public function add_request($req)
{
  if (!array_key_exists($req->cmd, $this->cmds)) {
    $this->cmds[$req->cmd] = new APICmd($this, $req->cmd);
  }

  $this->cmds[$req->cmd]->add_request($req);

  $this->reqs[] = $req;
}

#---

public static function csv_header()
{
  return "svc;".APICmd::csv_header();
}  
  
#---

public function csv_lines()
{
  $ret = '';
  ksort($this->cmds);
  foreach($this->cmds as $cmd) {
    $ret .= $this->svc.';'.$cmd->csv_line()."\n";
  }
  return $ret;
}

//----
}
