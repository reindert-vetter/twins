<?php
declare(strict_types=1);


namespace App\Console\Helpers;

class Json
{
    /**
     * @param string $json
     * @return string
     */
    public static function prettyPrint(string $json): string
    {
        if (! self::isJson($json)) {
            return $json;
        }

        $result          = '';
        $level           = 0;
        $in_quotes       = false;
        $in_escape       = false;
        $ends_line_level = null;
        $json_length     = strlen($json);

        for ($i = 0; $i < $json_length; $i++) {
            $char           = $json[$i];
            $new_line_level = null;
            $post           = "";
            if ($ends_line_level !== null) {
                $new_line_level  = $ends_line_level;
                $ends_line_level = null;
            }
            if ($in_escape) {
                $in_escape = false;
            } else if ($char === '"') {
                $in_quotes = !$in_quotes;
            } else if (!$in_quotes) {
                switch ($char) {
                    case '}':
                    case ']':
                        $level--;
                        $ends_line_level = null;
                        $new_line_level  = $level;
                        break;

                    case '{':
                    case '[':
                        $level++;
                    case ',':
                        $ends_line_level = $level;
                        break;

                    case ':':
                        $post = " ";
                        break;

                    case " ":
                    case "\t":
                    case "\n":
                    case "\r":
                        $char            = "";
                        $ends_line_level = $new_line_level;
                        $new_line_level  = null;
                        break;
                }
            } else if ($char === '\\') {
                $in_escape = true;
            }
            if ($new_line_level !== null) {
                $result .= "\n" . str_repeat("  ", $new_line_level);
            }
            $result .= $char . $post;
        }

        return $result;
    }

    /**
     * @param string $string
     * @return bool
     */
    public static function isJson(string $string): bool {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }
}
