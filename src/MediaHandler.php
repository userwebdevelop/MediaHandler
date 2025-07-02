<?php

namespace userwebdevelop\mediahandler;

use common\models\Image;
use Yii;
use yii\helpers\FileHelper;
use yii\validators\Validator;
use yii\web\UploadedFile;

trait MediaHandler
{
    public $image_order;
    public $images_to_delete;
    private $images;
    private $imageFile;
    private $videoFile;
    private $soundFile;
    private $class;
    private $uploadPath;

    public function init()
    {
        parent::init();
        $this->validators->append(Validator::createValidator('safe', $this, ['image_order', 'images_to_delete']));
        $this->uploadPath = Yii::getAlias("@frontend/web/upload/");
        $this->class = strtolower((new \ReflectionClass($this))->getShortName());
    }

    public function getImages()
    {
        return $this->hasMany(Image::class, ['object_id' => 'id'])->where(['object_type' => $this->class]);
    }

    public function beforeSave($insert)
    {
        $this->handleSingleMedia();
        return parent::beforeSave($insert);
    }
    public function handleSingleMedia()
    {
        if (in_array('image', $this->fields())) $this->prepareMediaBeforeSave('image');
        if (in_array('video', $this->fields())) $this->prepareMediaBeforeSave('video');
        if (in_array('sound', $this->fields())) $this->prepareMediaBeforeSave('sound');
        if ($this->images_to_delete) $this->deleteImages();
    }
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        $this->handleImages();
    }

    private function prepareMediaBeforeSave($mediaType)
    {
        $fileAttribute = $mediaType . 'File';
        $this->$fileAttribute = UploadedFile::getInstance($this, $mediaType);

        if ($this->$fileAttribute) {
            $this->saveMedia($this->$fileAttribute, $mediaType);
        }

        if (empty($this->$mediaType)) {
            $this->$mediaType = $this->getOldAttribute($mediaType);
        } elseif (!empty(trim($this->getOldAttribute($mediaType)))) {
            $this->deleteMediaHandler($this->getOldAttribute($mediaType));
        }
    }

    public function handleImages()
    {
        $this->images = UploadedFile::getInstances($this, 'images');

        if ($this->images) {
            $this->saveImages();
        }
        if ($this->image_order) {
            $this->sortImages();
        }
    }

    private function saveImages()
    {
        FileHelper::createDirectory($this->uploadPath);
        foreach ($this->images as $image) {
            if ($this->validateImage($image)) {
                $image_name = uniqid($this->class . "_") . '.' . $image->extension;
                $imageModel = new Image([
                    'object_id' => $this->id,
                    'object_type' => $this->class,
                    'image_name' => $image_name,
                ]);
                if ($imageModel->save()) {
                    $image->saveAs($this->uploadPath . $image_name);
                }
            }
        }
    }

    private function sortImages()
    {
        $orderData = json_decode($this->image_order);
        if (empty($orderData)) {
            return;
        }
        foreach ($orderData as $item) {
            $image = Image::findOne($item->id);
            if ($image) {
                $image->sort = $item->sort;
                $image->save();
            }
        }
    }

    private function deleteImages()
    {
        $imagesToDelete = json_decode($this->images_to_delete, true);
        if (empty($imagesToDelete)) {
            return;
        }
        foreach ($imagesToDelete as $key => $mediaCategory) {
            if ($key == 'image_name') {
                $images = Image::findAll(['image_name' => $mediaCategory]);
                foreach ($images as $image) {
                    $this->deleteMediaHandler($image->image_name);
                    $image->delete();
                }
            } else {
                foreach ($mediaCategory as $mediaFile) {
                    $this->deleteMediaHandler($mediaFile);
                    $this->$key = null;
                }
            }
        }
    }

    private function deleteMediaHandler($fileName)
    {
        $filePath = $this->uploadPath . $fileName;
        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }

    private function saveMedia($file, $mediaType)
    {
        if (!$file || !$this->{"validate" . ucfirst($mediaType)}($file)) {
            return;
        }

        $fileName = uniqid($this->class . "_") . '.' . $file->extension;
        FileHelper::createDirectory($this->uploadPath);

        if ($file->saveAs($this->uploadPath . $fileName)) {
            $this->$mediaType = $fileName;
        }
    }
    private function validateImage($file)
    {
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

        return $this->validateFile($file, $allowedExtensions, $allowedMimeTypes);
    }

    private function validateVideo($file)
    {
        $allowedExtensions = ['mp4', 'avi', 'mov', 'mkv'];
        $allowedMimeTypes = ['video/mp4', 'video/avi', 'video/quicktime', 'video/x-matroska'];

        return $this->validateFile($file, $allowedExtensions, $allowedMimeTypes);
    }

    private function validateFile($file, $allowedExtensions, $allowedMimeTypes)
    {
        if (!file_exists($file->tempName)) {
            return false;
        }
        $mimeType = mime_content_type($file->tempName) ?: $file->type;

        return in_array(strtolower($file->extension), $allowedExtensions) &&
            in_array($mimeType, $allowedMimeTypes);
    }
    private function validateSound($file)
    {
        $allowedExtensions = ['mp3', 'wav', 'ogg', 'm4a', 'flac'];
        $allowedMimeTypes = [
            'audio/mpeg',
            'audio/wav',
            'audio/ogg',
            'audio/mp4',
            'audio/flac'
        ];

        return $this->validateFile($file, $allowedExtensions, $allowedMimeTypes);
    }
}
