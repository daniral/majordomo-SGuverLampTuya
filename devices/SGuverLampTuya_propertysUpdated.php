<?php

if ($this->getProperty('colorBrightness') == '') $this->setProperty('colorBrightness', '50');
if ($this->getProperty('color') == '') $this->setProperty('color', '#ffff00');
if ($this->getProperty('brightness') == '') $this->setProperty('brightness', '50');
if ($this->getProperty('cct') == '') $this->setProperty('cct', '10');

$value = $params['NEW_VALUE'];

$transform = array(
		'red' => '#ff0000',
		'green' => '#00ff00',
		'blue' => '#0000ff',
		'yellow' => '#ffff00',
		'aqua'  => '#00ffff',
		'magenta' => '#ff00ff',
		'white' => '#ffffff'
		);
if (isset($transform[$value])) $value = $transform[$value];

$value = normalizeRange($value);
$colorSaved = $this->getProperty('colorSaved') ?? '#ffff00';
$colorBrightness = normalizeRange($this->getProperty('colorBrightness'),1);
$source = strtok($params['SOURCE'], " ") ?? null;
$property = $params['PROPERTY'] ?? null;
$status = $this->getProperty('status') ?? 0;

if(!is_null($value)) $this->setProperty($property , $value, 'worksUpdated');

if(in_array($property, ['color', 'colorBrightness']) && !is_null($value)){
	
	if($property == 'colorBrightness')  $value = $colorSaved;

	if($source != 'worksUpdated'){
		$this->setProperty('work_mode', 'colour');
		$hsvHex = rgbToHSVhex($value, $colorBrightness)?: '003c03e801f4';
		$this->setProperty('colorWork', $hsvHex, "propertysUpdated");
		if (!$status) $this->setProperty('status', 1);
		$this->setProperty('colorSaved', $value);
	}
}elseif(in_array($property, ['brightness', 'cct']) && is_numeric($value)){
	if($source != 'worksUpdated'){
		$this->setProperty('work_mode', 'white');
		$this->setProperty($property . 'Work', round($value * 10), 'propertysUpdated');
		if (!$status) $this->setProperty('status', 1);
		$this->setProperty($property . 'Saved', $value);
	}
}