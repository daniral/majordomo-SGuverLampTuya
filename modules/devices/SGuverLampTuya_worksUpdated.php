<?php

/**
 * Обновление свойств устройства SGuverLampTuya (Лампа Гайвера Tuya) для MajorDoMo.
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
 * @property int    $level Уровень яркости белого (0–100), не рабочий
 * @property int    $levelWork Рабочий уровень яркости белого (0–1000)
 * @property int    $cct Уровень температуры белого (0–100), не рабочий
 * @property int    $cctWork Рабочая температура белого (0–1000)
 * @property string $colorLevel Яркость цветного света (0–100)
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

if ($this->getProperty('scenesList') === '') {
    $this->setProperty('scenesList',
       'Спокойная=000e0d0000000000000000c80000,
        Чтение=010e0d0000000000000003e801f4,
        Работа=020e0d0000000000000003e803e8,
        Отдых=030e0d0000000000000001f401f4,
        Степь=04464602007803e803e800000000464602007803e8000a000000,
        Яркий=05464601000003e803e800000000464601007803e803e80000000046460100f003e803e800000000464601003d03e803e80000000046460100ae03e803e8000000004646010011303e803e800000000,
        Яркий2=06464601000003e803e800000000464601007803e803e80000000046460100f003e803e800000000,
        Пёстрый=07464602000003e803e800000000464602007803e803e80000000046460200f003e803e800000000464602003d03e803e80000000046460200ae03e803e800000000464602011303e803e8000000,
        Мягкий=2946460200000000000003e800d246460200000000000000c80000,
        Динамический=2a23230100000000000003e800d223230100000000000000c800d2,
        Ночной свет=08000000001e0320012c00000000,
        Синий неон=1446460200ae03e803e80000000046460200b4012c03e80000000046460200b4003203e8000000,
        Восход=1532320200f003e800640000000032320200f003e803e800000000464602012703e802ee00000000555502000003e803e800000000464602001302ee03e8000000004646020032025803e800000000323202005a038403e800000000,
        Закат=16323202005a0384006400000000323202005a038403e8000000004646020032025803e800000000505002001e02ee03e800000000323202000003e803e8000000,
        Океан=1746460200f003e803e80000000046460200dc02bc03e8000000,
        Подсолнух=184646020028032003e800000000464602001e038403e8000000004646020014038403e8000000,
        Лес=19464601007803e803e800000000464602006e0320025800000000464602005a038403e800000000,
        Кунг-Фу=1a464602000a038403e800000000464602000003e803e800000000,
        Свет свечи=1b464603001803e803e800000000,
        Фантазия=1c4646020104032003e800000000464602011802bc03e800000000464602011303e803e8000000,
        Средиземноморье=1d646401000003e803e80000000064640100f003e803e800000000646402007803e803e800000000646402003d03e803e8000000,
        Французский стиль=1e323201015e01f403e800000000323202003201f403e80000000032320200a001f403e800000000,
        Американский стиль=1f46460100dc02bc03e800000000464602006e03200258000000004646020014038403e800000000464601012703e802ee0000000046460100000384028a00000000,
        День рождения=20646401003d03e803e800000000646401007803e803e8000000005a5a01011303e803e8000000005a5a0100ae03e803e800000000646401003201f403e800000000646401000003e803e8000000,
        Годовщина=21323202015e01f403e800000000323202011303e803e800000000,
        Рождество=225a5a0100f003e803e8000000005a5a01003d03e803e800000000464601000003e803e8000000005a5a0100ae03e803e8000000005a5a01011303e803e800000000464601007803e803e800000000,
        День независимости=23505002000003e803e80000000046460200f003e803e800000000,
        Дивали=24464602000003e803e800000000464602003d03e803e800000000464602011303e803e80000000046460200f003e803e800000000464602007803e803e800000000,
        Холи=25464601011303e803e800000000464602000003e803e800000000464602003d03e803e8000000004646010154032003e8000000004646010140032003e800000000464601001e02ee03e800000000,
        День победы=265a5a020014006403e800000000464602000003e803e800000000,
        Пасха=275a5a020014006403e800000000464602000003e803e800000000323202015e01f403e800000000464602011303e803e800000000,
        Хэллоуин=28464601011303e803e800000000464601001e03e803e800000000'
    );
}
if ($this->getProperty('colorLevel') === '') $this->setProperty('colorLevel', '50');
if ($this->getProperty('color') === '') $this->setProperty('color', '#ffff00');
if ($this->getProperty('level') === '') $this->setProperty('level', '50');
if ($this->getProperty('cct') === '') $this->setProperty('cct', '10');
if ($this->getProperty('sceneName') === '') $this->setProperty('sceneName', 'Спокойная');

$property = $params['PROPERTY'] ?? null;
$source   = strtok($params['SOURCE'] ?? '', ' ');
$value = ($property === 'colorWork')
    ? normalizeRange($params['NEW_VALUE'], 1, 100, 'color')
    : (($property !== 'sceneWork' || $property === 'scenesList')
        ? normalizeRange($params['NEW_VALUE'], 1, 1000, 'number') / 10
        : ($params['NEW_VALUE'] ?? null));

// Защита от рекурсий. 
if ($source === 'propertysUpdated' || is_null($value)) return;

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