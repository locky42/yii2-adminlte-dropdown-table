<?php

namespace locky42\adminlte\dropdownTable;

use common\models\au\AuBrands;
use yii\data\ActiveDataProvider;
use yii\grid\Column;
use yii\helpers\Html;
use yii\widgets\BaseListView;
use yii\grid\GridView;

class DropdownTable extends GridView
{
    public $ariaExpanded = false;
    public $relations = [];

    public function renderTableRow($model, $key, $index)
    {
        $cells = [];
        /* @var $column Column */
        foreach ($this->columns as $column) {
            $cells[] = $column->renderDataCell($model, $key, $index);
        }
        if ($this->rowOptions instanceof Closure) {
            $options = call_user_func($this->rowOptions, $model, $key, $index, $this);
        } else {
            $options = $this->rowOptions;
        }
        $options['data-key'] = is_array($key) ? json_encode($key) : (string) $key;
        $options['data-widget'] = 'expandable-table';
        $options['aria-expanded'] = $this->ariaExpanded ? 'true' : 'false';
        $row = Html::tag('tr', implode('', $cells), $options);
        return $row . $this->getSubTable($model);
    }

    protected function getSubTable($model)
    {
        $data = '';
        foreach ($this->relations as $field => $relation) {
            $objects = $model->$field;
            $first = $objects[array_key_first($objects)];
            $class = $first::class;
            $primaryKey = $class::primaryKey();
            $ids = [];
            foreach ($objects as $object) {
                $class = $object::class;
                $ids[] = $object->getPrimaryKey();
            }
            $query = $class::find();

            $dataProvider = new ActiveDataProvider([
                'query' => $query,
            ]);

            $query->orFilterWhere(['IN', $primaryKey, $ids]);

            $data .= DropdownTable::widget(array_merge($relation, [
                'dataProvider' => $dataProvider,
            ]));
        }

        return Html::tag('tr', '<td colspan="' . count($this->columns) . '">' . $data . '</td>', ['class' => 'expandable-body']);
    }
}
