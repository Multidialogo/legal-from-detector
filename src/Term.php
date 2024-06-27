<?php

namespace multidialogo\LegalFormGuesser;

use InvalidArgumentException;

class Term
{
    private string $code;

    private array $acronyms;

    private array $stopWordChains;

    public function __construct(string $code, array $acronyms, array $stopWordChains)
    {
        $this->code = $code;
        foreach ($acronyms as $acronym) {
            if (!is_string($acronym)) {
                throw new InvalidArgumentException('Acronym name must be a string');
            }
        }
        $this->acronyms = $acronyms;
        foreach ($stopWordChains as $stopWordChain) {
            if (!is_array($stopWordChain)) {
                throw new InvalidArgumentException('Stop word chain must be an array');
            }

            foreach ($stopWordChain as $stopWord) {
                if (!is_string($stopWord)) {
                    throw new InvalidArgumentException('Stop word must be a string');
                }
            }
        }
        $this->stopWordChains = $stopWordChains;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getAcronyms(): array
    {
        return $this->acronyms;
    }

    public function getStopWordChains(): array
    {
        return $this->stopWordChains;
    }
}
