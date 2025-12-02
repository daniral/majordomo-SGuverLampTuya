<?php

/**
 * Обновление свойств устройства SGuverLampTuya (Лампа Гайвера Tuya).
 *
 * Этот метод обрабатывает изменения свойств устройства, включая:
 * - Белый свет (level, cct)
 * - Цветной свет (color, colorLevel)
 * - Сценарии (sceneName)
 *
 * При изменении свойств выполняются следующие действия:
 * - Обновление рабочей копии свойства (Work)
 * - Сохранение текущего значения (Saved)
 * - Перевод в соответствующий режим работы (white/colour/scene)
 * - Вызов функций обратного обновления (`propertysUpdated`, `worksUpdated`)
 * - Обновление статуса устройства (status)
 *
 * Входные параметры берутся из $params:
 *   - SOURCE: строка, источник изменения свойства
 *   - PROPERTY: имя свойства, которое изменилось
 *   - NEW_VALUE: новое значение свойства
 *
 * Обработка цветов:
 *   - Поддерживаются стандартные цвета (red, green, blue, white, yellow, cyan, magenta, orange, purple, pink, lime)
 *   - Цвета преобразуются в HEX формат
 *   - colorLevel используется для управления яркостью цвета
 *
 * Обработка сцен:
 *   - Список сцен хранится в свойстве scenesList в формате "Имя=Значение"
 *   - Рабочая сцена устанавливается в sceneWork
 *   - При совпадении имени сцены с sceneName сохраняется состояние
 *
 * Особенности:
 *   - Защита от рекурсивного вызова при source = 'worksUpdated'
 *   - Нормализация значений цвета и яркости через normalizeRange()
 *   - Преобразование цвета из слов в HEX
 *
 * @global int $status Текущий статус устройства (0 или 1)
 * @global string $colorSaved Последний сохранённый цвет
 * @global string $sceneNameSaved Последняя сохранённая сцена
 *
 * @param array $params {
 *     Входные параметры для обновления свойств устройства.
 *
 *     @type string SOURCE Источник изменения свойства (например, 'user', 'worksUpdated')
 *     @type string PROPERTY Имя изменяемого свойства ('level', 'cct', 'color', 'colorLevel', 'sceneName')
 *     @type mixed  NEW_VALUE Новое значение свойства (число или строка)
 * }
 *
 * @return void
 *
 * @see normalizeRange() Функция нормализации значений
 * @see rgbToHSVhex() Преобразование RGB цвета в HSV hex
 * @see setProperty() Метод устройства для обновления свойства
 */


