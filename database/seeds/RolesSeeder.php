<?php

use Illuminate\Database\Seeder;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

    	\App\Role::where('id', '>', 0)->delete();

	    \App\Role::create([
	    	'role_name' => 'admin'
	    ]);

	    \App\Role::create([
	    	'role_name' => 'user'
	    ]);
    }
}
