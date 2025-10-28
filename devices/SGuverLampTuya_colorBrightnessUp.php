<?php

/*
Увеличить яркость.(array('value'=>1--100)). Без  параметров +10.
*/
$colorBrightness = $this->getProperty('colorBrightness');
$inc = isset($params['value']) && is_numeric($params['value']) ? max(1, min(100, $params['value'])):10;
$colorBrightness = min(100, $colorBrightness + $inc);
$this->setProperty('colorBrightness', $colorBrightness);