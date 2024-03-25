<?php

namespace locky42\adminlte\dropdownTable;

use Closure;
use yii\db\ActiveRecord;
use yii\grid\Column;
use yii\helpers\Html;
use yii\grid\GridView;
use Throwable;
use yii;
use locky42\adminlte\dropdownTable\helpers\TreeHelper;
use locky42\adminlte\dropdownTable\helpers\FormatHelper;
use locky42\adminlte\dropdownTable\assets\AdminLteDropdownTableAsset;

class DropdownTable extends GridView
{
    public string|bool|int $ariaExpanded = 'false';
    public array $relations = [];
    public bool $ajax = false;
    public ?string $ajaxUrl = null;
    public string|null $title = null;
    public $custom_content = null;
    public $layout = "{title}\n{custom_content}\n{summary}\n{items}\n{pager}";
    /** @var self|null */
    public ?DropdownTable $parent = null;

    /** @deprecated */
    public ?int $currentId = null;

    protected int $relationsTotalCount = 0;

    public function run(): void
    {
        if (!$this->isAjax()) {
            $this->dataProvider->sort->params = FormatHelper::getUrlParams(yii::$app->request->referrer);
            $this->dataProvider->sort->route = FormatHelper::getUrl(yii::$app->request->referrer);
        }

        $this->tableOptions['class'] = $this->tableOptions['class'] . ' dropdown-table';
        $view = $this->getView();
        AdminLteDropdownTableAsset::register($view);
        parent::run();
    }

    /**
     * @param $name
     * @return string|bool
     */
    public function renderSection($name): string|bool
    {
        return match ($name) {
            '{title}' => $this->renderTitle(),
            '{custom_content}' => $this->renderCustomContent(),
            default => parent::renderSection($name),
        };
    }

    public function renderCustomContent()
    {
        $models = $this->dataProvider->getModels();
        if (isset($models[$this->getCurrentId()])) {
            $model = $models[$this->getCurrentId()];
        } else {
            $modelClass = $this->dataProvider->query->modelClass;
            $model = $this->dataProvider->query->one() ?? new $modelClass;
            $model->load($this->dataProvider->query->where, '');
        }

        if ($this->custom_content instanceof Closure) {
            $custom_content = call_user_func($this->custom_content, $model);
        } else {
            $custom_content = $this->custom_content;
        }

        return $custom_content;
    }

    /**
     * @return string
     */
    public function renderTitle(): string
    {
        return $this->title ? '<div class="pb-0 mb-0"><h5 class="mb-0">' . $this->title . '</h5></div>' : '';
    }

    /**
     * @param $model
     * @param $key
     * @param $index
     * @return string
     * @throws Throwable
     */
    public function renderTableRow($model, $key, $index): string
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
        $this->setAriaExpanded($this->ariaExpanded);

        $options['data-key'] = is_array($key) ? json_encode($key) : (string) $key;
        $options['data-relations'] = FormatHelper::serialize($this->relations);
        $options['data-model'] = $model::class;
        $options['data-widget'] = 'expandable-table';
        $options['data-ajax'] = $this->getAjax();
        $options['data-ajax-url'] = $this->ajaxUrl;
        $options['aria-expanded'] = $this->ariaExpanded;

        if ($this->relations && !$this->relationsTotalCount) {
            $options['class'] = 'relations-empty';
        } elseif (!$this->relations) {
            $options['class'] = 'no-relations';
        }

        $row = Html::tag('tr', implode('', $cells), $options);
        return $row . $subTable;
    }

    /**
     * @return string|null
     */
    protected function getAjax(): ?string
    {
        return var_export($this->isAjax(), true);
    }

    /**
     * @return bool
     */
    protected function isAjax(): bool
    {
        return filter_var($this->ajax, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * @return void
     * todo: remove this method
     */
    protected function getAriaExpanded(): void
    {
        $id = $this->getCurrentId();

        $isActive = in_array($id, TreeHelper::getParentsIds($this)) &&
            (
                yii::$app->request->get("dp-$id-sort") || yii::$app->request->get("dp-$id-page")
            );

        self::setParentExpanded($this, $isActive);
    }

    /**
     * @param $expanded
     * @return void
     */
    public function setAriaExpanded($expanded): void
    {
        $this->ariaExpanded = var_export(filter_var($expanded, FILTER_VALIDATE_BOOLEAN), true);
    }

    /**
     * @param DropdownTable|null $object
     * @param $expanded
     * @return void
     */
    public static function setParentExpanded(?self $object, $expanded): void
    {
        $object?->parent?->setAriaExpanded($expanded);

        if ($object?->parent) {
            $object?->parent::setParentExpanded($object->parent, $expanded);
        }
    }

    /**
     * @return int
     */
    public function getCurrentId(): int
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
    protected function getSubTable($model): string
    {
        $data = '';
        foreach ($this->relations as $field => $relation) {
            $objects = $model->$field;
            $totalCount = count($objects);
            $this->relationsTotalCount += $totalCount;
            if (!$this->ajax) {
                if (!$totalCount) {
                    continue;
                }

                $first = $objects[array_key_first($objects)];

                /** @var ActiveRecord $class */
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
