<?php

namespace App;

use AltoRouter;
use App\Template\{Template, TemplateVars};

class Router
{

    private $router;
    private $match;
    private $templateConfig;

    public function __construct(Template $templateConfig)
    {
        $this->router = new AltoRouter;
        $this->templateConfig = $templateConfig;
    }

    // routing methods

    public function get(
        string $routeUrl,
        string $viewFile,
        ?string $linkName = null,
        array $args = []
    ): self {
        $this->map('GET', $routeUrl, $viewFile, $linkName, $args);
        return $this;
    }

    public function post(
        string $routeUrl,
        string $viewFile,
        ?string $linkName = null,
        array $args = []
    ): self {
        $this->map('POST', $routeUrl, $viewFile, $linkName, $args);
        return $this;
    }

    public function both(
        string $routeUrl,
        string $viewFile,
        ?string $linkName = null,
        array $args = []
    ): self {
        $this->map('GET|POST', $routeUrl, $viewFile, $linkName, $args);
        return $this;
    }

    // router core

    private function map(
        string $method,
        string $routeUrl,
        string $viewFile,
        string $linkName,
        array $args
    ) {
        return $this->router->map(
            $method,
            $routeUrl,
            function () use ($viewFile, $args) {
                // vars
                $router = $this;
                $template = $this->templateConfig;
                $templateVars = $this->getVars($args);
                // show template
                ob_start();
                require $templateVars->getViewsPath() . DS . $viewFile;
                $content = ob_get_clean();
                require TEMPLATE_PATH . DS . $templateVars->getTemplateName() . '.php';
            },
            $linkName
        );
    }

    public function run()
    {
        $this->match = $this->router->match();
        if ($this->match) {
            call_user_func_array($this->match['target'], $this->match['params']);
        } else {
            $router = $this;
            $menus = $this->templateConfig->getMenus();
            $template = $this->templateConfig;
            $templateVars = $this->getVars([]);
            ob_start();
            require $this->templateConfig->getDefaultViewsPath() . DS . '404.php';
            $content = ob_get_clean();
            require TEMPLATE_PATH . DS . $this->templateConfig->getDefaultTemplateName() . '.php';
        }
    }

    /**
     * Return template variables list: specific arg if define OR default one (specified in Template instance)
     * Vars list: 'views_path' (string), 'template_name' (string), 'menu_header_main' (array)
     *
     * @param  mixed $args Options defined foreach router map
     * @return TemplateVars Object containing variables list (through getters)
     */
    private function getVars(array $args): TemplateVars
    {
        $config = $this->templateConfig;
        $viewsPath = isset($args['views_path']) ? $args['views_paths'] : $config->getDefaultViewsPath();
        $templateName = isset($args['template']) ? $args['template'] : $config->getDefaultTemplateName();
        $menuHeaderMain = isset($args['menu_header_main']) ? $args['menu_header_main'] : $config->getMenu($config->getDefaultMenu('menu_header_main'));
        $menuHeaderSecondary = isset($args['menu_header_secondary']) ? $args['menu_header_secondary'] : $config->getMenu($config->getDefaultMenu('menu_header_secondary'));
        return new TemplateVars($viewsPath, $templateName, $menuHeaderMain, $menuHeaderSecondary);
    }

    public function url(string $linkName, array $params = [])
    {
        return $this->router->generate($linkName, $params);
    }

    // getters

    public function getRouter(): AltoRouter
    {
        return $this->router;
    }

    /** @return array|bool Return false if no match */
    public function getMatch(?string $key = null)
    {
        if (!isset($this->match)) {
            $this->match = $this->router->match();
        }
        if (false !== $this->match && null !== $key) return $this->match[$key];
        return $this->match;
    }

    public function getParams(?string $key = null)
    {
        if (false !== $this->match && null !== $key) return $this->match['params'][$key];
        return $this->match['params'];
    }
}
