<?php

class Initial extends \Sokil\Mongo\Migrator\AbstractMigration
{
    public function up()
    {
        $this->getCollection('users')->createDocument(array(
            "email"     => "admin@ukr.net", 
            "password"  => "$2y$07$53f4c9a61eea000000000urI3HJyhlFg.OEZotqEEg4.0n1sJ5Zbu", // admin
            "salt"      => "53f4c9a61eea0", 
            "role"      => "manager", 
            "name"      => "admin", 
            "phone"     => "", 
            "status"    => "ACTIVE",
        ));
    }
    
    public function down()
    {
        
    }
}