<?php

namespace system\Generate;

class Kernel {

    private $config;
    private $commands = [
        "app" => [
            "app:name" => [
                "description" => "Set the applications namespace",
                "method_name" => "appName"
            ]
        ],
        "make" => [
            "make:auth" => [
                "description" => "Scaffold basic login and reqistration views and routes",
                "method_name" => "makeAuth"
            ],
            "make:module" => [
                "description" => "Create a new module",
                "method_name" => "makeModule"
            ],
            "make:controller" => [
                "description" => "Create a new controller class",
                "method_name" => "makeController"
            ],
            "make:model" => [
                "description" => "Create a new model class",
                "method_name" => "makeModel"
            ],
        ],
        "queue" => [
            "queue:work" => [
                "description" => "Start processing jobs on the queue as a daemon",
                "method_name" => "queueWork"
            ],
        ]
    ];

    public function __construct($config) {
        $this->config = $config;
    }

    /**
     * 
     * @return list commands
     */
    public function getCommands() {
        return $this->commands;
    }

    public function setCommands($commands) {
        $this->commands = $commands;
        return $this;
    }

    /**
     * 
     * @return list command methods
     */
    public function getCommandMethods() {
        $cm = [];
        foreach ($this->commands as $arr) {
            foreach ($arr as $key => $val) {
                $cm[$key] = $val["method_name"];
            }
        }

        return $cm;
    }

    public function getMethod($command) {
        $cm = $this->getCommandMethods();
        if (isset($cm[$command])) {
            return $cm[$command];
        }

        return "";
    }

    /**
     * 
     * @param type $group example make
     * @param type $key example make:module
     * @param type $value example [description=>create a new module, method_name=>makeModule]
     * @return $this
     */
    public function addCommand($group, $key, $value) {

        if (isset($this->commands[$group])) {
            $this->commands[$group][$key] = $value;
        } else {
            $this->commands[$group] = [$key => $value];
        }

        return $this;
    }

    /*
     * List methods
     */

    //Queue
    public function queueWork($argv) {
        Queue::work($argv);
    }

    //Make
    public function makeController($argv) {

        $console = new \system\Generate\Console();
        if (count($argv) <= 2) {
            $console->releaseError("Not enough arguments (missing: name of controller).");
            exit;
        }

        //select module
        $console->breakLine()
                ->whiteSpace()
                ->addComment("Please choose Module to install this Controller");
        $i = 0;
        $options = [];
        $listmodule = [];
        foreach ($this->config["TAMI_MODULE"] as $val) {
            $i++;
            $console->addCommand("$i: ", $val);
            $options[] = $i;
            $listmodule[$i] = $val;
        }

        do {

            $console->addMessage("Select the appropriate number [")
                    ->addInfo(implode("-", $options))
                    ->addMessage("] then [enter] (press 'c' to cancel): ");

            $console->release();

            $option = rtrim(fgets(STDIN));

            if ($option == "c") {
                $console->releaseError("Process create new controller was cancelled!!!.");
                exit;
            }

            $loop = true;
            if (in_array($option, $options)) {
                $console->addMessage(" You already choose ")
                        ->addInfo($listmodule[$option])
                        ->addMessage(" Module to install for this controller (yes/no)? [")
                        ->addInfo("yes|no")
                        ->addMessage("]: ");
                $console->release();
                $cf = rtrim(fgets(STDIN));
                if ($cf == "yes") {
                    $loop = false;
                }
            }
        } while ($loop);

        $module_name = $listmodule[$option];

        //make controller
        $controller_name = $argv[2];


        $this->addControllerToConfig($module_name, $controller_name, $console);

        $this->createController($module_name, $controller_name, $console);
    }

