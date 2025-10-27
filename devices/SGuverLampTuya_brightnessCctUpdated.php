<?php
$levelNew = $params['NEW_VALUE'];
$property = $params['PROPERTY'];

if(!is_numeric($levelNew)) return;

$levelNew = max(0, min(100, $levelNew));

if($property == 'brightness'){
	$this->setProperty('brightnessSaved', $levelNew);
	$levelNew=round($levelNew * 10);
	$this->setProperty('brightnessWork', $levelNew, 'brightnessCctUpdated');
}elseif($property == 'cct'){
	$this->setProperty('cctSaved', $levelNew);
	$levelNew=round($levelNew * 10);
	$this->setProperty('cctWork', $levelNew, 'brightnessCctUpdated');
}



