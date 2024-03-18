<?php

namespace locky42\adminlte\dropdownTable\helpers;

use yii\db\ActiveQuery;
use yii\db\ActiveQueryInterface;
use yii\db\ActiveRecord;

class ModelHelper
{
    /**
     * @param string|object $model
     * @return false|string
     */
    public static function getPrimaryKey(string|object $model): bool|string
    {
        return $model::primaryKey()[0];
    }

    /**
     * @param string|object $model
     * @param $relation
     * @return ActiveQuery|ActiveQueryInterface|null
     */
    public static function getRelation(string|object $model, $relation): ActiveQueryInterface|ActiveQuery|null
    {
        return self::getModel($model)->getRelation($relation);
    }

    /**
     * @param string|object $model
     * @return ActiveRecord
     */
    public static function getModel(string|object $model): ActiveRecord
    {
        if (is_string($model)) {
            $model = new $model;
        }
        return $model;
    }

    /**
     * @param string|object $model
     * @param $relation
     * @param $id
     * @return ActiveQuery
     */
    public static function getRelationQuery(string|object $model, $relation, $id): ActiveQuery
    {
        $relationInfo = self::getRelation($model, $relation);
        return $relationInfo->modelClass::find()->where([array_flip($relationInfo->link)[self::getPrimaryKey($model)] => $id]);
    }
}
