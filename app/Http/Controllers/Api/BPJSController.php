<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseApiPendaftaranController;
use Illuminate\Http\Request;
use App\Tiket;
use App\Pasien;
use App\Rules\CheckOpenDay;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Rules\NotToday;

class BPJSController extends BaseApiPendaftaranController
{
  //bpjs or mobile jkn channel mengacu pada table ant_channel
  private $channel_id = 2;

  // @override
  protected $mapPoli = [
    "001" => [
      "id" => 1,
      "nama" => "Poli Umum",
    ],
    "002" => [
      "id" => 2,
      "nama" => "Poli Gigi",
    ],
  ];

  public function index()
  {
    return response()->json(["message" => "Koneksi Tersambung"], 200);
  }

  /*
    $kodepoli = 001 poli umum, 002 poli gigi,
    $tanggalperiksa format YYYY-MM-DD
    */
  public function getStatusAntrian($kodepoli, $tanggalantrian)
  {
    // assign to private variable
    $poli = $this->_getMapPoli($kodepoli);

    $reqArray = [
      "kodepoli" => $kodepoli,
      "tanggalantrian" => $tanggalantrian,
    ];
    $validator = Validator::make($reqArray, [
      "kodepoli" => ["required", Rule::in(array_keys($this->mapPoli))],
      "tanggalantrian" => [
        "required",
        "date_format:Y-m-d",
        new NotToday(),
        "after:" . now()->format("Y-m-d"),
        new CheckOpenDay($poli->id, $tanggalantrian),
      ],
    ]);
    if ($validator->fails()) {
      return $this->responseWithError($validator->getMessageBag(), 500);
    }

    // assign to private variable
    $this->tanggalperiksa = $tanggalantrian;

    // response
    $total_antrian = $this->_getTotalPendaftar();
    $sisa_antrian = $this->_getSisaKuota();
    $antrian_panggil = $this->_getAntrianPanggil();

    return $this->responseWithSuccess([
      "namapoli" => $poli->nama,
      "totalantrean" => $total_antrian,
      "sisaantrean" => $sisa_antrian,
      "antreanpanggil" => $antrian_panggil,
      "keterangan" => "",
    ]);
  }

  public function registerAntrian(Request $request)
  {
    // assign to private variable
    $poli = $this->_getMapPoli($request->kodepoli);

    $validator = Validator::make($request->all(), [
      "nomorkartu" => "required|digits:13",
      "nik" => "required|digits:16",
      "kodepoli" => ["required", Rule::in(array_keys($this->mapPoli))],
      "tanggalperiksa" => [
        "required",
        "date_format:Y-m-d",
        "after:" . now()->format("Y-m-d"),
        "before_or_equal:" .
        now()
          ->addDays(config("antrian.online_booking.max_days"))
          ->format("Y-m-d"),
        new CheckOpenDay($poli->id, $request->tanggalperiksa),
      ],
      "keluhan" => "nullable",
    ]);
    if ($validator->fails()) {
      return $this->responseWithError($validator->getMessageBag(), 500);
    }
    // assign to private variable
    $this->tanggalperiksa = $request->tanggalperiksa;

    // validasi apakah pasien sudah terdaftar atau belum
    $pasien = Pasien::where("pasien_nik", $request->nik)
      ->where("pasien_nomor_kartu", $request->nomorkartu)
      ->first();

    if (!$pasien) {
      return $this->responseWithError(
        "Data pasien ini tidak ditemukan, silahkan Melakukan Registrasi Pasien Baru",
        500,
        202
      );
    }

    // validasi apakah sudah pernah daftar sebelumnya
    $antrian = Tiket::select("tiket_nomor")
      ->join(
        "ant_pasien",
        "ant_pasien.pasien_nik",
        "ant_tiket.tiket_pasien_nik"
      )
      ->where("ant_pasien.pasien_nomor_kartu", $request->nomorkartu)
      ->where("ant_tiket.tiket_poli_id", $poli->id)
      ->where(
        DB::raw('DATE_FORMAT(tiket_date,"%Y-%m-%d")'),
        $request->tanggalperiksa
      )
      ->first();

    if ($antrian) {
      return $this->responseWithError(
        "Nomor Antrean Hanya Dapat Diambil 1 Kali Pada Tanggal dan Poli Yang Sama",
        500
      );
    }

    $nomor_antri_poli = "";
    $is_prioritas = false;
    $success = true;
    $error = 0;
    do {
      $nomor_antri_poli = $this->get_nomor_poli(
        $poli->id,
        $is_prioritas,
        $request->tanggalperiksa
      );
      try {
        // insert ke table tiket
        Tiket::insert([
          "tiket_id" => Uuid::uuid1(),
          "tiket_nomor" => $nomor_antri_poli->nomor,
          "tiket_created" => date("Y-m-d H:i:s"),
          "tiket_poli_id" => $poli->id,
          "tiket_prioritas" => $is_prioritas ? 1 : 0,
          "tiket_date" => $request->tanggalperiksa,
          "tiket_channel_id" => $this->channel_id,
          "tiket_pasien_nik" => $request->nik,
          "tiket_pasien_keluhan" => $request->keluhan,
          "tiket_created_by" => $request->user()->id,
        ]);
        $success = true;
      } catch (\Illuminate\Database\QueryException $exception) {
        if (
          isset($exception->errorInfo[1]) &&
          $exception->errorInfo[1] == "1062"
        ) {
          $success = false;
        }
        $error++;
      }

      if ($error > 2) {
        return "failed";
      }
    } while ($success == false);

    // response
    $sisa_antrian = $this->_getSisaKuota();
    $antrian_panggil = $this->_getAntrianPanggil();
    return $this->responseWithSuccess([
      "nomorantrean" => $nomor_antri_poli->nomor,
      "angkaantrean" => $nomor_antri_poli->angka,
      "namapoli" => $poli->nama,
      "sisaantrean" => $sisa_antrian,
      "antreanpanggil" => $antrian_panggil,
      "keterangan" => "",
    ]);
  }

