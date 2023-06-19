<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Banner;
use Illuminate\Support\Facades\Validator;

class BannerController extends Controller
{
  private $image_ext = ["jpg", "jpeg", "png", "gif"];
  private $video_ext = ["mp4", "mpeg"];

  private function getType($ext)
  {
    if (in_array($ext, $this->image_ext)) {
      return "image";
    }

    if (in_array($ext, $this->video_ext)) {
      return "video";
    }

    return false;
  }

  //setting
  public function list_all()
  {
    $data = Banner::select()
      ->orderBy("banner_title", "ASC")
      ->get();
    $list = [];
    foreach ($data as $d) {
      $list[$d->banner_id] = $d;
    }

    return json_encode($list);
  }

  public function list_image($status = 0)
  {
    $data = Banner::select()
      ->where("banner_type", "image")
      ->orderBy("banner_title", "ASC");

    if ($status == 1) {
      $data = $data->where("banner_status", 1);
    }
    $data = $data->get();
    $list = [];
    foreach ($data as $d) {
      $list[] = $d;
    }

    return response()->json($list);
  }

  public function list_video($status = 0)
  {
    $data = Banner::select()
      ->where("banner_type", "video")
      ->orderBy("banner_title", "ASC");

    if ($status == 1) {
      $data = $data->where("banner_status", 1);
    }
    $data = $data->get();
    $list = [];
    foreach ($data as $d) {
      $list[] = $d;
    }

    return response()->json($list);
  }

  public function list_video_array($status = 0)
  {
    $data = Banner::select()
      ->where("banner_type", "video")
      ->orderBy("banner_title", "ASC");

    if ($status == 1) {
      $data = $data->where("banner_status", 1);
    }
    $data = $data->get();
    $list = [];
    foreach ($data as $d) {
      $list[] = $d->banner_path;
    }

    return json_encode($list);
  }

  public function del(Request $request)
  {
    $id = $request->get("banner_id");
    $res = Banner::where("banner_id", "=", $id)->delete()
      ? "SUCCESS"
      : "FAILED";

    return json_encode(["res" => $res]);
  }

  public function save(Request $request)
  {
    $banner = $request->all();

    $uploadedFile = $request->file("file");
    if ($uploadedFile) {
      $ext = $uploadedFile->getClientOriginalExtension();
      $type = $this->getType($ext);
      $banner["banner_type"] = $type;

      if ($type == "image") {
        $banner["banner_path"] = $uploadedFile->store("images/banners");
      } elseif ($type == "video") {
        $banner["banner_path"] = $uploadedFile->store("videos/banners");
      }

      $banner["banner_mime"] = $uploadedFile->getClientMimeType();
    }

    $res = false;
    unset($banner["file"]);

    if (isset($banner["banner_id"]) && !empty($banner["banner_id"])) {
      if ($b = Banner::find($banner["banner_id"])) {
        if (!empty($banner["banner_path"])) {
          $file_previous = public_path($b["banner_path"]);
          if (file_exists($file_previous)) {
            unlink($file_previous);
          }
        }

        try {
          Banner::where("banner_id", $banner["banner_id"])->update($banner);
          $res = true;
        } catch (\Illuminate\Database\QueryException $e) {
          $res = false;
        }
      }
    } else {
      $banner["banner_created"] = date("Y-m-d H:i:s");
      $banner["banner_author"] = Auth::User()->email;
      $res = Banner::create($banner);
    }

    $res = $res ? "SUCCESS" : "FAILED";
    return json_encode(["res" => $res]);
  }

  public function save_config(Request $request)
  {
    $req = $request->all();
    $config = $req["config"];

    DB::table("ant_config")
      ->where("config_key", $config["config_key"])
      ->update($config);

    Banner::where("banner_type", $config["config_value"])->update([
      "banner_status" => 0,
    ]);
    foreach ($req["banner"] as $id) {
      $res = Banner::where("banner_id", $id)->update(["banner_status" => 1]);
    }

    $res = "SUCCESS";
    return json_encode(["res" => $res]);
  }

  public function config()
  {
    $config = DB::table("ant_config")
      ->where("config_key", "display_banner")
      ->first();
    $banner = Banner::select("banner_id")
      ->where("banner_type", $config->config_value)
      ->where("banner_status", 1)
      ->get();
    $list = [];
    foreach ($banner as $b) {
      $list[] = $b->banner_id;
    }

    return json_encode([
      "type" => $config->config_value,
      "list_banner" => $list,
    ]);
  }
}
