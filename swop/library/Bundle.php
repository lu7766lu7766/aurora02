<?php

class Bundle
{

    static $allLink = '';

    public function __construct()
    {

    }

    static private $bundle = array(
        "jquery" => array(
            "~/public/js/jquery-1.12.0.min.js",
            "~/public/js/jquery.confirm.min.js"
        ),
        "validate" => array(
            "~/public/js/jquery.validate.min.js"
        ),
        "bootstrap" => array(
            "~/public/bootstrap/css/bootstrap.min.css",
            "~/public/bootstrap/css/bootstrap-theme.min.css",
            "~/public/bootstrap/js/bootstrap.min.js",
            "~/public/bootstrap/css/bootstrap-switch.min.css",
            "~/public/bootstrap/js/bootstrap-switch.min.js",
            "~/public/bootstrap/css/slidebar.css"
        ),
        "default" => array(
            "~/public/js/common.js",
            "~/public/css/common.css"
        ),
        "datetime" => array(
            "~/public/js/jquery.datetimepicker.min.js",
            "~/public/css/jquery.datetimepicker.min.css"
        ),
        "reactjs" => array(
            "~/public/js/react-with-addons-0.14.7.min.js",//"http://fb.me/react-with-addons-0.13.0.min.js",//
            "~/public/js/react-dom-0.14.7.min.js",//"http://fb.me/react-0.13.0.min.js",//
            "~/public/js/JSXTransformer-0.13.0.js"//"http://fb.me/JSXTransformer-0.13.0.js",//
        ),
        "alertify" => array(
            "~/public/css/alertify.core.css",
            "~/public/css/alertify.default.css",
            "~/public/js/alertify.min.js",
        ),
        "angular" => array(
            "~/public/js/angular.min.js",
        ),
        "vue" => ["https://cdnjs.cloudflare.com/ajax/libs/vue/1.0.16/vue.js"],
        "vue2" => ["~/public/js/vue.min.js"],
        "vue-component" => ["~/public/js/vue_component.js"],
        "lodash" => ["~/public/js/lodash.min.js"],
        "moment" => ["https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.22.2/moment.min.js"],
        "math" => ["https://cdnjs.cloudflare.com/ajax/libs/mathjs/5.0.4/math.min.js"],
        "loading" => [
            "https://cdn.jsdelivr.net/npm/jquery-easy-loading@1.3.0/dist/jquery.loading.min.css",
            "https://cdn.jsdelivr.net/npm/jquery-easy-loading@1.3.0/dist/jquery.loading.min.js"
        ],
        "file-saver" => [
            "https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/1.3.3/FileSaver.min.js",
            "https://rawgit.com/eligrey/Blob.js/master/Blob.js",
            "https://rawgit.com/b4stien/js-csv-encoding/master/encoding-indexes.js",
            "https://rawgit.com/b4stien/js-csv-encoding/master/encoding.js"
        ],
        "bootstrap-datetime" => [
            "~/public/bootstrap/css/bootstrap-datetimepicker.min.css",
            "~/public/bootstrap/js/bootstrap-datetimepicker.min.js"
        ]
    );

    static public function addLink($linkKey)
    {
        // 未來建議引入uglifyPHP ，可以直接壓縮發布，self::$allLink getter?
        // https://github.com/smallhadroncollider-deprecated/uglify-php

        global $config; //= new setting\Config();

        $result = "";
        $ver = "?ver=".$config->base["version"];

        foreach(self::$bundle[$linkKey] as $file)
        {
            $file = str_replace("~/", $config->base["folder"] ,$file);
            $ext = end(explode('.', $file));
            switch(strtolower($ext))
            {
                case "js":
                    $result .= "<script src=\"". $file . $ver ."\"></script>\n";
                    break;
                case "jsx":
                    $result .= "<script src=\"". $file ."\" type=\"text/jsx\"></script>\n";
                    break;
                case "css":
                    $result .= "<link rel='stylesheet' type='text/css' href='". $file . $ver ."'/>\n";
                    break;
            }
        }
        self::$allLink .= $result;
//        return $result;
    }
}

?>
