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
 *   - Рабочая сцена устанавливается в workScene
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

$source = strtok($params['SOURCE'], " ") ?? null;
$property = $params['PROPERTY'] ?? null;
$status = $this->getProperty('status') ?? 0;

if($source === 'worksUpdated') return;

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
	'lime'     => '#00ff00'
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
	$this->setProperty('work_mode', 'colour');
	$hsvHex = rgbToHSVhex($value, $colorLevel)?: '003c03e801f4';
	$this->setProperty('colorWork', $hsvHex, 'propertysUpdated');
	if (!$status) $this->setProperty('status', 1);
	$this->setProperty('colorSaved', $value);
}elseif(in_array($property, ['level', 'cct']) && is_numeric($value)){
	$this->setProperty('work_mode', 'white');
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
			// Если имя совпадает, обновляем workScene
			if ($name === $sceneName) {
				$foundName = true;
				$this->setProperty('work_mode', 'scene');
				$this->setProperty('workScene', $scene, 'propertysUpdated');
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

	// Какие свойства синхронизируем с таблицей commands
	$props = ['sceneName', 'dayScene', 'nightScene'];

	foreach ($props as $propName) {

		// 1. Ищем команду для данного свойства
		$rec = SQLSelectOne("
			SELECT *
			FROM commands
			WHERE LINKED_OBJECT='" . DBSafe($objectName) . "'
			AND LINKED_PROPERTY='" . DBSafe($propName) . "'
			LIMIT 1
		");

		if (!$rec) {
			//DebMes("Команда {$propName} не найдена для объекта {$objectName}");
			continue;
		}

		// 2. Список сцен из commands.DATA
		$commandsScenes = [];
		if (!empty($rec['DATA'])) {
			$sceneItems = preg_split('/\r\n|\n|\r/', trim($rec['DATA']));
			foreach ($sceneItems as $item) {
				$parts = explode('=', $item, 2);
				$commandsScenes[] = trim($parts[0]);
			}
		}

		// 3. Список сцен из объекта (один общий scenesList)
		$scenesList = trim($this->getProperty('scenesList'), " \t\n\r\0\x0B\"'");
		$sceneItems = preg_split('/\s*(?:,|\r\n|\n|\r)\s*/', $scenesList, -1, PREG_SPLIT_NO_EMPTY);

		$objectScenes = [];
		foreach ($sceneItems as $item) {
			$parts = preg_split('/\s*=\s*/', $item, 2);
			if (!empty($parts[0])) $objectScenes[] = $parts[0];
		}

		// 4. Сравнение и обновление
		if ($commandsScenes !== $objectScenes) {
			$rec['DATA'] = implode("\r\n", $objectScenes);
			SQLUpdate('commands', $rec);
			//DebMes("Обновлены {$propName} для {$objectName}");
		} else {
			//DebMes("{$propName} уже актуально, изменений нет");
		}
	}

	return;
}