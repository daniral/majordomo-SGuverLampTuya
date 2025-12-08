<?php
/**
 * Класс устройства "GuverLampTuya2" (Лампа Гайвера Tuya 2).
 *
 * Реализует управление:
 *  - яркостью белого света (level, levelWork, levelSaved),
 *  - температурой белого (cct, cctWork, cctSaved),
 *  - цветным RGB-светом (color, colorLevel, colorWork, colorSaved),
 *  - сценами (sceneName, sceneWork),
 *  - автоматическими режимами освещения (по времени, солнцу, датчикам),
 *  - а также вспомогательные параметры и триггеры MajorDoMo.
 *
 * @link https://mjdm.ru MajorDoMo Project
 *
 * --------------------------------------------------------------------------
 * PROPERTIES
 * --------------------------------------------------------------------------
 *
 * @property int    $level            Уровень яркости белого (0–100)
 * @property int    $levelWork        Рабочая яркость белого (0–1000)
 * @property int    $levelSaved       Последняя яркость белого
 *
 * @property int    $cct              Температура белого (0–100)
 * @property int    $cctWork          Рабочая температура (0–1000)
 * @property int    $cctSaved         Последняя температура
 *
 * @property string $color            Цвет RGB
 * @property int    $colorLevel       Яркость цветного света (0–100)
 * @property string $colorWork        Рабочий цвет HSV
 * @property string $colorSaved       Последний цвет
 *
 * @property string $modeWork        Режим работы лампы
 * @property string $sceneWork        Рабочая сцена
 * @property string $scenesList       Список сцен
 *
 * @property string $sceneName        Название выбранной сцены
 * @property string $sceneNameSaved   Последняя активная сцена
 *
 * Дневные/ночные режимы:
 * @property int    $dayLevel         Яркость днем
 * @property int    $nightLevel       Яркость ночью
 * @property int    $dayCct           Температура днем
 * @property int    $nightCct         Температура ночью
 * @property string $dayColor         Цвет днем
 * @property string $nightColor       Цвет ночью
 * @property string $dayScene         Сцена днем
 * @property string $nightScene       Сцена ночью
 *
 * Автоматизация:
 * @property int    $autoOnOff        Включение авто-режима
 * @property int    $timerOff         Выключение через N секунд
 * @property int    $workingDay       Режим работы: день/ночь/всегда
 * @property int    $workingBy        Работа по: времени/солнцу/датчику
 * @property string $dayBegin         Время начала дня
 * @property string $nightBegin       Время начала ночи
 * @property string $sunriseTime      Восход солнца
 * @property string $sunsetTime       Закат солнца
 * @property int    $signSunrise      Прибавление или вычитание смещения
 * @property string $addTimeSunrise   Смещение времени sunrise
 * @property int    $signSunset       Прибавление или вычитание смещения
 * @property string $addTimeSunset    Смещение sunset
 *
 * Датчики:
 * @property int    $illuminanceMax   Максимум освещенности
 * @property int    $illuminanceFlag  Флаг ограничения работы по датчику
 * @property int    $illuminance      Текущая освещенность
 * @property int    $presence         Датчик присутствия
 * @property int    $flag             Внутренний стоп-флаг
 *
 * --------------------------------------------------------------------------
 * METHODS (методы-контроллеры MajorDoMo)
 * --------------------------------------------------------------------------
 *
 * Управление питанием:
 * @method void turnOn()              Включить лампу
 * @method void turnOff()             Выключить лампу
 * @method void switch()              Переключить состояние
 *
 * Управление яркостью:
 * @method void levelUp(int $value)   Увеличить яркость белого
 * @method void levelDown(int $value) Уменьшить яркость белого
 * @method void setLevel(int $value)  Установить яркость белого
 *
 * Управление температурой:
 * @method void cctUp(int $value)     Увеличить температуру белого
 * @method void cctDown(int $value)   Уменьшить температуру белого
 * @method void setCct(int $value)    Установить температуру белого
 *
 * Управление цветом:
 * @method void colorLevelUp(int $value)  Увеличить яркость цвета
 * @method void colorLevelDown(int $value)Уменьшить яркость цвета
 * @method void setColor(string $rgb)     Установить RGB цвет
 * @method void setColorLevel(int $value) Установить яркость цветного света
 *
 * Служебные методы:
 * @method void worksUpdated()        Обработка изменения рабочих параметров
 * @method void propertysUpdated()    Обработка изменения свойств
 *
 * Дополнительные функции:
 * @method void byDefault()           Установить значения по умолчанию
 * @method void commandsMenu()        Создать меню управления
 */