    /**
     * create a new module
     */
    public function makeModule($argv) {

        $console = new \system\Generate\Console();

        if (count($argv) <= 2) {
            $console->releaseError("Not enough arguments (missing: name of module).");
            exit;
        }

        //module name
        $module_name = $argv[2];

        $dir_module = $this->config['DIR_ROOT'] . "/module/" . $module_name;

        //check name
        if (file_exists($dir_module)) {
            $console->releaseError("Module already exists! ");
            exit;
        }

        $options = [];
        $lenght = count($argv);
        //get options
        if ($lenght > 2) {
            for ($i = 3; $i < $lenght; $i++) {
                $options[] = $argv[$i];
            }
        }

        //options in arguments
        if (count($options) > 0) {
            
        }

        //mkdir module
        if (!mkdir($dir_module)) {
            $console->releaseError("Error, create folder module!!!");
            exit;
        }

        //config
        if (!mkdir($dir_module . "/config")) {
            $console->releaseError("Error, create folder config!!!");
            exit;
        }

        //file module config
        $this->createFileConfig($module_name, $console);

        //src
        if (!mkdir($dir_module . "/src")) {
            $console->releaseError("Error, create folder src!!!");
            exit;
        }


        //src/Module.php
        $myfile = fopen($dir_module . "/src/Module.php", "w");
        if (!$myfile) {
            $console->releaseError("Unable to open file! " . $dir_module . "/src/Module.php");
            exit;
        }

        $txt = "<?php

namespace $module_name;

class Module {

    public function getConfig() {
        return include __DIR__ . '/../config/module.config.php';
    }

}

";
        fwrite($myfile, $txt);
        fclose($myfile);


        //view
        if (!mkdir($dir_module . "/view")) {
            $console->releaseError("Error, create folder view!!!");
            exit;
        }

        //create controller
        $this->createController($module_name, "Index", $console);


        //edit composer
        $jsonString = file_get_contents('composer.json');
        $data = json_decode($jsonString, true);


        $data['autoload']['psr-4'][$module_name . "\\"] = "module/$module_name/src/";

        $newJsonString = json_encode($data, JSON_PRETTY_PRINT);
        file_put_contents('composer.json', str_replace("\/", "/", $newJsonString));

        //dumpautoload
        shell_exec("composer dump-autoload");

        //get config
        $module_config = include $this->config['DIR_ROOT'] . "/config/php/module.config.php";


        $module_config[] = $module_name;

        //create file /config/php/module.config.php
        $myfile = fopen($this->config['DIR_ROOT'] . "/config/php/module.config.php", "w");
        if (!$myfile) {
            $console->releaseError("Unable to open file! " . $this->config['DIR_ROOT'] . "/config/php/module.config.php");
            exit;
        }

        $txt = "<?php

return [
";
        foreach ($module_config as $val) {
            $txt = $txt . "    '$val',
";
        }

        $txt = $txt . "
];
";

        fwrite($myfile, $txt);
        fclose($myfile);




        //layout
        if (!mkdir($dir_module . "/view/layout")) {
            $console->releaseError("Error, create folder: " . $dir_module . "/view/layout");
            exit;
        }

        //create file view layout
        $myfile = fopen($dir_module . "/view/layout/layout.tami", "w");
        if (!$myfile) {
            $console->releaseError("Unable to open file! " . $dir_module . "/view/layout/layout.tami");
            exit;
        }

        $txt = '<!DOCTYPE html>
<html>
    <head>
        <?php
        $this->title("Học làm sản phẩm");
        ?>
       
        <?php
        $this->partialLayout(\'layout/head.tami\');
        ?>

    </head>
    <body>
        <?php
        $this->partialLayout(\'layout/left.tami\');
        $this->partialLayout(\'layout/right.tami\');
        ?>

        <?php
        $this->showViewFile();
        ?>
        <footer>
            <?php
            $this->partialLayout(\'layout/foot.tami\');
            ?>
        </footer>
    </body>
</html>';

        fwrite($myfile, $txt);
        fclose($myfile);

        //layout/layout
        if (!mkdir($dir_module . "/view/layout/layout")) {
            $console->releaseError("Error, create folder: " . $dir_module . "/view/layout/layout");
            exit;
        }


        //create file view layout head.tami
        $myfile = fopen($dir_module . "/view/layout/layout/head.tami", "w");
        if (!$myfile) {
            $console->releaseError("Unable to open file! " . $dir_module . "/view/layout/layout/head.tami");
            exit;
        }

        $txt = '<meta charset="utf-8">
<!-- Mobile specific metas -->
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1 user-scalable=no">
<!-- Force IE9 to render in normal mode -->
<!--[if IE]><meta http-equiv="x-ua-compatible" content="IE=9" /><![endif]-->
<meta name="author" content="" />
<meta name="description" content="" />
<meta name="keywords" content="" />
<meta name="application-name" content="" />

<!-- Fav and touch icons -->
<link rel="apple-touch-icon-precomposed" sizes="144x144" href="/static/img/ico/apple-touch-icon-144-precomposed.png">
<link rel="apple-touch-icon-precomposed" sizes="114x114" href="/static/img/ico/apple-touch-icon-114-precomposed.png">
<link rel="apple-touch-icon-precomposed" sizes="72x72" href="/static/img/ico/apple-touch-icon-72-precomposed.png">
<link rel="apple-touch-icon-precomposed" href="/static/img/ico/apple-touch-icon-57-precomposed.png">
<link rel="icon" href="/static/img/ico/favicon.ico" type="image/png">
<!-- Windows8 touch icon ( http://www.buildmypinnedsite.com/ )-->
<meta name="msapplication-TileColor" content="#3399cc" />

<!-- Import google fonts - Heading first/ text second -->
<link href=\'http://fonts.googleapis.com/css?family=Quattrocento+Sans:400,700\' rel=\'stylesheet\' type=\'text/css\'>

<?php $this->css(["/tami/css/tami.css", "/tami/resource/autoload.css"]) ?>
<?php $this->js(["/tami/js/tami.js", "/tami/js/common/autoload.js"]) ?>
';

        fwrite($myfile, $txt);
        fclose($myfile);



        //create file view foot.tami
        $myfile = fopen($dir_module . "/view/layout/layout/foot.tami", "w");
        if (!$myfile) {
            $console->releaseError("Unable to open file! " . $dir_module . "/view/layout/layout/foot.tami");
            exit;
        }

        $txt = "";

        fwrite($myfile, $txt);
        fclose($myfile);


        //create file view left.tami
        $myfile = fopen($dir_module . "/view/layout/layout/left.tami", "w");
        if (!$myfile) {
            $console->releaseError("Unable to open file! " . $dir_module . "/view/layout/layout/left.tami");
            exit;
        }

        $txt = "";

        fwrite($myfile, $txt);
        fclose($myfile);


        //create file view right.tami
        $myfile = fopen($dir_module . "/view/layout/layout/right.tami", "w");
        if (!$myfile) {
            $console->releaseError("Unable to open file! " . $dir_module . "/view/layout/layout/right.tami");
            exit;
        }

        $txt = "";

        fwrite($myfile, $txt);
        fclose($myfile);


        return $this;
    }

    /**
     * 
     * @param type $module_name
     * @param type $controller_name
     * @param type $console
     */
    public function createController($module_name, $controller_name, $console) {

        $dir_module = $this->config['DIR_ROOT'] . "/module/" . $module_name;

        //check folder controller
        if (!file_exists($dir_module . "/src/Controller")) {
            if (!mkdir($dir_module . "/src/Controller")) {
                $console->releaseError("Error, create folder src/Controller!!!");
                exit;
            }
        }

        //create file controller
        $myfile = fopen($dir_module . "/src/Controller/{$controller_name}Controller.php", "w");
        if (!$myfile) {
            $console->releaseError("Unable to open file! " . $dir_module . "/src/Controller/{$controller_name}Controller.php");
            exit;
        }

        $txt = "<?php

namespace $module_name\Controller;

class {$controller_name}Controller extends \system\Template\AbstractController {

    public function indexAction() {

        return [];
    }
    
}

";
        fwrite($myfile, $txt);
        fclose($myfile);

        //create view for controller


        if (!mkdir($dir_module . "/view/" . strtolower($controller_name))) {
            $console->releaseError("Error, create folder: " . $dir_module . "/view/" . strtolower($controller_name));
            exit;
        }


        //create file view
        $myfile = fopen($dir_module . "/view/" . strtolower($controller_name) . "/index.tami", "w");
        if (!$myfile) {
            $console->releaseError("Unable to open file! " . $dir_module . "/view/" . strtolower($controller_name) . "/index.tami");
            exit;
        }

        $txt = "view of $controller_name controller";

        fwrite($myfile, $txt);
        fclose($myfile);
    }

    /**
     * 
     * @param type $module_name
     * @param type $console
     */
    public function createFileConfig($module_name, $console) {

        $dir_module = $this->config['DIR_ROOT'] . "/module/" . $module_name;

        $myfile = fopen($dir_module . "/config/module.config.php", "w");
        if (!$myfile) {
            $console->releaseError("Unable to open file! " . $dir_module . "/config/module.config.php");
            exit;
        }

        $strlow = strtolower($module_name);

        $txt = "<?php

namespace {$module_name};

return [
    'router' => [
        '{$strlow}' => [
            'index' => Controller\IndexController::class
        ]
    ],
    'controller' => [
        'factories' => [
            Controller\IndexController::class => \system\Template\Factory::class
        ]
    ],
    'view_manager' => [
        'layout' => dirname(__DIR__) . '/view/layout/layout.tami'
    ]
];

";
        fwrite($myfile, $txt);
        fclose($myfile);
    }

    public function addControllerToConfig($module_name, $controller_name, $console) {
        $dir_module = $this->config['DIR_ROOT'] . "/module/" . $module_name;

        $this->setConfigFilePhp($dir_module, $module_name, $controller_name);
    }

    public function writeConfig($dir_module, $module_name, $mconfig) {
        $dir_module = $this->config['DIR_ROOT'] . "/module/" . $module_name;

        $myfile = fopen($dir_module . "/config/module.config.php", "w");
        if (!$myfile) {
            $console->releaseError("Unable to open file! " . $dir_module . "/config/module.config.php");
            exit;
        }

        $strlow = strtolower($module_name);

        $txt = "<?php

namespace {$module_name};

return [
    'router' => [
        '{$strlow}' => [
            'index' => Controller\IndexController::class
        ]
    ],
    'controller' => [
        'factories' => [
            Controller\IndexController::class => \system\Template\Factory::class
        ]
    ],
    'view_manager' => [
        'layout' => dirname(__DIR__) . '/view/layout/layout.tami'
    ]
];

";
        fwrite($myfile, $txt);
        fclose($myfile);
    }

    public function setConfigFilePhp($dir_module, $module_name, $controller_name) {


        $this->insertFile($dir_module . "/config/module.config.php", 'router', strtolower($module_name), "\n            '" . strtolower($controller_name) . "' => Controller\\{$controller_name}Controller::class,", ", '" . strtolower($module_name) . "' => [
            '" . strtolower($controller_name) . "' => Controller\\{$controller_name}Controller::class
        ],
"
        );

        $this->insertFile($dir_module . "/config/module.config.php", 'controller', 'factories', "\n            Controller\\{$controller_name}Controller::class => \system\Template\Factory::class,", ""
        );
    }

    public function insertFile($filedir, $key, $find, $text, $textnotfound) {
        $wordBoundries = array("\n", " ");
        $wordBuffer = "";

        $openBoundries = ['[', '('];
        $endBoundries = [']', ')'];

        $file = fopen($filedir, "r");

        $filetmp = fopen($filedir . "_tmp", "w");

        while (!feof($file)) {
            $c = fgetc($file);
            //is key
            if (in_array($c, $wordBoundries)) {
                // do something then clear the buffer
                if ($wordBuffer == "'$key'" || $wordBuffer == "\"$key\"") {
                    break;
                }
                $wordBuffer = "";
            } else {
                // add the letter to the buffer
                $wordBuffer .= $c;
            }

            //new file
            fwrite($filetmp, $c);
        }

        //flat
        $fkey = 0;
        $fwn = TRUE;
        $founed = FALSE;
        $fww = FALSE;
        while (!feof($file)) {
            $c = fgetc($file);


            //end
            if (in_array($c, $endBoundries)) {
                $fkey --;
                //exit []
                if ($fkey === 0) {
                    $fwn = FALSE;
                    if ($fww === FALSE) {
                        //new file
                        fwrite($filetmp, $textnotfound);
                        $fww = TRUE;
                    }
                }
            }

            //new file
            fwrite($filetmp, $c);

            //open
            if (in_array($c, $openBoundries)) {
                $fkey ++;

                if ($founed === TRUE) {
                    //new file
                    fwrite($filetmp, $text);
                    $founed = FALSE;
                    $fww = TRUE;
                }
            }


            //is key
            if ($fkey === 1 && $fwn === TRUE) {
                if (in_array($c, $wordBoundries)) {
                    // do something then clear the buffer
                    //insert data
                    if ($wordBuffer == "'$find'" || $wordBuffer == "\"$find\"") {
                        $founed = TRUE;
                        $fwn = FALSE;
                    }


                    $wordBuffer = "";
                } else {
                    // add the letter to the buffer
                    $wordBuffer .= $c;
                }
            }
        }

        fclose($filetmp);
        fclose($file);

        //move
        copy($filedir . "_tmp", $filedir);
        unlink($filedir . "_tmp");
    }

    //read command line real time
    public static function execute($cmd) {
        $descriptorspec = array(
            0 => array("pipe", "r"), // stdin is a pipe that the child will read from
            1 => array("pipe", "w"), // stdout is a pipe that the child will write to
            2 => array("pipe", "w")    // stderr is a pipe that the child will write to
        );
        flush();
        $process = proc_open($cmd, $descriptorspec, $pipes, realpath('./'), array());
        if (is_resource($process)) {
            while ($s = fgets($pipes[1])) {
                print $s;
                flush();
            }
        }
    }

}
