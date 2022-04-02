<?php

namespace App;

use DateTime;

class Helper
{

    public static function getWhoops(): void
    {
        $whoops = new \Whoops\Run;
        $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
        $whoops->register();
    }

    public static function getAbstract(string $text, int $maxChars = 50)
    {
        if (mb_strlen($text) <= $maxChars) return $text;
        $lastSpace = mb_strpos($text, ' ', $maxChars);
        $text = mb_substr($text, 0, $lastSpace);
        return strip_tags($text) . '...';
    }

    public static function getPositiveInt($value, int $default)
    {
        $value = (int)$value;
        if (!is_int($value) || $value <= 0) return $default;
        return $value;
    }

    public static function stringToSlug(string $string, string $separator = '-'): string
    {
        foreach ([
            // replace characters variations by the original corresponding character
            'a' => ['à', 'â', 'ä'],
            'e' => ['é', 'ê', 'è', 'ë'],
            'i' => ['î', 'ï'],
            'o' => ['ô', 'ö'],
            'u' => ['ù', 'û', 'ü'],
            'c' => 'ç',
            // replace those characters by the choosen seperator (hyphen by default)
            $separator => ['"', '\'', ' ', '#', '/', ',', ';', '.', ':'],
            // remove those characters
            '' => ['?', '!', '(', ')', '{', '}']
        ] as $after => $before) {
            $string = str_replace($before, $after, $string);
        }
        return strtolower(htmlspecialchars(strip_tags($string)));
    }

    static function requireFiles(): void
    {
        foreach ([
            'config/config.php',
        ] as $file) {
            require_once ROOT_PATH . DS . $file;
        }
    }

    /**
     * Escape text
     * @param  mixed $text
     * @return string
     */
    public static function e(string $text, bool $keepDoubleQuotes = false): ?string
    {
        if ($keepDoubleQuotes) {
            $text = str_replace('"', '\'\'', $text);
            $text = str_replace('û', 'Ã»', $text);
        }
        return htmlspecialchars(strip_tags($text));
    }

    public static function stringContainsForbiddenCharacters(string $string): bool
    {
        $filteredString = self::e($string);
        if (strcmp($string, $filteredString) !== 0) { // check if filtered string is the same as original one
            return true;
        } else {
            return false;
        }
    }

    public static function greetingsBasedOnTime(string $firstname): string
    {
        $hour = (int)(new DateTime('now'))->format('H');
        if ($hour >= 18 && $hour < 24) {
            $greetings = "Bonsoir";
        } elseif ($hour >= 0 && $hour < 6) {
            $greetings = "Belle nuit";
        } else {
            $greetings = "Bonjour";
        }
        $greetings .= " " . ucfirst($firstname);
        return $greetings;
    }
}
