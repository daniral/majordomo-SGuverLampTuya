# 💡 SGuverLampTuya2 (Tuya Guver Lamp for MajorDoMo)

> Управление цветом, яркостью, CCT, сценами и автоматическими режимами работы лампочек Tuya/Guver через MajorDoMo.

<p align="center">
  <img src="https://img.shields.io/badge/PHP-7.4%2B-blue" />
  <img src="https://img.shields.io/badge/MajorDoMo-Device%20Module-green" />
  <img src="https://img.shields.io/badge/Status-Production-success" />
  <img src="https://img.shields.io/badge/Type-Smart%20Lighting-yellow" />
</p>

---

## 📘 Описание

**`SGuverLampTuya2`** — расширенный класс устройства MajorDoMo для ламп *Guver Lamp (Tuya)*.
Поддерживает:

* Цветной свет
* Белый свет (яркость + температура CCT)
* Сцены
* Автовключение и автовыключение
* Работа по датчику движения, света, времени или по солнцу
* Авто-режимы День/Ночь/24 часа
* Создание и удаление меню управления в MajorDoMo

При первом запуске автоматически создаются все необходимые свойства.

---

# ⚙️ Привязка свойств

| Tuya поле      | Свойство MajorDoMo |
| -------------- | ------------------ |
| `switch_led`   | `status`           |
| `work_mode`    | `workMode`         |
| `bright_value` | `levelWork`        |
| `temp_value`   | `cctWork`          |
| `colour_data`  | `colorWork`        |
| `scene_data`   | `sceneWork`        |

---

# 🚦 Обычный режим

Включение лампы:

```php
callMethod('ObjectName.turnOn');
```

Если параметры не указаны — берутся сохранённые (`...Saved`) или значения по умолчанию:

| Параметр          | Если пусто |
| ----------------- | ---------- |
| `levelSaved`      | 100        |
| `cctSaved`        | 100        |
| `colorSaved`      | #FFFFFF    |
| `colorLevelSaved` | 100        |
| `sceneNameSaved`  | Спокойная  |

Включение с параметрами:

```php
callMethod('Object.turnOn', [
  'level'      => 1..100,
  'cct'        => 1..100,
  'color'      => '#RRGGBB',
  'colorLevel' => 1..100,
  'sceneName'  => 'Название сцены'
]);
```

При обычном включении ставится `flag=1`, блокируя авто-режим.

---

# 🤖 Авто-режим

Запуск:

```php
callMethod('Object.turnOn', ['autoMode' => 1]);
```

Особенности:

* Работает таймер `timerOff` — авто-выключение.
* Пока `presence=1` — не выключается.
* При переходе `presence 1→0` — запускается `autoOff()`.
* Три режима работы: **День**, **Ночь**, **24 часа**

### Режимы по источнику (`workingBy`)

| Значение | Описание             |
| -------- | -------------------- |
| `1`      | По времени           |
| `2`      | По солнцу            |
| `3`      | По датчику освещения |

### По солнцу:

Требует свойства:

* `sunriseTime`
* `sunsetTime`

Можно корректировать:

* `addTimeSunrise` + `signSunrise`
* `addTimeSunset` + `signSunset`

### По датчику освещения:

Использует:

* свойство `illuminance`
* порог `illuminanceMax`

---

# 🎨 Работа с цветом

## Форматы цвета

Цвет может задаваться:

### ✔ HEX-кодами

* `#RRGGBB`
* `#RGB`

### ✔ Цветовыми пресетами

Используемые имена:

```
red, green, blue, white, yellow,
cyan, magenta, orange, purple,
pink, lime
```

---

# 🎬 Сцены

## 📄 `scenesList`

Формат:

```
Название=Код,Название2=Код2,...
```

Если пусто — создаётся список по умолчанию.

## 🏷 `sceneName`

Имя текущей активной сцены.
Если указанной сцены нет — используется сохранённая.

---

# 🔧 Методы

### Отключение

```php
callMethod('Object.turnOff');
```

Сбрасывает `flag=0`
           `illuminanceFlag = 0`

---

## Переключение

```php
callMethod('Object.switch');
```

Поведение:

* Если лампа **в авто-режиме** — включит сохранённые значения.
* Если **выключена** — включит сохранённые значения.
* Если **включена вручную** — выключит.

---

## Управление цветом

| Метод            | Описание                 |
| ---------------- | ------------------------ |
| `setColor`       | Установить цвет          |
| `setColorLevel`  | Установить яркость цвета |
| `colorLevelDown` | Уменьшить                |
| `colorLevelUp`   | Увеличить                |

Все методы → `flag=1`.

---

## Управление белым светом (яркость)

| Метод       | Описание           |
| ----------- | ------------------ |
| `setLevel`  | Установить яркость |
| `levelDown` | Уменьшить          |
| `levelUp`   | Увеличить          |

Все методы → `flag=1`.

---

## Управление CCT

| Метод     | Описание               |
| --------- | ---------------------- |
| `setCct`  | Установить температуру |
| `cctDown` | Уменьшить              |
| `cctUp`   | Увеличить              |

Поддерживаются пресеты:

```
coolest, cool, warm, warmest
```

---

## Меню управления

Создать меню:

```php
callMethod('Object.createCommandsMenu');
```

Удалить меню:

```php
callMethod('Object.deleteCommandsMenu');
```

---

# 📝 Поведение при первом запуске

Метод `turnOn` автоматически создаёт все недостающие свойства устройства.

---