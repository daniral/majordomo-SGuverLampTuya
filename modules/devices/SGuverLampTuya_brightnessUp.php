<?php

/*
Увеличить яркость.(array('value'=>1--100)). Без  параметров +10.
*/
$brightness = $this->getProperty('brightness');
$inc = isset($params['value']) && is_numeric($params['value']) ? max(1, min(100, $params['value'])):10;
$brightness = min(100, $brightness + $inc);
$this->setProperty('brightness', $brightness);