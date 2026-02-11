<?php

use PHPUnit\Framework\TestCase;
use \TnFAT\Planner\User\UserRecord;

class UserRecordTest extends TestCase
{
    public function testUserRecordCreationFailsWithSimplePassword() {
        $user = new UserRecord('testuser', '123');
        $this->assertFalse($user->canBeCreated());
    }

    public function testUserRecordCreationFailsWithoutRole() {
        $user = new UserRecord('testuser', 'N0tSoS1mple');
        //$this->expectException(\PDOException::class);
        $this->assertFalse($user->create());
    }

    public function testUserRecordCreationWorksWithRole() {
        $user = new UserRecord('testuser', 'N0tSoS1mple');
        $user->setRole('admin');
        $this->assertTrue($user->canBeCreated());
    }

    public function testUserRecordCreationFailsWithSimpleName() {
        $user = new UserRecord('testuser', 'Abc');
        $this->assertFalse($user->create());
    }

    public function testUserRecordCreationFailsWithDisallowedName() {
        $user = new UserRecord('testuser', 'Abc?');
        $this->assertFalse($user->create());
        $user = new UserRecord('testuser', 'Abc#');
        $this->assertFalse($user->create());
        $user = new UserRecord('testuser', 'Abc\\');
        $this->assertFalse($user->create());
        $user = new UserRecord('testuser', 'Abc/');
        $this->assertFalse($user->create());      
    }
}