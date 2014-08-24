<?php

abstract class Report 
{
    protected $_dateFrom;
    protected $_dateTo;

    abstract public function getDataProvider();

    /**
     * @param str $date YYYY-MM-DD
     * @return $this
     */
    public function fromDate($date = null) 
    {
        if(!is_numeric($date)) {
            $date = strtotime($date);
            if(!$date) {
                throw new \Exception('Wrong date');
            }
        }
        
        $this->_dateFrom = strtotime(date('Y-m-d 00:00:00', $date));
        
        return $this;
    }
    
    public function getDateFrom($format = null)
    {
        return $format ? date($format, $this->_dateFrom) : $this->_dateFrom;
    }

    /**
     * @param str $date YYYY-MM-DD
     * @return $this
     */
    public function toDate($date)
    {
        if(!is_numeric($date)) {
            $date = strtotime($date);
            if(!$date) {
                throw new \Exception('Wrong date');
            }
        }
        
        $this->_dateTo = strtotime(date('Y-m-d 23:59:59', $date));
        
        return $this;
    }
    
    public function getDateTo($format = null)
    {
        return $format ? date($format, $this->_dateTo) : $this->_dateTo;
    }

}
