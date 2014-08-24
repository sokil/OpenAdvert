<?php

Yii::import('zii.widgets.grid.CDataColumn');

class DataColumn extends CDataColumn
{

    protected function renderFooterCellContent()
    {
        $total = $this->grid->dataProvider->getSum($this->name);
        echo $this->grid->getFormatter()->format($total, $this->type);
    }

}
