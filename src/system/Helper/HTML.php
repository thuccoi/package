<?php

namespace system\Helper;

class HTML {

    public static $TAMI_GET = "GET";
    public static $TAMI_POST = "POST";

    public static function addQuery($code, $stringparam) {
        //url decode
        $stringurl = urldecode($stringparam);

        if ($stringurl) {

            //get document
            $documents = explode('&', $stringurl);

            if ($documents) {
                foreach ($documents as $val) {
                    $document = explode('=', $val);
                    if (count($document) == 2) {
                        $_GET[$code->purify($document[0])] = $code->purify($document[1]);
                    }
                }
            }
        }
    }

    public static function getPathUri($code, $path_uri, $sysconfig) {
        //check config router
        if (!isset($sysconfig['routerDefault'])) {
            echo "Router default not config";
            exit;
        }

        if (!isset($sysconfig['routerDefault']['module'])) {
            echo "Router default module not config";
            exit;
        }


        if (!isset($sysconfig['routerDefault']['controller'])) {
            echo "Router default controller not config";
            exit;
        }


        if (!isset($sysconfig['routerDefault']['action'])) {
            echo "Router default action not config";
            exit;
        }



        $detach = explode("/", $path_uri);

        //purify detach
        $arr = [];
        if ($detach) {
            foreach ($detach as $val) {
                if ($val) {
                    $arr [] = $code->purify($val);
                }
            }
        }

        //output
        $module = "";
        $controller = "";
        $action = "";
        $id = "";

        //analytis arr
        if (count($arr) == 4) {
            $module = $arr[0];
            $controller = $arr[1];
            $action = $arr[2];
            $id = $arr[3];
        } else if (count($arr) == 3) {
            $module = $arr[0];
            $controller = $arr[1];
            $action = $arr[2];
        } else if (count($arr) == 2) {
            $module = $arr[0];
            $controller = $arr[1];
            $action = $sysconfig['routerDefault']['action'];
        } else if (count($arr) == 1) {
            $module = $arr[0];
            $controller = $sysconfig['routerDefault']['controller'];
            $action = $sysconfig['routerDefault']['action'];
        } else if (count($arr) == 0) {
            $module = $sysconfig['routerDefault']['module'];
            $controller = $sysconfig['routerDefault']['controller'];
            $action = $sysconfig['routerDefault']['action'];
        } else {
            echo "Invalid URL";
            exit;
        }


        //init router
        $router = new \system\Router($module, $controller, $action, $id, [], $sysconfig);

        return $router;
    }

    /**
     * 
     *   $files = array(
     *       'http://myframework.com/static/plugins/core/pace/pace.min.js',
     *       'http://myframework.com/static/js/libs/jquery-2.1.1.min.js',
     *       'http://myframework.com/static/js/libs/jquery-ui-1.10.4.min.js',
     *   );
     *
     *  echo \system\Helper\HTML::combine($files, 'minified_files/', md5("my_mini_file") . ".js");
     */
    public static function combine($array_files, $destination_dir, $dest_file_name) {

        if (!is_file($destination_dir . $dest_file_name)) { //continue only if file doesn't exist
            $content = "";
            foreach ($array_files as $file) { //loop through array list
                $content .= file_get_contents($file); //read each file
            }

            //You can use some sort of minifier here 
            //minify_my_js($content);

            $new_file = fopen($destination_dir . $dest_file_name, "w"); //open file for writing
            fwrite($new_file, $content); //write to destination
            fclose($new_file);

            return '<script src="' . $destination_dir . $dest_file_name . '"></script>'; //output combined file
        } else {
            //use stored file
            return '<script src="' . $destination_dir . $dest_file_name . '"></script>'; //output combine file
        }
    }

    //generate file static default
    public static function mergeUIStaticDefault($domain) {
        
        \system\Template\Container::defineDirRoot();
        
        //merge css and js
        static::mergeUICSS($domain, DIR_ROOT . '/public/tami/css/', 'tami.css');
        static::mergeUIJS($domain, DIR_ROOT . '/public/tami/js/', 'tami.js');
    }

    //merge css
    public static function mergeUICSS($domain, $dirtosave, $filename) {
        $files = [
            $domain . "/static/css/icons.css",
            $domain . "/static/css/bootstrap.css",
            $domain . "/static/css/plugins.css",
            $domain . "/static/css/main.css",
            $domain . "/static/css/custom.css",
        ];

        //combine code
        \system\Helper\HTML::combine($files, $dirtosave, $filename);
    }

