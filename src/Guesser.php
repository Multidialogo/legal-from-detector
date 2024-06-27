<?php

namespace multidialogo\LegalFormGuesser;

class Guesser
{
    public static function guess(Dictionary $dictionary, string $completeCompanyName): string
    {
        $terms = $dictionary->getTerms();
        foreach ($terms as $term) {
            $acronyms = $term->getAcronyms();
            foreach ($acronyms as $acronym) {
                if (static::matchAcronym($acronym, $completeCompanyName)) {
                    return $term->getCode();
                }
            }

            $stopWordChains = $term->getStopWordChains();
            foreach ($stopWordChains as $stopWordChain) {
                if (static::matchStopWordChain($stopWordChain, $completeCompanyName)) {
                    return $term->getCode();
                }
            }
        }

        return 'UNKNOWN';
    }

    private static function matchAcronym(array $acronyms, string $haystack): bool
    {
        $normalizedHaystack = Util::normalizeText($haystack);
        $variedAcronyms = Util::generateAcronymVariations($acronyms);

        foreach ($variedAcronyms as $values) {
            foreach ($values as $value) {
                if (Util::stringEndsWith(" {$value}", $normalizedHaystack)) {
                    return true;
                }
            }
        }

        return false;
    }

    private static function matchStopWordChain(array $stopWordsChain, string $haystack): bool
    {
        $normalizedHaystack = Util::normalizeText($haystack);

        $result = true;
        foreach ($stopWordsChain as $stopWord) {
            $result = $result && (false !== stripos($normalizedHaystack, $stopWord));
        }

        return $result;
    }
}
