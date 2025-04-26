<?php

namespace App\Helpers;

use App\Models\Auth\Setting;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

class Misc
{
    public const LEVEL_UP = 'Level up';

    public const CRUISE_CONTROL = 'Cruise Control';

    public const PEAK_PERFORMANCE = 'Peak Performance';

    public const ROAD_MASTER = 'Road Master';

    public static function searchSubArray(array $array, $key, $value)
    {
        foreach ($array as $subarray) {
            if (isset($subarray[$key]) && strtolower($subarray[$key]) == strtolower($value)) {
                return $subarray;
            }
        }
    }

    public static function spinData(): array
    {
        return [
            'colorArray' => ['#ffbd49', '#ff005a', '#00ea98', '#000e20', '#2d82ec'],

            'segmentValuesArray' => [
                [
                    'type' => 'string',
                    'value' => 'Christmas Chicken^🍗',
                    'win' => true,
                    'resultText' => "You've won a pack of chicken",
                ],
                [

                    'type' => 'string',
                    'value' => 'YOU WERE^SO CLOSE^😣',
                    'win' => false,
                    'resultText' => 'Better luck next spin. Try again!',
                ],
                [

                    'type' => 'string',
                    'value' => 'FREE CAR^DIAGNOSTICS^🚗',
                    'win' => true,
                    'resultText' => "You've won a free car diagnostics",
                ],
                [

                    'type' => 'string',
                    'value' => 'BETTER LUCK^NEXT TIME^😪',
                    'win' => false,
                    'resultText' => 'Better luck next spin. Try again!',
                ],
                [
                    'type' => 'string',
                    'value' => 'YOU ARE^ALMOST THERE^👍🏽',
                    'win' => false,
                    'resultText' => 'Better luck next spin. Try again!',
                ],
                [

                    'type' => 'string',
                    'value' => '20,000^IN CASH^🤑',
                    'win' => true,
                    'resultText' => "You've won 20,000 in cash",
                ],
                [

                    'type' => 'string',
                    'value' => 'LOSE ALL',
                    'win' => false,
                    'resultText' => 'Better luck next spin. Try again!',
                ],
            ],
            'svgWidth' => 1024,
            'svgHeight' => 1024,
            'wheelStrokeColor' => '#000000',
            'wheelStrokeWidth' => 10,
            'wheelSize' => 900,
            'wheelTextOffsetY' => 120,
            'wheelTextColor' => '#EDEDED',
            'wheelTextSize' => '1.8em',
            'wheelImageOffsetY' => 40,
            'wheelImageSize' => 50,
            'centerCircleSize' => 244,
            'centerCircleStrokeColor' => '#EDEDED',
            'centerCircleStrokeWidth' => 10,
            'centerCircleFillColor' => '#EDEDED',
            'centerCircleImageUrl' => 'asset/NewWebsite/SpinWheel/logo.webp',
            'centerCircleImageWidth' => 244,
            'centerCircleImageHeight' => 244,
            'segmentStrokeColor' => '#E2E2E2',
            'segmentStrokeWidth' => 8,
            'centerX' => 512,
            'centerY' => 512,
            'hasShadows' => false,
            'numSpins' => 2,
            'spinDestinationArray' => [],
            'minSpinDuration' => 2,
            'maxSpinDuration' => 6,
            'gameOverText' => 'I HOPE YOU ENJOYED THE GAME.',
            'invalidSpinText' => 'INVALID SPIN. PLEASE SPIN AGAIN.',
            'introText' => "YOU HAVE TO<br>SPIN IT <span style='color:#ff005a;'>2</span> WIN IT!",
            'hasSound' => true,
            'gameId' => '9a0232ec06bc431114e2a7f3aea03bbe2164f1aa',
            'clickToSpin' => true,
            'spinDirection' => 'cw',
            'disabledText' => 'You have no more spins today',

        ];
    }

