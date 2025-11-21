<?php
/**
 * Устанавливает температуру белого цвета лампы.
 *   
 * Принимает массив параметров с ключом 'value', где указана температура в процентах
 * или один из строковых пресетов:
 *   - 'coolest'
 *   - 'cool'
 *   - 'warm'
 *   - 'warmest'
 *
 *  callMethod('имя объекта.setCct', array("value"=>0--100));
 *  callMethod('имя объекта.setCct', array("value"=>'coolest'));
 * 
 * @param array{
 *     value: int|string|null   // Цветовая температура 0–100% или строковый пресет
 * } $params Ассоциативный массив параметров.
 *
 * @return void
 */

if (!isset($params['value'])) return;
$value = $params['value'];

$presets = [
			'coolest' => 100,
			'cool'    => 66,
			'warm'    => 33,
			'warmest' => 0,
		];

if (isset($presets[$value])) {
	$value = $presets[$value];
}
		
$this->setProperty('cct', $value ?? null, 'setLevelCct');