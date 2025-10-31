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
		'brightness' => array('DESCRIPTION' => 'Уровень яркости белого(0<-->100).', 'ONCHANGE' => 'propertysUpdated', 'DATA_KEY' => 1,'VALIDATION_TYPE' => 1, 'VALIDATION_NUM_MIN' => 1, 'VALIDATION_NUM_MAX' => 100),
		'brightnessWork' => array('DESCRIPTION' => 'Рабочая яркость белого (0<-->1000).', 'ONCHANGE' => 'worksUpdated', 'VALIDATION_TYPE' => 1, 'VALIDATION_NUM_MIN' => 1, 'VALIDATION_NUM_MAX' => 1000),
		'brightnessSaved' => array('DESCRIPTION' => 'Сохраненная яркость белого.'),
		'cct' => array('DESCRIPTION' => 'Уровень температуры белого (0-100).', 'ONCHANGE' => 'propertysUpdated', 'DATA_KEY' => 1,'VALIDATION_TYPE' => 1, 'VALIDATION_NUM_MIN' => 1, 'VALIDATION_NUM_MAX' => 100),
		'cctWork' => array('DESCRIPTION' => 'Рабочая температура белого (0<-->1000).', 'ONCHANGE' => 'worksUpdated' ,'VALIDATION_TYPE' => 1, 'VALIDATION_NUM_MIN' => 1, 'VALIDATION_NUM_MAX' => 1000),
		'cctSaved' => array('DESCRIPTION' => 'Сохраненная температура белого.'),
		'color' => array('DESCRIPTION' => 'Цвет (RGB).', 'ONCHANGE' => 'propertysUpdated', 'DATA_KEY' => 1, 'VALIDATION_TYPE' => 100 ,'VALIDATION_CODE' => '$value = preg_match(\'/^#?[0-9a-f]{6}$/i\', $value )?$value:\'#ffff00\';'),
		'colorBrightness' => array('DESCRIPTION' => 'Яркость белого цвета (0<-->100).', 'ONCHANGE' => 'propertysUpdated', 'DATA_KEY' => 1 ,'VALIDATION_TYPE' => 1, 'VALIDATION_NUM_MIN' => 1, 'VALIDATION_NUM_MAX' => 100),
		'colorWork' => array('DESCRIPTION' => 'Рабочий цвет (HSV).', 'ONCHANGE' => 'worksUpdated', 'VALIDATION_TYPE' => 100 ,'VALIDATION_CODE' => '$value = preg_match(\'/^[0-9a-f]{12}$/i\', $value )?$value:\'003c03e801f4\';'),
		'colorSaved' => array('DESCRIPTION' => 'Сохраненный цвет.'),
		'work_mode' => array('DESCRIPTION' => 'Режим работы.'),
		'workScene' => array('DESCRIPTION' => 'Рабочая сцена.', 'ONCHANGE' => 'worksUpdated'),
		'scenesList' => array('DESCRIPTION' => 'Список сцен.'),
		'sceneName' => array('DESCRIPTION' => 'Название текущей сцены.', 'DATA_KEY' => 1),
	),
	'METHODS' => array(
		'brightnessUp' => array('DESCRIPTION' => 'Увеличить яркость белого.', '_CONFIG_SHOW' => 1, '_CONFIG_REQ_VALUE' => 1),
		'brightnessDown' => array('DESCRIPTION' => 'Уменьшить яркость белого.', '_CONFIG_SHOW' => 1, '_CONFIG_REQ_VALUE' => 1),
		'cctUp' => array('DESCRIPTION' => 'Увеличить температуру белого.', '_CONFIG_SHOW' => 1, '_CONFIG_REQ_VALUE' => 1),
		'cctDown' => array('DESCRIPTION' => 'Уменьшить температуру белого.', '_CONFIG_SHOW' => 1, '_CONFIG_REQ_VALUE' => 1),
		'colorBrightnessUp' => array('DESCRIPTION' => 'Увеличить яркость цвета.', '_CONFIG_SHOW' => 1, '_CONFIG_REQ_VALUE' => 1),
		'colorBrightnessDown' => array('DESCRIPTION' => 'Уменьшить яркость цвета.', '_CONFIG_SHOW' => 1, '_CONFIG_REQ_VALUE' => 1),
		'worksUpdated' => array('DESCRIPTION' => 'Запускается при смене рабочих параметров'),
		'propertysUpdated' => array('DESCRIPTION' => 'Запускается при смене параметров'),
	),
);
