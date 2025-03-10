<?php 

namespace Jaisocx\ObjData;

use Jaisocx\ObjData\ObjData;

class ExampleObjData {

  static public function testParsing() {
    $jsonFileRelativePath = join(
      "",
      [
        ".",
        "/test_data",
        "/ease.od"
      ]
    );

    $odFilePath = realpath( $jsonFileRelativePath );
    $odFileContent = file_get_contents( $odFilePath );
    $bitsBuf = unpack('C*', $odFileContent); 
    $phpArray = ObjData::parse( $bitsBuf );

    header("Content-Type: application/json; charset=" . ObjData::CHARSET, true);
    header("Content-Disposition: inline", true);

    echo json_encode( $phpArray, JSON_PRETTY_PRINT );

  }


  static public function testSerialization() {

    $jsonFileRelativePath = join(
      "",
      [
        ".",
        "/test_data",
        "/ease.json"
      ]
    );

    $jsonFilePath = realpath( $jsonFileRelativePath );
    $json = file_get_contents( $jsonFilePath );
    $phpArray = json_decode( $json, true );

    $objdata = ObjData::serialize( $phpArray );

    $objdataRelativePath = join(
      "",
      [
        ".",
        "/test_data",
        "/ease.od"
      ]
    );

    $objdataSaveResult = file_put_contents( $objdataRelativePath, $objdata );

//    header("Content-Type: text/plain; charset=UTF-8", true);
    header("Content-Type: application/objdata", true);
    header("Content-Disposition: inline", true);
    header("Content-Encoding: gzip", true);

    echo gzencode( $objdata );
  }
}

