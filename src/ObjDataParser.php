<?php

namespace Jaisocx\ObjData;

use Jaisocx\ObjData\ObjData;
use Jaisocx\ObjData\ObjDataPackage;
use Jaisocx\ObjData\Constants\ObjDataConstantsFieldPointers;
use Jaisocx\ObjData\Constants\ObjDataConstantsDataTypes;
use Jaisocx\ObjData\ObjDataHelpingProps;

class ObjDataParser {

    public static function parse($objDataByteBuf) {
        $dataHelper = ObjDataParser::parsePropHeaders($objDataByteBuf, 0);
        return ObjDataParser::parseProperty($objDataByteBuf, 0, $dataHelper);
    }

    public static function parseProperty(
        $objDataByteBuf, 
        $offset, 
        $dataHelper
    ) {
        $retValue = null;

        if ($dataHelper->datatype === ObjDataConstantsDataTypes::ARRAY ||
            $dataHelper->datatype === ObjDataConstantsDataTypes::OBJECT) {

            $retValue = [];

            $arrayItemsAmount = $dataHelper->propsAmount;
            $arrayItemOffset = $offset + $dataHelper->propertyValueStart;

            for ($loopCounter = 0; $loopCounter < $arrayItemsAmount; $loopCounter++) {

                $arrayItemDataHelper = ObjDataParser::parsePropHeaders($objDataByteBuf, $arrayItemOffset);

                $key = ObjDataParser::getPropName( 
                    $arrayItemDataHelper, 
                    $objDataByteBuf, 
                    $arrayItemOffset,
                    $dataHelper->datatype 
                );

                $subProp = ObjDataParser::parseProperty(
                    $objDataByteBuf, 
                    $arrayItemOffset, 
                    $arrayItemDataHelper
                );

                $retValue[$key] = $subProp;

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
                ObjData::CHARSET
            );

        } else {
            $retValue = "Hu hu";
        }

        return $retValue;
    }

    public static function getPropName (
        ObjDataHelpingProps $dataHelper,
        array $objDataByteBuf,
        int $offset,
        int $datatypeHolder
    ): string|int {
        $key = null;
        if ( $datatypeHolder === ObjDataConstantsDataTypes::ARRAY ) {
            $key = ObjDataPackage::parseByteBufToNumber(
                $objDataByteBuf,
                $offset + $dataHelper->propertyNameStart,
                $dataHelper->propertyNameLength
            );

        } else if ( $datatypeHolder === ObjDataConstantsDataTypes::OBJECT ) {
            $key = ObjDataPackage::parseByteBufToText(
                $objDataByteBuf,
                $offset + $dataHelper->propertyNameStart,
                $dataHelper->propertyNameLength,
                ObjData::CHARSET
            );
        }

        return $key;
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
}
