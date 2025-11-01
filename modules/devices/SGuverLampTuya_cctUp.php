<?php

/*
Увеличить температуру.(array("value"=>1--100)). Без  параметров +10.
*/
$cctLevel = $this->getProperty('cctLevel');
$inc = isset($params['value']) && is_numeric($params['value']) ? max(1, min(100, $params['value'])):10;
$cctLevel = min(100, $cctLevel + $inc);
$this->setProperty('cct', $cctLevel);