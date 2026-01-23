<?php

require 'autoload.php';
use PHPUnit\Framework\TestCase;
use \TnFAT\Planner\User\UserRecord;

class UserRecordTest extends TestCase
{
    public function testUserRecordCreation()
    {
        $user = new UserRecord('testuser', 'toosimple');
        $this->assertFalse($user->canBeCreated());
        

    }
}