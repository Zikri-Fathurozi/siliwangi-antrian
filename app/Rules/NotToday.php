<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class NotToday implements Rule
{
  /**
   * Create a new rule instance.
   *
   * @return void
   */
  public function __construct()
  {
    //
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
    return $value != now()->format("Y-m-d");
  }

  /**
   * Get the validation error message.
   *
   * @return string
   */
  public function message()
  {
    return "Pendaftaran Sudah Tutup, Maksimal Pendaftaran H-1";
  }
}
