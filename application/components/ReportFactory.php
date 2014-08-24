<?php

class ReportFactory extends CApplicationComponent {

    public function get($reportName) {
        
        $className = '\\Report\\' . implode('\\', array_map('ucfirst', explode('.', $reportName))) . 'Report';
        if (!class_exists($className)) {
            throw new \Exception(ucfirst($reportName) . 'Report not found');
        }

        return new $className();
    }

}
