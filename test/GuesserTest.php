<?php

namespace multidialogo\test\LegalFormGuesser;

use multidialogo\LegalFormGuesser\Dictionary;
use multidialogo\LegalFormGuesser\Guesser;
use PHPUnit\Framework\TestCase;

class GuesserTest extends TestCase
{
    /**
     * @dataProvider provideGuessableData
     */
    public function testGuess(string $caseName, string $countryCode, string $text, string $expectedLegalFormCode): void
    {
        $dictionary = Dictionary::makeFromDictionaryFile(__DIR__ . '/../res', 'it');

        static::assertEquals(
            $expectedLegalFormCode,
            Guesser::guess($dictionary, $text)
        );
    }

    public function provideGuessableData(): array
    {
        return [
            ['No resolution', 'it', 'SEMPROSIO', 'UNKNOWN',],
            ['Exact acronym match', 'it', 'SEMPROSIO SRL', 'LIMITED-LIABILITY',],
            ['Exact acronym match with trailing space', 'it', 'SEMPROSIO SRL ', 'LIMITED-LIABILITY',],
            ['Exact acronym match case insensitive', 'it', 'SEMPROSIO srl', 'LIMITED-LIABILITY',],
            ['Exact acronym match dotted version', 'it', 'SEMPROSIO S.R.L.', 'LIMITED-LIABILITY',],
            ['Exact contained by another acronym, acronym match','it', 'SEMPROSIO SRLS', 'SIMPLE-LIMITED-LIABILITY',],
            ['Do not match when acronym is part of other word','it', 'SEMPROSIOSPA', 'UNKNOWN',],
            ['Exact stop word match', 'it', 'SEMPROSIO SOCIETA SEMPLIFICATA A RESPONSABILITA LIMITATA', 'SIMPLE-LIMITED-LIABILITY',],
            ['Exact stop word match with accented chars', 'it', 'SEMPROSIO SOCIETÀ SEMPLIFICATA A RESPONSABILITA LIMITATA', 'SIMPLE-LIMITED-LIABILITY',], #fix
            ['Exact stop word match', 'it', 'DITTA INDIVIDUALE DI SEMPROSIO SEMPROSIO', 'SOLE-PROPRIETORSHIP',],
            ['Exact stop word match', 'it', 'SEMPROSIO DITTA INDIVIDUALE DI SEMPROSIO', 'SOLE-PROPRIETORSHIP',],
        ];
    }
}