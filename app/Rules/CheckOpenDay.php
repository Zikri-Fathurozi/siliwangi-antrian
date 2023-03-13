<?php

namespace App\Rules;

use App\Poli;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;

class CheckOpenDay implements Rule
{
  private $poli_id;
  private $tanggal_periksa;
  /**
   * Create a new rule instance.
   *
   * @return void
   */
  public function __construct($poli_id, $tanggal_periksa)
  {
    $this->poli_id = $poli_id;
    $this->tanggal_periksa = $tanggal_periksa;
  }

  /**
   * Determine if the validation rule passes.
   *
   * @param  string  $attribute
   * @param  mixed  $value
   * @return bool
   */
  public function passes($attribute, $value)
  {
    $poli = Poli::select([
      DB::raw(
        "FIND_IN_SET(DAYOFWEEK(STR_TO_DATE('" .
          $this->tanggal_periksa .
          "','%Y-%m-%d')),replace(poli_dayopen, ';', ',')) as buka"
      ),
    ])
      ->where("poli_status", 1)
      ->where("poli_id", $this->poli_id)
      ->first();

    return $poli ? ($poli->buka == "0" ? false : true) : true;
  }

  /**
   * Get the validation error message.
   *
   * @return string
   */
  public function message()
  {
    return "Pendaftaran ke Poli Ini Sedang Tutup";
  }
}
