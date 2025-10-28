<?php

/*
Уменьшить яркость на (array("value"=>1--100)). Без  параметров на 10.
*/
$colorBrightness = $this->getProperty('colorBrightness');
$inc = isset($params['value']) && is_numeric($params['value']) ? max(1, min(100, $params['value'])):10;
$colorBrightness = max(0, $colorBrightness - $inc);
$this->setProperty('colorBrightness', $colorBrightness);