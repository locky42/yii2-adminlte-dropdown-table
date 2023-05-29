<?php

namespace locky42\adminlte\dropdownTable;

use common\models\au\AuBrands;
use yii\data\ActiveDataProvider;
use yii\grid\Column;
use yii\helpers\Html;
use yii\widgets\BaseListView;
use yii\grid\GridView;
use Throwable;
use yii;
use locky42\adminlte\dropdownTable\helpers\TreeHelper;
use locky42\adminlte\dropdownTable\DropdownDataProvider;

class DropdownTable extends GridView
{
    public $ariaExpanded = 'false';
    public $relations = [];
    public $title = null;
    public $layout = "{title}\n{summary}\n{items}\n{pager}";
    public $parent = null;
    public $currentId = null;

    /**
     * @param $name
     * @return bool|string
     */
    public function renderSection($name)
    {
        switch ($name) {
            case '{title}':
                return $this->renderTitle();
            default:
                return parent::renderSection($name);
        }
    }

    /**
     * @return string
     */
    public function renderTitle()
    {
        return $this->title ? '<div class="pb-0 mb-0"><h5 class="mb-0">' . $this->title . '</h5></div>' : '';
    }
    /**
     * @param $model
     * @param $key
     * @param $index
     * @return string
     */
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

        $subTable = $this->getSubTable($model);
        $this->getAriaExpanded($key, $model);

        $options['data-key'] = is_array($key) ? json_encode($key) : (string) $key;
        $options['data-widget'] = 'expandable-table';
        $options['aria-expanded'] = $this->ariaExpanded;
        $row = Html::tag('tr', implode('', $cells), $options);
        return $row . $subTable;
    }

    /**
     * @param $key
     * @param $model
     * @return void
     */
    protected function getAriaExpanded($key, $model)
    {
        $id = $this->getCurrentId();

        $isActive = in_array($id, TreeHelper::getId($this)) &&
        (
            yii::$app->request->get("dp-$id-sort") || yii::$app->request->get("dp-$id-page")
        ) ? : false;

        $this->setParentExpanded($isActive);
    }

    /**
     * @param $expanded
     * @return void
     */
    public function setAriaExpanded($expanded)
    {
        $this->ariaExpanded = $expanded ? 'true' : 'false';
    }

    /**
     * @param $expanded
     * @return void
     */
    public function setParentExpanded($expanded)
    {
        $this->parent?->setAriaExpanded($expanded);
        $this->parent?->setParentExpanded($expanded);
    }

    /**
     * @return int
     */
    public function getCurrentId()
    {
        if ($this->currentId === null) {
            $this->currentId = (int) str_replace($this::$autoIdPrefix, null, $this->options['id']);
        }
        return $this->currentId;
    }

    /**
     * @param $model
     * @return string
     * @throws Throwable
     */
    protected function getSubTable($model)
    {
        $data = '';
        foreach ($this->relations as $field => $relation) {
            $objects = $model->$field;
            if (empty($objects)) {
                continue;
            }
            $first = $objects[array_key_first($objects)];
            $class = $first::class;
            $primaryKey = $class::primaryKey();
            $ids = [];
            foreach ($objects as $object) {
                $class = $object::class;
                $ids[] = $object->getPrimaryKey();
            }
            $query = $class::find();

            $dataProvider = new DropdownDataProvider([
                'query' => $query,
            ]);

            $query->orFilterWhere(['IN', $primaryKey, $ids]);

            $data .= DropdownTable::widget(array_merge($relation, [
                'dataProvider' => $dataProvider,
                'parent' => $this,
            ]));
        }

        $td = Html::tag('td', $data, ['colspan' => count($this->columns)]);
        return Html::tag('tr', $td, ['class' => 'expandable-body']);
    }
}
