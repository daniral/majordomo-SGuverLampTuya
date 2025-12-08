<?php
/**
 * Обработчик голосовых команд для устройств типа SGuverLampTuya.
 *
 * Данный блок кода анализирует текст команды пользователя ($command)
 * и формирует управляющие действия ($run_code) для умной RGB-ленты
 * SGuverLampTuya в MajorDoMo.
 *
 * Поддерживаются следующие группы команд:
 *
 * ----------------------------------------------------------------------
 * 1. Управление питанием (вкл/выкл/переключить)
 * ----------------------------------------------------------------------
 *  Распознаётся по словарям:
 *    - LANG_DEVICES_PATTERN_TURNON
 *    - LANG_DEVICES_PATTERN_TURNOFF
 *    - LANG_DEVICES_PATTERN_SWITCH
 *
 *  Действия:
 *    - turnOn()
 *    - turnOff()
 *    - switch()
 *
 * ----------------------------------------------------------------------
 * 2. Управление ЯРКОСТЬЮ (белого света)
 * ----------------------------------------------------------------------
 *  Ключевые шаблоны: LANG_SGuverLampTuya_PATTERN_BRIGHTNESS
 *
 *  Поддерживаемые варианты команд:
 *
 *  ● Установка абсолютного значения  
 *      "яркость 70", "поставь 30", "сделай 100", "на 15%"
 *      Распознаются числа от 1 до 100.
 *
 *  ● Увеличение яркости  
 *      "ярче", "прибавь", "увеличь", "добавь", "больше"
 *      Увеличивает текущий уровень на +10.
 *
 *  ● Уменьшение яркости  
 *      "тусклее", "убавь", "меньше", "приглуши", "потемнее"
 *      Уменьшает уровень на -10.
 *
 *  Вызывает метод:
 *      setLevel(value)
 *
 * ----------------------------------------------------------------------
 * 3. Управление ЦВЕТОМ
 * ----------------------------------------------------------------------
 *  Словарь: LANG_SGuverLampTuya_PATTERN_COLOR
 *
 *  Поддерживаемые цвета:
 *      красный, зелёный, синий, белый, жёлтый,
 *      голубой, пурпурный, оранжевый, фиолетовый,
 *      розовый, лайм.
 *
 *  Определяет цвет по части слова.
 *
 *  Действие:
 *      setColor(value => '<color>')
 *
 *  Также автоматически сохраняет предыдущий цвет,
 *  формируя $opposite_code для отмены команды.
 *
 * ----------------------------------------------------------------------
 * 4. Управление ЯРКОСТЬЮ ЦВЕТНОГО СВЕТА (colorLevel)
 * ----------------------------------------------------------------------
 *  Словарь: LANG_SGuverLampTuya_PATTERN_COLOR_BRIGHTNESS
 *
 *  Поддержка:
 *      - установка числового значения: "цвет на 50"
 *      - увеличение: "цвет ярче", "яркость цвета больше"
 *      - уменьшение: "цвет темнее", "уменьши яркость цвета"
 *
 *  Управляемый параметр: colorLevel (0–100)
 *
 *  Действие:
 *      setColorLevel(value)
 *
 * ----------------------------------------------------------------------
 * 5. Управление СЦЕНАМИ
 * ----------------------------------------------------------------------
 *  Шаблон: LANG_SGuverLampTuya_PATTERN_SCENE
 *
 *  Список сцен берётся из свойства:
 *      $linked_object.scenesList
 *
 *  Формат списка сцен:
 *      Имя1=Значение1,Имя2=Значение2,...
 *
 *  Обработчик ищет совпадение имени сцены в команде
 *  и устанавливает свойство:
 *      sceneName
 *
 *  Сохраняет предыдущую сцену в $opposite_code.
 *
 * ----------------------------------------------------------------------
 *
 * Глобальные переменные, модифицируемые обработчиком:
 *
 * @global string $run_code        Генерируемый код действия
 * @global string $opposite_code   Код отмены действия (если возможно)
 * @global int    $processed       Флаг успешной обработки
 * @global int    $reply_confirm   Нужно ли голосовое подтверждение
 *
 * ----------------------------------------------------------------------
 *
 * Ожидаемые внешние входные параметры:
 *
 * @param string $device_type    Тип устройства (должен быть 'SGuverLampTuya')
 * @param string $command        Исходная голосовая команда пользователя
 * @param string $linked_object  Имя объекта MajorDoMo
 * @param string $device_title   Человекочитаемое имя устройства
 * @param string $add_phrase     Фраза для TTS (например: "в комнате")
 *
 * ----------------------------------------------------------------------
 *
 * Алгоритм:
 *   1. Проверяет, относится ли команда к данному устройству.
 *   2. Сравнивает текст с паттернами (регэкспами).
 *   3. При совпадении формирует действие ($run_code).
 *   4. При необходимости — действие отмены ($opposite_code).
 *   5. Устанавливает $processed = 1.
 *   6. В отдельных случаях включает подтверждение ($reply_confirm = 1).
 *
 * ----------------------------------------------------------------------
 *
 * @return void
 */


