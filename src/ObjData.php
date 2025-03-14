<?php

namespace Jaisocx\ObjData;

use Jaisocx\ObjData\ObjDataParser;
use Jaisocx\ObjData\ObjDataSerializer;

class ObjData {

  const CHARSET = "UTF-8";

  public static function parse($objDataByteBuf) {
    return ObjDataParser::parse($objDataByteBuf);
  }

  public static function serialize($anyValue) {
    return ObjDataSerializer::serialize($anyValue);
  }
}

