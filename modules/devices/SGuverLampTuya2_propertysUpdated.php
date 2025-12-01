<?php

 /** Обрабатывает изменения свойств устройства SGuverLampTuya2 (Лампа Гайвера Tuya)
 *   и синхронизирует его рабочее состояние ('Work' свойства), режимы ('work_mode')
 *   и сохраненные значения ('Saved' свойства).
 *
 * Функция выполняет следующие критические задачи:
 *
 * 1. Инициализация: Вызывает 'byDefault' для установки базовых свойств.
 * 2. Защита от рекурсии: Игнорирует вызовы с источником 'worksUpdated'.
 * 3. Преобразование значений: Конвертирует строковые названия цветов и температуры
 * (например, 'red', 'coolest') в числовые/HEX значения.
 * 4. Обработка 'presence': При изменении присутствия запускает таймер 'autoOff', если установлен.
 * 5. Обработка цветного света ('color', 'colorLevel'):
 * - Валидирует и нормализует входящие значения (HEX-цвет и уровень яркости 1-100).
 * - Устанавливает 'workMode' в 'colour'.
 * - Конвертирует RGB-HEX и 'colorLevel' в 12-символьный HSV-HEX код ('colorWork').
 * - Устанавливает 'status' в 1 (вкл.) и сохраняет новые значения.
 * 6. Обработка белого света ('level', 'cct'):
 * - Валидирует и нормализует яркость и теплоту белого (1-100).
 * - Устанавливает 'workMode' в 'white'.
 * - Преобразует значение в рабочий формат (умножение на 10, например, 'levelWork').
 * - Устанавливает 'status' в 1 (вкл.) и сохраняет новые значения.
 * 7. Обработка сцены ('sceneName'):
 * - Ищет код сцены в 'scenesList' по имени.
 * - При успешном поиске устанавливает 'workMode' в 'scene' и код сцены в 'sceneWork'.
 * - Устанавливает 'status' в 1 (вкл.) и сохраняет имя сцены ('sceneNameSaved').
 * - При неудаче откатывается к сохраненному имени сцены.
 * 8. Синхронизация списка сцен ('scenesList'):
 * - Очищает и нормализует список сцен в свойстве.
 * - **Критически:** Синхронизирует список сцен с данными (DATA) в командах
 * ('sceneName', 'dayScene', 'nightScene') для актуализации выпадающих списков в интерфейсе.
 *
 * @param array $params {
 * Входные параметры для обновления свойств устройства.
 *
 * @type string SOURCE    Источник изменения свойства (например, 'user', 'propertysUpdated').
 * @type string PROPERTY  Имя изменяемого свойства ('level', 'cct', 'color', 'colorLevel', 'sceneName', 'scenesList').
 * @type mixed  NEW_VALUE Новое значение свойства (число или строка).
 * }
 *
 * @global string $object_title Имя объекта устройства (для SQL-запросов синхронизации команд).
 *
 * @property string color       Текущий HEX-цвет (например, #ff00ff).
 * @property int    colorLevel  Текущий уровень яркости цветного света (1-100).
 * @property int    level       Текущий уровень яркости белого света (1-100).
 * @property int    cct         Текущая цветовая температура белого света (1-100).
 * @property string sceneName   Текущее имя активной сцены.
 * @property string scenesList  Строка списка сцен в формате "Имя=Значение,..."
 * @property int    status      Включено (1) или Выключено (0).
 *
 * @property string workMode   Текущий режим работы: 'white', 'colour', или 'scene'.
 * @property string colorWork   12-символьный HSV-HEX код для отправки устройству.
 * @property string levelWork   Рабочее значение яркости белого света (level * 10).
 * @property string cctWork     Рабочее значение цветовой температуры (cct * 10).
 * @property string sceneWork   Рабочий код сцены для отправки устройству.
 *
 * @return void
 *
 * @see normalizeRange()
 * @see rgbToHSVhex()
 * @uses autoOff() Внешняя функция для таймера выключения.
 */
//


// --- Дефолтные свойства
$this->callMethod('byDefault');

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
$value = $property === 'color' ? normalizeRange($value, 1, 100, 'color') : 
         $property !== 'sceneName' ? 
         $property !== 'presence' ? normalizeRange($value, 1, 100, 'number') : 
         normalizeRange($value, 0, 1, 'number') : 
         $params['NEW_VALUE'] ?? null;

// --- Защита от рекурсий и не верных данных
if ($source === 'worksUpdated' || is_null($value)) {
    if(is_null($value)){
        $this->setProperty($property, $this->getProperty($property . 'Saved'), 'worksUpdated');
    }
    return;
}

// --- Обработка presence
if ($property === 'presence') {
    if ((int)$this->getProperty('timerOff') > 0) {
        autoOff($this);
    }
    return;
}