    public static function calculateTime($timer, $start_date): ?array
    {
        if (! is_null($timer)) {
            $date = Carbon::parse($start_date)->addMinutes($timer)->timezone('Africa/Lagos');
            $diff_In_Minutes = $date->diffInMinutes(now(), true);

            return [
                'expiry_date' => $date->toDateTimeString(),
                'minute_left' => $diff_In_Minutes,
            ];
        }

        return null;
    }

    public static function settings($slug)
    {
        if (Cache::has($slug)) {
            return Cache::get($slug);
        }

        return Cache::rememberForever($slug, function () use ($slug) {
            return Setting::bySlug($slug)->data;
        });
    }

    public static function user_role(array $roles)
    {
        if (! auth()->check()) {
            return;
        }

        if (! auth()->user()->hasRole($roles)) {
            return;
        }

        //Cache::
    }

    // adjust implementation as needed
    // current implementation taken from https://www.php.net/manual/en/function.com-create-guid.php
    public static function generateGUID(): string
    {

        if (function_exists('com_create_guid') === true) {
            return trim(com_create_guid(), '{}');
        }

        $data = openssl_random_pseudo_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0F | 0x40); // set version to 0100
        $data[8] = chr(ord($data[8]) & 0x3F | 0x80); // set bits 6-7 to 10

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    public static function mergeData($existing_data, $new_data)
    {
        if (is_null($existing_data)) {
            $existing_data = [];
        }

        foreach ($new_data as $key => $value) {
            if (array_key_exists($key, $existing_data)) {
                $existing_data[$key] = array_merge($existing_data[$key], $value);
            } else {
                $existing_data[$key] = $value;
            }
        }

        //dd('dd');
        return $existing_data;
    }

    public static function numberToWords($number)
    {
        $hyphen = '-';
        $conjunction = ' and ';
        $dictionary = [
            0 => 'zero',
            1 => 'one',
            2 => 'two',
            3 => 'three',
            4 => 'four',
            5 => 'five',
            6 => 'six',
            7 => 'seven',
            8 => 'eight',
            9 => 'nine',
            10 => 'ten',
            11 => 'eleven',
            12 => 'twelve',
            13 => 'thirteen',
            14 => 'fourteen',
            15 => 'fifteen',
            16 => 'sixteen',
            17 => 'seventeen',
            18 => 'eighteen',
            19 => 'nineteen',
            20 => 'twenty',
            30 => 'thirty',
            40 => 'forty',
            50 => 'fifty',
            60 => 'sixty',
            70 => 'seventy',
            80 => 'eighty',
            90 => 'ninety',
            100 => 'hundred',
            1000 => 'thousand',
            1000000 => 'million',
        ];

        if (! is_numeric($number)) {
            return false;
        }

        $fraction = null;

        if (strpos($number, '.') !== false) {
            [$number, $fraction] = explode('.', $number);
        }

        $number = intval($number);
        $response = '';

        if ($number == 0) {
            $response = 'zero naira';
        } else {
            $response = self::convertNumberToWords($number, $dictionary);
        }

        if ($fraction !== null && is_numeric($fraction)) {
            $fraction = intval($fraction);
            if ($fraction > 0) {
                $response .= $conjunction.self::convertNumberToWords($fraction, $dictionary).' kobo';
            } else {
                $response .= ' only';
            }
        } else {
            $response .= ' naira only';
        }

        return $response;
    }

    private static function convertNumberToWords($number, $dictionary)
    {
        $hyphen = '-';
        $conjunction = ' and ';

        switch (true) {
            case $number < 21:
                $string = $dictionary[$number];
                break;
            case $number < 100:
                $tens = ((int) ($number / 10)) * 10;
                $units = $number % 10;
                $string = $dictionary[$tens];
                if ($units) {
                    $string .= $hyphen.$dictionary[$units];
                }
                break;
            case $number < 1000:
                $hundreds = $number / 100;
                $remainder = $number % 100;
                $string = $dictionary[$hundreds].' '.$dictionary[100];
                if ($remainder) {
                    $string .= $conjunction.self::convertNumberToWords($remainder, $dictionary);
                }
                break;
            default:
                $baseUnit = pow(1000, floor(log($number, 1000)));
                $numBaseUnits = (int) ($number / $baseUnit);
                $remainder = $number % $baseUnit;
                $string = self::convertNumberToWords($numBaseUnits, $dictionary).' '.$dictionary[$baseUnit];
                if ($remainder) {
                    $string .= $remainder < 100 ? $conjunction : ', ';
                    $string .= self::convertNumberToWords($remainder, $dictionary);
                }
                break;
        }

        return ucwords($string);
    }

