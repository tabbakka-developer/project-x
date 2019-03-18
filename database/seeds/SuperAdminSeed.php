<?php

use Illuminate\Database\Seeder;

class SuperAdminSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	\App\User::where('name', 'Super Admin')->delete();

        \App\User::createAdmin([
        	'name' => 'Super Admin',
	        'email' => 'superadmin@admin.com',
	        'password' => 'admin1234',
	        'phoneNumber' => '+380001232323'
        ]);
    }
}
