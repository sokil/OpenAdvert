<?php

class Formatter extends CFormatter
{

    public function formatMoney($value)
    {
        return '$' . $this->formatNumber($value);
    }

    public function formatPre($value)
    {
        return '<pre>' . $this->formatRaw($value) . '</pre>';
    }

    public function formatPercent($value)
    {
        return $this->formatNumber($value * 100) . '%';
    }

}
