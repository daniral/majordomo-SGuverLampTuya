<?php

/** Обновление свойств устройства SGuverLampTuya2 (Лампа Гайвера Tuya) для MajorDoMo.
 *
 * Этот метод обрабатывает изменения следующих свойств:
 * - colorWork (цвет в формате HSV, преобразуется в RGB и сохраняется)
 * - levelWork (рабочий уровень яркости белого)
 * - cctWork (рабочая температура белого)
 * - sceneWork (рабочая сцена, устанавливается имя сцены)
 *
 * Логика обработки:
 * 1. Если свойства colorLevel, color, level или cct пусты, устанавливаются значения по умолчанию.
 * 2. Игнорируется источник 'propertysUpdated', чтобы избежать рекурсивных вызовов.
 * 3. colorWork преобразуется в RGB и обновляется color, colorSaved и colorLevel.
 * 4. levelWork и cctWork преобразуются в значения для отображения (деление на 10 для уровня и температуры).
 * 5. sceneWork сверяется с scenesList и обновляет sceneName и sceneNameSaved.
 *
 * Особенности обработки scenesList:
 * - Каждая сцена хранится в формате "Имя=Значение"
 * - Разделители между сценами: запятая или перенос строки
 * - Пробелы, кавычки и символы переноса строк обрезаются с начала и конца
 * - Поиск сцены ограничен первой найденной совпадающей записи
 *
 * @param array $params {
 *     Входные параметры изменения свойства.
 *
 *     @type string SOURCE Источник изменения свойства ('user', 'worksUpdated', 'propertysUpdated')
 *     @type string PROPERTY Имя изменяемого свойства ('colorWork', 'levelWork', 'cctWork', 'sceneWork')
 *     @type mixed  NEW_VALUE Новое значение свойства
 * }
 *
 * @property int    $level Уровень яркости белого (1–100), не рабочий
 * @property int    $levelWork Рабочий уровень яркости белого (1–1000)
 * @property int    $cct Уровень температуры белого (1–100), не рабочий
 * @property int    $cctWork Рабочая температура белого (1–1000)
 * @property string $colorLevel Яркость цветного света (1–100)
 * @property string $color RGB значение текущего цвета
 * @property string $colorSaved Сохранённый последний цвет
 * @property string $sceneName Имя текущей сцены
 * @property string $sceneNameSaved Сохранённая последняя сцена
 * @property string $scenesList Список всех сцен устройства
 * @property int    $status Статус устройства (0 или 1)
 *
 * @return void
 *
 * @see normalizeRange() Функция нормализации значений
 * @see hsvToRgbHex() Преобразование HSV в RGB и вычисление яркости
 * @see setProperty() Метод установки свойства устройства
 */
//


// --- Дефолтные свойства
$this->callMethod('byDefault');

$property = $params['PROPERTY'] ?? null;
$source   = strtok($params['SOURCE'] ?? '', ' ');
$value = ($property === 'colorWork')
    ? normalizeRange($params['NEW_VALUE'], 1, 100, 'color')
    : (($property !== 'sceneWork')
        ? normalizeRange($params['NEW_VALUE'], 1, 1000, 'number') / 10
        : ($params['NEW_VALUE'] ?? null));

// Защита от рекурсий. 
if ($source === 'propertysUpdated' || is_null($value)) return;

//$this->setProperty('flag', 1);

//  Обработка colorWork: HSV -> RGB/Level ---
if ($property === 'colorWork' ) {
    // Преобразуем HSV в RGB Hex и Яркость
    $data = hsvToRgbHex($value);
    $color = $data['rgbHex'];
    $colorLevel = $data['brightness'];
    // Обновляем свойства.
    $this->setProperty('color', $color, 'worksUpdated');
    $this->setProperty('colorSaved', $color);
    $this->setProperty('colorLevel', $colorLevel, 'worksUpdated');
    $this->setProperty('colorLevelSaved', $colorLevel);
    return; // Завершаем работу, если обработано colorWork
}

