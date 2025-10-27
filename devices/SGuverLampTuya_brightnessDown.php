<?php

/*
Уменьшить яркость на (array("value"=>1--100)). Без  параметров на 10.
*/
$brightness = $this->getProperty('brightness');
$inc = isset($params['value']) && is_numeric($params['value']) ? max(1, min(100, $params['value'])):10;
$brightness = max(0, $brightness - $inc);
$this->setProperty('brightness', $brightness);