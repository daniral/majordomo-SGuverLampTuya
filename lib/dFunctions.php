<?php

if (!function_exists('normalizeRange')) {
    function normalizeRange($val, $min = 0, $max = 100) {
        $val = strtolower(trim($val));
		if (preg_match('/^[0-9a-f]{12}$/i', $val)) {
            return $val;
        }elseif (preg_match('/^#?[0-9a-f]{6}$/i', $val)) {
            return $val;
        }elseif (is_numeric($val)) {
			return (int)max($min, min($max, $val));
        }else 
			return null;
    }
}

if (!function_exists('hsvToRgbHex')) {
	function hsvToRgbHex($hsvHex) {	
	
		$hsvHex = strtolower(trim($hsvHex));
        // Проверка 12-значного HEX
        if (!preg_match('/^[0-9a-f]{12}$/', $hsvHex)) {
            return ['rgbHex' => '#ffff00', 'brightness' => 50];;
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

if (!function_exists('rgbToHSVhex')) {
	function rgbToHSVhex($rgbHex, $brightness = 100) {
		// Убираем возможный символ "#"
		$rgbHex = ltrim($rgbHex, '#');
		$rgbHex = strtolower(trim($rgbHex));

		// Проверяем, что это ровно 6 символов 0–f
		if (!preg_match('/^[0-9a-f]{6}$/', $rgbHex)) {
			return null;
		}
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
		$hsv = 	str_pad(dechex($hue), 4, '0', STR_PAD_LEFT) .
				str_pad(dechex($sat), 4, '0', STR_PAD_LEFT) .
				str_pad(dechex($val), 4, '0', STR_PAD_LEFT);
				 
		return strtolower($hsv);
	}
}