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

выполнить команду `composer require userwebdevelop/media-handler`

После установки пакета необходимо выполнить следующие шаги (порядок не важен):

В файле `console/config/main.php` добавить:

```php
'controllerMap' => [
    // остальной код
    'uwb-media' => 'userwebdevelop\mediahandler\DB',
];
```
В файле `backend\views\layouts\main.php` добавить:
```php
\userwebdevelop\mediahandler\assets\MediaHandlerAsset::register($this);
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