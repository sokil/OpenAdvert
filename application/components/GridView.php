<?php

Yii::import('zii.widgets.grid.CGridView');

class GridView extends CGridView
{
    public $itemsCssClass = 'table table-hover table-striped table-condensed';
    
    public $pager = array('class'=>'TbPager');
    
    public $pagerCssClass = 'pagination';
    
    public $summaryText = false;
}