if (SETTINGS_SITE_LANGUAGE && file_exists(ROOT . 'languages/SGuverLampTuya2_' . SETTINGS_SITE_LANGUAGE . '.php')) {
	include_once(ROOT . 'languages/SGuverLampTuya2_' . SETTINGS_SITE_LANGUAGE . '.php');
} else {
	include_once(ROOT . 'languages/SGuverLampTuya2_default.php'); 
}

$this->device_types['guverLampTuya2'] = array(
	'TITLE' => 'Освещение (Tuya Guver Lamp) - 2',
	'PARENT_CLASS' => 'SControllers',
	'CLASS' => 'SGuverLampTuya2',
	'DESCRIPTION'=>'Tuya Guver Lamp - 2',
	'PROPERTIES' => array(
		'level' => array('DESCRIPTION' => 'Уровень яркости белого(1<-->100).', 'ONCHANGE' => 'propertysUpdated', 'DATA_KEY' => 1),
		'levelWork' => array('DESCRIPTION' => 'Рабочая яркость белого (1<-->1000).', 'ONCHANGE' => 'worksUpdated'),
		'levelSaved' => array('DESCRIPTION' => 'Последняя яркость белого.'),
		'cct' => array('DESCRIPTION' => 'Уровень температуры белого (1-100).', 'ONCHANGE' => 'propertysUpdated', 'DATA_KEY' => 1),
		'cctWork' => array('DESCRIPTION' => 'Рабочая температура белого (1<-->1000).', 'ONCHANGE' => 'worksUpdated'),
		'cctSaved' => array('DESCRIPTION' => 'Последняя температура белого.'),
		'color' => array('DESCRIPTION' => 'Цвет (RGB).', 'ONCHANGE' => 'propertysUpdated', 'DATA_KEY' => 1),
		'colorWork' => array('DESCRIPTION' => 'Рабочий цвет (HSV).', 'ONCHANGE' => 'worksUpdated'),
		'colorSaved' => array('DESCRIPTION' => 'Последний цвет.'),
		'colorLevel' => array('DESCRIPTION' => 'Яркость цветного света (1<-->100).', 'ONCHANGE' => 'propertysUpdated', 'DATA_KEY' => 1),
		'colorLevelSaved' => array('DESCRIPTION' => 'Последняя Яркость цветного света.', 'ONCHANGE' => 'propertysUpdated', 'DATA_KEY' => 1),
		'sceneName' => array('DESCRIPTION' => 'Название текущей сцены.', 'ONCHANGE' => 'propertysUpdated', 'DATA_KEY' => 1),
		'sceneWork' => array('DESCRIPTION' => 'Рабочая сцена.', 'ONCHANGE' => 'worksUpdated'),
		'scenesList' => array('DESCRIPTION' => 'Список сцен.', 'ONCHANGE' => 'propertysUpdated'),
		'sceneNameSaved' => array('DESCRIPTION' => 'Последняя сцена.'),
		'modeWork' => array('DESCRIPTION' => 'Режим работы.'),
		'mode' => array('DESCRIPTION' => 'Что включать (цвет, белый, сцена)','_CONFIG_TYPE'=>'select','_CONFIG_OPTIONS'=>'1=Цвет,2=Белый,3=Сцена'),

		'dayColor' => array('DESCRIPTION' => 'Цвет днем', '_CONFIG_TYPE' => 'num',),
		'dayColorLevel' => array('DESCRIPTION' => 'Уровень яркости цвета днем', '_CONFIG_TYPE' => 'num',),
		'dayLevel' => array('DESCRIPTION' => 'Уровень яркости белого днем', '_CONFIG_TYPE' => 'num',),
		'dayCct' => array('DESCRIPTION' => 'Уровень температуры белого днем', '_CONFIG_TYPE' => 'num',),
		'dayScene' => array('DESCRIPTION' => 'Сцена днем', '_CONFIG_TYPE' => 'num',),
		'dayMode' => array('DESCRIPTION' => 'Что включать днем (цвет, белый, сцена)','_CONFIG_TYPE'=>'select','_CONFIG_OPTIONS'=>'1=Цвет,2=Белый,3=Сцена'),

		'nightColor' => array('DESCRIPTION' => 'Цвет ночью', '_CONFIG_TYPE' => 'num',),
		'nightColorLevel' => array('DESCRIPTION' => 'Уровень яркости цвета  ночью', '_CONFIG_TYPE' => 'num',),
		'nightLevel' => array('DESCRIPTION' => 'Уровень яркости белого ночью', '_CONFIG_TYPE' => 'num',),
		'nightCct' => array('DESCRIPTION' => 'Уровень температуры белого ночью', '_CONFIG_TYPE' => 'num',),
		'nightScene' => array('DESCRIPTION' => 'Сцена ночью', '_CONFIG_TYPE' => 'num',),
		'nightMode' => array('DESCRIPTION' => 'Что включать ночью (цвет, белый, сцена)','_CONFIG_TYPE'=>'select','_CONFIG_OPTIONS'=>'1=Цвет,2=Белый,3=Сцена'),

		'autoOnOff' => array('DESCRIPTION' => 'Автовключение','_CONFIG_TYPE'=>'select','_CONFIG_OPTIONS'=>'1=Включено,0=Отключено'),
		'timerOff' => array('DESCRIPTION' => 'Выключить через(сек). 0-не выключать', '_CONFIG_TYPE' => 'num'),
		'workingDay' => array('DESCRIPTION' => 'Включать','_CONFIG_TYPE'=>'select','_CONFIG_OPTIONS'=>'1=Днём,2=Ночью,3=Круглосуточно'),
		'workingBy' => array('DESCRIPTION' => 'Работать по','_CONFIG_TYPE'=>'select','_CONFIG_OPTIONS'=>'1=Времени,2=Солнцу,3=Датчику'),
		'dayBegin' => array('DESCRIPTION' => 'Начало режима день(hh:mm)', '_CONFIG_TYPE' => 'num'),
		'nightBegin' => array('DESCRIPTION' => 'Начало режима ночь(hh:mm)', '_CONFIG_TYPE' => 'num'),
		'sunriseTime' => array('DESCRIPTION' => 'Время восхода солнца'),
		'sunsetTime' => array('DESCRIPTION' => 'Время захода солнца'),
		'signSunrise' => array('DESCRIPTION' => 'Восход','_CONFIG_TYPE'=>'select','_CONFIG_OPTIONS'=>'1=прибавить,0=отнять'),
		'addTimeSunrise' => array('DESCRIPTION' => 'Часов:Минут(00:00)', '_CONFIG_TYPE' => 'num'),
		'signSunset' => array('DESCRIPTION' => 'Закат','_CONFIG_TYPE'=>'select','_CONFIG_OPTIONS'=>'1=прибавить,0=отнять'),
		'addTimeSunset' => array('DESCRIPTION' => 'Часов:Минут(00:00)', '_CONFIG_TYPE' => 'num'),
		'illuminanceMax' => array('DESCRIPTION' => 'Макc.освещение(датчик)', '_CONFIG_TYPE' => 'num'),
		'illuminanceFlag' => array('DESCRIPTION' => 'Стопер датчика освещения'),
		'illuminance' => array('DESCRIPTION' => 'Данные с датчика освещения', 'DATA_KEY' => 1),
		'presence' => array('DESCRIPTION' => 'Данные с датчика присутствия', 'ONCHANGE' => 'propertysUpdated', 'DATA_KEY' => 1),
		'flag' => array('DESCRIPTION' => 'Стопер'),
	),
	'METHODS' => array(
		'turnOn' => array('DESCRIPTION' => 'Включить', '_CONFIG_SHOW' => 1),
		'turnOff' => array('DESCRIPTION' => 'Выключить', '_CONFIG_SHOW' => 1),
		'switch' => array('DESCRIPTION' => 'Переключить'),

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
		'statusUpdated' => array('DESCRIPTION' => 'Запускается при смене статуса'),
	
		'byDefault' => array('DESCRIPTION' => 'Установить свойства по умолчанию.'),
		'createCommandsMenu' => array('DESCRIPTION' => 'Создает меню управления.', '_CONFIG_SHOW' => 1),
		'deleteCommandsMenu' => array('DESCRIPTION' => 'Удаляет меню управления.', '_CONFIG_SHOW' => 1),	
	),	
);
