<?php
namespace App\Http\Controllers\Api;

use App\Dokter;
use App\Http\Controllers\Api\BaseApiPendaftaranController;
use Illuminate\Http\Request;
use App\Tiket;
use App\Pasien;

use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Rules\NotToday;
use App\Rules\CheckOpenDay;

class MobileController extends BaseApiPendaftaranController
{
  //mobile siliwangi channel mengacu pada table ant_channel
  private $channel_id = 3;

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
    "005" => [
      "id" => 5,
      "nama" => "Poli Vaksin",
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
    ]);
    if ($validator->fails()) {
      return $this->responseWithError($validator->getMessageBag(), 500);
    }

    // assign to private variable
    $this->tanggalperiksa = $request->tanggalperiksa;

    // validasi apakah pasien sudah terdaftar atau belum
    $pasien = Pasien::where("pasien_nik", $request->nik)
      ->where("pasien_channel", $this->channel_id)
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
      ->where("tiket_pasien_nik", $request->nik)
      ->where("tiket_poli_id", $poli->id)
      ->where(
        DB::raw('DATE_FORMAT(ant_tiket.tiket_date,"%Y-%m-%d")'),
        ">",
        now()->format("Y-m-d")
      )
      ->first();

    if ($antrian) {
      return $this->responseWithError(
        "Anda masih memiliki antrean yang belum diselesaikan pada Poli ini, jika ingin mengganti jadwal silakan batalkan dahulu antrean sebelumnya",
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
          "tiket_pasien_keluhan" => "",
          "tiket_created_by" => $request->uid,
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
      "dokter" => Dokter::where("dokter_poli_id", $poli->id)->get(),
      "sisaantrean" => $sisa_antrian,
      "antreanpanggil" => $antrian_panggil,
      "date" => $request->tanggalperiksa,
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

  public function getCurrentPeserta(Request $request)
  {
    $validator = Validator::make($request->all(), [
      "uid" => "required",
    ]);

    if ($validator->fails()) {
      return $this->responseWithError($validator->getMessageBag(), 500);
    }

    $pasien = Pasien::where("pasien_uid", $request->get("uid"))->first();
    if ($pasien) {
      return $this->responseWithSuccess($pasien);
    } else {
      return $this->responseWithError("Pasien tidak ditemukan", 404);
    }
  }

  public function updateCurrentPeserta(Request $request)
  {
    $validator = Validator::make($request->all(), [
      "uid" => "required",
      "nik" => "required|digits:16",
      "name" => "required",
      "tanggallahir" => "required|date_format:Y-m-d",
    ]);

    if ($validator->fails()) {
      return $this->responseWithError($validator->getMessageBag(), 500);
    }

    try {
      $pasien = Pasien::where("pasien_uid", $request->get("uid"))->first();
      if ($pasien) {
        $pasien->pasien_nik = $request->get("nik");
        $pasien->pasien_nama = $request->get("name");
        $pasien->pasien_birth_date = $request->get("tanggallahir");
        $pasien->save();
      } else {
        $pasien = new Pasien();
        $pasien->pasien_nik = $request->get("nik");
        $pasien->pasien_nama = $request->get("name");
        $pasien->pasien_birth_date = $request->get("tanggallahir");
        $pasien->pasien_uid = $request->get("uid");
        $pasien->pasien_channel = $this->channel_id;
        $pasien->save();
      }
    } catch (\Illuminate\Database\QueryException $exception) {
      if (
        isset($exception->errorInfo[1]) &&
        $exception->errorInfo[1] == "1062"
      ) {
        return $this->responseWithError("Nik sudah terdaftar sebelumnya", 500);
      }
      return $this->responseWithError("Gagal Update", 500);
    }

    return $this->responseWithSuccess("Berhasil diupdate");
  }

  public function getAntrianTerdekat(Request $request)
  {
    $validator = Validator::make($request->all(), [
      "uid" => "required",
    ]);

    if ($validator->fails()) {
      return $this->responseWithError($validator->getMessageBag(), 500);
    }

    $tiket = Tiket::select(["ant_tiket.tiket_id", "ant_tiket.tiket_date"])
      ->join(
        "ant_pasien",
        "ant_pasien.pasien_nik",
        "ant_tiket.tiket_pasien_nik"
      )
      ->where("ant_pasien.pasien_uid", $request->get("uid"))
      ->where(
        DB::raw('DATE_FORMAT(ant_tiket.tiket_date,"%Y-%m-%d")'),
        ">=",
        now()->format("Y-m-d")
      )
      ->orderBy("ant_tiket.tiket_date", "asc")
      ->first();
    if ($tiket) {
      return $this->responseWithSuccess($tiket);
    } else {
      return $this->responseWithError("Tiket tidak ditemukan", 404);
    }
  }

  public function getAntrianById($id, Request $request)
  {
    $validator = Validator::make($request->all(), [
      "uid" => "required",
    ]);

    if ($validator->fails()) {
      return $this->responseWithError($validator->getMessageBag(), 500);
    }

    $tiket = Tiket::select([
      "ant_pasien.pasien_nik",
      "ant_pasien.pasien_nama",
      "ant_tiket.tiket_id",
      "ant_tiket.tiket_date",
      "ant_tiket.tiket_nomor",
      "ant_tiket.tiket_status",
      "ant_poli.poli_nama",
      "ant_tiket.tiket_poli_id",
    ])
      ->join(
        "ant_pasien",
        "ant_pasien.pasien_nik",
        "ant_tiket.tiket_pasien_nik"
      )
      ->join("ant_poli", "ant_poli.poli_id", "ant_tiket.tiket_poli_id")
      ->where("ant_pasien.pasien_uid", $request->get("uid"))
      ->where("ant_tiket.tiket_id", $id)
      ->with(["doctors"])
      ->first();
    if ($tiket) {
      return $this->responseWithSuccess($tiket);
    } else {
      return $this->responseWithError("Tiket tidak ditemukan", 404);
    }
  }

  public function getMyAntrian(Request $request)
  {
    $validator = Validator::make($request->all(), [
      "uid" => "required",
    ]);

    if ($validator->fails()) {
      return $this->responseWithError($validator->getMessageBag(), 500);
    }

    $tiket = Tiket::select([
      "ant_pasien.pasien_nik",
      "ant_pasien.pasien_nama",
      "ant_tiket.tiket_id",
      "ant_tiket.tiket_date",
      "ant_tiket.tiket_nomor",
      "ant_tiket.tiket_status",
      "ant_tiket.deleted_at",
      "ant_poli.poli_nama",
    ])
      ->join(
        "ant_pasien",
        "ant_pasien.pasien_nik",
        "ant_tiket.tiket_pasien_nik"
      )
      ->join("ant_poli", "ant_poli.poli_id", "ant_tiket.tiket_poli_id")
      ->where("ant_pasien.pasien_uid", $request->get("uid"))
      ->where(
        DB::raw('DATE_FORMAT(ant_tiket.tiket_date,"%Y-%m-%d")'),
        ">=",
        now()->format("Y-m-d")
      )
      ->orderBy("ant_tiket.deleted_at", "asc")
      ->orderBy("ant_tiket.tiket_date", "asc")

      ->withTrashed()
      ->get();

    if ($tiket) {
      return $this->responseWithSuccess($tiket);
    } else {
      return $this->responseWithError("Tiket tidak ditemukan", 404);
    }
  }

  public function unregisterAntrian($id)
  {
    $antrian = Tiket::find($id);

    if (!$antrian) {
      return $this->responseWithError("Antrean tidak Ditemukan", 500);
    }

    if ($antrian->tiket_date == now()->format("Y-m-d")) {
      return $this->responseWithError(
        "Pasien Sudah Dilayani, Antrean Tidak Dapat Dibatalkan",
        500
      );
    }

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

    $antrian->delete();
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
