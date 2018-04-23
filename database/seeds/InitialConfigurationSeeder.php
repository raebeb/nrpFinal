<?php

use App\User;
use App\Role;
use App\Hospital;
use App\Country;
use App\Service;

use Illuminate\Database\Seeder;

class InitialConfigurationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::truncate();
        Role::truncate();
        Hospital::truncate();
        Country::truncate();
        Service::truncate();

        Country::create([
          'name' => 'Chile',
        ]);

        Country::create([
          'name' => 'Colombia',
        ]);

        Hospital::Create([
          'name' => 'Hospital Dr. Sótero del Río',
          'country_id' => '1',
        ]);

        Hospital::Create([
          'name' => 'Clínica Las Condes',
          'country_id' => '2',
        ]);

        Role::create([
          'name' => 'admin',
          'display_name' => 'Administrador',
        ]);

        Role::create([
          'name' => 'moderador',
          'display_name' => 'Jefe de recursos humanos',
        ]);

        Role::create([
          'name' => 'autor',
          'display_name' => 'Enfermera jefe',
        ]);

        User::create([
          'name' => 'Francisca',
          'lastname' => 'Osores',
          'email' => 'fran@unab.cl',
          'password' => bcrypt('unab.toor'),
          'status' => '0',
          'role_id' => '1',
          'hospital_id' => '1',
        ]);

        User::create([
          'name' => 'Byron',
          'lastname' => 'Oyarzún',
          'email' => 'byron@unab.cl',
          'password' => bcrypt('unab.toor'),
          'status' => '0',
          'role_id' => '1',
          'hospital_id' => '2',
        ]);

        User::create([
          'name' => 'Hayde',
          'lastname' => 'Mora',
          'email' => 'hayde@unab.cl',
          'password' => bcrypt('unab.toor'),
          'status' => '0',
          'role_id' => '3',
          'hospital_id' => '2',
        ]);

        Service::create([
          'name' => 'Enfermeria',
        ]);

    }
}
