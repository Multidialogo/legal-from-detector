<?php

namespace multidialogo\LegalFormGuesser;

class Guesser
{
    public const LEGAL_FORMS = [
        'UNKNOWN' => 'UNKNOWN',
        'FREELANCE' => 'FREELANCE',
        'GENERAL-PARTNERSHIP' => 'GENERAL-PARTNERSHIP',
        'JOINT-STOCK' => 'JOINT-STOCK',
        'LIMITED-LIABILITY' => 'LIMITED-LIABILITY',
        'LIMITED-PARTNERSHIP' => 'LIMITED-PARTNERSHIP',
        'LIMITED-PARTNERSHIP-JOINT-STOCK' => 'LIMITED-PARTNERSHIP-JOINT-STOCK',
        'ONE-PERSON-LIMITED-LIABILITY' => 'ONE-PERSON-LIMITED-LIABILITY',
        'PERSON' => 'PERSON',
        'SIMPLE-LIMITED-LIABILITY' => 'SIMPLE-LIMITED-LIABILITY',
        'SIMPLE-PARTNERSHIP' => 'SIMPLE-PARTNERSHIP',
        'SOLE-PROPRIETORSHIP' => 'SOLE-PROPRIETORSHIP',
    ];

    private const ACRONYMS = [
        'it' => [
            'GENERAL-PARTNERSHIP' => ['SNC',],
            'JOINT-STOCK' => ['SPA',],
            'LIMITED-LIABILITY' => ['SRL',],
            'LIMITED-PARTNERSHIP' => ['SAS',],
            'LIMITED-PARTNERSHIP-JOINT-STOCK' => ['SAPA',],
            'ONE-PERSON-LIMITED-LIABILITY' => ['SRLU',],
            'SIMPLE-LIMITED-LIABILITY' => ['SRLS',],
            'SIMPLE-PARTNERSHIP' => ['SS',],
            'SOLE-PROPRIETORSHIP' => ['DI',],
        ],
    ];

    /**
     * Note: If one stop word contains another must come before it,
     * ex. SOCIETA UNIPERSONALE A RESPONSABILITA LIMITATA contains UNIPERSONALE
     */
    private const STOP_WORDS = [
        'it' => [
            'GENERAL-PARTNERSHIP' => ['SOCIETA IN NOME COLLETTIVO',],
            'JOINT-STOCK' => ['SOCIETA PER ACQUISTI',],
            'LIMITED-LIABILITY' => ['SOCIETA A RESPONSABILITA LIMITATA',],
            'LIMITED-PARTNERSHIP' => ['SOCIETA IN ACCOMANDITA SEMPLICE',],
            'LIMITED-PARTNERSHIP-JOINT-STOCK' => ['SOCIETA IN ACCOMANDITA PER AZIONI',],
            'ONE-PERSON-LIMITED-LIABILITY' => ['SOCIETA UNIPERSONALE A RESPONSABILITA LIMITATA',],
            'SIMPLE-LIMITED-LIABILITY' => ['SOCIETA SEMPLIFICATA A RESPONSABILITA LIMITATA',],
            'SIMPLE-PARTNERSHIP' => ['SOCIETA SEMPLICE',],
            'SOLE-PROPRIETORSHIP' => ['DITTA INDIVIDUALE',],
            'FREELANCE' => ['UNIPERSONALE',],
        ],
    ];

    public static function guess(string $countryCode, string $completeCompanyName): string
    {
        $legalForm = static::LEGAL_FORMS['UNKNOWN'];

        if (!isset(self::ACRONYMS[$countryCode])) {
            return $legalForm;
        }

        $matchedLegalForm = static::matchAcronym(self::ACRONYMS[$countryCode], $completeCompanyName);
        if ($matchedLegalForm) {
            return static::LEGAL_FORMS[$matchedLegalForm];
        }

        $matchedLegalForm = static::matchStopWords(self::STOP_WORDS[$countryCode], $completeCompanyName);
        if ($matchedLegalForm) {
            return static::LEGAL_FORMS[$matchedLegalForm];
        }

        return static::LEGAL_FORMS['UNKNOWN'];
    }

    private static function matchAcronym(array $acronyms, string $haystack): ?string
    {
        $normalizedHaystack =  static::normalizeText($haystack);
        $variedAcronyms = static::generateAcronymVariations($acronyms);

        foreach ($variedAcronyms as $key => $values) {
            foreach ($values as $value) {
                if (static::stringEndsWith(" {$value}", $normalizedHaystack)) {
                    return $key;
                }
            }
        }

        return null;
    }

    private static function matchStopWords(array $stopWords, string $haystack): ?string
    {
        $normalizedHaystack =  static::normalizeText($haystack);

        foreach ($stopWords as $key => $values) {
            foreach ($values as $value) {
                if (false !== stripos($normalizedHaystack, $value)) {
                    return $key;
                }
            }
        }

        return null;
    }

    private static function generateAcronymVariations(array $acronyms): array
    {
        $variedAcronyms = [];
        foreach ($acronyms as $key => $values) {
            $variedAcronyms[$key] = [];
            foreach ($values as $value) {
                $variedAcronyms[$key][] = $value;
                $variedAcronyms[$key][] = implode('.', str_split($value)) . '.';
            }
        }

        return $variedAcronyms;
    }

    private static function stringEndsWith(string $needle, string $haystack): bool
    {
        $length = strlen($needle);
        if (!$length) {
            return true;
        }

        return substr($haystack, -$length ) === $needle;
    }

    private static function normalizeText(string $text): string
    {
        return strtoupper(trim(static::transliterateAccents($text)));
    }

    private static function transliterateAccents(string $text): string
    {
        $unwanted_array = array(    'Š'=>'S', 'š'=>'s', 'Ž'=>'Z', 'ž'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E',
            'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U',
            'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss', 'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c',
            'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o',
            'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y' );

        return strtr($text, $unwanted_array );
    }
}