if ($this->getProperty('colorLevel') === '') $this->setProperty('colorLevel', '50');
if ($this->getProperty('color') === '') $this->setProperty('color', '#ffff00');
if ($this->getProperty('level') === '') $this->setProperty('level', '50');
if ($this->getProperty('cct') === '') $this->setProperty('cct', '10');
if ($this->getProperty('scenesList') === '') $this->setProperty('scenesList','Спокойная=000e0d0000000000000000c80000,Чтение=010e0d0000000000000003e801f4,Работа=020e0d0000000000000003e803e8,Отдых=1b464603001803e803e800000000,Степь=04464602007803e803e800000000464602007803e8000a000000,Яркий=05464601000003e803e800000000464601007803e803e80000000046460100f003e803e800000000464601003d03e803e80000000046460100ae03e803e800000000464601011303e803e800000000,Яркий2=06464601000003e803e800000000464601007803e803e80000000046460100f003e803e800000000,Пёстрый=07464602000003e803e800000000464602007803e803e80000000046460200f003e803e800000000464602003d03e803e80000000046460200ae03e803e800000000464602011303e803e8000000,Мягкий=2946460200000000000003e800d246460200000000000000c800,Динамический=2a23230100000000000003e800d223230100000000000000c800d2,Ночной=08000000001e0320012c00000000,Синий неон=1446460200ae03e803e80000000046460200b4012c03e80000000046460200b4003203e8000000,Восход=1532320200f003e800640000000032320200f003e803e800000000464602012703e802ee00000000555502000003e803e800000000464602001302ee03e8000000004646020032025803e800000000323202005a038403e800000000,Закат=16323202005a0384006400000000323202005a038403e8000000004646020032025803e800000000505002001e02ee03e800000000323202000003e803e8000000,Океан=1746460200f003e803e80000000046460200dc02bc03e8000000,Подсолнух=184646020028032003e800000000464602001e038403e8000000004646020014038403e8000000,Лес=19464601007803e803e800000000464602006e0320025800000000464602005a038403e800000000,Кунг-Фу=1a464602000a038403e800000000464602000003e803e800000000,Свет свеча=1b464603001803e803e800000000,Фантазия=1c4646020104032003e800000000464602011802bc03e800000000464602011303e803e8000000,Средиземноморье=1d646401000003e803e80000000064640100f003e803e800000000646402007803e803e800000000646402003d03e803e8000000,Французский стиль=1e323201015e01f403e800000000323202003201f403e80000000032320200a001f403e800000000,Америка=1f46460100dc02bc03e800000000464602006e03200258000000004646020014038403e800000000464601012703e802ee0000000046460100000384028a00000000,День рождения=20646401003d03e803e800000000646401007803e803e8000000005a5a01011303e803e8000000005a5a0100ae03e803e800000000646401003201f403e800000000646401000003e803e8000000,Годовщина=21323202015e01f403e800000000323202011303e803e800000000,Рождество=225a5a0100f003e803e8000000005a5a01003d03e803e800000000464601000003e803e8000000005a5a0100ae03e803e8000000005a5a01011303e803e800000000464601007803e803e800000000,День независимости=23505002000003e803e80000000046460200f003e803e800000000,Дивали=24464602000003e803e800000000464602003d03e803e800000000464602011303e803e80000000046460200f003e803e800000000464602007803e803e800000000,Холи=25464601011303e803e800000000464602000003e803e800000000464602003d03e803e8000000004646010154032003e8000000004646010140032003e800000000464601001e02ee03e800000000,День победы=265a5a020014006403e800000000464602000003e803e800000000,Пасха=275a5a020014006403e800000000464602000003e803e800000000323202015e01f403e800000000464602011303e803e800000000,Хэллоуин=28464601011303e803e800000000464601001e03e803e800000000');
if ($this->getProperty('sceneName') === '') $this->setProperty('sceneName', 'Спокойная');

$value    = $params['NEW_VALUE'] ?? null;

// --- Преобразование предустановок цвета
static $transform = [
    'red' => '#ff0000', 'green' => '#00ff00', 'blue' => '#0000ff',
    'white' => '#ffffff', 'yellow' => '#ffff00', 'cyan' => '#00ffff',
    'magenta' => '#ff00ff', 'orange' => '#ffa500', 'purple' => '#800080',
    'pink' => '#ffc0cb', 'lime' => '#00ff00',	
	'coolest' => 100,'cool' => 66, 'warm' => 33,'warmest' => 1
];
if (isset($transform[$value])) {
    $value = $transform[$value];
}

$property = $params['PROPERTY'] ?? null;
$source   = strtok($params['SOURCE'] ?? '', ' ');
$value = ($property === 'color')
    ? normalizeRange($value, 1, 100, 'color') // Если color
    : (($property === 'sceneName' || $property === 'scenesList')
        ? ($params['NEW_VALUE'] ?? null) // Если sceneName (сырое значение)
        : normalizeRange($value, 1, 100, 'number')); // Иначе (level, cct, colorLevel и т.д.)

// --- Защита от рекурсий и не верных данных
if ($source === 'worksUpdated' || is_null($value)) {
    if(is_null($value)){
        $this->setProperty($property, $this->getProperty($property . 'Saved'), 'worksUpdated');
    }
    return;
}

