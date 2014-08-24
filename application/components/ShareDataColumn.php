<?php

Yii::import('zii.widgets.grid.CDataColumn');

class ShareDataColumn extends CDataColumn
{
    public $total;

    protected function renderDataCellContent($row, $data)
    {
        if ($this->value !== null) {
            $value = $this->evaluateExpression($this->value, array('data' => $data, 'row' => $row));
        } elseif ($this->name !== null) {
            $value = CHtml::value($data, $this->name);
        }
        
        $total = $this->grid->dataProvider->getSum($this->name);
        if ($value && $total) {
            $value = $value / $total;
        }

        echo $value === null ? $this->grid->nullDisplay : $this->grid->getFormatter()->format($value, $this->type);
    }

    protected function renderFooterCellContent()
    {
        if ($this->grid->dataProvider->getSum($this->name)) {
            $total = 1;
        } else {
            $total = 0;
        }
        echo $this->grid->getFormatter()->format($total, $this->type);
    }

}
