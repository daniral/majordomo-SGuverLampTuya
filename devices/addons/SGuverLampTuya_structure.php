<?php

if (SETTINGS_SITE_LANGUAGE && file_exists(ROOT . 'languages/SGuverLampTuya_' . SETTINGS_SITE_LANGUAGE . '.php')) {
	include_once(ROOT . 'languages/SGuverLampTuya_' . SETTINGS_SITE_LANGUAGE . '.php');
} else {
	include_once(ROOT . 'languages/SGuverLampTuya_default.php'); //
}

$this->device_types['GuverLampTuya'] = array(
	'TITLE' => 'Лампа Гайвера(Tuya)',
	'PARENT_CLASS' => 'SControllers',
	'CLASS' => 'SGuverLampTuya',
	'DESCRIPTION'=>'Лампа Гайвера(Tuya)',
	'PROPERTIES' => array(
		'brightness' => array('DESCRIPTION' => 'Яркость (0<-->100)', 'ONCHANGE' => 'brightnessCctUpdated', 'DATA_KEY' => 1),
		'brightnessWork' => array('DESCRIPTION' => 'Рабочая яркость.', 'ONCHANGE' => 'brightnessCctWorkUpdated'),
		'brightnessSaved' => array('DESCRIPTION' => 'Сохраненная яркость.'),
		'cct' => array('DESCRIPTION' => 'Уровень температуры: (0-100)', 'ONCHANGE' => 'brightnessCctUpdated', 'DATA_KEY' => 1),
		'cctWork' => array('DESCRIPTION' => 'Рабочая теплота.', 'ONCHANGE' => 'brightnessCctWorkUpdated'),
		'cctSaved' => array('DESCRIPTION' => 'Сохраненная теплота.'),
		'color' => array('DESCRIPTION' => 'Цвет (RGB)', 'ONCHANGE' => 'colorUpdated', 'DATA_KEY' => 1),
		'colorBrightness' => array('DESCRIPTION' => 'Яркость (0<-->100)', 'ONCHANGE' => 'colorUpdated', 'DATA_KEY' => 1),
		'colorWork' => array('DESCRIPTION' => 'Рабочий цвет.', 'ONCHANGE' => 'colorWorkUpdated'),
		'colorSaved' => array('DESCRIPTION' => 'Сохраненный цвет.'),
		'work_mode' => array('DESCRIPTION' => 'Режим работы.'),
		'workScene' => array('DESCRIPTION' => 'Рабочая сцена.', 'ONCHANGE' => 'sceneWorkUpdated'),
		'scenesList' => array('DESCRIPTION' => 'Список сцен.'),
		'sceneName' => array('DESCRIPTION' => 'Название текущей сцены.', 'DATA_KEY' => 1),
	),
	'METHODS' => array(
		'brightnessUp' => array('DESCRIPTION' => 'Увеличить яркость белого.', '_CONFIG_SHOW' => 1, '_CONFIG_REQ_VALUE' => 1),
		'brightnessDown' => array('DESCRIPTION' => 'Уменьшить яркость белого.', '_CONFIG_SHOW' => 1, '_CONFIG_REQ_VALUE' => 1),
		'brightnessCctUpdated' => array('DESCRIPTION' => 'Запускается при смене яркости или теплоты'),
		'brightnessCctWorkUpdated' => array('DESCRIPTION' => 'Запускается при смене абочих яркости или теплоты'),		
		'cctUp' => array('DESCRIPTION' => 'Увеличить температуру белого.', '_CONFIG_SHOW' => 1, '_CONFIG_REQ_VALUE' => 1),
		'cctDown' => array('DESCRIPTION' => 'Уменьшить температуру белого.', '_CONFIG_SHOW' => 1, '_CONFIG_REQ_VALUE' => 1),
		'colorUpdated' => array('DESCRIPTION' => 'Запускается при смене цвета'),
		'colorWorkUpdated' => array('DESCRIPTION' => 'Запускается при смене рабочего цвета'),
		'colorBrightnessUp' => array('DESCRIPTION' => 'Увеличить яркость цвета.', '_CONFIG_SHOW' => 1, '_CONFIG_REQ_VALUE' => 1),
		'colorBrightnessDown' => array('DESCRIPTION' => 'Уменьшить яркость цвета.', '_CONFIG_SHOW' => 1, '_CONFIG_REQ_VALUE' => 1),
		'sceneWorkUpdated' => array('DESCRIPTION' => 'Запускается при смене рабочей сцены'),
	),
);
