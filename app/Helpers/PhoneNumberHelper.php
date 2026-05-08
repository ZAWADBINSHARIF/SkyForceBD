<?php

if (! function_exists('normalize_phone_numbers')) {

    function normalize_phone_numbers(array $numbers): array
    {
        return collect($numbers)
            ->map(function (string $number): string {

                $number = preg_replace('/\s+/', '', trim($number));

                // 017XXXXXXXX -> 88017XXXXXXXX
                if (str_starts_with($number, '01')) {
                    return '88' . $number;
                }

                // +8801XXXXXXXXX -> 8801XXXXXXXXX
                if (str_starts_with($number, '+8801')) {
                    return ltrim($number, '+');
                }

                return $number;
            })
            ->filter(fn(string $number): bool => is_valid_bd_phone($number))
            ->unique()
            ->values()
            ->toArray();
    }
}

if (! function_exists('is_valid_bd_phone')) {

    function is_valid_bd_phone(string $number): bool
    {
        return (bool) preg_match(
            '/^8801[3-9]\d{8}$/',
            $number
        );
    }
}

if (! function_exists('recipient_count')) {

    function recipient_count(
        array $phoneNumbers,
        array $customNumbers = []
    ): int {

        return count(
            array_unique([
                ...$phoneNumbers,
                ...$customNumbers,
            ])
        );
    }
}