// Обработка levelWork , cctWork 
if ($property === 'levelWork' || $property === 'cctWork') {
	$this->setProperty(str_replace('Work', '', $property), $value, 'worksUpdated');
	$this->setProperty(str_replace('Work', '', $property).'Saved', $value);
	return; // Завершаем работу, если обработано
}

//  Обработка sceneWork: Код Сцены -> Имя Сцены ---
if ($property === 'sceneWork') {
    $sceneWork = trim($value, " \t\n\r\0\x0B\"'");
    $scenesListRaw = $this->getProperty('scenesList');
    // Разбиваем список сцен на ассоциативный массив [код_сцены => имя_сцены]
    $sceneMap = [];
    $sceneItems = preg_split('/\s*(?:,|\r\n|\n|\r)\s*/', $scenesListRaw, -1, PREG_SPLIT_NO_EMPTY);
    foreach ($sceneItems as $item) {
        [$name, $code] = array_map('trim', explode('=', $item, 2));
        if ($name && $code) {
            // Ключ - код сцены (sceneWork), Значение - имя сцены (sceneName)
            $sceneMap[$code] = $name;
        }
    }
    $sceneNameToSet = $sceneMap[$sceneWork] ?? 'unknown';
    // Устанавливаем найденное имя и сохраняем его
    if ($sceneNameToSet !== 'unknown') {
        $this->setProperty('sceneNameSaved', $sceneNameToSet);
    }
    // Обновляем sceneName для UI
    $this->setProperty('sceneName', $sceneNameToSet, 'worksUpdated');
}


// $value = normalizeRange($params['NEW_VALUE'], 1, 1000);
// $colorLevel = $this->getProperty('colorLevel');
// $source = strtok($params['SOURCE'], " ");
// $property = $params['PROPERTY'];

// if($source === 'propertysUpdated') return;

// if(in_array($property, ['colorWork']) && !is_null($value)){
// 	$data = hsvToRgbHex($value);
// 	$colorLevel = $data['brightness'];
// 	$colorRGB = $data['rgbHex'];
// 	$this->setProperty('color', $colorRGB, 'worksUpdated');
// 	$this->setProperty('colorSaved', $colorRGB);
// 	$this->setProperty('colorLevel', $colorLevel, 'worksUpdated');
// 	$this->setProperty('colorLevelSaved', $colorLevel, 'worksUpdated');
// }elseif(in_array($property, ['levelWork', 'cctWork']) && !is_null($value)){
// 	$value=round($value / 10);
// 	$this->setProperty(str_replace('Work', '', $property), $value, 'worksUpdated');
// 	$this->setProperty(str_replace('Work', '', $property).'Saved', $value);
// }elseif(in_array($property, ['sceneWork']) && !is_null($params['NEW_VALUE'])){
// 	$sceneWork = trim($params['NEW_VALUE'], " \t\n\r\0\x0B\"'");
// 	$sceneNameToSet = 'unknown';
// 	// Получаем список сцен и очищаем его от пробелов и кавычек по краям
// 	$scenesList = trim($this->getProperty('scenesList'), " \t\n\r\0\x0B\"'");

// 	// Разбиваем на отдельные сцены (по запятой или переносу строки)
// 	$sceneItems = preg_split('/\s*(?:,|\r\n|\n|\r)\s*/', $scenesList, -1, PREG_SPLIT_NO_EMPTY);

// 	// Перебираем массив сцен
// 	foreach ($sceneItems as $item) {
// 		// Каждая сцена имеет формат "Имя=Значение"
// 		$parts = explode('=', $item, 2); 
// 		if (count($parts) == 2) {
// 			$name  = $parts[0];
// 			$scene = $parts[1];
// 			// Если значение совпадает, обновляем sceneName
// 			if ($sceneWork === $scene) {
// 				$sceneNameToSet = $name;
// 				$this->setProperty('sceneNameSaved', $sceneNameToSet);
// 				break; // нашли нужную сцену, дальше не ищем
// 			}
// 		}
// 	}
// 	$this->setProperty('sceneName', $sceneNameToSet, 'worksUpdated');
// }