<?php
/**
 * Конфигурация устройства SGuverLampTuya (Лампа Гайвера Tuya) для MajorDoMo.
 *
 * Этот блок определяет тип устройства, его свойства и методы.
 * Используется для управления RGB-лентой с возможностью регулировки
 * яркости белого и цветного света, управления температурой белого,
 * цветовыми сценами и сохранением предыдущих настроек.
 *
 * Загружаемые языковые файлы:
 *  - SGuverLampTuya_<LANG>.php
 *  - SGuverLampTuya_default.php
 *
 * @see SETTINGS_SITE_LANGUAGE
 *
 * @property array $device_types['modeWork'] Структура описания устройства
 * @property string TITLE Название устройства для интерфейса
 * @property string PARENT_CLASS Родительский класс устройства
 * @property string CLASS Класс устройства
 * @property string DESCRIPTION Описание устройства
 * @property array PROPERTIES Свойства устройства с описанием и действиями
 * @property array METHODS Методы устройства с описанием и параметрами
 *
 * PROPERTIES:
 *  - level: Уровень яркости белого (0–100), обновление вызывает propertysUpdated
 *  - levelWork: Рабочая яркость белого (0–1000), обновление вызывает worksUpdated
 *  - levelSaved: Последняя яркость белого
 *  - cct: Уровень температуры белого (0–100), обновление вызывает propertysUpdated
 *  - cctWork: Рабочая температура белого (0–1000), обновление вызывает worksUpdated
 *  - cctSaved: Последняя температура белого
 *  - color: Цвет (RGB), обновление вызывает propertysUpdated
 *  - colorLevel: Яркость белого цвета (0–100), обновление вызывает propertysUpdated
 *  - colorWork: Рабочий цвет (HSV), обновление вызывает worksUpdated
 *  - colorSaved: Последний цвет
 *  - modeWork: Режим работы
 *  - sceneWork: Рабочая сцена, обновление вызывает worksUpdated
 *  - scenesList: Список сцен
 *  - sceneName: Текущая сцена, обновление вызывает propertysUpdated
 *  - sceneNameSaved: Последняя сцена
 *
 * METHODS:
 *  - levelUp: Увеличить яркость белого
 *  - levelDown: Уменьшить яркость белого
 *  - setLevel: Установить уровень яркости белого
 *  - cctUp: Увеличить температуру белого
 *  - cctDown: Уменьшить температуру белого
 *  - setCct: Установить уровень температуры белого
 *  - colorLevelUp: Увеличить яркость цвета
 *  - colorLevelDown: Уменьшить яркость цвета
 *  - setColor: Установить цвет
 *  - setColorLevel: Установить яркость цвета
 *  - worksUpdated: Срабатывает при изменении рабочих параметров
 *  - propertysUpdated: Срабатывает при изменении свойств устройства
 *
 * Все методы с _CONFIG_SHOW и _CONFIG_REQ_VALUE отображаются в интерфейсе
 * и требуют передачи значения при вызове.
 */


if (SETTINGS_SITE_LANGUAGE && file_exists(ROOT . 'languages/SGuverLampTuya_' . SETTINGS_SITE_LANGUAGE . '.php')) {
	include_once(ROOT . 'languages/SGuverLampTuya_' . SETTINGS_SITE_LANGUAGE . '.php');
} else {
	include_once(ROOT . 'languages/SGuverLampTuya_default.php'); 
}

