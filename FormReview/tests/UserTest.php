<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UserTest extends TestCase
{
    public function testLogin()
    {
        $this->visit('/login')
            ->type('ahlinyi@qq.com', 'email')
            ->type('123456', 'password')
            ->press('登录')
            ->seePageIs('/web/form');
    }
}
