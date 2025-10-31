<?php

if (!function_exists('rgbToHSVhex')) {
	function rgbToHSVhex($rgbHex, $brightness = 100) {
		// Убираем возможный символ "#"
		$rgbHex = ltrim($rgbHex, '#');
		// Разбираем HEX на компоненты RGB (0–255)
		$r = hexdec(substr($rgbHex, 0, 2)) / 255;
		$g = hexdec(substr($rgbHex, 2, 2)) / 255;
		$b = hexdec(substr($rgbHex, 4, 2)) / 255;

		$max = max($r, $g, $b);
		$min = min($r, $g, $b);
		$d = $max - $min;

		$h = 0;
		if ($d != 0) {
			if ($max == $r) {
				$h = fmod((($g - $b) / $d), 6);
			} elseif ($max == $g) {
				$h = (($b - $r) / $d) + 2;
			} else {
				$h = (($r - $g) / $d) + 4;
			}
			$h *= 60;
			if ($h < 0) {
				$h += 360;
			}
		}

		$s = $max == 0 ? 0 : $d / $max;
		//$v = $max * ($brightness / 100);

		$hue = round($h);
		$sat = round($s * 1000);
		//$val = round($v * 1000);
		$val = $brightness * 10;
		// Формируем строку в формате Tuya (по 4 hex-цифры на каждый параметр)
		$hsv = str_pad(dechex($hue), 4, '0', STR_PAD_LEFT) .
				 str_pad(dechex($sat), 4, '0', STR_PAD_LEFT) .
				 str_pad(dechex($val), 4, '0', STR_PAD_LEFT);
				 
		return strtolower($hsv);
	}
}

if ($this->getProperty('colorBrightness') == '') $this->setProperty('colorBrightness', '100');
if ($this->getProperty('color') == '') $this->setProperty('color', '#0000ff');

$colorRGB = strtolower(trim($params['NEW_VALUE']));
$colorSaved = $this->getProperty('colorSaved');
$colorBrightness = max(1, min(100, $this->getProperty('colorBrightness')));
$source = strtok($params['SOURCE'], " ");
$property = $params['PROPERTY'];
$status = $this->getProperty('status');

$transform = array(
	'red' => '#ff0000',
	'green' => '#00ff00',
	'blue' => '#0000ff',
	'yellow' => '#ffff00',
	'aqua'  => '#00ffff',
	'magenta' => '#ff00ff',
	'white' => '#ffffff'
	);
if (isset($transform[$colorRGB])) {
	$colorRGB = $transform[$colorRGB];
	$this->setProperty('color', $colorRGB, 'colorWorkUpdated');
}

if($property == 'colorBrightness'){
	$colorRGB = $colorSaved;
}

if($source != 'colorWorkUpdated'){
	$this->setProperty('work_mode', 'colour');
	$hsvHex = rgbToHSVhex($colorRGB, $colorBrightness);
	$this->setProperty('colorWork', $hsvHex, "colorUpdated");
	if (!$status) $this->setProperty('status', 1);
}

$this->setProperty('colorSaved', $colorRGB);