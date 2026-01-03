<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class AdminRelationshipsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('admin_relationships')->delete();
        
        
        
    }
}