<?php

namespace system;

class Session {

    //config session
    private $config;

    public function __construct(array $config = null) {
        $this->setConfig($config);
    }

    //set config system
    public function setConfig($config) {
        $this->config = $config;
        return $this;
    }

    //get config
    public function getConfig() {
        return $this->config;
    }

    //session working
    public function working() {

        $config = $this->config;

        //check config session
        if (!isset($config['session'])) {
            $config['session'] = [];
        }

        if (isset($config['session'])) {
            if (!isset($config['session']['save_path'])) {
                $config['session']['save_path'] = DIR_ROOT . 'data/Sessions';
            }

            //Expire the session if user is inactive for 30
            //minutes or more.
            if (!isset($config['session']['timeout'])) {
                $config['session']['timeout'] = 30;
            }
        }

        //check session already started
        if (session_status() == PHP_SESSION_NONE) {

            //Save session
            ini_set('session.save_path', $config['session']['save_path']);

            //Start our session.
            session_start();
        }


        //Check to see if our "last action" session
        //variable has been set.
        if (isset($_SESSION['last_action'])) {

            //Figure out how many seconds have passed
            //since the user was last active.
            $secondsInactive = time() - $_SESSION['last_action'];

            //Convert our minutes into seconds.
            $expireAfterSeconds = $config['session']['timeout'] * 60;

            //Check to see if they have been inactive for too long.
            if ($secondsInactive >= $expireAfterSeconds) {
                //User has been inactive for too long.
                //Kill their session.
                session_unset();
                session_destroy();

                return FALSE;
            }
        }

        //Assign the current timestamp as the user's
        //latest activity
        $_SESSION['last_action'] = time();

        return TRUE;
    }

    //create new session
    public function set($name, $value) {
        //check time expired
        if ($this->working()) {
            //set new session
            $_SESSION[$name] = $value;
        }

        return $this;
    }

    //get session
    public function get($name) {
        //check time expired
        if ($this->working()) {
            //check exists session name
            if (isset($_SESSION[$name])) {

                //return session name
                return $_SESSION[$name];
            }
        }

        return null;
    }

}
