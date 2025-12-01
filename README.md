# 💡 Tuya Guver Lamp - 2
## Простое устройство для MajorDoMo

<p align="center">
  <img src="https://img.shields.io/badge/PHP-7.4%2B-blue" />
  <img src="https://img.shields.io/badge/MajorDoMo-Device%20Module-green" />
  <img src="https://img.shields.io/badge/Status-Production-success" />
  <img src="https://img.shields.io/badge/Type-Smart%20Lighting-yellow" />
  <img src="https://img.shields.io/badge/Version-2.0-orange" />
</p>
---

## 📘 Описание

**`SGuverLampTuya`** — расширяет класс *SControllers* 
> Простое устройство *Guver Lamp (Tuya)* для MajorDoMo.
---
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
| `colorSaved`      | #FFFFFF  |
| `colorLevelSaved` | 100        |
| `sceneNameSaved`  | Спокойная  |

Включение с параметрами:
```php
callMethod('Object.turnOn', [
  'level'      => 1..100,
  'cct'        => 1..100 или присеты,
  'color'      => '#RRGGBB' , '#RGB' или присеты,
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
* При изменении `presence` = 0 — запускается `autoOff()`.
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

Не проверялось. Нету датчика.
---

# 🎨 Работа с цветом

## Цвет может задаваться:

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

## Отключение 
```php
callMethod('Object.turnOff');
```
Сбрасывает: `flag=0` `illuminanceFlag = 0`

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

```php
callMethod('Имя Объекта.setColor', array("value"=>`#RRGGBB` или `#RGB` или присет));
callMethod('Имя Объекта.setColorLevel', array("value"=>1--100));
callMethod('Имя Объекта.colorLevelUp', array("value"=>1--100));
  *callMethod('Имя Объекта.colorLevelUp'); увеличит на 10
callMethod('Имя Объекта.colorLevelDown', array("value"=>1--100));
  *callMethod('Имя Объекта.colorLevelUp'); уменьшит на 10
```
Все методы → `flag=1`.
---
## Управление белым светом (Яркость)

| Метод       | Описание           |
| ----------- | ------------------ |
| `setLevel`  | Установить яркость |
| `levelDown` | Уменьшить          |
| `levelUp`   | Увеличить          |

```php
callMethod('Имя Объекта.setLevel', array("value"=>1--100));
callMethod('Имя Объекта.levelUp', array("value"=>1--100));
  *callMethod('Имя Объекта.levelUp'); увеличит на 10
callMethod('Имя Объекта.levelDown', array("value"=>1--100));
  *callMethod('Имя Объекта.levelDown'); уменьшит на 10
```
Все методы → `flag=1`.
---
## Управление CCT

| Метод     | Описание               |
| --------- | ---------------------- |
| `setCct`  | Установить температуру |
| `cctDown` | Уменьшить              |
| `cctUp`   | Увеличить              |

```php
callMethod('Имя Объекта.setCct', array("value"=>1--100 или присет));
  *Присеты - `coolest`, `cool`, `warm`, `warmest`
callMethod('Имя Объекта.cctUp', array("value"=>1--100));
  *callMethod('Имя Объекта.cctUp'); увеличит на 10
callMethod('Имя Объекта.cctDown', array("value"=>1--100));
  *callMethod('Имя Объекта.cctDown'); уменьшит на 10
```
Все методы → `flag=1`.
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