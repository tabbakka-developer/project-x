<?php

use Illuminate\Database\Seeder;

class LayoutsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	\App\Template::where('id', '>', 0)->delete();
        \App\Template::create([
        	'name' => 'Layout 1',
	        'css_path' => asset('css/layout_1.css')
        ]);

        \App\Template::create([
        	'name' => 'Layout 2',
	        'css_path' => asset('css/layout_2.css')
        ]);
    }
}
