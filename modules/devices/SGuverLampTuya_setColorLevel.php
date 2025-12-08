<?php

/**
 * Устанавливает яркость света лампы.
 *
 * Принимает один из параметров: 'brightness', 'colorBrightness', 
 *                               'level', 'colorLevel', 'value'.
 * Значение яркости должно быть в диапазоне 1–100.
 *
 * Пример вызова:
 *      callMethod('Объект.setLevel', array("value" => 1–100));
 *
 * @param array{
 *     brightness?: int|null,          // Яркость, если передана через brightness
 *     colorBrightness?: int|null,     // Яркость, если передана через level
 *     level?: int|null,               // Яркость, если передана через level
 *     colorLevel?: int|null,          // Яркость, если передана через level
 *     value?: int|null                // Яркость, если передана через value
 * } $params Ассоциативный массив параметров.
 *
 * @return void
 */

$colorLevel = $params['brightness'] ?? $params['level'] ?? $params['colorBrightness'] ?? $params['colorLevel'] ?? $params['value'] ?? null;
if ($colorLevel === null) return;

$this->setProperty('colorLevel', $colorLevel);
