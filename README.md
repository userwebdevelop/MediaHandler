# Что делает пакет

Трейт автоматически создаёт методы `afterSave` и `beforeSave`. В случае, если возникла необходимость написать кастомные методы  `afterSave` или `beforeSave` в классе модели, необходимо вызывать в них соответстующие методы для правильной работы с изображениями:
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
# После установки пакета необходимо сделать следующие шаги (порядок не важен):
- В файле `console/config/main.php` добавить следующий код:
```php
'controllerMap' => [
    //остальной код
    'uwb-media' => 'userwebdevelop\mediahandler\DB',
];
```
# Команды

Пакет предоставляет две команды:
`php yii uwb-media/create-table` - создаёт таблицу images для хранения и сортировки медиа файлов
`php yii uwb-media/drop-table` - удаляет таблицу images
