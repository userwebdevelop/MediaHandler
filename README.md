# MediaHandler

## Что делает пакет

Трейт автоматически создаёт методы `afterSave` и `beforeSave`.
В случае, если нужно написать кастомные методы в модели, важно вызывать внутри них методы работы с медиа:

```php
public function beforeSave($insert)
{
    $this->handleSingleMedia(); // необходимо для работы с одиночными изображениями
    return parent::beforeSave($insert);
}

public function afterSave($insert, $changedAttributes)
{
    parent::afterSave($insert, $changedAttributes);
    $this->handleImages(); // необходимо для работы с галереей
}
```

---

## Установка и настройка

После установки пакета необходимо выполнить следующие шаги (порядок не важен):

В файле `console/config/main.php` добавить:

```php
'controllerMap' => [
    // остальной код
    'uwb-media' => 'userwebdevelop\mediahandler\DB',
];
```

---

## Примеры использования в админке

### Базовое скрытое поле (обязательно всегда):

```php
<?= $form->field($model, 'images_to_delete')->hiddenInput(['id' => 'images-to-delete'])->label(false) ?>
```

### Одиночное изображение

```php
<?= $form->field($model, 'image')->fileInput() ?>
<?= MediaHandlerRenderer::getImageHTML($model->image) ?>
```

### Несколько изображений (галерея)

```php
<?= $form->field($model, 'images[]')->fileInput(['multiple' => true, 'accept' => 'image/*']) ?>
<?= $form->field($model, 'image_order')->hiddenInput(['id' => 'image-order'])->label(false) ?>
<?= MediaHandlerRenderer::getGalleryHTML($model->image) ?>
```

### Звуки

```php
<?= $form->field($model, 'sound')->fileInput(['accept' => 'audio/*']) ?>
<?= MediaHandlerRenderer::getSoundHTML($model->sound) ?>
```

### Видео

```php
<?= $form->field($model, 'video')->fileInput(['accept' => 'video/*']) ?>
<?= MediaHandlerRenderer::getVideoHtml($model->video) ?>
```

---

## Консольные команды

* `php yii uwb-media/create-table` — создаёт таблицу `images` для хранения и сортировки медиафайлов
* `php yii uwb-media/drop-table` — удаляет таблицу `images`

---

## Сравнение с рендерером

### Что совпадает:

* Поддержка **одиночного изображения** (`getImageHTML`)
* Поддержка **галереи изображений** (`getGalleryHTML`)
* Поддержка **аудио** (`getSoundHTML`)
* Поддержка **видео** (`getVideoHTML`)
* Методы для интеграции с GridView (`getImageField`, `getImagesField`, `getSoundField`, `getVideoField`)

### Чего не хватает в рендерере:

* Логики обработки файлов при `beforeSave` и `afterSave` (это делает трейд).
* Управления удалением и сортировкой (рендерер только рисует HTML, а сам функционал реализует трейд + JS).
* Консольных команд (`create-table`, `drop-table`) — это не в рендерере, а в пакете в целом.

**Вывод:**
Рендерер = чисто фронтовый помощник для отображения медиа.
Трейт и консольные команды = серверная часть, которая хранит и обрабатывает файлы.