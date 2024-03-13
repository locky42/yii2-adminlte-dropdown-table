<?php

namespace locky42\adminlte\dropdownTable\helpers;

class ModelHelper
{
    /**
     * @param string $model
     * @return false|string
     */
    public static function getPrimaryKey(string|object $model)
    {
        return $model::primaryKey()[0];
    }

    /**
     * @param string|object $model
     * @param $relation
     * @return mixed
     */
    public static function getRelation(string|object $model, $relation)
    {
        return self::getModel($model)->getRelation($relation);
    }

    /**
     * @param string|object $model
     * @return mixed|object|string
     */
    public static function getModel(string|object $model)
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
     * @return mixed
     */
    public static function getRelationQuery(string|object $model, $relation, $id)
    {
        $relationInfo = self::getRelation($model, $relation);
        $query = $relationInfo->modelClass::find()->where([array_flip($relationInfo->link)[self::getPrimaryKey($model)] => $id]);

        return $query;
    }
}
