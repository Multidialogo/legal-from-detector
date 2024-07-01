<?php

namespace multidialogo\LegalFormGuesser;

use InvalidArgumentException;

class Dictionary
{

    private const ACCEPTED_TYPES = [
        "FREELANCE",
        "GENERAL-PARTNERSHIP",
        "JOINT-STOCK",
        "LIMITED-LIABILITY",
        "LIMITED-PARTNERSHIP",
        "LIMITED-PARTNERSHIP-JOINT-STOCK",
        "ONE-PERSON-LIMITED-LIABILITY",
        "PERSON",
        "SIMPLE-LIMITED-LIABILITY",
        "SIMPLE-PARTNERSHIP",
        "SOLE-PROPRIETORSHIP",
    ];

    private string $countryCode;

    /**
     * @var term[]
     */
    private array $terms;

    public static function makeFromFile(?string $dictionaryPath, string $countryCode): self
    {
        if (!$dictionaryPath) {
            $dictionaryPath = __DIR__ . '/../res';
        }

        $dictionaryFilePath = "{$dictionaryPath}/{$countryCode}.json";
        if (!is_file($dictionaryFilePath)) {
            throw new  InvalidArgumentException("File {$dictionaryFilePath} does not exist");
        }

        $termsData = json_decode(file_get_contents($dictionaryFilePath));
        if (!$termsData) {
            throw new  InvalidArgumentException("File {$dictionaryFilePath} is not a valid JSON");
        }

        $terms = [];
        foreach ($termsData as $termData) {
            if (!in_array($termData->type, static::ACCEPTED_TYPES)) {
                throw new InvalidArgumentException("Invalid term code: {$termData->type}");
            }
            $terms[] = new Term($termData->type, $termData->acronyms ?? [], $termData->stopWords);
        }

        return new self($countryCode, $terms);
    }

    private function __construct(string $countryCode, array $terms)
    {
        $this->countryCode = $countryCode;

        foreach ($terms as $term) {
            if (!($term instanceof term)) {
                throw new InvalidArgumentException('term must be instance of term');
            }
        }
        $this->terms = $terms;
    }

    public function getCountryCode(): string
    {
        return $this->countryCode;
    }

    public function getTerms(): array
    {
        return $this->terms;
    }
}