<?php

require 'autoload.php';
use PHPUnit\Framework\TestCase;
use \TnFAT\Planner\User\UserRecord;

class UserRecordTest extends TestCase
{
    public function testUserRecordCreationFailsWithSimplePassword() {
        $user = new UserRecord('testuser', 'toosimple');
        $this->assertFalse($user->canBeCreated());
    }

    public function testUserRecordCreationFailsWithoutRole() {
        $user = new UserRecord('testuser', 'N0tSoS1mple');
        $this->assertFalse($user->canBeCreated());

        $user->setRole('admin');
        $this->assertTrue($user->canBeCreated());
    }

    public function testUserRecordCreationWorksWithRole() {
        $user = new UserRecord('testuser', 'N0tSoS1mple');
        $user->setRole('admin');
        $this->assertTrue($user->canBeCreated());
    }
}