<?php

use Illuminate\Database\Seeder;
use App\Poli;
class PoliTableSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    // klinik
    // $count = Poli::count();
    // if ($count == 0) {
    //   Poli::truncate();
    //   $poli = [
    //     [
    //       "poli_id" => 1,
    //       "poli_nama" => "POLI UMUM",
    //       "poli_color" => "blue",
    //       "poli_icon" => "fa fa-user-md",
    //       "poli_status" => 1,
    //       "poli_gedung" => 1,
    //       "poli_prefix" => "U",
    //       "poli_audio" => "ruang_tensi",
    //       "poli_deskripsi" => "Pasien 0 bulan – 69 tahun",
    //       "poli_kuota" => 5,
    //       "poli_dayopen" => "2;3;4;5;6;7",
    //       "poli_closehour" => "24:00",
    //     ],
    //     [
    //       "poli_id" => 2,
    //       "poli_nama" => "POLI GIGI",
    //       "poli_color" => "red",
    //       "poli_icon" => "fa fa-shield",
    //       "poli_status" => 1,
    //       "poli_gedung" => 2,
    //       "poli_prefix" => "G",
    //       "poli_audio" => "poli_gigi",
    //       "poli_deskripsi" => "Kuota 15 pasien / Hari",
    //       "poli_kuota" => 1,
    //       "poli_dayopen" => "2;3;4;5;6",
    //       "poli_closehour" => "17:00",
    //     ],
    //     [
    //       "poli_id" => 3,
    //       "poli_nama" => "POLI KIA",
    //       "poli_color" => "purple",
    //       "poli_icon" => "fa fa-smile-o",
    //       "poli_status" => 1,
    //       "poli_gedung" => 2,
    //       "poli_prefix" => "K",
    //       "poli_audio" => "poli_kia",
    //       "poli_deskripsi" => "Kesehatan Ibu & Anak (balita sehat), untuk balita sakit langsung ke poli umum",
    //       "poli_kuota" => 0,
    //       "poli_dayopen" => "2;3;4;5;6;7",
    //       "poli_closehour" => "24:00",
    //     ],
    //     [
    //       "poli_id" => 4,
    //       "poli_nama" => "POLI LANSIA",
    //       "poli_color" => "red",
    //       "poli_icon" => "fa fa-blind",
    //       "poli_status" => 1,
    //       "poli_gedung" => 1,
    //       "poli_prefix" => "L",
    //       "poli_audio" => "poli_lansia",
    //       "poli_deskripsi" => "70 tahun keatas, Prioritas : disabilitas/cacat, kondisi pasien parah, kolonel dst",
    //       "poli_kuota" => 0,
    //       "poli_dayopen" => "2;3;4;5;6;7",
    //       "poli_closehour" => "17:00",
    //     ],
    //   ];
    //   Poli::insert($poli);
    // }

    //puskesmas
    $count = Poli::count();
    if ($count == 0) {
      Poli::truncate();
      $poli = [
        [
          "poli_id" => 1,
          "poli_nama" => "POLI UMUM",
          "poli_color" => "blue",
          "poli_icon" => "fa fa-user-md",
          "poli_status" => 1,
          "poli_gedung" => 1,
          "poli_prefix" => "U",
          "poli_audio" => "poli_umum",
          "poli_deskripsi" => "Pasien 5 - 49 tahun",
          "poli_kuota" => 5,
          "poli_dayopen" => "2;3;4;5;6;7",
          "poli_closehour" => "24:00",
        ],
        [
          "poli_id" => 2,
          "poli_nama" => "POLI ANAK",
          "poli_color" => "green",
          "poli_icon" => "fa fa-user-circle",
          "poli_status" => 1,
          "poli_gedung" => 1,
          "poli_prefix" => "A",
          "poli_audio" => "poli_anak",
          "poli_deskripsi" => "Pasien 0 - 4 tahun",
          "poli_kuota" => 0,
          "poli_dayopen" => "2;3;4;5;6;7",
          "poli_closehour" => "15:00",
        ],
        [
          "poli_id" => 3,
          "poli_nama" => "POLI LANSIA",
          "poli_color" => "red",
          "poli_icon" => "fa fa-blind",
          "poli_status" => 1,
          "poli_gedung" => 1,
          "poli_prefix" => "L",
          "poli_audio" => "poli_lansia",
          "poli_deskripsi" => "Pasien 50 tahun keatas",
          "poli_kuota" => 0,
          "poli_dayopen" => "2;3;4;5;6;7",
          "poli_closehour" => "17:00",
        ],
        [
          "poli_id" => 4,
          "poli_nama" => "POLI GIGI",
          "poli_color" => "red",
          "poli_icon" => "fa fa-shield",
          "poli_status" => 1,
          "poli_gedung" => 2,
          "poli_prefix" => "G",
          "poli_audio" => "poli_gigi",
          "poli_deskripsi" => "Kuota 15 pasien / Hari",
          "poli_kuota" => 1,
          "poli_dayopen" => "2;3;4;5;6",
          "poli_closehour" => "17:00",
        ],
        [
          "poli_id" => 5,
          "poli_nama" => "POLI KIA",
          "poli_color" => "purple",
          "poli_icon" => "fa fa-smile-o",
          "poli_status" => 1,
          "poli_gedung" => 2,
          "poli_prefix" => "K",
          "poli_audio" => "poli_kia",
          "poli_deskripsi" => "Kesehatan Ibu & Anak",
          "poli_kuota" => 0,
          "poli_dayopen" => "2;3;4;5;6;7",
          "poli_closehour" => "24:00",
        ],
        [
          "poli_id" => 6,
          "poli_nama" => "POLI TB/DOTS",
          "poli_color" => "orange",
          "poli_icon" => "fa fa-user",
          "poli_status" => 1,
          "poli_gedung" => 2,
          "poli_prefix" => "T",
          "poli_audio" => "poli_tb",
          "poli_deskripsi" => "Tuberkulosis Paru",
          "poli_kuota" => 0,
          "poli_dayopen" => "2;3;4;5;6;7",
          "poli_closehour" => "17:00",
        ],
        [
          "poli_id" => 7,
          "poli_nama" => "POLI KONSELING",
          "poli_color" => "green",
          "poli_icon" => "fa fa-comment",
          "poli_status" => 1,
          "poli_gedung" => 2,
          "poli_prefix" => "S",
          "poli_audio" => "poli_konseling",
          "poli_deskripsi" => "",
          "poli_kuota" => 0,
          "poli_dayopen" => "2;3;4;5;6;7",
          "poli_closehour" => "17:00",
        ],
        [
          "poli_id" => 8,
          "poli_nama" => "POLI RUJUKAN",
          "poli_color" => "blue",
          "poli_icon" => "fa fa-comment",
          "poli_status" => 1,
          "poli_gedung" => 1,
          "poli_prefix" => "R",
          "poli_audio" => "poli_rujukan",
          "poli_deskripsi" => "Batas Kuota 10 pasien",
          "poli_kuota" => 1,
          "poli_dayopen" => "	2;3;4;5;6;7",
          "poli_closehour" => "17:00",
        ],
      ];
      Poli::insert($poli);
    }
  }
}
