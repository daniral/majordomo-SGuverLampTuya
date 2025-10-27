<?php
	
if (!function_exists('hsvToRgbHex')) {
	function hsvToRgbHex($hsvHex) {	
		$hueHex = substr($hsvHex, 0, 4);
		$satHex = substr($hsvHex, 4, 4);
		$valHex = substr($hsvHex, 8, 4);

		// Конвертируем HEX → десятичные числа
		$hue = hexdec($hueHex);        // Hue: 0–360 (градусы)
		$sat = hexdec($satHex) / 1000; // Saturation: 0.0–1.0
		$val = hexdec($valHex) / 1000; // Value (яркость): 0.0–1.0

		// HSV → RGB: классическая формула
		$h = $hue / 60.0;              // Делим на 60, чтобы определить "сектор" (0–6)
		$c = $val * $sat;              // "Chroma" — интенсивность цвета
		$x = $c * (1 - abs(fmod($h, 2) - 1)); // Вспомогательное значение для промежуточных цветов
		$m = $val - $c;                // Сдвиг для корректировки яркости

		// Определяем, в каком секторе находится Hue и рассчитываем RGB (в диапазоне 0–1)
		if ($h >= 0 && $h < 1)      { $r = $c; $g = $x; $b = 0; }
		elseif ($h < 2)             { $r = $x; $g = $c; $b = 0; }
		elseif ($h < 3)             { $r = 0; $g = $c; $b = $x; }
		elseif ($h < 4)             { $r = 0; $g = $x; $b = $c; }
		elseif ($h < 5)             { $r = $x; $g = 0; $b = $c; }
		else                        { $r = $c; $g = 0; $b = $x; }

		// Переводим RGB из 0–1 в 0–255 и учитываем яркость (m)
		$r = round(($r + $m) * 255);
		$g = round(($g + $m) * 255);
		$b = round(($b + $m) * 255);

		// Формируем HEX-строку в виде #RRGGBB
		$rgbHex = sprintf("#%02x%02x%02x", $r, $g, $b);
		// Вычисляем яркость в процентах (0–100)
		$brightness = round($val * 100);
		return [
			'rgbHex' => $rgbHex,
			'brightness' => $brightness
		];
	}
}
	
$colorHSV = strtolower(trim($params['NEW_VALUE']));
$colorHSVold = strtolower($params['OLD_VALUE']);
$colorBrightness = $this->getProperty('colorBrightness');
$source = strtok($params['SOURCE'], " ");

//DebMes($params);

$data = hsvToRgbHex($colorHSV);
$colorBrightness = $data['brightness'];
$colorRGB = $data['rgbHex'];

if($source != 'colorUpdated'){
	$this->setProperty('color', $colorRGB, 'colorWorkUpdated');
	$this->setProperty('colorBrightness', $colorBrightness, 'colorWorkUpdated');
}