    //merge js
    public static function mergeUIJS($domain, $dirtosave, $filename) {

        $files = [
            $domain . "/static/plugins/core/pace/pace.min.js",
            $domain . "/static/js/libs/jquery-2.1.1.min.js",
            $domain . "/static/js/libs/jquery-ui-1.10.4.min.js",
            $domain . "/static/js/bootstrap/bootstrap.js",
            $domain . "/static/js/libs/modernizr.custom.js",
            $domain . "/static/js/jRespond.min.js",
            $domain . "/static/plugins/core/slimscroll/jquery.slimscroll.min.js",
            $domain . "/static/plugins/core/slimscroll/jquery.slimscroll.horizontal.min.js",
            $domain . "/static/plugins/core/fastclick/fastclick.js",
            $domain . "/static/plugins/core/velocity/jquery.velocity.min.js",
            $domain . "/static/plugins/core/quicksearch/jquery.quicksearch.js",
            $domain . "/static/plugins/ui/bootbox/bootbox.js",
            $domain . "/static/plugins/ui/bootstrap-slider/bootstrap-slider.js",
            $domain . "/static/plugins/misc/touchpunch/jquery.ui.touch-punch.js",
            $domain . "/static/plugins/charts/pie-chart/jquery.easy-pie-chart.js",
            $domain . "/static/plugins/charts/canvas-gauge/gauge.js",
            $domain . "/static/plugins/charts/gauge/gauge.js",
            $domain . "/static/plugins/ui/tabdrop/bootstrap-tabdrop.js",
            $domain . "/static/plugins/ui/title-notifier/title_notifier.js",
            $domain . "/static/plugins/ui/notify/jquery.gritter.js",
            $domain . "/static/plugins/ui/bootstrap-sweetalert/sweet-alert.js",
            $domain . "/static/plugins/ui/nestable/jquery.nestable.js",
            $domain . "/static/plugins/forms/autosize/jquery.autosize.js",
            $domain . "/static/plugins/forms/maxlength/bootstrap-maxlength.js",
            $domain . "/static/plugins/forms/maskedinput/jquery.maskedinput.js",
            $domain . "/static/plugins/forms/dual-list-box/jquery.bootstrap-duallistbox.js",
            $domain . "/static/plugins/forms/spinner/jquery.bootstrap-touchspin.js",
            $domain . "/static/plugins/forms/bootstrap-datepicker/bootstrap-datepicker.js",
            $domain . "/static/plugins/forms/bootstrap-timepicker/bootstrap-timepicker.js",
            $domain . "/static/plugins/forms/bootstrap-colorpicker/bootstrap-colorpicker.js",
            $domain . "/static/js/libs/typeahead.bundle.js",
            $domain . "/static/plugins/forms/bootstrap-markdown/bootstrap-markdown.js",
            $domain . "/static/plugins/forms/bootstrap-filestyle/bootstrap-filestyle.js",
            $domain . "/static/plugins/forms/select2/select2.js",
            $domain . "/static/plugins/forms/bootstrap-wizard/jquery.bootstrap.wizard.js",
            $domain . "/static/plugins/forms/validation/jquery.validate.js",
            $domain . "/static/plugins/forms/validation/additional-methods.min.js",
            $domain . "/static/plugins/forms/codemirror/codemirror.js",
            $domain . "/static/plugins/forms/codemirror/lang/xml/xml.js",
            $domain . "/static/plugins/forms/codemirror/lang/css/css.js",
            $domain . "/static/plugins/forms/codemirror/lang/vbscript/vbscript.js",
            $domain . "/static/plugins/forms/codemirror/lang/javascript/javascript.js",
            $domain . "/static/plugins/forms/codemirror/lang/htmlmixed/htmlmixed.js",
            $domain . "/static/plugins/forms/codemirror/addons/edit/matchbrackets.js",
            $domain . "/static/plugins/forms/codemirror/addons/selection/active-line.js",
            $domain . "/static/plugins/tables/datatables/jquery.dataTables.js",
            $domain . "/static/plugins/tables/datatables/dataTables.tableTools.js",
            $domain . "/static/plugins/tables/datatables/dataTables.bootstrap.js",
            $domain . "/static/plugins/tables/datatables/dataTables.responsive.js",
            $domain . "/static/plugins/tables/editable-table/mindmup-editabletable.js",
            $domain . "/static/plugins/tables/editable-table/numeric-input-example.js",
            $domain . "/static/plugins/forms/summernote/summernote.min.js",
//    $domain . "/static/plugins/misc/gmaps/gmaps.js",
//    $domain . "/static/plugins/misc/vectormaps/jquery-jvectormap-1.2.2.min.js",
//    $domain . "/static/plugins/misc/vectormaps/maps/jquery-jvectormap-world-mill-en.js",
            $domain . "/static/js/libs/date.js",
            $domain . "/static/plugins/charts/flot/jquery.flot.custom.min.js",
            $domain . "/static/plugins/charts/flot/jquery.flot.pie.min.js",
            $domain . "/static/plugins/charts/flot/jquery.flot.resize.min.js",
            $domain . "/static/plugins/charts/flot/jquery.flot.time.min.js",
            $domain . "/static/plugins/charts/flot/jquery.flot.growraf.min.js",
            $domain . "/static/plugins/charts/flot/jquery.flot.categories.min.js",
            $domain . "/static/plugins/charts/flot/jquery.flot.stack.min.js",
            $domain . "/static/plugins/charts/flot/jquery.flot.orderBars.js",
            $domain . "/static/plugins/charts/flot/jquery.flot.tooltip.min.js",
            $domain . "/static/js/libs/raphael-min.js",
            $domain . "/static/plugins/charts/morris/morris.min.js",
            $domain . "/static/plugins/charts/chartjs/Chart.min.js",
            $domain . "/static/plugins/forms/bootstrap-tagsinput/bootstrap-tagsinput.min.js",
            $domain . "/static/plugins/ui/waypoint/waypoints.js",
            $domain . "/static/plugins/forms/fancyselect/fancySelect.js",
            $domain . "/static/js/libs/moment.js",
            $domain . "/static/plugins/ui/calendar/fullcalendar.min.js",
            $domain . "/static/plugins/charts/sparklines/jquery.sparkline.min.js",
            $domain . "/static/plugins/ui/lightbox/ekko-lightbox.min.js",
            $domain . "/static/plugins/forms/checkall/jquery.checkAll.min.js",
            $domain . "/static/plugins/forms/dropzone/dropzone.js",
            $domain . "/static/js/jquery.dynamic.min.js",
            $domain . "/static/js/main.min.js",
        ];

        //combine code
        \system\Helper\HTML::combine($files, $dirtosave, $filename);
    }

}
