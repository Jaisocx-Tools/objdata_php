<?php

namespace Jaisocx\ObjData;


use Jaisocx\ObjData\Constants\ObjDataConstantsFieldPointers;

class ObjDataHelpingProps {

    public $lengthAll;
    public $datatype;
    public $numberValueUnit;
    public $propsAmount;
    public $propertyNameLength;
    public $propertyNameStart;
    public $propertyValueLength;
    public $propertyValueStart;

    public function __construct() {
        $this->lengthAll = 0;
        $this->datatype = 0;
        $this->numberValueUnit = 0;
        $this->propsAmount = 0;
        $this->propertyNameLength = 0;
        $this->propertyNameStart = ObjDataConstantsFieldPointers::PROPERTY_NAME_START;
        
        $this->propertyValueLength = 0;
        $this->propertyValueStart = 0;
    }
}
