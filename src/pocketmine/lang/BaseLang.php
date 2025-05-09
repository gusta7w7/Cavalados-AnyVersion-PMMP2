<?php

/*
 *
 *  ____            _        _   __  __ _                  __  __ ____
 * |  _ \ ___   ___| | _____| |_|  \/  (_)_ __   ___      |  \/  |  _ \
 * | |_) / _ \ / __| |/ / _ \ __| |\/| | | '_ \ / _ \_____| |\/| | |_) |
 * |  __/ (_) | (__|   <  __/ |_| |  | | | | | |  __/_____| |  | |  __/
 * |_|   \___/ \___|_|\_\___|\__|_|  |_|_|_| |_|\___|     |_|  |_|_|
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author PocketMine Team
 * @link http://www.pocketmine.net/
 *
 *
*/

namespace pocketmine\lang;

use pocketmine\event\TextContainer;
use pocketmine\event\TranslationContainer;

use function array_shift;
use function count;
use function explode;
use function file_exists;
use function file_get_contents;
use function implode;
use function ord;
use function str_replace;
use function strlen;
use function strpos;
use function strtolower;
use function substr;
use function trim;

class BaseLang
{
    const FALLBACK_LANGUAGE = "eng";

    protected $langName;

    protected $lang = [];
    protected $fallbackLang = [];

    public function __construct($lang, $path = null, $fallback = self::FALLBACK_LANGUAGE)
    {

        $this->langName = strtolower($lang);

        if ($path === null) {
            $path = \pocketmine\PATH . "src/pocketmine/lang/locale/";
        }

        $this->loadLang($path . $this->langName . ".ini", $this->lang);
        $this->loadLang($path . $fallback . ".ini", $this->fallbackLang);
    }

    public function getName() : string
    {
        return $this->get("language.name");
    }

    public function getLang()
    {
        return $this->langName;
    }

    protected function loadLang($path, array &$d)
    {
        if (file_exists($path) && strlen($content = file_get_contents($path)) > 0) {
            foreach (explode("\n", $content) as $line) {
                $line = trim($line);
                if ($line === "" || $line[0] === "#") {
                    continue;
                }

                $t = explode("=", $line);
                if (count($t) < 2) {
                    continue;
                }

                $key = trim(array_shift($t));
                $value = trim(implode("=", $t));

                if ($value === "") {
                    continue;
                }

                $d[$key] = $value;
            }
        }
    }

    /**
     * @param string   $str
     * @param string[] $params
     *
     * @return string
     */
    public function translateString($str, array $params = [], $onlyPrefix = null)
    {
        $baseText = $this->get($str);
        $baseText = $this->parseTranslation(($baseText !== null && ($onlyPrefix === null || strpos($str, $onlyPrefix) === 0)) ? $baseText : $str, $onlyPrefix);

        foreach ($params as $i => $p) {
            $baseText = str_replace("{%$i}", $this->parseTranslation((string) $p), $baseText, $onlyPrefix);
        }

        return str_replace("%0", "", $baseText); //fixes a client bug where %0 in translation will cause freeze
    }

    public function translate(TextContainer $c)
    {
        if ($c instanceof TranslationContainer) {
            $baseText = $this->internalGet($c->getText());
            $baseText = $this->parseTranslation($baseText !== null ? $baseText : $c->getText());

            foreach ($c->getParameters() as $i => $p) {
                $baseText = str_replace("{%$i}", $this->parseTranslation($p), $baseText);
            }
        } else {
            $baseText = $this->parseTranslation($c->getText());
        }

        return $baseText;
    }

    public function internalGet($id)
    {
        if (isset($this->lang[$id])) {
            return $this->lang[$id];
        } elseif (isset($this->fallbackLang[$id])) {
            return $this->fallbackLang[$id];
        }

        return null;
    }

    public function get($id)
    {
        if (isset($this->lang[$id])) {
            return $this->lang[$id];
        } elseif (isset($this->fallbackLang[$id])) {
            return $this->fallbackLang[$id];
        }

        return $id;
    }

    protected function parseTranslation($text, $onlyPrefix = null)
    {
        $newString = "";

        $replaceString = null;

        $len = strlen($text);
        for ($i = 0; $i < $len; ++$i) {
            $c = $text[$i];
            if ($replaceString !== null) {
                $ord = ord($c);
                if (
                    ($ord >= 0x30 && $ord <= 0x39) // 0-9
                    || ($ord >= 0x41 && $ord <= 0x5a) // A-Z
                    || ($ord >= 0x61 && $ord <= 0x7a) || // a-z
                    $c === "." || $c === "-"
                ) {
                    $replaceString .= $c;
                } else {
                    if (($t = $this->internalGet(substr($replaceString, 1))) !== null && ($onlyPrefix === null || strpos($replaceString, $onlyPrefix) === 1)) {
                        $newString .= $t;
                    } else {
                        $newString .= $replaceString;
                    }
                    $replaceString = null;

                    if ($c === "%") {
                        $replaceString = $c;
                    } else {
                        $newString .= $c;
                    }
                }
            } elseif ($c === "%") {
                $replaceString = $c;
            } else {
                $newString .= $c;
            }
        }

        if ($replaceString !== null) {
            if (($t = $this->internalGet(substr($replaceString, 1))) !== null && ($onlyPrefix === null || strpos($replaceString, $onlyPrefix) === 1)) {
                $newString .= $t;
            } else {
                $newString .= $replaceString;
            }
        }

        return $newString;
    }
}
