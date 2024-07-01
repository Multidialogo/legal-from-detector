<?php

namespace multidialogo\LegalFormGuesser;


class Guesser
{
    public static function guess(Dictionary $dictionary, string $completeCompanyName): ?string
    {
        $type = null;
        $maxChainSize = 0;
        $terms = $dictionary->getTerms();
        foreach ($terms as $term) {
            if (static::matchAcronyms($term->getAcronyms(), $completeCompanyName)) {
                return $term->getType();
            }

            $stopWordChains = $term->getStopWordChains();
            foreach ($stopWordChains as $stopWordChain) {
                if (static::matchStopWordChain($stopWordChain, $completeCompanyName)) {
                    if (count($stopWordChains) > $maxChainSize) {
                        $type = $term->getType();
                        $maxChainSize = count($stopWordChains);
                    }
                }
            }
        }

        return $type;
    }

    private static function matchAcronyms(array $acronyms, string $haystack): bool
    {
        $normalizedHaystack = Util::normalizeText($haystack);
        $variedAcronyms = Util::generateAcronymVariations($acronyms);

        foreach ($variedAcronyms as $value) {
            if (Util::stringEndsWith(" {$value}", $normalizedHaystack)) {
                return true;
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
