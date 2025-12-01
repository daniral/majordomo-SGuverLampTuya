# 💡 Tuya Guver Lamp - simpl devaise MajorDoMo
## Простое устройство для MajorDoMo

> Управление цветом, яркостью, теплотой и сценами через MajorDoMo.

<p align="center">
  <img src="https://img.shields.io/badge/PHP-7.4%2B-blue" />
  <img src="https://img.shields.io/badge/MajorDoMo-Device%20Module-green" />
  <img src="https://img.shields.io/badge/Status-Production-success" />
  <img src="https://img.shields.io/badge/Type-Smart%20Lighting-yellow" />
  <img src="https://img.shields.io/badge/Version-1.0-orange" />
</p>

---

## 📘 Описание

**`SGuverLampTuya`** — расширенный класс устройства *Guver Lamp (Tuya)* для MajorDoMo.

Поддерживает управление:

* Цветной свет
* Белый свет (яркость + температура CCT)
* Сцены

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

`После привязки свойств надо поизменять свойства из приложения чтобы прилетели данные в объект`.

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

## Включение

```php
callMethod('Object.turnOn');
```

## Отключение

```php
callMethod('Object.turnOff');
```

## Переключение

```php
callMethod('Object.switch');

---

## Управление цветом

| Метод            | Описание                 |
| ---------------- | ------------------------ |
| `setColor`       | Установить цвет          |
| `setColorLevel`  | Установить яркость цвета |
| `colorLevelDown` | Уменьшить                |
| `colorLevelUp`   | Увеличить                |

---

## Управление белым светом (яркость)

| Метод       | Описание           |
| ----------- | ------------------ |
| `setLevel`  | Установить яркость |
| `levelDown` | Уменьшить          |
| `levelUp`   | Увеличить          |

---

## Управление белым светом (теплота)

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
# 📝 Поведение при первом запуске

Метод `turnOn` автоматически создаёт все недостающие свойства устройства.

---



# 💡 Лампа Гайвера (Tuya)
### Простое устройство для MajorDoMo

Модуль добавляет поддержку умной лампы **Guver Lamp от Tuya** в MajorDoMo.  
Расширяет встроенный класс `SControllers`, добавляя новый класс **`SGuverLampTuya`**.

---

## ⚙️ Привязка свойств

| Свойство Tuya | Свойство в MajorDoMo | Описание |
|----------------|----------------------|-----------|
| `switch_led`   | `status`             | Включение / выключение лампы |
| `work_mode`    | `work_mode`          | Режим работы (белый, цветной, сцена и т.п.) |
| `bright_value` | `levelWork`     | Яркость белого света |
| `temp_valuec`  | `ctWork`             | Цветовая температура |
| `colour_datax` | `colour_data`        | Цвет RGB в HEX |
| `scene_data`   | `workScene`          | Активная сцена |

`После привязки свойств надо поизменять свойства из приложения чтобы прилетели данные в объект`.
---

## 🔧 Методы управления

| Метод | Назначение | Пример вызова |
|--------|-------------|----------------|
| `turnOn` | Включить лампу | `callMethod('lamp.turnOn');` |
| `turnOff` | Выключить лампу | `callMethod('lamp.turnOff');` |
| `switch` | Переключить состояние | `callMethod('lamp.switch');` |
| `brightnessDown` | Уменьшить яркость белого(по умолчанию 10) | `callMethod('lamp.brightnessDown', array("value"=>1--100));` |
| `brightnessUp` | Увеличить яркость белого(по умолчанию 10) | `callMethod('lamp.brightnessUp', array("value"=>1--100));` |
| `cctDown` | Уменьшить температуру белого (по умолчанию 10) | `callMethod('lamp.cctDown', array("value"=>1--100));` |
| `cctUp` | Увеличить температуру белого (по умолчанию 10) | `callMethod('lamp.cctUp', array("value"=>1--100));` |
| `colorBrightnessDown` | Уменьшить яркость цветного света (по умолчанию 10)| `callMethod('lamp.colorBrightnessDown', array("value"=>1--100));` |
| `colorBrightnessUp` | Увеличить яркость цветного света (по умолчанию 10) | `callMethod('lamp.colorBrightnessUp', array("value"=>1--100));` |

---

## 🎨 Работа со сценами

### 🧾 `scenesList`

Хранит список доступных сцен в формате:

Название=Значение,Название=Значение,...

**Пример:**
Спокойная=000e0d0000000000000000c80000,Чтение=010e0d0000000000000003e801f4,Работа=020e0d0000000000000003e803e8

- Если свойство `scenesList` пустое — оно автоматически заполнится дефолтными сценами.  
- Можно редактировать список вручную, добавлять свои сцены или полностью заменить его.  

---

### 🏷 `sceneName`

Хранит **имя текущей активной сцены**.

- Можно установить сцену по имени (Majordomo автоматически подставит нужный код).  
- Если указанное имя отсутствует в списке — установится **последняя сохранённая сцена**.  
- Если в `sceneName` записано `"unknown"` — в интерфейсе отображается **«Неизвестная сцена»**, но список сцен остаётся доступен для выбора.