// --- Обработка Цвет / Яркость цвета
if ($property === 'color' || $property === 'colorLevel') {
    // Обновляем режим
    $this->setProperty('workMode', 'colour');
    // Генерация HSV-HEX
    $color = $property === 'color' ? $value : $this->getProperty('color');
    $colorLevel = $property === 'colorLevel' ? $value : $this->getProperty('colorLevel');
    $hsvHex = rgbToHSVhex($color, $colorLevel);
    $this->setProperty('colorWork', $hsvHex, 'propertysUpdated');
}

// --- Обработка Яркость / Теплота белого
if ($property === 'level' || $property === 'cct') {
    // Обновляем режим
	$this->setProperty('workMode', 'white');
	$this->setProperty($property . 'Work', round($value * 10), 'propertysUpdated');
}

// --- Обработка выбора сцены
if ($property === 'sceneName') {
    $foundScen = false;
    $sceneName = trim($value, " \t\n\r\0\x0B\"'");
    if ($sceneName === '' || $sceneName === 'unknown') return;
    $scenesList = trim($this->getProperty('scenesList'), " \t\n\r\0\x0B\"'");
    if ($scenesList === '') return;
    $sceneItems = preg_split('/\s*(?:,|\r\n|\n|\r)\s*/', $scenesList, -1, PREG_SPLIT_NO_EMPTY);
    //  поиск
    foreach ($sceneItems as $item) {
        [$name, $scene] = array_pad(explode('=', $item, 2), 2, null);
        if ($name === $sceneName && $scene !== null) {
            $foundScen = true;
            $this->setProperty('workMode', 'scene');
            $this->setProperty('sceneWork', $scene, 'propertysUpdated');
			break;
        }
    }
    // Сцена не найдена → откат
    if(!$foundScen){
        $this->setProperty('sceneName', $this->getProperty('sceneNameSaved'));
        return;
    }
}

if ($property !== 'scenesList') {
    if (!$this->getProperty('status')) {
        $this->setProperty('status', 1);
    }
	// Если значение реально изменилось — сохраняем
    if ($value != $this->getProperty($property)) {
        $this->setProperty($property, $value, 'worksUpdated');
    }
    $this->setProperty($property . 'Saved', $value);
    return;
}

