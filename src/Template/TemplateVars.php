<?php namespace App\Template;

class TemplateVars {

    private $viewsPath;
    private $templateName;
    private $menuHeaderMain;
    private $menuHeaderSecondary;

    public function __construct(string $viewsPath, string $templateName, array $menuHeaderMain, array $menuHeaderSecondary)
    {
        $this->viewsPath = $viewsPath;
        $this->templateName = $templateName;
        $this->menuHeaderMain = $menuHeaderMain;
        $this->menuHeaderSecondary = $menuHeaderSecondary;
    }

    // getters

    public function getViewsPath()
    {
        return $this->viewsPath;
    }

    public function getTemplateName()
    {
        return $this->templateName;
    }

    public function getMenuHeaderMain()
    {
        return $this->menuHeaderMain;
    }

    public function getMenuHeaderSecondary()
    {
        return $this->menuHeaderSecondary;
    }

}