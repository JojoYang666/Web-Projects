<?php

use Illuminate\Database\Seeder;

class FormSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('forms')->delete();

        for ($i=0; $i < 10; $i++) {
            \App\Form::create([
                'name'   => 'form '.$i,
                'title'    => 'title '.$i,
                'creator' => 1,
            ]);
        }
    }
}
