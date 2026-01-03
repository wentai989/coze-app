<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class AdminPagesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('admin_pages')->delete();
        
        
        
    }
}