    /**
     * @throws Exception
     */
    public static function generateVariableAmounts($totalAmount, $numVariables = 5, $minAmount = 5000)
    {
        if ($numVariables <= 0 || $totalAmount < $minAmount) {
            return [$totalAmount];
        }

        if ($totalAmount < $minAmount * $numVariables) {
            return [$totalAmount];
        }

        // Generate a list of possible rounded amounts
        $possibleAmounts = [];
        for ($i = $minAmount; $i <= $totalAmount; $i += 500) {
            $possibleAmounts[] = $i;
        }

        $amounts = [];
        do {
            // Shuffle the list to get randomness
            shuffle($possibleAmounts);

            // Pick the first n-1 amounts
            $amounts = array_slice($possibleAmounts, 0, $numVariables - 1);

        } while ($totalAmount <= array_sum($amounts)); // Ensure the sum of the first n-1 amounts is less than the total amount

        // Calculate the last amount by subtracting the sum of generated amounts from the total
        $amounts[] = $totalAmount - array_sum($amounts);

        // Sort the amounts for better readability
        sort($amounts);

        return $amounts;
    }

    public static function generateAmounts($totalAmount, $numVariables = 5)
    {
        // Ensure the total amount is at least 5000
        if ($totalAmount < 5000) {
            return [$totalAmount];
        }

        // Generate amounts based on percentages
        $percentages = [5, 10, 25, 50, 75];
        $generatedAmounts = [];
        foreach ($percentages as $percentage) {
            $generatedAmounts[] = ($totalAmount * $percentage) / 100;
        }

        // If the number of variables requested is more than the generated amounts
        while ($numVariables > count($generatedAmounts)) {
            $remainingAmount = $totalAmount - array_sum($generatedAmounts);
            if ($remainingAmount > 0) {
                $generatedAmounts[] = $remainingAmount;
            } else {
                break;
            }
        }

        // Ensure no duplicates
        $generatedAmounts = array_unique($generatedAmounts);

        // Fallback mechanism
        if (count($generatedAmounts) < 2) {
            return [$totalAmount / 2, $totalAmount / 2];
        }

        return $generatedAmounts;
    }

    /**
     * Categorize the month based on the given month.
     *
     * @param  int  $month  The month to categorize.
     * @return string The category of the month.
     *
     * @throws Exception If the month is not within the range of 1 to 12.
     */
    public static function categorizeMonth(int $month): string
    {
        if ($month >= 1 && $month <= 3) {
            return Misc::LEVEL_UP;
        } elseif ($month >= 4 && $month <= 6) {
            return Misc::CRUISE_CONTROL;
        } elseif ($month >= 7 && $month <= 9) {
            return Misc::PEAK_PERFORMANCE;
        } elseif ($month >= 10 && $month <= 12) {
            return Misc::ROAD_MASTER;
        }
    }

    /**
     * Retrieves the corresponding to unlock text based on the provided category.
     *
     * @param  string  $category  The category constant.
     * @return string The unlock text.
     */
    public static function getUnlockText(string $category): string
    {
        return match ($category) {
            Misc::LEVEL_UP => 'These benefits unlock from month 1 - 3',
            Misc::CRUISE_CONTROL => 'These benefits unlock from month 4 - 6',
            Misc::PEAK_PERFORMANCE => 'These benefits unlock from month 7 - 9',
            Misc::ROAD_MASTER => 'These benefits unlock from month 10 - 12',
            default => '',
        };
    }
}