// --- Обработка списка сцен scenesList
if ($property === 'scenesList') {
    $raw = $this->getProperty('scenesList');
    // Удаляем переносы
    $rawClean = str_replace(["\r", "\n"], '', $raw);
    // Разбиваем по запятым
    $items = explode(',', $rawClean);
    $valid = [];
    foreach ($items as $item) {
        $item = trim($item);
        if ($item === '' || strpos($item, '=') === false) continue;
        [$name, $val] = array_map('trim', explode('=', $item, 2));
        if ($name !== '' && $val !== '') {
            $valid[] = "$name=$val";
        }
    }
    $cleaned = implode(',', $valid);
    // Если изменилось — пишем и завершаемся (метод запустится заново)
    if ($cleaned !== $raw) {
        $this->setProperty('scenesList', $cleaned);
        return;
    }
    // --- Синхронизация команд
    $objectName = $this->object_title;
    $props = ['sceneName', 'dayScene', 'nightScene'];
    foreach ($props as $prop) {
        $rec = SQLSelectOne("
            SELECT * FROM commands
            WHERE LINKED_OBJECT='" . DBSafe($objectName) . "'
              AND LINKED_PROPERTY='" . DBSafe($prop) . "'
            LIMIT 1
        ");
        if (!$rec) continue;
        // Сцены из commands.DATA
        $cmdScenes = [];
        if ($rec['DATA'] !== '') {
            foreach (preg_split('/\R/', trim($rec['DATA'])) as $line) {
                $cmdScenes[] = trim(explode('=', $line)[0]);
            }
        }
        // Сцены из объекта
        $objScenes = [];
        foreach (explode(',', $cleaned) as $item) {
            $objScenes[] = trim(explode('=', $item)[0]);
        }
        // Сравнение и обновление
        if ($cmdScenes !== $objScenes) {
            $rec['DATA'] = implode("\r\n", $objScenes);
            SQLUpdate('commands', $rec);
        }
    }
    return;
}





// $source = strtok($params['SOURCE'], " ") ?? null;
// $property = $params['PROPERTY'] ?? null;
// $status = $this->getProperty('status') ?? 0;

// if($source === 'worksUpdated') return;

// $value = $params['NEW_VALUE'];
// $transform = array(
// 	'red'      => '#ff0000',
// 	'green'    => '#00ff00',
// 	'blue'     => '#0000ff',
// 	'white'    => '#ffffff',
// 	'yellow'   => '#ffff00',
// 	'cyan'     => '#00ffff',
// 	'magenta'  => '#ff00ff',
// 	'orange'   => '#ffa500',
// 	'purple'   => '#800080',
// 	'pink'     => '#ffc0cb',
// 	'lime'     => '#00ff00'
// 	);
// if (isset($transform[$value])) $value = $transform[$value];

// $value = normalizeRange($value,1);
// $colorSaved = $this->getProperty('colorSaved') ?? '#ffff00';
// $colorLevel = normalizeRange($this->getProperty('colorLevel'),1);
// $sceneName = trim($this->getProperty('sceneName'), " \t\n\r\0\x0B\"'");
// $sceneNameSaved = $this->getProperty('sceneNameSaved') ?? 'unknown';

// if(!is_null($value)) $this->setProperty($property , $value, 'worksUpdated');

// if(in_array($property, ['color', 'colorLevel']) && !is_null($value)){
// 	if($property == 'colorLevel')  $value = $colorSaved;
// 	$this->setProperty('workMode', 'colour');
// 	$hsvHex = rgbToHSVhex($value, $colorLevel)?: '003c03e801f4';
// 	$this->setProperty('colorWork', $hsvHex, 'propertysUpdated');
// 	if (!$status) $this->setProperty('status', 1);
// 	$this->setProperty('colorSaved', $value);
// }elseif(in_array($property, ['level', 'cct']) && is_numeric($value)){
// 	$this->setProperty('workMode', 'white');
// 	$this->setProperty($property . 'Work', round($value * 10), 'propertysUpdated');
// 	if (!$status) $this->setProperty('status', 1);
// 	$this->setProperty($property . 'Saved', $value);
// }elseif(in_array($property, ['sceneName']) && $sceneName != 'unknown'){
// 	// Получаем список сцен и очищаем его от пробелов, кавычек и переводов строк по краям
// 	$scenesList = trim($this->getProperty('scenesList'), " \t\n\r\0\x0B\"'");
// 	// Разбиваем на отдельные сцены (по запятой или новой строке)
// 	$sceneItems = preg_split('/\s*(?:,|\r\n|\n|\r)\s*/', $scenesList, -1, PREG_SPLIT_NO_EMPTY);
// 	$foundName = false;
// 	foreach ($sceneItems as $item) {
// 		// Каждая сцена имеет формат "Имя=Значение"
// 		$parts = explode('=', $item, 2); // ограничиваем на 2, чтобы значения с '=' не ломали парсинг
// 		if (count($parts) == 2) {
// 			$name  = $parts[0];
// 			$scene = $parts[1];
// 			// Если имя совпадает, обновляем sceneWork
// 			if ($name === $sceneName) {
// 				$foundName = true;
// 				$this->setProperty('workMode', 'scene');
// 				$this->setProperty('sceneWork', $scene, 'propertysUpdated');
// 				$this->setProperty('sceneNameSaved', $name);
// 				if (!$status) $this->setProperty('status', 1);
// 				break; // нашли нужную сцену, дальше не ищем
// 			}
// 		}
// 	}
// 	if(!$foundName){
// 		$this->setProperty('sceneName', $sceneNameSaved);
// 	}
// }