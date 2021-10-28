<?php

class MovingAverage {

private $size=0;
private $a;
private $lastx;

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

public function append($x, $val)
{
  if (count($this->a) == 0) $this->lastx = ($x - 1);

  # Insert null values for gaps

  while(($x - $this->lastx) > 1) {
    $this->add(0);
    $this->lastx++;
  }

  $this->add($val);
  $this->lastx = $x;
  return $this->value();
}

#---

public function add($val)
{
$this->a[] = $val;
if (count($this->a) > $this->size) array_shift($this->a);
}

//----
}
