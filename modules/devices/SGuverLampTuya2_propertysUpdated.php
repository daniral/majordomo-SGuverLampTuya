<?php

/**
 * Обновление свойств устройства SGuverLampTuya2 (Лампа Гайвера Tuya).
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
//

// --- Дефолтные свойства
$this->callMethod('byDefault');

$source = strtok($params['SOURCE'], " ") ?? null;
$property = $params['PROPERTY'] ?? null;
$status = $this->getProperty('status') ?? 0;
$flag = $this->getProperty('flag') ?? 0;

if($source === 'worksUpdated') return;
if($source !== 'autoMode'){
	if(!$flag) $this->setProperty('flag', 1);
}
$value = $params['NEW_VALUE'];
$transform = array(
	'red'      => '#ff0000',
	'green'    => '#00ff00',
	'blue'     => '#0000ff',
	'white'    => '#ffffff',
	'yellow'   => '#ffff00',
	'cyan'     => '#00ffff',
	'magenta'  => '#ff00ff',
	'orange'   => '#ffa500',
	'purple'   => '#800080',
	'pink'     => '#ffc0cb',
	'lime'     => '#00ff00',
	'coolest' => 100,
	'cool'    => 66,
	'warm'    => 33,
	'warmest' => 1,
	);
if (isset($transform[$value])) $value = $transform[$value];

$value = normalizeRange($value,1);
$colorSaved = $this->getProperty('colorSaved') ?? '#ffff00';
$colorLevel = normalizeRange($this->getProperty('colorLevel'),1);
$sceneName = trim($this->getProperty('sceneName'), " \t\n\r\0\x0B\"'");
$sceneNameSaved = $this->getProperty('sceneNameSaved') ?? 'unknown';

if(!is_null($value)) $this->setProperty($property , $value, 'worksUpdated');

if(in_array($property, ['color', 'colorLevel']) && !is_null($value)){
	if($property == 'colorLevel')  $value = $colorSaved;
	$this->setProperty('modeWork', 'colour');
	$hsvHex = rgbToHSVhex($value, $colorLevel)?: '003c03e801f4';
	$this->setProperty('colorWork', $hsvHex, 'propertysUpdated');
	if (!$status) $this->setProperty('status', 1);
	$this->setProperty('colorSaved', $value);
	$this->setProperty('colorLevelSaved', $colorLevel);
}elseif(in_array($property, ['level', 'cct']) && is_numeric($value)){
	$this->setProperty('modeWork', 'white');
	$this->setProperty($property . 'Work', round($value * 10), 'propertysUpdated');
	if (!$status) $this->setProperty('status', 1);
	$this->setProperty($property . 'Saved', $value);
}elseif(in_array($property, ['sceneName']) && $sceneName != 'unknown'){
	// Получаем список сцен и очищаем его от пробелов, кавычек и переводов строк по краям
	$scenesList = trim($this->getProperty('scenesList'), " \t\n\r\0\x0B\"'");
	// Разбиваем на отдельные сцены (по запятой или новой строке)
	$sceneItems = preg_split('/\s*(?:,|\r\n|\n|\r)\s*/', $scenesList, -1, PREG_SPLIT_NO_EMPTY);
	$foundName = false;
	foreach ($sceneItems as $item) {
		// Каждая сцена имеет формат "Имя=Значение"
		$parts = explode('=', $item, 2); // ограничиваем на 2, чтобы значения с '=' не ломали парсинг
		if (count($parts) == 2) {
			$name  = $parts[0];
			$scene = $parts[1];
			// Если имя совпадает, обновляем sceneWork
			if ($name === $sceneName) {
				$foundName = true;
				$this->setProperty('modeWork', 'scene');
				$this->setProperty('sceneWork', $scene, 'propertysUpdated');
				$this->setProperty('sceneNameSaved', $name);
				if (!$status) $this->setProperty('status', 1);
				break; // нашли нужную сцену, дальше не ищем
			}
		}
	}
	if(!$foundName){
		$this->setProperty('sceneName', $sceneNameSaved);
	}
}elseif(in_array($property, ['scenesList'])){
	$objectName = $this->object_title;
	// ---------------------------------------------
	// 1. Очистка и валидирование scenesList
	// ---------------------------------------------
	$rawScenesList = $this->getProperty('scenesList');
	// Удаляем переносы строк — безопасно
	$rawScenesListNormalized = str_replace(["\r", "\n"], '', $rawScenesList);
	// Разбиваем ТОЛЬКО по запятым
	$sceneItems = explode(',', $rawScenesListNormalized);
	$cleanItems = [];
	foreach ($sceneItems as $item) {
		// trim только по краям
		$item = trim($item);
		if ($item === '') continue; // пустой элемент - невалиден
		// Должен быть знак '='
		if (strpos($item, '=') === false) continue;
		// Разделяем имя и значение
		[$name, $val] = explode('=', $item, 2);
		// Тоже trim только по краям
		$name = trim($name);
		$val  = trim($val);
		// Проверяем валидность
		if ($name === '' || $val === '') continue;
		// Имя и значение оставляем как есть (внутренние пробелы и табы не трогаем)
		$cleanItems[] = $name . '=' . $val;
	}
	// Собираем строку обратно
	$cleanScenesList = implode(',', $cleanItems);
	// Если строка изменилась — записываем и выходим,
	// метод запустится снова автоматически
	if ($cleanScenesList !== $rawScenesList) {
		$this->setProperty('scenesList', $cleanScenesList);
		return;
	}
	// ---------------------------------------------
	// 2. Синхронизация с таблицей commands
	// ---------------------------------------------
	$props = ['sceneName', 'dayScene', 'nightScene'];
	foreach ($props as $propName) {
		// Ищем команду
		$rec = SQLSelectOne("
			SELECT *
			FROM commands
			WHERE LINKED_OBJECT='" . DBSafe($objectName) . "'
			AND LINKED_PROPERTY='" . DBSafe($propName) . "'
			LIMIT 1
		");
		if (!$rec) continue;
		// Список сцен из commands.DATA
		$commandsScenes = [];
		if (!empty($rec['DATA'])) {
			$sceneItems = preg_split('/\r\n|\n|\r/', trim($rec['DATA']));
			foreach ($sceneItems as $item) {
				$parts = explode('=', $item, 2);
				$commandsScenes[] = trim($parts[0]);
			}
		}
		// Список сцен из объекта
		$scenesList = $cleanScenesList; // уже очищенный вариант
		$sceneItems = explode(',', $scenesList);
		$objectScenes = [];
		foreach ($sceneItems as $item) {
			$parts = explode('=', $item, 2);
			if (!empty($parts[0])) {
				$objectScenes[] = trim($parts[0]);
			}
		}
		// Обновляем commands.DATA если отличается
		if ($commandsScenes !== $objectScenes) {
			$rec['DATA'] = implode("\r\n", $objectScenes);
			SQLUpdate('commands', $rec);
		}
	}
	return;
}

