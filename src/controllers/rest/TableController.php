<?php

namespace locky42\adminlte\dropdownTable\controllers\rest;

use Throwable;
use yii;
use yii\rest\Controller;
use locky42\adminlte\dropdownTable\DropdownTable;
use locky42\adminlte\dropdownTable\DropdownDataProvider;
use locky42\adminlte\dropdownTable\helpers\ModelHelper;
use locky42\adminlte\dropdownTable\helpers\FormatHelper;

class TableController extends Controller
{
    /**
     * @return string
     * @throws Throwable
     */
    public function actionIndex(): string
    {
        $modelClass = yii::$app->request->post('model');
        /** @var $model yii\db\ActiveRecord */
        $model = new $modelClass;

        $id = yii::$app->request->post('id');
        $relations = FormatHelper::unserialize(yii::$app->request->post('relations'));

        $result = '';
        foreach ($relations as $relation => $relationData) {
            $query = ModelHelper::getRelationQuery($model, $relation, $id);
            $dataProvider = new DropdownDataProvider([
                'query' => $query
            ]);
            $result .= DropdownTable::widget(
                array_merge($relationData, [
                    'dataProvider' => $dataProvider,
                ])
            );
        }

        return $result;
    }

    /**
     * @param $action
     * @param null $model
     * @param array $params
     * @return void
     */
    public function checkAccess($action, $model = null, $params = [])
    {
    }
}
