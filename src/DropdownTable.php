<?php

namespace locky42\adminlte\dropdownTable;

use common\models\au\AuBrands;
use yii\data\ActiveDataProvider;
use yii\grid\Column;
use yii\grid\GridViewAsset;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\widgets\BaseListView;
use yii\grid\GridView;
use Throwable;
use yii;
use locky42\adminlte\dropdownTable\helpers\TreeHelper;
use locky42\adminlte\dropdownTable\DropdownDataProvider;
use locky42\adminlte\dropdownTable\assets\AdminLteDropdownTableAsset;

class DropdownTable extends GridView
{
    public $ariaExpanded = 'false';
    public array $relations = [];
    public bool $ajax = false;
    public ?string $ajaxUrl = null;
    public $title = null;
    public $layout = "{title}\n{summary}\n{items}\n{pager}";
    /** @var self|null */
    public $parent = null;
    public $currentId = null;

    public function run()
    {
        $this->tableOptions['class'] = $this->tableOptions['class'] . ' dropdown-table';
        $view = $this->getView();
        AdminLteDropdownTableAsset::register($view);
        parent::run();
    }

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
        $this->getAriaExpanded();

        $options['data-key'] = is_array($key) ? json_encode($key) : (string) $key;
        $options['data-relations'] = json_encode($this->relations);
        $options['data-model'] = $model::class;
        $options['data-widget'] = 'expandable-table';
        $options['data-ajax'] = $this->getAjax();
        $options['data-ajax-url'] = $this->ajaxUrl;
        $options['aria-expanded'] = $this->ariaExpanded;
        $row = Html::tag('tr', implode('', $cells), $options);
        return $row . $subTable;
    }

    /**
     * @return void
     */
    protected function getAriaExpanded()
    {
        $id = $this->getCurrentId();

        $isActive = in_array($id, TreeHelper::getParentsIds($this)) &&
        (
            yii::$app->request->get("dp-$id-sort") || yii::$app->request->get("dp-$id-page")
        ) ? : false;

        self::setParentExpanded($this, $isActive);
    }

    /**
     * @return string|null
     */
    protected function getAjax()
    {
        return var_export($this->isAjax(), true);
    }

    /**
     * @return bool
     */
    protected function isAjax()
    {
        return filter_var($this->ajax, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * @param $expanded
     * @return void
     */
    public function setAriaExpanded($expanded)
    {
        $this->ariaExpanded = var_export(filter_var($expanded, FILTER_VALIDATE_BOOLEAN), true);
    }

    /**
     * @param $object
     * @param $expanded
     * @return void
     */
    public static function setParentExpanded(?self $object, $expanded)
    {
        $object?->parent?->setAriaExpanded($expanded);

        if ($object?->parent) {
            $object?->parent::setParentExpanded($object?->parent, $expanded);
        }
    }

    /**
     * @return int
     */
    public function getCurrentId()
    {
        if ($this->currentId === null) {
            $this->currentId = (int) str_replace(self::$autoIdPrefix, null, $this->options['id']);
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
            $totalCount = count($objects);
            if (!$this->ajax) {
                if (!$totalCount) {
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
        }

        $td = Html::tag('td', $data, ['colspan' => count($this->columns)]);
        return Html::tag('tr', $td, ['class' => 'expandable-body']);
    }
}