$this->device_types['guverLampTuya'] = array(
	'TITLE' => 'Освещение (Tuya Guver Lamp)',
	'PARENT_CLASS' => 'SControllers',
	'CLASS' => 'SGuverLampTuya',
	'DESCRIPTION'=>'Tuya Guver Lamp',
	'PROPERTIES' => array(
		'level' => array('DESCRIPTION' => 'Уровень яркости белого(0<-->100).', 'ONCHANGE' => 'propertysUpdated', 'DATA_KEY' => 1),
		'levelWork' => array('DESCRIPTION' => 'Рабочая яркость белого (0<-->1000).', 'ONCHANGE' => 'worksUpdated'),
		'levelSaved' => array('DESCRIPTION' => 'Последняя яркость белого.'),
		'cct' => array('DESCRIPTION' => 'Уровень температуры белого (0-100).', 'ONCHANGE' => 'propertysUpdated', 'DATA_KEY' => 1),
		'cctWork' => array('DESCRIPTION' => 'Рабочая температура белого (0<-->1000).', 'ONCHANGE' => 'worksUpdated'),
		'cctSaved' => array('DESCRIPTION' => 'Последняя температура белого.'),
		'color' => array('DESCRIPTION' => 'Цвет (RGB).', 'ONCHANGE' => 'propertysUpdated', 'DATA_KEY' => 1),
		'colorLevel' => array('DESCRIPTION' => 'Яркость цветного света (0<-->100).', 'ONCHANGE' => 'propertysUpdated', 'DATA_KEY' => 1),
		'colorWork' => array('DESCRIPTION' => 'Рабочий цвет (HSV).', 'ONCHANGE' => 'worksUpdated'),
		'colorSaved' => array('DESCRIPTION' => 'Последний цвет.'),
		'modeWork' => array('DESCRIPTION' => 'Режим работы.'),
		'sceneWork' => array('DESCRIPTION' => 'Рабочая сцена.', 'ONCHANGE' => 'worksUpdated'),
		'scenesList' => array('DESCRIPTION' => 'Список сцен.', 'ONCHANGE' => 'propertysUpdated'),
		'sceneName' => array('DESCRIPTION' => 'Название текущей сцены.', 'ONCHANGE' => 'propertysUpdated', 'DATA_KEY' => 1),
		'sceneNameSaved' => array('DESCRIPTION' => 'Последняя сцена.'),
	),
	'METHODS' => array(
		'levelUp' => array('DESCRIPTION' => 'Увеличить яркость белого.', '_CONFIG_SHOW' => 1, '_CONFIG_REQ_VALUE' => 1),
		'levelDown' => array('DESCRIPTION' => 'Уменьшить яркость белого.', '_CONFIG_SHOW' => 1, '_CONFIG_REQ_VALUE' => 1),
		'setLevel' => array('DESCRIPTION' => 'Установить уровень яркости белого.', '_CONFIG_SHOW' => 1, '_CONFIG_REQ_VALUE' => 1),
		'cctUp' => array('DESCRIPTION' => 'Увеличить температуру белого.', '_CONFIG_SHOW' => 1, '_CONFIG_REQ_VALUE' => 1),
		'cctDown' => array('DESCRIPTION' => 'Уменьшить температуру белого.', '_CONFIG_SHOW' => 1, '_CONFIG_REQ_VALUE' => 1),
		'setCct' => array('DESCRIPTION' => 'Установить уровень температуры белого.', '_CONFIG_SHOW' => 1, '_CONFIG_REQ_VALUE' => 1),
		'colorLevelUp' => array('DESCRIPTION' => 'Увеличить яркость цветного света.', '_CONFIG_SHOW' => 1, '_CONFIG_REQ_VALUE' => 1),
		'colorLevelDown' => array('DESCRIPTION' => 'Уменьшить яркость цветного света.', '_CONFIG_SHOW' => 1, '_CONFIG_REQ_VALUE' => 1),
		'setColor' => array('DESCRIPTION' => 'Установить цвет.', '_CONFIG_SHOW' => 1, '_CONFIG_REQ_VALUE' => 1),
		'setColorLevel' => array('DESCRIPTION' => 'Установить яркость цветного света.', '_CONFIG_SHOW' => 1, '_CONFIG_REQ_VALUE' => 1),
		'worksUpdated' => array('DESCRIPTION' => 'Запускается при смене рабочих параметров'),
		'propertysUpdated' => array('DESCRIPTION' => 'Запускается при смене параметров'),
	),
);
