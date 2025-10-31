<?php

if (!function_exists('hsvToRgbHex')) {
	function hsvToRgbHex($hsvHex) {	
		if (!is_string($hsvHex) || strlen($hsvHex) < 12) {
			return ['rgbHex' => '#000000', 'brightness' => 0];
		}

		$hueHex = substr($hsvHex, 0, 4);
		$satHex = substr($hsvHex, 4, 4);
		$valHex = substr($hsvHex, 8, 4);

		$hue = hexdec($hueHex);
		$sat = hexdec($satHex) / 1000;
		$val = hexdec($valHex) / 1000;

		$hue = max(0, min(360, $hue));
		$sat = max(0, min(1, $sat));
		$val = max(0, min(1, $val));

		// --- Вычисляем цвет только по Hue и Saturation (без Value!)
		$h = $hue / 60.0;
		$c = 1.0 * $sat; // всегда полная насыщенность
		$x = $c * (1 - abs(fmod($h, 2) - 1));
		$m = 1.0 - $c;

		if ($h >= 0 && $h < 1)      { $r = $c; $g = $x; $b = 0; }
		elseif ($h < 2)             { $r = $x; $g = $c; $b = 0; }
		elseif ($h < 3)             { $r = 0; $g = $c; $b = $x; }
		elseif ($h < 4)             { $r = 0; $g = $x; $b = $c; }
		elseif ($h < 5)             { $r = $x; $g = 0; $b = $c; }
		else                        { $r = $c; $g = 0; $b = $x; }

		// --- RGB в диапазоне 0–255, без применения Value
		$r = round(($r + $m) * 255);
		$g = round(($g + $m) * 255);
		$b = round(($b + $m) * 255);

		$rgbHex = sprintf("#%02x%02x%02x", $r, $g, $b);
		$brightness = round($val * 100);

		return [
			'rgbHex' => $rgbHex,     // Цвет с сохранённой насыщенностью
			'brightness' => $brightness // Яркость отдельно
		];
	}
}

$colorHSV = strtolower(trim($params['NEW_VALUE']));
$colorBrightness = $this->getProperty('colorBrightness');
$source = strtok($params['SOURCE'], " ");

$data = hsvToRgbHex($colorHSV);
$colorBrightness = $data['brightness'];
$colorRGB = $data['rgbHex'];

if($source != 'colorUpdated'){
	$this->setProperty('color', $colorRGB, 'colorWorkUpdated');
	$this->setProperty('colorBrightness', $colorBrightness, 'colorWorkUpdated');
}