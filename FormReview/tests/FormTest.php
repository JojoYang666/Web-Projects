<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Storage;

class FormTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testAjaxUpdate()
    {
        $user = App\User::where(['email'=>'wuhulinyi@sina.com'])->first();
        $response = $this->actingAs($user)
            ->call('POST', '/web/form/ajax-update',[
            'id'=>21,
            'name'=>'publish',
            'value'=>'false'
        ]);
        $this->assertEquals(200, $response->status());
        $this->assertEquals('{"status":0,"error":""}', $response->content());
        $this->assertEquals(false, App\Form::find(21)->publish);
    }
    public function testDataData()
    {
        $user = App\User::where(['email'=>'ahlinyi@qq.com'])->first();
        $this->actingAs($user)
            ->get('/web/form/19/datadata')->seeJsonStructure([
                '*' => [
                    '_id', 'fid', 'status'
                ]
            ]);
    }

    public function testGetAdminOpenids()
    {
        return true;
        $info = new \App\Http\Controllers\Pub\InfoController();
        $formdata = \App\FormData::find('5902ede864a4c811c056cef2');
        $openids = $info->getAdminOpenids(23,$formdata);
        var_dump($openids);
    }
}
