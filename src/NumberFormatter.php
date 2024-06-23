<?php

class Options {
    public $precision = 'high';
    public $template = 'number';
    public $language = 'en';
    public $outputFormat = 'plain';
    public $prefixMarker = 'i';
    public $postfixMarker = 'i';
    public $prefix = '';
    public $postfix = '';
}

class FormattedObject {
    public $value = '';
    public $prefix = '';
    public $postfix = '';
    public $sign = '';
    public $wholeNumber = '';
}

class LanguageConfig {
    public $prefixMarker = 'i';
    public $postfixMarker = 'i';
    public $prefix = '';
    public $postfix = '';
}

class NumberFormatter {
    private $options;

    public function __construct($options = []) {
        $this->options = new Options();
        foreach ($options as $key => $value) {
            $this->options->$key = $value;
        }
    }

    public function setLanguage($lang, $config = []) {
        $this->options->language = $lang;
        $this->options->prefixMarker = isset($config['prefixMarker']) ? $config['prefixMarker'] : $this->options->prefixMarker;
        $this->options->postfixMarker = isset($config['postfixMarker']) ? $config['postfixMarker'] : $this->options->postfixMarker;
        $this->options->prefix = isset($config['prefix']) ? $config['prefix'] : $this->options->prefix;
        $this->options->postfix = isset($config['postfix']) ? $config['postfix'] : $this->options->postfix;
        return $this;
    }

    public function setTemplate($template, $precision) {
        $this->options->template = $template;
        $this->options->precision = $precision;
        return $this;
    }

    public function toJson($input) {
        $formattedObject = $this->format($input);
        unset($formattedObject->value);
        return json_encode($formattedObject);
    }

    public function toString($input) {
        $formattedObject = $this->format($input);
        return $formattedObject->value;
    }

    public function toPlainString($input) {
        $this->options->outputFormat = 'plain';
        $formattedObject = $this->format($input);
        return $formattedObject->value;
    }

    public function toHtmlString($input) {
        $this->options->outputFormat = 'html';
        $formattedObject = $this->format($input);
        return $formattedObject->value;
    }

    public function toMdString($input) {
        $this->options->outputFormat = 'markdown';
        $formattedObject = $this->format($input);
        return $formattedObject->value;
    }

    private function isENotation($input) {
        return preg_match('/^[-+]?[0-9]*\\.?[0-9]+([eE][-+][0-9]+)$/', $input);
    }

