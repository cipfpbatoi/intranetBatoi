<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class NewProject extends Seeder
{

    public function run()
    {
        $sql = file_get_contents(public_path('initial.sql'));
        DB::beginTransaction();
        DB::unprepared($sql);
        DB::commit();
    }

}
