<?php 

namespace Jaisocx\ObjData;

use Jaisocx\ObjData\ObjData;

class ExampleObjData {
  static public function test() {
    $jsonFileRelativePath = join(
      "",
      [
        ".",
        "/test_json",
        "/ease.json"
      ]
    );

    $jsonFilePath = realpath( $jsonFileRelativePath );
    $json = file_get_contents( $jsonFilePath );
    $phpArray = json_decode( $json, true );

    $objdata = ObjData::serialize( $phpArray );

//    header("Content-Type: text/plain; charset=UTF-8", true);
    header("Content-Type: application/octet-stream", true);
    header("Content-Disposition: inline", true);

    // echo strlen($objdata);
    echo gzencode( $objdata );
    // echo $objdata;

  }
}

