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
| `work_mode`    | `workMode`         |
| `bright_value` | `levelWork`        |
| `temp_value`   | `cctWork`          |
| `colour_data`  | `colorWork`        |
| `scene_data`   | `sceneWork`        |

`После привязки свойств надо поизменять свойства из приложения чтобы прилетели данные в объект`.


---

# 🔧 Методы

## Включение

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
# 🎬 Управление сценами

📄 `scenesList`

Формат:

```
Название=Код,Название2=Код2,...
```

Если пусто — создаётся список по умолчанию.

🏷 `sceneName`

Имя текущей активной сцены.
Если указанной сцены нет — используется сохранённая.

---
# 📝 Поведение при первом запуске

Метод `turnOn` автоматически создаёт все недостающие свойства устройства.

---