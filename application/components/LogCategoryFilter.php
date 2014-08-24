<?php

class LogCategoryFilter extends CLogFilter
{
    public $categories;

    public function filter(&$logs)
    {
        $skipedCategories = is_array($this->categories)
            ? $this->categories
            : array_map('trim', explode(',', $this->categories));
        
        foreach ($logs as $i => $log) {
            
            $category = $log[2];
            
            foreach ($skipedCategories as $skipedCategory) {
                
                // exact match
                if ($skipedCategory === $category) {
                    unset($logs[$i]);
                    break;
                }
                
                // category in skip list has no asterisk
                if(strpos($skipedCategory, '.*') === false) {
                    break;
                }
                
                $category = explode('.', $category);
                $skipedCategory = explode('.', $skipedCategory);
                
                if(count($category) !== count($skipedCategory)) {
                    break;
                }
                
                foreach($skipedCategory as $j => $skipedCategorySection) {
                    if($skipedCategorySection !== '*' && $skipedCategorySection !== $category[$j]) {
                        break;
                    }
                }
                
                unset($logs[$i]);
            }
        }
        
        $this->format($logs);

        return $logs;
    }

}
