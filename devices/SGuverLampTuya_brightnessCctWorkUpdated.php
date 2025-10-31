<?php

$levelNew = $params['NEW_VALUE'];
$property = $params['PROPERTY'];
$source = strtok($params['SOURCE'], " ");

if(!is_numeric($levelNew)) return;

$levelNew = max(1, min(1000, $levelNew));
$levelNew=round($levelNew / 10);

if($source != 'brightnessCctUpdated'){
	if($property == 'brightnessWork'){
		$this->setProperty('brightness', $levelNew, 'brightnessCctWorkUpdated');
	}elseif($property == 'cctWork'){
		$this->setProperty('cct', $levelNew, 'brightnessCctWorkUpdated');
	}
}else return;