  public function getSisaAntrian($nomorkartu_jkn, $kodepoli, $tanggalperiksa)
  {
    $reqArray = [
      "nomorkartu_jkn" => $nomorkartu_jkn,
      "kodepoli" => $kodepoli,
      "tanggalperiksa" => $tanggalperiksa,
    ];
    $validator = Validator::make($reqArray, [
      "nomorkartu_jkn" => "required|digits:13",
      "kodepoli" => ["required", Rule::in(array_keys($this->mapPoli))],
      "tanggalperiksa" => "required|date_format:Y-m-d",
    ]);
    if ($validator->fails()) {
      return $this->responseWithError($validator->getMessageBag(), 500);
    }
    // assign to private variable
    $this->tanggalperiksa = $tanggalperiksa;
    $poli = $this->_getMapPoli($kodepoli);

    $antrian = Tiket::select("tiket_nomor")
      ->join(
        "ant_pasien",
        "ant_pasien.pasien_nik",
        "ant_tiket.tiket_pasien_nik"
      )
      ->where("ant_pasien.pasien_nomor_kartu", $nomorkartu_jkn)
      ->where("ant_tiket.tiket_poli_id", $poli->id)
      ->where(DB::raw('DATE_FORMAT(tiket_date,"%Y-%m-%d")'), $tanggalperiksa)
      ->first();

    if (!$antrian) {
      return $this->responseWithError("Antrean Tidak Ditemukan", 500);
    }

    // response
    $sisa_antrian = $this->_getSisaKuota();
    $antrian_panggil = $this->_getAntrianPanggil();
    return $this->responseWithSuccess([
      "nomorantrean" => $antrian->tiket_nomor,
      "namapoli" => $poli->nama,
      "sisaantrean" => $sisa_antrian,
      "antreanpanggil" => $antrian_panggil,
      "keterangan" => "",
    ]);
  }

