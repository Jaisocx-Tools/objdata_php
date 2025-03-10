# jaisocx/objdata
ObjData format for objects and arrays in old good style of packets with fixed lengths and offsets fields.

1. npm install
2. composer install


## related URLs

#### See in action:
[https://workspace.brightday.email/ExampleSimple_ObjDataByPhpEndpoint.html](https://workspace.brightday.email/ExampleSimple_ObjDataByPhpEndpoint.html)

#### SourceCode
[https://github.com/Jaisocx-Tools/Workspace/blob/main/code/ts/www/ExampleSimple_ObjDataByPhpEndpoint.html](https://github.com/Jaisocx-Tools/Workspace/blob/main/code/ts/www/ExampleSimple_ObjDataByPhpEndpoint.html)


#### Related OpenSource
1. PHP Composer lib: [https://github.com/Jaisocx-Tools/objdata_php](https://github.com/Jaisocx-Tools/objdata_php)
2. PHP example ObjData usage: [https://github.com/Jaisocx-Tools/Workspace/tree/main/code/php/objdata_example](https://github.com/Jaisocx-Tools/Workspace/tree/main/code/php/objdata_example)
3. JS ObjData lib: [https://github.com/Jaisocx-Tools/Workspace/tree/main/code/ts/www/packages/ObjData](https://github.com/Jaisocx-Tools/Workspace/tree/main/code/ts/www/packages/ObjData)
4. How to get ObjData with JS in a web browser: [https://github.com/Jaisocx-Tools/Workspace/tree/main/code/ts/www/packages/Api](https://github.com/Jaisocx-Tools/Workspace/tree/main/code/ts/www/packages/Api)
5. Jaisocx-Tools repos: [https://github.com/orgs/Jaisocx-Tools/repositories](https://github.com/orgs/Jaisocx-Tools/repositories)
6. Jaisocx-Tools for TypeScript and JavaScript development, "Workspace" repo: [https://github.com/Jaisocx-Tools/Workspace](https://github.com/Jaisocx-Tools/Workspace)




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



## How to develop in TypeScript or JavaScript for use in web browsers

### npm install
```
npm install @jaisocx/objdata
```

### Basic usage
```
import { ObjData } from "@jaisocx/objdata";

const obj: any = ObjData.parse( objdataFormattedBytebuf );
```


### Basic usage from a remote URL.
```
fetch( "https://example.com/some-url/data.od" )
  .then( ( response: Response ): Promise<ArrayBuffer> => {
    return response.arrayBuffer();
  })
  .then( ( buf: ArrayBuffer ): any => {
    let objdata: Uint8Array = new Uint8Array( buf, 0, buf.byteLength );

    // obtaining JS object or array.
    let obj: any = ObjData.parse( objdata );
    return obj;
  });
```