    private function format($input) {
        $precision = $this->options->precision;
        $template = $this->options->template;
        $language = $this->options->language;
        $outputFormat = $this->options->outputFormat;
        $prefixMarker = $this->options->prefixMarker;
        $postfixMarker = $this->options->postfixMarker;
        $prefix = $this->options->prefix;
        $postfix = $this->options->postfix;

        if (!$input) return new FormattedObject();

        if (!in_array($template, ['number', 'usd', 'irt', 'irr', 'percent'])) {
            $template = 'number';
        }

        if ($this->isENotation(strval($input))) {
            $input = $this->convertENotationToRegularNumber(floatval($input));
        }

        $numberString = preg_replace('/[^\d.-]/', '', str_replace(
            array_map(fn($c) => chr($c), range(0x0660, 0x0669)),
            range(0, 9),
            str_replace(
                array_map(fn($c) => chr($c), range(0x06F0, 0x06F9)),
                range(0, 9),
                strval($input)
            )
        ));

        $numberString = preg_replace('/^0+(?=\d)/', '', preg_replace('/(?<=\.\d*)0+$|(?<=\.\d)0+\b/', '', $numberString));
        $number = abs(floatval($numberString));
        $p = $d = $r = $c = $f = 0;

        if ($precision === 'auto') {
            if (in_array($template, ['usd', 'irt', 'irr'])) {
                if ($number >= 0.0001 && $number < 100000000000) {
                    $precision = 'high';
                } else {
                    $precision = 'medium';
                }
            } else if ($template === 'number') {
                $precision = 'medium';
            } else if ($template === 'percent') {
                $precision = 'low';
            }
        }

        if ($precision === 'medium') {
            if ($number >= 0 && $number < 0.0001) {
                $p = 33;
                $d = 4;
                $r = false;
                $c = true;
            } else if ($number >= 0.0001 && $number < 0.001) {
                $p = 7;
                $d = 4;
                $r = false;
                $c = false;
            } else if ($number >= 0.001 && $number < 0.01) {
                $p = 5;
                $d = 3;
                $r = false;
                $c = false;
            } else if ($number >= 0.01 && $number < 0.1) {
                $p = 3;
                $d = 2;
                $r = false;
                $c = false;
            } else if ($number >= 0.1 && $number < 1) {
                $p = 1;
                $d = 1;
                $r = false;
                $c = false;
            } else if ($number >= 1 && $number < 10) {
                $p = 3;
                $d = 3;
                $r = false;
                $c = false;
            } else if ($number >= 10 && $number < 100) {
                $p = 2;
                $d = 2;
                $r = false;
                $c = false;
            } else if ($number >= 100 && $number < 1000) {
                $p = 1;
                $d = 1;
                $r = false;
                $c = false;
            } else if ($number >= 1000) {
                $x = floor(log10($number)) % 3;
                $p = 2 - $x;
                $d = 2 - $x;
                $r = true;
                $c = true;
            } else {
                $p = 0;
                $d = 0;
                $r = true;
                $c = true;
            }
        } else if ($precision === 'low') {
            if ($number >= 0 && $number < 0.01) {
                $p = 2;
                $d = 0;
                $r = true;
                $c = false;
                $f = 2;
            } else if ($number >= 0.01 && $number < 0.1) {
                $p = 2;
                $d = 1;
                $r = true;
                $c = false;
            } else if ($number >= 0.1 && $number < 1) {
                $p = 2;
                $d = 2;
                $r = true;
                $c = false;
            } else if ($number >= 1 && $number < 10) {
                $p = 2;
                $d = 2;
                $r = true;
                $c = false;
                $f = 2;
            } else if ($number >= 10 && $number < 100) {
                $p = 1;
                $d = 1;
                $r = true;
                $c = false;
                $f = 1;
            } else if ($number >= 100 && $number < 1000) {
                $p = 0;
                $d = 0;
                $r = true;
                $c = false;
            } else if ($number >= 1000) {
                $x = floor(log10($number)) % 3;
                $p = 1 - $x;
                $d = 1 - $x;
                $r = true;
                $c = true;
            } else {
                $p = 0;
                $d = 0;
                $r = true;
                $c = true;
                $f = 2;
            }
        } else {
            // precision === "high"
            if ($number >= 0 && $number < 1) {
                $p = 33;
                $d = 4;
                $r = false;
                $c = true;
            } else if ($number >= 1 && $number < 10) {
                $p = 3;
                $d = 3;
                $r = false;
                $c = true;
            } else if ($number >= 10 && $number < 100) {
                $p = 2;
                $d = 2;
                $r = false;
                $c = true;
            } else if ($number >= 100 && $number < 1000) {
                $p = 1;
                $d = 1;
                $r = false;
                $c = true;
            } else if ($number >= 1000) {
                $p = 0;
                $d = 0;
                $r = true;
                $c = true;
            } else {
                $p = 0;
                $d = 0;
                $r = true;
                $c = true;
            }
        }

        $formatNumber = function ($value, $dp) use ($p, $d, $r, $c, $f) {
            $number = $value;
            if ($d >= 1) {
                $number = number_format($number, $d, '.', '');
            }
            if ($c) {
                $number = number_format($number, $d, '.', ',');
            }
            if ($r) {
                $number = round($number, $dp);
            }
            return $number;
        };

        $prefix = str_replace($prefixMarker, $prefix, $input);
        $postfix = str_replace($postfixMarker, $postfix, $input);

        $formattedValue = $formatNumber($number, $p);

        if ($template === 'usd') {
            $prefix = '$';
            $postfix = '';
        } else if ($template === 'irt') {
            $prefix = '﷼';
            $postfix = '';
        } else if ($template === 'irr') {
            $prefix = '﷼';
            $postfix = '';
        } else if ($template === 'percent') {
            $prefix = '';
            $postfix = '%';
        }

        if ($outputFormat === 'html') {
            $formattedValue = htmlspecialchars($formattedValue);
        } else if ($outputFormat === 'markdown') {
            $formattedValue = str_replace('*', '\\*', $formattedValue);
        }

        $sign = $input < 0 ? '-' : '';

        $formattedObject = new FormattedObject();
        $formattedObject->value = $prefix . $formattedValue . $postfix;
        $formattedObject->prefix = $prefix;
        $formattedObject->postfix = $postfix;
        $formattedObject->sign = $sign;
        $formattedObject->wholeNumber = strval($number);

        return $formattedObject;
    }

    private function convertENotationToRegularNumber($value) {
        return rtrim(rtrim(sprintf('%.20f', $value), '0'), '.');
    }
}
?>
