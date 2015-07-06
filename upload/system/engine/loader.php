<?php
final class Loader {
    private $registry;

    public function __construct($registry) {
        $this->registry = $registry;
    }

    public function controller($route, $args = array()) {
        $action = new Action($route, $args);

        return $action->execute($this->registry);
    }

    public function model($model) {
        $file = DIR_APPLICATION . 'model/' . $model . '.php';
        $class = 'Model' . preg_replace('/[^a-zA-Z0-9]/', '', $model);

        if (file_exists($file)) {
            include_once($file);

            $this->registry->set('model_' . str_replace('/', '_', $model), new $class($this->registry));
        } else {
            trigger_error('Error: Could not load model ' . $file . '!');
            exit();
        }
    }

    public function view($template, $data = array()) {
        $file = DIR_TEMPLATE . $template;

        if (file_exists($file)) {
            if( ! $this->registry->get('twig')) {

                $this->library('twig');

                Twig_Autoloader::register();

                if($this->registry->get('config') && is_dir(DIR_TEMPLATE . $this->registry->get('config')->get('config_template') . '/template')) {
                    $paths[] = DIR_TEMPLATE . $this->registry->get('config')->get('config_template') . '/template';
                }

                if(is_dir(DIR_TEMPLATE . 'default/template')) {
                    $paths[] = DIR_TEMPLATE . 'default/template';
                }

                $paths[] = DIR_TEMPLATE;

                $loader = new Twig_Loader_Chain(array(new Twig_Loader_Filesystem($paths), new Twig_Loader_String()));

                $cache = false;

                if(defined('TWIG_CACHE')) {
                    $cache = TWIG_CACHE;
                }

                $twig = new Twig_Environment($loader,array(
                    'autoescape' => false,
                    'cache' => $cache,
                    'debug' => true
                ));

                $twig->addExtension(new Twig_Extension_Debug());
                $twig->addExtension(new Twig_Extension_Opencart($this->registry));

                $this->registry->set('twig', $twig);
            }

            extract($data);
            ob_start();

            // First Step - Render Twig Native Templates
            $output = $this->registry->get('twig')->render($template, $data);

            // Second Step - IF template has PHP Syntax, then execute
            eval(' ?>' . $output);

            $output = ob_get_contents();

            ob_end_clean();

            return $output;
        } else {
            trigger_error('Error: Could not load template ' . $file . '!');
            exit();
        }
    }

    public function library($library) {
        $file = DIR_SYSTEM . 'library/' . $library . '.php';

        if (file_exists($file)) {
            include_once($file);
        } else {
            trigger_error('Error: Could not load library ' . $file . '!');
            exit();
        }
    }

    public function helper($helper) {
        $file = DIR_SYSTEM . 'helper/' . $helper . '.php';

        if (file_exists($file)) {
            include_once($file);
        } else {
            trigger_error('Error: Could not load helper ' . $file . '!');
            exit();
        }
    }

    public function config($config) {
        $this->registry->get('config')->load($config);
    }

    public function language($language) {
        return $this->registry->get('language')->load($language);
    }
}