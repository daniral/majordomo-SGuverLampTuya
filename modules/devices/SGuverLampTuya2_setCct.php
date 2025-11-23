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
 *  callMethod('имя объекта.setCct', array("value"=>1--100));
 *  callMethod('имя объекта.setCct', array("value"=>'coolest'));
 * 
 * @param array $params Ассоциативный массив параметров,
 *                      содержащий ключи:
 *                      - 'cct' (string) — число или пресет;
 *                      - 'value' (string) — альтернативное имя параметра.
 *
 * @return void
 */

$cct = $params['cct'] ?? $params['value'] ?? null;
if ($cct === null) return;

$this->setProperty('cct', $cct, 'setLevelCct');