if ($device_type == 'SGuverLampTuya') {

    // --- ВКЛ / ВЫКЛ / ПЕРЕКЛЮЧИТЬ ---
    if (preg_match('/' . LANG_DEVICES_PATTERN_TURNON . '/uis', $command)) {
        sayReplySafe(LANG_TURNING_ON . ' ' . $device_title . $add_phrase, 2);
        $run_code      .= "callMethod('$linked_object.turnOn');";
        $opposite_code .= "callMethod('$linked_object.turnOff');";
        $processed = 1;
    }
    elseif (preg_match('/' . LANG_DEVICES_PATTERN_TURNOFF . '/uis', $command)) {
        sayReplySafe(LANG_TURNING_OFF . ' ' . $device_title . $add_phrase, 2);
        $run_code      .= "callMethod('$linked_object.turnOff');";
        $opposite_code .= "callMethod('$linked_object.turnOn');";
        $processed = 1;
    }
    elseif (preg_match('/' . LANG_DEVICES_PATTERN_SWITCH . '/uis', $command)) {
        sayReplySafe(LANG_SWITCH . ' ' . $device_title . $add_phrase, 2);
        $run_code      .= "callMethod('$linked_object.switch');";
        $opposite_code .= "callMethod('$linked_object.switch');";
        $processed = 1;
    }

    // --- ЯРКОСТЬ ---
    elseif (preg_match('/' . LANG_SGuverLampTuya_PATTERN_BRIGHTNESS . '/uis', $command)) {
        $currentLevel = (int)getGlobal("$linked_object.level");
        $step = 10;
        if (preg_match('/(?:\s)(\d{1,2}|100)(?:%|\s|$)/uis', $command, $matches)) {
            $value = (int)$matches[1];
        }
        elseif (preg_match('/(ярче|увелич|добав|больше)/uis', $command)) {
            $value = min(100, $currentLevel + $step);
        }
        elseif (preg_match('/(тусклее|меньше|приглуш|потемн)/uis', $command)) {
            $value = max(0, $currentLevel - $step);
        }
        if (isset($value)) {
            $run_code      .= "callMethod('$linked_object.setLevel', array('value' => $value));";
            $opposite_code .= "callMethod('$linked_object.setLevel', array('value' => $value));";
            $processed = 1;
            $reply_confirm = 1;
        }
    }

    // --- ТЕМПЕРАТУРА БЕЛОГО СВЕТА (white temp) ---
    elseif (preg_match('/' . LANG_SGuverLampTuya_PATTERN_TEMPERATURE . '/uis', $command)) {
        $currentLevel = (int)getGlobal("$linked_object.cct");
        $step = 10; // Шаг изменения по умолчанию
        
        // Установка по числу: "температура 70" (от 0 до 100)
        if (preg_match('/(?:\s)(\d{1,2}|100)(?:%|\s|$)/uis', $command, $matches)) {
            $value = (int)$matches[1];
        }
        // Установка по абсолютному значению
        elseif (preg_match('/(холодн|синее|прохладн)/uis', $command)) {
            // Холодный белый (ближе к 100%)
            $value = 100;
        }
        elseif (preg_match('/(тепл|желт)/uis', $command)) {
            // Теплый белый (ближе к 0%)
            $value = 0;
        }
        elseif (preg_match('/(нейтрал|средн)/uis', $command)) {
            // Нейтральный белый (примерно 50%)
            $value = 50;
        }
        // Увеличение: "температура выше", "холоднее"
        elseif (preg_match('/(выше|холодн|добав|больше|увелич)/uis', $command)) {
            $value = min(100, $currentLevel + $step);
        }
        // Уменьшение: "температура ниже", "теплее"
        elseif (preg_match('/(ниже|тепл|меньше|уменьш)/uis', $command)) {
            $value = max(0, $currentLevel - $step);
        }

        if (isset($value)) {
            $run_code      .= "callMethod('$linked_object.setCct', array('value' => $value));";
            // Для противоположного действия используем текущее значение, чтобы можно было отменить
            $opposite_code .= "callMethod('$linked_object.setCct', array('value' => $currentLevel));";
            $processed = 1;
            $reply_confirm = 1;
        }
    }

    // --- ЦВЕТ ---
    elseif (preg_match('/' . LANG_SGuverLampTuya_PATTERN_COLOR . '/uis', $command)) {
        $colors = array(
            'красн' => 'red', 'зел' => 'green', 'син' => 'blue',
            'бел' => 'white', 'ж[её]лт' => 'yellow',
            'голуб' => 'cyan', 'пурпур' => 'purple', 'оранж' => 'orange',
            'фиолет' => 'violet', 'розов' => 'pink', 'лайм' => 'lime'
        );
        $newColor = null;
        foreach ($colors as $pattern => $clr) {
            if (preg_match('/' . $pattern . '/uis', $command)) {
                $newColor = $clr;
                break;
            }
        }
        if ($newColor) {
            $prevColor = getGlobal("$linked_object.color");
            $run_code      .= "callMethod('$linked_object.setColor', array('value' => '$newColor'));" ;
            if ($prevColor) $opposite_code .= "callMethod('$linked_object.setColor', array('value' => '$prevColor'));" ;
            $processed = 1;
            $reply_confirm = 1;
        }
    }
    // --- ЯРКОСТЬ ЦВЕТНОГО СВЕТА (colorLevel) ---
    elseif (preg_match('/' . LANG_SGuverLampTuya_PATTERN_COLOR_BRIGHTNESS . '/uis', $command)) {

        $currentLevel = (int)getGlobal("$linked_object.colorLevel");
        $step = 10;

        // Установка по числу: "яркость цвета 70"
        if (preg_match('/(?:\s)(\d{1,2}|100)(?:%|\s|$)/uis', $command, $matches)) {
            $value = (int)$matches[1];
        }
        // Увеличение: "цвет ярче", "увеличь яркость цвета"
        elseif (preg_match('/(ярче|увелич|добав|больше)/uis', $command)) {
            $value = min(100, $currentLevel + $step);
        }
        // Уменьшение: "цвет темнее", "уменьши яркость цвета"
        elseif (preg_match('/(тусклее|меньше|приглуш|потемн)/uis', $command)) {
            $value = max(0, $currentLevel - $step);
        }

        if (isset($value)) {
            $run_code      .= "callMethod('$linked_object.setColorLevel', array('value' => $value));";
            $opposite_code .= "callMethod('$linked_object.setColorLevel', array('value' => $currentLevel));";
            $processed = 1;
            $reply_confirm = 1;
        }
    }

    // --- СЦЕНЫ ---
    elseif (preg_match('/' . LANG_SGuverLampTuya_PATTERN_SCENE . '/uis', $command)) {
        $scenesRaw = getGlobal("$linked_object.scenesList");
        if ($scenesRaw) {
            // Разделяем сцены по запятой или переводу строки, сохраняем пробелы внутри имен
            $sceneItems = preg_split('/\s*(?:,|\r\n|\n|\r)\s*/', $scenesRaw, -1, PREG_SPLIT_NO_EMPTY);
            $commandLower = mb_strtolower(trim($command), 'UTF-8');

            foreach ($sceneItems as $item) {
                $parts = explode('=', $item, 2);
                if (count($parts) === 2) {
                    $sceneName = trim($parts[0]);
                    $sceneValue = trim($parts[1]);

                    // Приводим имя сцены к нижнему регистру для сравнения
                    $sceneNameLower = mb_strtolower($sceneName, 'UTF-8');

                    // Совпадение только по точной фразе
                    if (mb_stripos($commandLower, $sceneNameLower) !== false) {
                        $prevScene = getGlobal("$linked_object.sceneName");
                        $run_code .= "setProperty('$linked_object.sceneName', '$sceneName');";
                        $opposite_code .= "setProperty('$linked_object.sceneName', '$prevScene');";
                        $processed = 1;
                        $reply_confirm = 1;
                        break; // нашли сцену — дальше не ищем
                    }
                }
            }
        }
    }
}
