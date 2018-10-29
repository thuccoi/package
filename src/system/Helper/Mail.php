<?php

namespace system\Helper;

use PHPMailer\PHPMailer\PHPMailer;

class Mail {

    private $mail;

    //system config
    public function __construct($config) {

        //read https://github.com/PHPMailer/PHPMailer to more information
        if (!isset($config['mail'])) {
            $config['mail'] = [
                'CharSet' => 'UTF-8',
                'Host' => 'smtp.gmail.com',
                'SMTPSecure' => 'tls',
                'Port' => 587,
                'SMTPDebug' => 1,
                'SMTPAuth' => true,
                'isHTML' => true,
                'Username' => 'me.sender@gmail.com',
                'Password' => '123!@#',
                'SetFrom' => 'me.sender@gmail.com',
                'nameFrom' => 'test',
                'replyTo' => 'no-reply@mycomp.com',
                'nameReply' => 'no-reply'
            ];
        }

        if (!isset($config['mail']['CharSet'])) {
            echo "Require config mail CharSet";
            exit;
        }

        if (!isset($config['mail']['Host'])) {
            echo "Require config mail Host";
            exit;
        }

        if (!isset($config['mail']['SMTPSecure'])) {
            echo "Require config mail SMTPSecure";
            exit;
        }

        if (!isset($config['mail']['Port'])) {
            echo "Require config mail Port";
            exit;
        }

        if (!isset($config['mail']['SMTPDebug'])) {
            echo "Require config mail SMTPDebug";
            exit;
        }

        if (!isset($config['mail']['SMTPAuth'])) {
            echo "Require config mail SMTPAuth";
            exit;
        }

        if (!isset($config['mail']['isHTML'])) {
            echo "Require config mail isHTML";
            exit;
        }

        if (!isset($config['mail']['Username'])) {
            echo "Require config mail Username";
            exit;
        }

        if (!isset($config['mail']['Password'])) {
            echo "Require config mail Password";
            exit;
        }

        if (!isset($config['mail']['SetFrom'])) {
            echo "Require config mail SetFrom";
            exit;
        }

        if (!isset($config['mail']['nameFrom'])) {
            echo "Require config mail nameFrom";
            exit;
        }

        if (!isset($config['mail']['replyTo'])) {
            echo "Require config mail replyTo";
            exit;
        }

        if (!isset($config['mail']['nameReply'])) {
            echo "Require config mail nameReply";
            exit;
        }

        $mail = new PHPMailer();
        $mail->CharSet = $config['mail']['CharSet'];

        $mail->IsSMTP();
        $mail->Host = $config['mail']['Host'];

        $mail->SMTPSecure = $config['mail']['SMTPSecure'];
        $mail->Port = $config['mail']['Port'];
        $mail->SMTPDebug = $config['mail']['SMTPDebug'];
        $mail->SMTPAuth = $config['mail']['SMTPAuth'];

        $mail->Username = $config['mail']['Username'];
        $mail->Password = $config['mail']['Password'];

        $mail->SetFrom($config['mail']['SetFrom'], $config['mail']['nameFrom']);
        $mail->AddReplyTo($config['mail']['replyTo'], $config['mail']['nameReply']);


        $mail->isHTML($config['mail']['isHTML']);

        $this->mail = $mail;
    }

    //address to
    public function to($email, $name = null) {
        if ($name) {
            $this->mail->AddAddress($email, $name);
        } else {
            $this->mail->AddAddress($email);
        }
    }

    //add cc
    public function cc($email) {
        $this->mail->addCC($email);
    }

    //add bcc
    public function bcc($email) {
        $this->mail->addBCC($email);
    }

    //address
    public function AddAttachment($filename, $name = null) {
        if ($name) {
            $this->mail->AddAttachment($filename, $name);
        } else {
            $this->mail->AddAttachment($filename);
        }
    }

    //subject
    public function subject($subject) {
        $this->mail->Subject = $subject;
    }

    //$mail->msgHTML("body abc");
    //$mail->msgHTML(file_get_contents('contents.html'), __DIR__);
    //$Mail->msgHTML('<img src="/etc/hostname">test');
    public function body($message, $basedir = '', $advanced = false) {
        $this->mail->MsgHTML($message, $basedir, $advanced);
    }

    //body
    public function altBody($alt) {
        $this->mail->AltBody = $alt;
    }

    //send
    public function send() {
        $this->mail->send();
    }

}
