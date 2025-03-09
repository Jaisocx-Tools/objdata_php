<?php

namespace Jaisocx\ObjData;

use Jaisocx\ObjData\ObjDataPackage;
use Jaisocx\ObjData\Constants\ObjDataConstantsFieldPointers;
use Jaisocx\ObjData\Constants\ObjDataConstantsDataTypes;
use Jaisocx\ObjData\ObjDataHelpingProps;

class ObjDataParser {

    public static function parse($objDataByteBuf) {
        $dataHelper = ObjDataParser::parsePropHeaders($objDataByteBuf, 0);
        return ObjDataParser::parseProperty($objDataByteBuf, 0, $dataHelper, null);
    }

    public static function parseProperty($objDataByteBuf, $offset, $dataHelper, $parentObject) {
        $retValue = null;

        if ($dataHelper->datatype === ObjDataConstantsDataTypes::ARRAY ||
            $dataHelper->datatype === ObjDataConstantsDataTypes::OBJECT) {

            if ($dataHelper->datatype === ObjDataConstantsDataTypes::ARRAY) {
                $retValue = [];
            } else {
                $retValue = [];
            }

            $arrayItemsAmount = $dataHelper->propsAmount;
            $arrayItemOffset = $offset + $dataHelper->propertyValueStart;

            for ($loopCounter = 0; $loopCounter < $arrayItemsAmount; $loopCounter++) {
                $arrayItemDataHelper = ObjDataParser::parsePropHeaders($objDataByteBuf, $arrayItemOffset);
                ObjDataParser::parseProperty($objDataByteBuf, $arrayItemOffset, $arrayItemDataHelper, $retValue);
                $arrayItemOffset += $arrayItemDataHelper->lengthAll;
            }

        } elseif ($dataHelper->datatype === ObjDataConstantsDataTypes::NUMBER) {
            $retValue = ObjDataPackage::parseByteBufToNumber(
                $objDataByteBuf,
                $offset + $dataHelper->propertyValueStart,
                $dataHelper->propertyValueLength
            );

        } elseif ($dataHelper->datatype === ObjDataConstantsDataTypes::TEXT_PLAIN) {
            $retValue = ObjDataPackage::parseByteBufToText(
                $objDataByteBuf,
                $offset + $dataHelper->propertyValueStart,
                $dataHelper->propertyValueLength,
                "utf8"
            );

        } else {
            $retValue = "Hu hu";
        }

        if ($parentObject !== null) {
            $propName = null;
            if (is_array($parentObject)) {
                $propName = ObjDataPackage::parseByteBufToNumber(
                    $objDataByteBuf,
                    $offset + $dataHelper->propertyNameStart,
                    $dataHelper->propertyNameLength
                );

            } else {
                $propName = ObjDataPackage::parseByteBufToText(
                    $objDataByteBuf,
                    $offset + $dataHelper->propertyNameStart,
                    $dataHelper->propertyNameLength,
                    "utf8"
                );
            }

            $parentObject[$propName] = $retValue;
        }

        return $retValue;
    }

    public static function parsePropHeaders($byteBuf, $offset) {
        $dataHelper = new ObjDataHelpingProps();

        $fieldOffset = $offset;
        $dataHelper->lengthAll = ObjDataPackage::parseByteBufToNumber(
            $byteBuf,
            $fieldOffset,
            ObjDataConstantsFieldPointers::LENGTH_ALL_FIELD_LEN
        );

        $fieldOffset += ObjDataConstantsFieldPointers::LENGTH_ALL_FIELD_LEN;

        $dataHelper->datatype = ObjDataPackage::parseByteBufToNumber(
            $byteBuf,
            $fieldOffset,
            ObjDataConstantsFieldPointers::DATATYPE_FIELD_LEN
        );

        $fieldOffset += ObjDataConstantsFieldPointers::DATATYPE_FIELD_LEN;

        $dataHelper->numberValueUnit = ObjDataPackage::parseByteBufToNumber(
            $byteBuf,
            $fieldOffset,
            ObjDataConstantsFieldPointers::NUMBER_VALUE_UNIT_FIELD_LEN
        );

        $fieldOffset += ObjDataConstantsFieldPointers::NUMBER_VALUE_UNIT_FIELD_LEN;

        $dataHelper->propsAmount = ObjDataPackage::parseByteBufToNumber(
            $byteBuf,
            $fieldOffset,
            ObjDataConstantsFieldPointers::PROPS_AMOUNT_FIELD_LEN
        );

        $fieldOffset += ObjDataConstantsFieldPointers::PROPS_AMOUNT_FIELD_LEN;

        $dataHelper->propertyNameLength = ObjDataPackage::parseByteBufToNumber(
            $byteBuf,
            $fieldOffset,
            ObjDataConstantsFieldPointers::PROPERTY_NAME_LENGTH_FIELD_LEN
        );

        $dataHelper->propertyValueStart = $dataHelper->propertyNameStart + $dataHelper->propertyNameLength;
        $dataHelper->propertyValueLength = $dataHelper->lengthAll - $dataHelper->propertyValueStart;

        return $dataHelper;
    }

    // Fetch method to get and parse data from the server with flexible headers and method options
    public static function fetchData($url, $method = "GET", $headers = []) {
        // Fetch request with flexible headers and method
        $response = file_get_contents($url); // Use file_get_contents() or cURL in PHP for HTTP requests

        // Check for successful response
        if ($response === false) {
            throw new \Exception("HTTP error!");
        }

        // Get response as binary data (string in PHP)
        $uint8Array = unpack("C*", $response); // Unpack into an array of unsigned integers (byte data)

        // Deserialize the data
        return $uint8Array;
    }
}
