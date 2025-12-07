# 💡 Tuya Guver Lamp
## Простое устройство для MajorDoMo

<p align="center">
  <img src="https://img.shields.io/badge/PHP-7.4%2B-blue" />
  <img src="https://img.shields.io/badge/MajorDoMo-Device%20Module-green" />
  <img src="https://img.shields.io/badge/Status-Production-success" />
  <img src="https://img.shields.io/badge/Type-Smart%20Lighting-yellow" />
  <img src="https://img.shields.io/badge/Version-1.0-orange" />
</p>

---

## 📘 Описание

**`SGuverLampTuya`** — расширяет класс *SControllers* 
> Простое устройство *Guver Lamp (Tuya)* для MajorDoMo.

Поддерживает управление:

* 🎨 Цветной свет
* 💡 Белый свет (яркость + температура CCT)
* 🎬 Сцены

---

# ⚙️ Привязка свойств

| Tuya поле      | Свойство MajorDoMo |
| -------------- | ------------------ |
| `switch_led`   | `status`           |
| `work_mode`    | `modeWork`         |
| `bright_value` | `levelWork`        |
| `temp_value`   | `cctWork`          |
| `colour_data`  | `colorWork`        |
| `scene_data`   | `sceneWork`        |

`После привязки свойств надо поизменять свойства из приложения чтобы прилетели данные в объект`.


---

# 🔧 Методы

## 💡 Включение

```php
callMethod('Object.turnOn');
```

## ⛔ Отключение

```php
callMethod('Object.turnOff');
```

## 🔁 Переключение

```php
callMethod('Object.switch');
```
---

## 🎨 Управление цветом

### Цвет может задаваться:

✔ HEX-кодами

* `#RRGGBB`
* `#RGB`

✔ Цветовыми пресетами

```
red, green, blue, white, yellow,
cyan, magenta, orange, purple,
pink, lime
```


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

---

## 💡 Управление белым светом (яркость)

| Метод       | Описание           |
| ----------- | ------------------ |
| `setLevel`  | Установить яркость |
| `levelUp`   | Увеличить          |
| `levelDown` | Уменьшить          |

```php
callMethod('Имя Объекта.setLevel', array("value"=>1--100));
callMethod('Имя Объекта.levelUp', array("value"=>1--100));
  *callMethod('Имя Объекта.levelUp'); увеличит на 10
callMethod('Имя Объекта.levelDown', array("value"=>1--100));
  *callMethod('Имя Объекта.levelDown'); уменьшит на 10
```

---

## 🔥 Управление белым светом (теплота)

| Метод     | Описание               |
| --------- | ---------------------- |
| `setCct`  | Установить температуру |
| `cctUp`   | Увеличить              |
| `cctDown` | Уменьшить              |

```php
callMethod('Имя Объекта.setCct', array("value"=>1--100 или присет));
  *Присеты - `coolest`, `cool`, `warm`, `warmest`
callMethod('Имя Объекта.cctUp', array("value"=>1--100));
  *callMethod('Имя Объекта.cctUp'); увеличит на 10
callMethod('Имя Объекта.cctDown', array("value"=>1--100));
  *callMethod('Имя Объекта.cctDown'); уменьшит на 10
```

---
## 🎬 Управление сценами

📄 `scenesList`

Хранит список доступных сцен в формате:

Название=Значение,Название=Значение,...

**Пример:**
```php
Спокойная=000e0d0000000000000000c80000,
Чтение=010e0d0000000000000003e801f4,
Работа=020e0d0000000000000003e803e8
```

- Если свойство `scenesList` пустое — оно автоматически заполнится дефолтными сценами.  
- Можно редактировать список вручную, добавлять свои сцены или полностью заменить его.  

---
🏷 `sceneName`

Хранит **имя текущей активной сцены**.

- Можно установить сцену по имени (Majordomo автоматически подставит нужный код).  
- Если указанное имя отсутствует в списке — установится **последняя сохранённая сцена**.  
- Если в `sceneName` записано `"unknown"` — в интерфейсе отображается **«Неизвестная сцена»**, но список сцен остаётся доступен для выбора.

---