  public function registerPeserta(Request $request)
  {
    $validator = Validator::make($request->all(), [
      "nomorkartu" => "required|digits:13",
      "nik" => "required|digits:16",
      "nomorkk" => "required",
      "nama" => "required",
      "jeniskelamin" => ["required", Rule::in(["L", "P"])],
      "tanggallahir" => "required|date_format:Y-m-d",
      "alamat" => "required",
      "kodeprop" => "required",
      "namaprop" => "required",
      "kodedati2" => "required",
      "namadati2" => "required",
      "kodekec" => "required",
      "namakec" => "required",
      "kodekel" => "required",
      "namakel" => "required",
      "rw" => "nullable",
      "rt" => "nullable",
    ]);

    if ($validator->fails()) {
      return $this->responseWithError($validator->getMessageBag(), 500);
    }

    $pasien = new Pasien();
    $pasien->pasien_nik = $request->nik;
    $pasien->pasien_nomor_kartu = $request->nomorkartu;
    $pasien->pasien_nomor_kk = $request->nomorkk;
    $pasien->pasien_nama = $request->nama;
    $pasien->pasien_gender = $request->jeniskelamin;
    $pasien->pasien_birth_date = $request->tanggallahir;
    $pasien->pasien_address = $request->alamat;
    $pasien->pasien_prop_kode = $request->kodeprop;
    $pasien->pasien_prop_nama = $request->namaprop;
    $pasien->pasien_dati2_kode = $request->kodedati2;
    $pasien->pasien_dati2_nama = $request->namadati2;
    $pasien->pasien_kec_kode = $request->kodekec;
    $pasien->pasien_kec_nama = $request->namakec;
    $pasien->pasien_kel_kode = $request->kodekel;
    $pasien->pasien_kel_nama = $request->namakel;
    $pasien->pasien_rt = $request->rt;
    $pasien->pasien_rw = $request->rw;

    try {
      $pasien->save();
    } catch (\Illuminate\Database\QueryException $ex) {
      return $this->responseWithError(
        "Data Peserta Sudah Pernah Dientrikan",
        500
      );
    }

    return $this->responseWithSuccess("Data Pasien Baru berhasil disimpan");
  }

  public function unregisterAntrian(Request $request)
  {
    $validator = Validator::make($request->all(), [
      "nomorkartu" => "required|digits:13",
      "kodepoli" => ["required", Rule::in(array_keys($this->mapPoli))],
      "tanggalperiksa" => "required|date_format:Y-m-d",
    ]);
    if ($validator->fails()) {
      return $this->responseWithError($validator->getMessageBag(), 500);
    }

    if ($request->tanggalperiksa == now()->format("Y-m-d")) {
      return $this->responseWithError(
        "Pasien Sudah Dilayani, Antrean Tidak Dapat Dibatalkan",
        500
      );
    }
    $poli = $this->_getMapPoli($request->kodepoli);

    $antrian = Tiket::select(["ant_tiket.tiket_id", "ant_tiket.tiket_call"])
      ->join(
        "ant_pasien",
        "ant_pasien.pasien_nik",
        "ant_tiket.tiket_pasien_nik"
      )
      ->where("ant_pasien.pasien_nomor_kartu", $request->nomorkartu)
      ->where("ant_tiket.tiket_poli_id", $poli->id)
      ->where(
        DB::raw('DATE_FORMAT(ant_tiket.tiket_date,"%Y-%m-%d")'),
        $request->tanggalperiksa
      )
      ->first();
    if (!$antrian) {
      return $this->responseWithError("Antrean Tidak Ditemukan", 500);
    } else {
      if ($antrian->trashed()) {
        return $this->responseWithError(
          "Antrean Tidak Ditemukan atau Sudah Dibatalkan",
          500
        );
      }
      if ((int) $antrian->tiket_call > 0) {
        return $this->responseWithError(
          "Pasien Sudah dilayani, Antrean Tidak Dapat Dibatalkan",
          500
        );
      }
    }

    Tiket::find($antrian->tiket_id)->delete();
    return $this->responseWithSuccess("Pembatalan antrian berhasil");
  }

  // @override
  protected function responseWithError(
    $message,
    $code,
    $codeErrorMetadata = 201
  ) {
    if (is_object($message)) {
      $messages = [];
      foreach ($message->toArray() as $field => $arrError) {
        $messages[] = $arrError[0];
      }
      $message = $messages[0];
    }

    return response()->json(
      [
        "response" => null,
        "metadata" => [
          "message" => $message,
          "code" => $codeErrorMetadata,
        ],
      ],
      $code
    );
  }
}
