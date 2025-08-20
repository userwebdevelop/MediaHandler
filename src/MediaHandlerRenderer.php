<?php

namespace userwebdevelop\mediahandler;

class MediaHandlerRenderer
{
    private static function getMinimalClassName($objectClass)
    {
        return strtolower(array_reverse(explode("\\", get_class($objectClass)))[0]);
    }
    public static function getImageHTML($image, bool $isEdit = true, string $fieldName = "image"): string
    {
        $imageName = trim($image, "");
        if (empty($imageName)) {
            return "<div class='no-image'>Изображение отсутствует</div>";
        }
        $editButton = $isEdit ? "<button type='button' class='btn btn-danger btn-xs delete-image-btn' data-name='$imageName' data-field='$fieldName'>×</button>" : '';
        return "<div class='single-image single-media--js' data-name='$imageName' data-field='$fieldName' style=\"background-image: url(/upload/$imageName);\">$editButton</div>";
    }

    public static function getGalleryHTML($images, bool $isEdit = true)
    {
        $html = '<div class="existing-images">';
        if (!empty($images)) {
            $html .= '<h4 class="mb-3">Загруженные изображения:</h4>
            <div id="sortable-images" class="row g-4">';
            foreach ($images as $image) {
                $imageHtml = self::getImageHTML($image->image_name, $isEdit, 'image_name');
                $html .= "<div class='position-relative image-container' data-id='$image->id' data-sort='$image->sort'>$imageHtml</div>";
            }
            $html .= '</div>';
        } else {
            $html .= '<p class="text-muted">Нет загруженных изображений.</p>';
        }
        $html .= '</div>';
        return $html;
    }
    public static function getSoundHTML($audioFile, bool $isEdit = true, string $fieldName = "sound"): string
    {
        $audioName = trim($audioFile);
        if (empty($audioName)) {
            return "<div class='no-image'>Аудиофайл отсутствует</div>";
        }
        $filePath = "/upload/$audioName";
        $deleteButton = $isEdit
            ? "<button type='button' class='btn btn-danger btn-xs delete-image-btn delete-audio-btn' data-name='$audioName' data-field='$fieldName'>×</button>"
            : '';
        $audioPlayer = "
        <audio controls style='width: 100%; margin-top: 10px;'>
            <source src='$filePath' type='audio/mpeg'>
            Ваш браузер не поддерживает аудио.
        </audio>
    ";
        return "
        <div class='single-audio single-media--js' data-name='$audioName' data-field='$fieldName'>
            $audioPlayer
            $deleteButton
        </div>
    ";
    }
    public static function getImagesField($objectClass)
    {
        $className = self::getMinimalClassName($objectClass);
        return [
            'attribute' => 'images',
            'value' => function ($data) use ($className) {
                $images = \common\models\Image::find()->where(['object_type' => $className, 'object_id' => $data->id])
                    ->orderBy(['sort' => SORT_ASC, 'id' => SORT_DESC])->all();

                return self::getGalleryHTML($images, false);
            },
            'format' => 'raw',
        ];
    }

    public static function getImageField($model = null)
    {
        if (isset($model)) {
            $value = self::getImageHTML($model->image, false);
        } else {
            $value = function ($data) {
                return self::getImageHTML($data->image, false);
            };
        }
        return [
            'attribute' => 'image',
            'value' => $value,
            'format' => 'raw',
        ];
    }
    public static function getSoundField($model = null)
    {
        if (isset($model)) {
            $value = self::getSoundHTML($model->sound, false);
        } else {
            $value = function ($data) {
                return self::getSoundHTML($data->sound, false);
            };
        }
        return [
            'attribute' => 'sound',
            'value' => $value,
            'format' => 'raw',
        ];
    }
    public static function getImageUrl($imageName)
    {
        return "/upload/$imageName";
    }
}
