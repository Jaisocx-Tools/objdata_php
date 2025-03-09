<?php

namespace Jaisocx\ObjData;

class ObjDataPackage {

    // Concatenate byte arrays (Uint8Array) into a single array (string)
    public static function concatByteArrays(array $arrays) {
        return implode("", $arrays);
    }

    // Parse byte buffer to number (similar to parsing to a 4-byte integer in JS)
    public static function parseByteBufToNumber($byteBuf, $offset, $len) {
        $result = 0;
        for ($loopCounter = 0; $loopCounter < $len; $loopCounter++) {
            $byteBufOffset = $offset + $loopCounter;
            $num = $byteBuf[$byteBufOffset];
            $shiftBytesNumber = ($len - 1 - $loopCounter);
            $shiftBitsNumber = ($shiftBytesNumber << 3 ); // power 3 is multiple by 8. 8 bits in a byte.
            $byteBuf[$byteBufOffset] = (($num >> $shiftBitsNumber) & 0xFF);
            $byteValueOfAChar = ord($num);
            $result |= ( $byteValueOfAChar << $shiftBitsNumber );
        }
        return $result;
    }

    // Parse byte buffer to text (similar to decoding text from a buffer in JS)
    public static function parseByteBufToText($byteBuf, $offset, $len, $charsetName) {
        $text = substr($byteBuf, $offset, $len);
        return mb_convert_encoding($text, 'UTF-8', $charsetName);
    }

    // Serialize text to byte buffer (similar to using TextEncoder in JS)
    public static function serializeTextToByteBuf($text) {
        return mb_convert_encoding($text, 'UTF-8');
    }

    // Serialize number to byte buffer (like JS's byte array handling)
    public static function serializeNumberToByteBuf($num, $len, $offset, &$byteBuf) {
        for ($loopCounter = 0; $loopCounter < $len; $loopCounter++) {
            $byteBufOffset = $offset + $loopCounter;
            $shiftBytesNumber = ($len - 1 - $loopCounter);
            $shiftBitsNumber = ($shiftBytesNumber << 3 ); // power 3 is multiple by 8. 8 bits in a byte.
            $byteBuf[$byteBufOffset] = (($num >> $shiftBitsNumber) & 0xFF);
        }
    }

    public static function serializeByteBufToString($byteBuf): string {
        $retVal = array_map( function( $byte ) { return chr( $byte ); }, $byteBuf );
        return join( "", $retVal );
    }

    public static function serializeNumberToByteBufString($num, $len) {
        $serializedByteBuf = (new \SplFixedArray($len))->toArray();
        ObjDataPackage::serializeNumberToByteBuf($num, $len, 0, $serializedByteBuf);
        $serializedString = ObjDataPackage::serializeByteBufToString( $serializedByteBuf );
        return $serializedString;
    }
    public static function isAssociativeArray(array $array): bool {
        return array_keys($array) !== range(0, count($array) - 1);
    }
    
}



