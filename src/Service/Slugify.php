<?php

namespace App\Service;

class Slugify
{
    public function generate(string $input): string
    {
        $patterns[0] = '/(á|â|à|å|ä)/';
        $patterns[1] = '/(ð|é|ê|è|ë)/';
        $patterns[2] = '/(í|î|ì|ï)/';
        $patterns[3] = '/(ó|ô|ò|ø|õ|ö)/';
        $patterns[4] = '/(ú|û|ù|ü)/';
        $patterns[5] = '/æ/';
        $patterns[6] = '/ç/';
        $patterns[7] = '/œ/';
        $replacements[0] = 'a';
        $replacements[1] = 'e';
        $replacements[2] = 'i';
        $replacements[3] = 'o';
        $replacements[4] = 'u';
        $replacements[5] = 'ae';
        $replacements[6] = 'c';
        $replacements[7] = 'oe';

        $lower =  mb_strtolower(trim($input));
        $regex = preg_replace($patterns, $replacements, $lower);
        $specialChar = preg_replace('/[^\s\w]/', '', $regex);
        $slug = strtolower(preg_replace('/\s+/', '-', $specialChar));

        return $slug;
    }
}
