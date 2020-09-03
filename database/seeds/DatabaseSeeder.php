<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class DatabaseSeeder extends Seeder
{
    public static $seeders = [];
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $this->call(CountriesSeeder::class);
        foreach (self::$seeders as $seeder){
            $this->call($seeder);
        }
        $this->call(UserSeeder::class);

        Model::reguard();
    }
}
