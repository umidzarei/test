<?php
function identifyAndNormalizeUsername(string $input): array
{
    $originalInput = $input;
    $trimmedInput  = trim($input);
    $emailRegex    = '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/i';
    if (preg_match($emailRegex, $trimmedInput)) {
        return [
            'type'     => 'email',
            'value'    => $trimmedInput,
            'original' => $originalInput,
        ];
    }
    $cleanedMobileInput = $trimmedInput;
    $leadingPlus        = '';
    if (strpos($cleanedMobileInput, '+') === 0) {
        $leadingPlus        = '+';
        $cleanedMobileInput = substr($cleanedMobileInput, 1);
    }
    $cleanedMobileInput = preg_replace('/\D/', '', $cleanedMobileInput);
    $cleanedMobileInput = $leadingPlus . $cleanedMobileInput;
    $iranianMobileRegex = '/^(?:0|\+98|98)?(9\d{9})$/';
    if (preg_match($iranianMobileRegex, $cleanedMobileInput, $matches)) {
        $normalizedMobile = '0' . $matches[1];
        return [
            'type'     => 'phone',
            'value'    => $normalizedMobile,
            'original' => $originalInput,
        ];
    }
    return [
        'type'     => 'unknown',
        'value'    => $trimmedInput,
        'original' => $originalInput,
    ];
}

function normalizePhone(string $input): string
{
    $cleanedMobileInput = trim($input);
    $leadingPlus        = '';
    if (strpos($cleanedMobileInput, '+') === 0) {
        $leadingPlus        = '+';
        $cleanedMobileInput = substr($cleanedMobileInput, 1);
    }
    $cleanedMobileInput = preg_replace('/\D/', '', $cleanedMobileInput);
    $cleanedMobileInput = $leadingPlus . $cleanedMobileInput;
    $iranianMobileRegex = '/^(?:0|\+98|98)?(9\d{9})$/';
    if (preg_match($iranianMobileRegex, $cleanedMobileInput, $matches)) {
        $normalizedMobile = '0' . $matches[1];
        return $normalizedMobile;
    } else {
        throw new Exception("has been error => $input", 1);
    }
}
