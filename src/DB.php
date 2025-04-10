<?php
namespace userwebdevelop\mediahandler;

use Yii;
use yii\db\Schema;

class DB
{
    public function actionCreateTable()
    {
        $db = Yii::$app->db;
        $tableName = '{{%images}}';

        if (!in_array($db->schema->getRawTableName($tableName), $db->schema->getTableNames())) {
            $db->createCommand()->createTable($tableName, [
                'id' => Schema::TYPE_PK,
                'object_id' => Schema::TYPE_INTEGER . ' NOT NULL',
                'object_type' => Schema::TYPE_STRING . '(255) NOT NULL',
                'image_name' => Schema::TYPE_STRING . '(255) NOT NULL',
                'sort' => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 500',
                'created_at' => Schema::TYPE_TIMESTAMP . ' DEFAULT CURRENT_TIMESTAMP',
            ])->execute();
        }
    }

    public function actionDropTable()
    {
        $db = Yii::$app->db;
        $tableName = '{{%images}}';
        if (in_array($db->schema->getRawTableName($tableName), $db->schema->getTableNames())) {
            $db->createCommand()->dropTable($tableName)->execute();
        }
    }
}
