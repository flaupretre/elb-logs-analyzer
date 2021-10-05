<?php

class MovingAverage {

private $size=0;
private $a;

#---------

public function __construct($size)
{
  $this->a = array();
  $this->size = $size;
}

#---

public function value()
{
  if (count($this->a) == 0) return 0;

  return round(array_sum($this->a)/count($this->a));
}

#---

public function add($val)
{
$this->a[] = $val;
if (count($this->a) > $this->size) array_shift($this->a);
}

//----
}
