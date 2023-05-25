<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Exception;
use App\Helpers\MyBridgingBPJSV4;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use LZCompressor\LZString;

class BridgingBpjsController extends Controller
{
  public function index()
  {
    return response()->json(["message" => "Koneksi Tersambung"], 200);
  }

  public function getPeserta(Request $request)
  {
    $bpjsnumber = $request['no_asuransi'];
    if (strlen($bpjsnumber) == 16) {
      $bpjsnumber = (strlen($bpjsnumber) == 16) ? "nik/".$bpjsnumber : "";
    }
    $response = MyBridgingBPJSV4::sendApi("peserta/".$bpjsnumber, "");
    
    // KodePPK - Nama PPK : 00930020 - Klinik Siliwangi Kesdam Cimahi
    if (isset($response['response']['noKartu'])) {
      if ($response['response']['kdProviderPst']['kdProvider'] == '00930020') {
        $response['response']['status'] = true;
        $response['response']['keteranganFaskes'] = 'Sesuai Faskes';
      } else {
        $response['response']['status'] = true;
        $response['response']['keteranganFaskes'] = 'Diluar Faskes';
      }
    }
    return $response;
  }
}