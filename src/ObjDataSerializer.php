<?php

namespace Jaisocx\ObjData;

use Jaisocx\ObjData\ObjDataPackage;
use Jaisocx\ObjData\Constants\ObjDataConstantsFieldPointers;
use Jaisocx\ObjData\Constants\ObjDataConstantsDataTypes;
use Jaisocx\ObjData\ObjDataHelpingProps;

class ObjDataSerializer {

    public static function serialize($anyValue) {
        return ObjDataSerializer::serializeProperty("Root", $anyValue );
    }

    public static function serializeProperty($propName, $propValue) {
        $propertyNameSerialized = "";
        $propertyValueSerialized = "";
        $dataHelper = new ObjDataHelpingProps();
        $dataType = gettype($propValue);

        if ($dataType === "array") {
            // $dataType = ObjDataPackage::isAssociativeArray( $propValue ) ? "object" : "array";
            $dataType = array_is_list( $propValue ) ? "array" : "object";
        }

        if (is_numeric($propName)) {
            $propertyNameSerialized = ObjDataPackage::serializeNumberToByteBufString(
                $propName,
                ObjDataConstantsFieldPointers::PROPERTY_NAME_LENGTH_FIELD_LEN
            );
        } elseif (is_string($propName)) {
            $propertyNameSerialized = ObjDataPackage::serializeTextToByteBuf($propName);
        }

        if ($dataType === "integer" || $dataType === "float" || $dataType === "double") {
            $dataHelper->datatype = ObjDataConstantsDataTypes::NUMBER;
            $propertyValueSerialized = ObjDataPackage::serializeNumberToByteBufString(
                $propValue,
                4
            );

        } elseif ($dataType === "string") {
            $dataHelper->datatype = ObjDataConstantsDataTypes::TEXT_PLAIN;
            $propertyValueSerialized = ObjDataPackage::serializeTextToByteBuf($propValue);
        } elseif ($dataType === "boolean") {
            $dataHelper->datatype = ObjDataConstantsDataTypes::NUMBER;
            $propertyValueSerialized = $propValue ? [0, 0, 0, 1] : [0, 0, 0, 0];
        } elseif ($dataType === "object") {
            $dataHelper->datatype = ObjDataConstantsDataTypes::OBJECT;
            $objectKeys = array_keys($propValue);
            $dataHelper->propsAmount = count($objectKeys);
            $byteBufs = [];

            foreach ($objectKeys as $subPropName) {
                $subPropValue = $propValue[$subPropName];
                $subPropValueSerialized = ObjDataSerializer::serializeProperty($subPropName, $subPropValue);
                $byteBufs[] = $subPropValueSerialized;
            }
            $propertyValueSerialized = ObjDataPackage::concatByteArrays($byteBufs);
        } elseif ($dataType === "array") {
            $dataHelper->datatype = ObjDataConstantsDataTypes::ARRAY;
            $dataHelper->propsAmount = count($propValue);
            $byteBufs = [];

            foreach ($propValue as $i => $subPropValue) {
                $subPropValueSerialized = ObjDataSerializer::serializeProperty($i, $subPropValue);
                $byteBufs[] = $subPropValueSerialized;
            }
            $propertyValueSerialized = ObjDataPackage::concatByteArrays($byteBufs);
        } else {
            $dataHelper->datatype = ObjDataConstantsDataTypes::BINARY;
            $propertyValueSerialized = $propValue;
        }

        $dataHelper->propertyNameLength = is_string( $propertyNameSerialized ) ? strlen( $propertyNameSerialized ) : count($propertyNameSerialized);
        $dataHelper->propertyValueStart = $dataHelper->propertyNameStart + $dataHelper->propertyNameLength;
        $dataHelper->propertyValueLength = is_string( $propertyValueSerialized ) ? strlen( $propertyValueSerialized ) : count($propertyValueSerialized);
        $dataHelper->lengthAll = (ObjDataConstantsFieldPointers::HEADERS_LENGTH) + ($dataHelper->propertyNameLength) + ($dataHelper->propertyValueLength);

        $headers = ObjDataSerializer::serializePropHeaders($dataHelper);

        return ObjDataPackage::concatByteArrays([
            $headers,
            $propertyNameSerialized,
            $propertyValueSerialized
        ]);
    }

    public static function serializePropHeaders($dataHelper) {
        $zeroValueByte = chr(0);
        // $headersBuf = str_repeat($zeroValueByte, ObjDataConstantsFieldPointers::HEADERS_LENGTH);
        $headersBuf = (new \SplFixedArray(ObjDataConstantsFieldPointers::HEADERS_LENGTH))->toArray(); // Creates an array with a fixed length of HEADERS_LENGTH

        ObjDataPackage::serializeNumberToByteBuf(
            $dataHelper->lengthAll, 
            ObjDataConstantsFieldPointers::LENGTH_ALL_FIELD_LEN, 
            ObjDataConstantsFieldPointers::LENGTH_ALL, 
            $headersBuf
        );

        ObjDataPackage::serializeNumberToByteBuf(
            $dataHelper->datatype, 
            ObjDataConstantsFieldPointers::DATATYPE_FIELD_LEN, 
            ObjDataConstantsFieldPointers::DATATYPE, 
            $headersBuf
        );

        ObjDataPackage::serializeNumberToByteBuf(
            $dataHelper->numberValueUnit, 
            ObjDataConstantsFieldPointers::NUMBER_VALUE_UNIT_FIELD_LEN, 
            ObjDataConstantsFieldPointers::NUMBER_VALUE_UNIT, 
            $headersBuf
        );

        ObjDataPackage::serializeNumberToByteBuf(
            $dataHelper->propsAmount, 
            ObjDataConstantsFieldPointers::PROPS_AMOUNT_FIELD_LEN, 
            ObjDataConstantsFieldPointers::PROPS_AMOUNT, 
            $headersBuf
        );

        ObjDataPackage::serializeNumberToByteBuf(
            $dataHelper->propertyNameLength, 
            ObjDataConstantsFieldPointers::PROPERTY_NAME_LENGTH_FIELD_LEN, 
            ObjDataConstantsFieldPointers::PROPERTY_NAME_LENGTH, 
            $headersBuf
        );

        return ObjDataPackage::serializeByteBufToString( $headersBuf );
    }
}
