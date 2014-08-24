<?php 

class LogCategoryFilterTest extends PHPUnit_Framework_TestCase
{
    public function testFilter_ExactMatch()
    {
        $filter = new LogCategoryFilter();
        $filter->categories = 'exactCategory';
        
        $logs = array(
            array(
                0   => 'message',
                1   => 'error',
                2   => 'exactCategory',
                3   => '12345678',
            ),
        );
        
        $filteredLogs = $filter->filter($logs);
        
        $this->assertEquals(array(), $filteredLogs);
    }
    
    public function testFilter_ExactNotMatch()
    {
        $filter = new LogCategoryFilter();
        $filter->categories = 'exactCategory';
        
        $logs = array(
            array(
                0   => 'message',
                1   => 'error',
                2   => 'notExactCategory',
                3   => '12345678',
            ),
        );
        
        $filteredLogs = $filter->filter($logs);
        
        $this->assertEquals($logs, $filteredLogs);
    }
    
    public function testFilter_AsteriskMatch()
    {
        $filter = new LogCategoryFilter();
        $filter->categories = 'category.*';
        
        $logs = array(
            array(
                0   => 'message',
                1   => 'error',
                2   => 'category.someSubCategory',
                3   => '12345678',
            ),
        );
        
        $filteredLogs = $filter->filter($logs);
        
        $this->assertEquals(array(), $filteredLogs);
    }
    
    public function testFilter_AsteriskNotMatch()
    {
        $filter = new LogCategoryFilter();
        $filter->categories = 'otherCategory.*';
        
        $logs = array(
            array(
                0   => 'message',
                1   => 'error',
                2   => 'category.someSubCategory',
                3   => '12345678',
            ),
        );
        
        $filteredLogs = $filter->filter($logs);
        
        $this->assertEquals($logs, $filteredLogs);
    }
}