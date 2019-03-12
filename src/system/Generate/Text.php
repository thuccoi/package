<?php

namespace system\Generate;

class Text {

    private $components = [
        "info"    => "\033[38;2;0;182;0m",
        "comment" => "\033[38;2;180;180;0m",
        "error"   => "\033[41m"
    ];

    public function info($message) {
        return "<info>$message</info>";
    }

    public function error($message) {
        return "<error>$message</error>";
    }

    public function comment($message) {
        return "<comment>$message</comment>";
    }

    /**
     * 
     * @param type $tag is component
     * @param type $code is color or background, style
     * @return $this 
     */
    public function addComponent($tag, $code) {
        $this->components[$tag] = $code;
        return $this;
    }

    /**
     * 
     * @param type $text type is html
     * @param type $tag type component
     * @return type $text is console
     */
    public function convert($text, $tag) {
        if (isset($this->components[$tag])) {
            return "{$this->components[$tag]}{$text}\033[0m";
        }
        return $text;
    }

    /**
     * 
     * @param type $text type html
     * @return type $text console
     */
    public function parsing($text) {

        $html = str_get_html($text);

        foreach ($this->components as $tag => $code) {

            foreach ($html->find($tag) as $element) {

                $text = str_replace($element->outertext, $this->convert($element->innertext, $tag), $text);
            }
        }

        return $text;
    }

}