// --- Обработка Цвет / Яркость цвета
if ($property === 'color' || $property === 'colorLevel') {
    // Обновляем режим
    $this->setProperty('workMode', 'colour');
    // Если значение реально изменилось — сохраняем
    if ($value != $this->getProperty($property)) {
        $this->setProperty($property, $value, 'worksUpdated');
    }
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
	// Если значение реально изменилось — сохраняем
    if ($value != $this->getProperty($property)) {
        $this->setProperty($property, $value, 'worksUpdated');
    }
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
    if ($source !== 'autoMode') {
        $this->setProperty('flag', 1);
        $this->setProperty($property . 'Saved', $value);
    }
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


// $value = normalizeRange($value,1);
// $colorSaved = $this->getProperty('colorSaved') ?? '#ffff00';
// $colorLevel = normalizeRange($this->getProperty('colorLevel'),1);
// $sceneName = trim($this->getProperty('sceneName'), " \t\n\r\0\x0B\"'");
// $sceneNameSaved = $this->getProperty('sceneNameSaved') ?? 'unknown';

// $source = strtok($params['SOURCE'], " ") ?? null;
// $property = $params['PROPERTY'] ?? null;
// $status = $this->getProperty('status') ?? 0;
// $flag = $this->getProperty('flag') ?? 0;

// if($source === 'worksUpdated') return;
// if($source !== 'autoMode'){
// 	if(!$flag) $this->setProperty('flag', 1);
// }
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
// 	'lime'     => '#00ff00',
// 	'coolest' => 100,
// 	'cool'    => 66,
// 	'warm'    => 33,
// 	'warmest' => 1,
// 	);
// if (isset($transform[$value])) $value = $transform[$value];

// if(!is_null($value)) $this->setProperty($property , $value, 'worksUpdated');

// if(in_array($property, ['color', 'colorLevel']) && !is_null($value)){
// 	if($property == 'colorLevel')  $value = $colorSaved;
// 	$this->setProperty('workMode', 'colour');
// 	$hsvHex = rgbToHSVhex($value, $colorLevel)?: '003c03e801f4';
// 	$this->setProperty('colorWork', $hsvHex, 'propertysUpdated');
// 	if (!$status) $this->setProperty('status', 1);
// 	$this->setProperty('colorSaved', $value);
// 	$this->setProperty('colorLevelSaved', $colorLevel);
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
// 				$this->setProperty('work_mode', 'scene');
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
// }elseif(in_array($property, ['scenesList'])){
// 	$objectName = $this->object_title;
// 	// ---------------------------------------------
// 	// 1. Очистка и валидирование scenesList
// 	// ---------------------------------------------
// 	$rawScenesList = $this->getProperty('scenesList');
// 	// Удаляем переносы строк — безопасно
// 	$rawScenesListNormalized = str_replace(["\r", "\n"], '', $rawScenesList);
// 	// Разбиваем ТОЛЬКО по запятым
// 	$sceneItems = explode(',', $rawScenesListNormalized);
// 	$cleanItems = [];
// 	foreach ($sceneItems as $item) {
// 		// trim только по краям
// 		$item = trim($item);
// 		if ($item === '') continue; // пустой элемент - невалиден
// 		// Должен быть знак '='
// 		if (strpos($item, '=') === false) continue;
// 		// Разделяем имя и значение
// 		[$name, $val] = explode('=', $item, 2);
// 		// Тоже trim только по краям
// 		$name = trim($name);
// 		$val  = trim($val);
// 		// Проверяем валидность
// 		if ($name === '' || $val === '') continue;
// 		// Имя и значение оставляем как есть (внутренние пробелы и табы не трогаем)
// 		$cleanItems[] = $name . '=' . $val;
// 	}
// 	// Собираем строку обратно
// 	$cleanScenesList = implode(',', $cleanItems);
// 	// Если строка изменилась — записываем и выходим,
// 	// метод запустится снова автоматически
// 	if ($cleanScenesList !== $rawScenesList) {
// 		$this->setProperty('scenesList', $cleanScenesList);
// 		return;
// 	}
// 	// ---------------------------------------------
// 	// 2. Синхронизация с таблицей commands
// 	// ---------------------------------------------
// 	$props = ['sceneName', 'dayScene', 'nightScene'];
// 	foreach ($props as $propName) {
// 		// Ищем команду
// 		$rec = SQLSelectOne("
// 			SELECT *
// 			FROM commands
// 			WHERE LINKED_OBJECT='" . DBSafe($objectName) . "'
// 			AND LINKED_PROPERTY='" . DBSafe($propName) . "'
// 			LIMIT 1
// 		");
// 		if (!$rec) continue;
// 		// Список сцен из commands.DATA
// 		$commandsScenes = [];
// 		if (!empty($rec['DATA'])) {
// 			$sceneItems = preg_split('/\r\n|\n|\r/', trim($rec['DATA']));
// 			foreach ($sceneItems as $item) {
// 				$parts = explode('=', $item, 2);
// 				$commandsScenes[] = trim($parts[0]);
// 			}
// 		}
// 		// Список сцен из объекта
// 		$scenesList = $cleanScenesList; // уже очищенный вариант
// 		$sceneItems = explode(',', $scenesList);
// 		$objectScenes = [];
// 		foreach ($sceneItems as $item) {
// 			$parts = explode('=', $item, 2);
// 			if (!empty($parts[0])) {
// 				$objectScenes[] = trim($parts[0]);
// 			}
// 		}
// 		// Обновляем commands.DATA если отличается
// 		if ($commandsScenes !== $objectScenes) {
// 			$rec['DATA'] = implode("\r\n", $objectScenes);
// 			SQLUpdate('commands', $rec);
// 		}
// 	}
// 	return;
// }

