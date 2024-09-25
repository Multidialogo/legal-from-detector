<?php

namespace multidialogo\LegalFormGuesser;

class Util
{
    public static function generateAcronymVariations(array $acronyms): array
    {
        $variedAcronyms = [];
        foreach ($acronyms as $acronym) {
            $variedAcronyms[] = $acronym;
            $variedAcronyms[] = implode('.', str_split($acronym)) . '.';

            if (strlen($acronym) < 3) {
                $variedAcronyms[] = implode('.', str_split($acronym));
            }
        }

        return $variedAcronyms;
    }

    public static function stringContains(string $needle, string $haystack): bool
    {
        // 'DI' is not an acronym in the middle of a company name
        if ('DI' === $needle && preg_match("/ $needle /", $haystack)) {
            return false;
        }

        if ($needle !== '' && ((preg_match("/ $needle($|\s)/", $haystack))) ||
            (preg_match("/ $needle /", $haystack))
        ) {
            return true;
        }

        return false;
    }

    public static function normalizeText(string $text): string
    {
        return strtoupper(trim(static::transliterateAccents($text)));
    }

    private static function transliterateAccents(string $text): string
    {
        $unwanted_array = ['Š' => 'S', 'š' => 's', 'Ž' => 'Z', 'ž' => 'z', 'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A', 'Æ' => 'A', 'Ç' => 'C', 'È' => 'E', 'É' => 'E',
            'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I', 'Ñ' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O', 'Ø' => 'O', 'Ù' => 'U',
            'Ú' => 'U', 'Û' => 'U', 'Ü' => 'U', 'Ý' => 'Y', 'Þ' => 'B', 'ß' => 'Ss', 'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a', 'æ' => 'a', 'ç' => 'c',
            'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i', 'ð' => 'o', 'ñ' => 'n', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o',
            'ö' => 'o', 'ø' => 'o', 'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ý' => 'y', 'þ' => 'b', 'ÿ' => 'y',];

        return strtr($text, $unwanted_array);
    }
}