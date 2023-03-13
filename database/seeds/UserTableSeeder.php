<?php

use Illuminate\Database\Seeder;
use App\Role;
use App\User;
use Illuminate\Support\Facades\Hash;

class UserTableSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $role = Role::where("role_id", "superadmin")->first();
    if (!$role) {
      Role::create([
        "role_id" => "superadmin",
        "role_nama" => "Super Admin",
        "role_deskripsi" => "role for Super Admin / Developer / Support",
      ]);
    }

    $role = Role::where("role_id", "admin")->first();
    if (!$role) {
      Role::create([
        "role_id" => "admin",
        "role_nama" => "Admin",
        "role_deskripsi" => "role for Admin",
      ]);
    }

    $role = Role::where("role_id", "pendaftaran")->first();
    if (!$role) {
      Role::create([
        "role_id" => "pendaftaran",
        "role_nama" => "Pendaftaran",
        "role_deskripsi" => "Petugas Pendaftaran",
      ]);
    }

    $role = Role::where("role_id", "poli")->first();
    if (!$role) {
      Role::create([
        "role_id" => "poli",
        "role_nama" => "Poli",
        "role_deskripsi" => "Petugas Poli",
      ]);
    }

    $role = Role::where("role_id", "channel")->first();
    if (!$role) {
      Role::create([
        "role_id" => "channel",
        "role_nama" => "Channel",
        "role_deskripsi" => "Channel / Mitra integrasi",
      ]);
    }

    $user = User::where("email", "superadmin@email.com")->first();
    if (!$user) {
      User::create([
        "name" => "SuperAdmin",
        "email" => "superadmin@email.com",
        "password" => Hash::make("1234"),
        "role" => "superadmin",
      ]);
    }

    $user = User::where("email", "admin@email.com")->first();
    if (!$user) {
      User::create([
        "name" => "Administrator",
        "email" => "admin@email.com",
        "password" => Hash::make("1234"),
        "role" => "admin",
      ]);
    }

    $user = User::where("email", "loket1@email.com")->first();
    if (!$user) {
      User::create([
        "name" => "Loket 1",
        "email" => "loket1@email.com",
        "password" => Hash::make("1234"),
        "loket" => 1,
        "role" => "pendaftaran",
      ]);
    }

    $user = User::where("email", "BpjsAkses123")->first();
    if (!$user) {
      User::create([
        "name" => "BPJS",
        "email" => "BpjsAkses123",
        "password" => Hash::make("BpjsAkses123"),
        "role" => "channel",
      ]);
    }

    $user = User::where("email", "Migrasi123!@#")->first();
    if (!$user) {
      User::create([
        "name" => "Miggrasi Internal",
        "email" => "Migrasi123!@#",
        "password" => Hash::make("Migrasi123!@#"),
        "role" => "channel",
      ]);
    }
  }
}
