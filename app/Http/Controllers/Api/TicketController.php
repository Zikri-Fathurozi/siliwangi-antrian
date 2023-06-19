<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\EscposImage;
use Mike42\Escpos\Printer;

class TicketController extends Controller
{
  public function index()
  {
    return response()->json(["message" => "Koneksi Tersambung"], 200);
  }

  public function createRegisterTicket(Request $request)
  {
    $validator = Validator::make($request->all(), [
      "printer_alias" => "required",
      "tiket_nomor" => "required",
      "poli_nama" => "required",
      "prioritas" => "required",
      "date" => "required",
    ]);
    if ($validator->fails()) {
      return response()->json($validator->getMessageBag(), 500);
    }

    try {
      $has_logo = config("antrian.print.has_logo");
      if ($has_logo) {
        $logo = EscposImage::load(public_path("/logo-ticket.png"), false);
      }

      $connector = new WindowsPrintConnector($request->printer_alias);
      $printer = new Printer($connector);

      $copy = 2;

      for ($x = 0; $x < $copy; $x++) {
        $printer->initialize();
        $printer->setJustification(Printer::JUSTIFY_CENTER);

        if ($has_logo) {
          $printer->graphics($logo);
        }

        $printer->selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
        $printer->setEmphasis(true);
        $printer->text("\n" . strtoupper(config("antrian.print.name")));
        $printer->selectPrintMode();
        $printer->setEmphasis(false);
        $printer->text("\n" . config("antrian.print.address"));
        $printer->feed(2);

        $printer->setTextSize(1, 1);
        $printer->setEmphasis(true);
        $printer->text("Nomor Antrian\n");
        $printer->text($request->poli_nama);
        $printer->setEmphasis(false);
        $printer->feed();

        $printer->setTextSize(5, 5);
        $printer->text($request->tiket_nomor);
        $printer->feed(2);

        if ($request->prioritas) {
          $printer->setTextSize(1, 1);
          $printer->setEmphasis(true);
          $printer->text("- PRIORITAS -");
          $printer->feed(2);
        } else {
          $printer->feed(1);
        }

        $printer->setTextSize(1, 1);
        $printer->text("Mohon menunggu hingga nomor dipanggil\n");
        $printer->text($request->date);
        $printer->feed(1);

        $printer->cut();
      }

      $printer->close();
      return response()->json(["code" => 200, "message" => "Print OK"]);
    } catch (Exception $e) {
      return response()->json(
        ["code" => 500, "message" => $e->getMessage()],
        500
      );
    }
  }
}
