<?php
/**
 * Устанавливает свойства объекта лампы по умолчанию.
 *
 * Значения подходят для AqaraBulbZigBee. Для других устройств могут потребоваться изменения.
 *
 * Устанавливаются следующие свойства:
 *   - level: яркость лампы (0–100)
 *   - cct: цветовая температура (0–100)
 *   - dayLevel / dayCct: настройки для дневного режима
 *   - nightLevel / nightCct: настройки для ночного режима
 *   - levelMinWork / levelMaxWork: минимальная и максимальная яркость
 *   - cctMinWork / cctMaxWork: минимальная и максимальная температура
 *   - timerOff: авто-выключение (секунды)
 *   - presence: состояние датчика присутствия (0/1)
 *   - dayBegin / nightBegin: время начала дня и ночи
 *   - autoOnOff: включение авто-режима (0/1)
 *   - flag: стопер авто-режима (0/1)
 *   - illuminanceFlag: флаг работы по освещённости (0/1)
 *   - illuminance / illuminanceMax: текущие и максимальные значения освещённости
 *   - workingDay: режим работы дня (1=день, 2=ночь, 3=круглосуточно)
 *   - workingBy: источник авто-режима (1=по времени, 2=по солнцу, 3=по датчику)
 *   - addTimeSunrise / addTimeSunset: смещение времени восхода/заката
 *   - signSunrise / signSunset: направление смещения (0=отнять, 1=прибавить)
 *   - sunriseTime / sunsetTime: время восхода и заката
 *
 * @return void
 */

$defaults = [
	'level' => '50', 'cct' => '50',
    'dayLevel' => '100', 'dayCct' => '0',
    'nightLevel' => '30', 'nightCct' => '100',
    'levelMinWork' => '0', 'levelMaxWork' => '254',
    'cctMinWork' => '153', 'cctMaxWork' => '370',
    'timerOff' => '45', 'presence' => '0',
    'dayBegin' => '08:00', 'nightBegin' => '18:00',
    'autoOnOff' => '1', 'flag' => '0', 'illuminanceFlag' => '0',
    'illuminance' => '0', 'illuminanceMax' => '0',
    'workingDay' => '2', 'workingBy' => '1',
    'addTimeSunrise' => '00:00', 'addTimeSunset' => '00:00',
    'signSunrise' => '1', 'signSunset' => '1',
    'sunriseTime' => '08:00', 'sunsetTime' => '18:00'
];
initDefaults($this, $defaults);