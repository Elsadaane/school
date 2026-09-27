<?php

namespace App\Helpers;

class NumberToArabicWords
{
    public static function convert($number)
    {
        if ($number == 0) {
            return 'صفر';
        }

        $ones = ['', 'واحد', 'اثنان', 'ثلاثة', 'أربعة', 'خمسة', 'ستة', 'سبعة', 'ثمانية', 'تسعة'];
        $teens = ['عشرة', 'أحد عشر', 'اثنا عشر', 'ثلاثة عشر', 'أربعة عشر', 'خمسة عشر', 'ستة عشر', 'سبعة عشر', 'ثمانية عشر', 'تسعة عشر'];
        $tens = ['', '', 'عشرون', 'ثلاثون', 'أربعون', 'خمسون', 'ستون', 'سبعون', 'ثمانون', 'تسعون'];
        $hundreds = ['', 'مئة', 'مئتان', 'ثلاثمئة', 'أربعمئة', 'خمسمئة', 'ستمئة', 'سبعمئة', 'ثمانمئة', 'تسعمئة'];
        
        $thousands = ['', 'ألف', 'ألفان', 'آلاف'];
        $millions = ['', 'مليون', 'مليونان', 'ملايين'];

        $number = round($number, 3);
        $integerPart = floor($number);
        $decimalPart = round(($number - $integerPart) * 1000);

        $words = self::convertInteger($integerPart, $ones, $teens, $tens, $hundreds, $thousands, $millions);

        if ($decimalPart > 0) {
            $decimalWords = self::convertInteger($decimalPart, $ones, $teens, $tens, $hundreds, $thousands, $millions);
            $words .= ' و ' . $decimalWords . ' بيسة';
        }

        return $words;
    }

    private static function convertInteger($number, $ones, $teens, $tens, $hundreds, $thousands, $millions)
    {
        if ($number == 0) {
            return '';
        }

        $words = '';

        // الملايين
        if ($number >= 1000000) {
            $millionsPart = floor($number / 1000000);
            $number %= 1000000;
            
            if ($millionsPart == 1) {
                $words .= 'مليون ';
            } elseif ($millionsPart == 2) {
                $words .= 'مليونان ';
            } elseif ($millionsPart >= 3 && $millionsPart <= 10) {
                $words .= self::convertLessThanThousand($millionsPart, $ones, $teens, $tens, $hundreds) . ' ملايين ';
            } else {
                $words .= self::convertLessThanThousand($millionsPart, $ones, $teens, $tens, $hundreds) . ' مليون ';
            }
        }

        // الآلاف
        if ($number >= 1000) {
            $thousandsPart = floor($number / 1000);
            $number %= 1000;
            
            if ($thousandsPart == 1) {
                $words .= 'ألف ';
            } elseif ($thousandsPart == 2) {
                $words .= 'ألفان ';
            } elseif ($thousandsPart >= 3 && $thousandsPart <= 10) {
                $words .= self::convertLessThanThousand($thousandsPart, $ones, $teens, $tens, $hundreds) . ' آلاف ';
            } else {
                $words .= self::convertLessThanThousand($thousandsPart, $ones, $teens, $tens, $hundreds) . ' ألف ';
            }
        }

        // الباقي
        if ($number > 0) {
            $words .= self::convertLessThanThousand($number, $ones, $teens, $tens, $hundreds);
        }

        return trim($words);
    }

    private static function convertLessThanThousand($number, $ones, $teens, $tens, $hundreds)
    {
        $words = '';

        if ($number >= 100) {
            $hundredsPart = floor($number / 100);
            $number %= 100;
            $words .= $hundreds[$hundredsPart] . ' ';
        }

        if ($number >= 20) {
            $tensPart = floor($number / 10);
            $onesPart = $number % 10;
            
            if ($onesPart > 0) {
                $words .= $ones[$onesPart] . ' و' . $tens[$tensPart] . ' ';
            } else {
                $words .= $tens[$tensPart] . ' ';
            }
        } elseif ($number >= 10) {
            $words .= $teens[$number - 10] . ' ';
        } elseif ($number > 0) {
            $words .= $ones[$number] . ' ';
        }

        return trim($words);
    }
}