<?php

if (!function_exists('amountInWords')) {
    function amountInWords($number)
    {
        $no = floor($number);
        $decimal = round($number - $no, 2) * 100;

        $words = array(
            '0' => '',
            '1' => 'One',
            '2' => 'Two',
            '3' => 'Three',
            '4' => 'Four',
            '5' => 'Five',
            '6' => 'Six',
            '7' => 'Seven',
            '8' => 'Eight',
            '9' => 'Nine',
            '10' => 'Ten',
            '11' => 'Eleven',
            '12' => 'Twelve',
            '13' => 'Thirteen',
            '14' => 'Fourteen',
            '15' => 'Fifteen',
            '16' => 'Sixteen',
            '17' => 'Seventeen',
            '18' => 'Eighteen',
            '19' => 'Nineteen',
            '20' => 'Twenty',
            '30' => 'Thirty',
            '40' => 'Forty',
            '50' => 'Fifty',
            '60' => 'Sixty',
            '70' => 'Seventy',
            '80' => 'Eighty',
            '90' => 'Ninety'
        );

        $digits = array('', 'Hundred', 'Thousand', 'Lakh', 'Crore');
        $str = array();
        $i = 0;

        while ($no > 0) {
            $divider = ($i == 1) ? 10 : 100;
            $number_part = $no % $divider;
            $no = floor($no / $divider);

            if ($number_part) {
                $plural = (($number_part > 9) ? '' : '');
                $hundred = ($i == 1 && !empty($str)) ? '' : '';

                if ($number_part < 21) {
                    $str[] = $words[$number_part] . ' ' . $digits[$i];
                } else {
                    $str[] = $words[floor($number_part / 10) * 10] . ' ' . $words[$number_part % 10] . ' ' . $digits[$i];
                }
            }
            $i++;
        }

        $str = array_reverse($str);
        $result = trim(implode(' ', $str));

        if ($decimal > 0) {
            return $result . " Rupees and " . $words[$decimal] . " Paise";
        } else {
            return $result . " Rupees";
        }
    }
}
