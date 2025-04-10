# Описание работы трейта для сохранения изображений

Трейт автоматически создаёт методы `afterSave` и `beforeSave`. В случае, если возникла необходимость написать кастомные методы  `afterSave` или `beforeSave` в классе модели, необходимо вызывать в них соответстующие методы для правильной работы с изображениями:
```
public function beforeSave($insert)
{
    $this->prepareImageBeforeSave(); // необходимо для работы с одиночными изображениями
    return parent::beforeSave($insert);
}

public function afterSave($insert, $changedAttributes)
{
    parent::afterSave($insert, $changedAttributes);
    $this->handleImages(); // необходимо для работы с галереей
}
```

# Команды

Пакет предоставляет две команды:
`uwb-media/install` - создаёт таблицу images для хранения и сортировки медиа файлов
`uwb-media/uninstall` - удаляет таблицу images
