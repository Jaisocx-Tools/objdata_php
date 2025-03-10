# objdata-php
ObjData format for objects and arrays in old good style of packets with fixed lengths and offsets fields.

1. npm install
2. composer install


## Parser, ObjData to PHP Array
```
use Jaisocx\ObjData\ObjData;

$odFileContent = file_get_contents( "/some-path/data.od" );
$bitsBuf = unpack('C*', $odFileContent); 
$phpArray = ObjData::parse( $bitsBuf );
```


## Serializer, PHP Array to ObjData
```
use Jaisocx\ObjData\ObjData;

$phpArray = [
  "message" => "Hello World",
];

$objdata = ObjData::serialize( $phpArray );

header("Content-Type: application/objdata", true);
header("Content-Disposition: inline", true);
header("Content-Encoding: gzip", true);

echo gzencode( $objdata );
```

