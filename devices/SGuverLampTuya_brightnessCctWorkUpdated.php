<?php
$levelNew = $params['NEW_VALUE'];
$property = $params['PROPERTY'];

if(!is_numeric($levelNew)) return;

$levelNew = max(0, min(1000, $levelNew));
$levelNew=round($levelNew / 10);

if($property == 'brightnessWork'){
	$this->setProperty('brightness', $levelNew, 'brightnessCctWorkUpdated');
}elseif($property == 'cctWork'){
	$this->setProperty('cct', $levelNew, 'brightnessCctWorkUpdated');
}