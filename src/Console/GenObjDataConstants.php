<?php 

namespace Jaisocx\ObjData\Console;


class GenObjDataConstants {

  const TEMPLATE_CLASS_NAME = "<?php 

namespace {{ NAMESPACE }};

class {{ CLASS_NAME }} {
  ";
  const TEMPLATE_CLASS_LINE = "    public const {{ CONSTANT_NAME }} = {{ CONSTANT_VALUE }};";

  protected $projectRoot = ".";
  protected $namespace = "Jaisocx\\ObjData\\Constants";
  protected $node_modules_RelativePath = "/node_modules/";
  protected $npmDependencyName = "@jaisocx/objdata";
  protected $jsonFileName = "ObjDataConstants.json";


  public static function run(): void {
    (new GenObjDataConstants())->gen();
  }

  public function gen(): void {

    $path = realpath(".");

    $jsonFileRelativePath = join(
      "", 
      [
        $this->projectRoot,
        $this->node_modules_RelativePath,
        $this->npmDependencyName,
        "/",
        $this->jsonFileName
      ]
    );

    $jsonFilePath = realpath($jsonFileRelativePath );

    if ( file_exists($jsonFilePath) === false ) {
        die("Error: ObjDataConstants.json not found. To obtain .json file You need to run this console command: npm install @jaisocx/objdata\n");
    }

    $objdataConstantsFileContents = file_get_contents($jsonFilePath);

    $toAssociativeArray = true;
    $objdataConstants = json_decode(
      $objdataConstantsFileContents, 
      $toAssociativeArray
    );

    $this->genClass(
      "FIELDS_POINTERS",
      "ObjDataConstantsFieldPointers",
      $objdataConstants
    );

    $this->genClass(
      "UNITS",
      "ObjDataConstantsUnits",
      $objdataConstants
    );

    $this->genClass(
      "DATA_TYPES",
      "ObjDataConstantsDataTypes",
      $objdataConstants
    );

  }


  public function genClass(
    string $fieldName,
    string $className,
    array $objdataConstants
  ): void {

    $classLines = [];
    $classLines[] = $this->templatePlaceholdersFill(
      GenObjDataConstants::TEMPLATE_CLASS_NAME,
      [
        "NAMESPACE" => $this->namespace,
        "CLASS_NAME" => $className
      ]
    );

    $data = $objdataConstants[$fieldName];
    foreach ($data as $key => $value) {
      $classLines[] = $this->templatePlaceholdersFill(
        GenObjDataConstants::TEMPLATE_CLASS_LINE,
        [
          "CONSTANT_NAME" => $key,
          "CONSTANT_VALUE" => var_export($value, true)
        ]
      );

    }
    $classLines[] = "\n}\n";

    $classText = join( "\n" , $classLines );

    $outputFile = join(
        [
          $this->projectRoot,
          "/src/Constants/",
          $className,
          ".php"
        ]
    );

    // Write the generated class to a PHP file
    $fileWriteResult = file_put_contents($outputFile, $classText);

    echo $fileWriteResult . "\n\n";

    if ( file_exists( $outputFile ) && is_readable( $outputFile ) ) {
      echo $outputFile . "✅ .php successfully generated!\n";
    }
  }

  public function templatePlaceholdersFill (
    $template,
    $placeholdersPayload
  ): string {

    $result = $template;

    foreach ( $placeholdersPayload as $key => $value ) {
      $result = str_ireplace( "{{ {$key} }}", $value, $result );
    }

    return $result;

  }

  

}



