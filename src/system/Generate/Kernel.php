<?php

namespace system\Generate;

class Kernel {

    private $config;
    private $commands = [
        "app"  => [
            "app:name" => [
                "description" => "Set the applications namespace",
                "method_name" => "appName"
            ]
        ],
        "make" => [
            "make:auth"       => [
                "description" => "Scaffold basic login and reqistration views and routes",
                "method_name" => "makeAuth"
            ],
            "make:module"     => [
                "description" => "Create a new module",
                "method_name" => "makeModule"
            ],
            "make:controller" => [
                "description" => "Create a new controller class",
                "method_name" => "makeController"
            ],
            "make:model"      => [
                "description" => "Create a new model class",
                "method_name" => "makeModel"
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

    /**
     * create a new module
     */
    public function makeModule($argv, \system\Generate\Console $console) {


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


        //create controller
        $this->createController($module_name, "Index", $console);


        //edit composer
        $jsonString = file_get_contents('composer.json');
        $data = json_decode($jsonString, true);


        $data['autoload']['psr-4'][$module_name . "\\"] = "module/$module_name/src/";

        $newJsonString = json_encode($data);
        file_put_contents('composer.json', str_replace("\/", "/", $newJsonString));

        //dumpautoload
        shell_exec("composer dump-autoload");

        //get config
        $module_config = require_once $this->config['DIR_ROOT'] . "/config/php/module.config.php";

        $module_config[] = $module_name;

        //create file /config/php/module.config.php
        $myfile = fopen($this->config['DIR_ROOT'] . "/config/php/module.config.php", "w");
        if (!$myfile) {
            $console->releaseError("Unable to open file! " . $this->config['DIR_ROOT'] . "/config/php/module.config.php");
            exit;
        }

        $txt = "<?php

return [";
        foreach ($module_config as $val) {
            $txt = $txt . "'$val',
                    ";
        }

        $txt = $txt . "];
";

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
        //view
        if (!mkdir($dir_module . "/view")) {
            $console->releaseError("Error, create folder view!!!");
            exit;
        }

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

}
