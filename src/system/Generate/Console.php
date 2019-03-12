<?php

namespace system\Generate;

class Console extends Text {

    private $text;

    public function __construct($text = "") {
        $this->text = $text;
    }

    /**
     * 
     * @param type $message is string
     * @return type
     */
    public function addCommand($key, $value) {

        $command = sprintf("%-40s%s", $this->info($key), $value);

        $this->whiteSpace(2)
                ->addMessage($command)
                ->breakLine()
                ->whiteSpace()
        ;

        return $this;
    }

    /**
     * 
     * @param type $num is number white spaces
     * @return $this
     */
    public function whiteSpace($num = 1) {
        $char = " ";
        $increment = 1;
        if ($num % 4 == 0) {
            $char = "\t";
            $increment = 4;
        }

        for ($i = 0; $i < $num; $i = $i + $increment) {
            $this->text = $this->text . $char;
        }
        return $this;
    }

    /**
     * 
     * @param type $num is number lines
     * @return $this
     */
    public function breakLine($num = 1) {
        for ($i = 0; $i < $num; $i++) {
            $this->text = $this->text . "\n";
        }
        return $this;
    }

    /**
     * 
     * @param type $message is string
     * @return type
     */
    public function addMessage($message) {
        return $this->addText($message);
    }

    /**
     * 
     * @param type $message is string
     * @return type
     */
    public function addSuccess($message) {
        return $this->addInfo($message);
    }

    /**
     * 
     * @param type $message is string
     * @return $this
     */
    public function addInfo($message) {
        $this->addText($this->info($message));
        return $this;
    }

    /**
     * 
     * @param type $message is string
     * @return $this
     */
    public function addError($message) {
        $this->addText($this->error($message));
        return $this;
    }

    /**
     * 
     * @param type $message is string
     * @return $this
     */
    public function addComment($message) {
        $this->addText($this->comment($message))
                ->breakLine()
                ->whiteSpace()
        ;
        return $this;
    }

    /**
     * 
     * @param type $text is initialize
     * @return $this
     */
    public function setText($text) {
        $this->text = $text;
        return $this;
    }

    /**
     * 
     * @param type $text is add html
     * @return $this
     */
    public function addText($text) {
        $this->text = $this->text . $text;
        return $this;
    }

    /**
     * 
     * @return type is $text html
     */
    public function getText() {
        return $this->text;
    }

    /**
     * Display $text
     */
    public function show() {
        echo $this->text . ' 
            
';
        return $this;
    }

    /**
     * Parsing text, html to console string
     */
    public function output() {
        $this->text = $this->parsing($this->text);
        return $this->show();
    }

}
