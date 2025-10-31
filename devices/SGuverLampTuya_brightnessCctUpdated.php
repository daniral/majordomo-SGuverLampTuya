<?php
if ($this->getProperty('brightness') == '') $this->setProperty('brightness', '50');
if ($this->getProperty('cct') == '') $this->setProperty('cct', '10');

$property = $params['PROPERTY'] ?? '';
$value = $params['NEW_VALUE'] ?? 0;
$status = $this->getProperty('status');

// Приводим уровень к диапазону
$levelNew = max(1, min(100, $value));

if (!in_array($property, ['brightness', 'cct']) || !is_numeric($value)) return;

if($source != 'brightnessCctWorkUpdated'){
	$this->setProperty('work_mode', 'white');
	$this->setProperty($property . 'Work', round($levelNew * 10), 'brightnessCctUpdated');
	if (!$status) $this->setProperty('status', 1);
}
$this->setProperty($property . 'Saved', $levelNew);