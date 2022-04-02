<?php namespace App\Template;

use Exception;

class Template {

    private $defaultTemplateName;
    private $defaultViewsPath;
    private $menus;
    private $defaultMenus;
    /** @var array */
    private $headerScripts = [];
    /** @var array */
    private $footerScripts = [];


    public function __construct(
        string $defaultTemplateName, 
        string $defaultViewsPath, 
        array $menus, 
        array $defaultMenus)
    {
        $this->defaultTemplateName = $defaultTemplateName;
        $this->defaultViewsPath = $defaultViewsPath;
        $this->menus = $menus;
        $this->defaultMenus = $defaultMenus;
        
        // Load global CSS and JS (from root/config/settings.php)
        foreach(SITE_CSS_GLOBAL as $fileName => $defer) {
            if($defer) {
                $this->footerScripts['css'][] = 'global' . DS . $fileName;
            } else {
                $this->headerScripts['css'][] = 'global' . DS . $fileName;
            }
        }
        foreach(SITE_JS_GLOBAL as $fileName => $defer) {
            if($defer) {
                $this->footerScripts['js'][] = 'global' . DS . $fileName;
            } else {
                $this->headerScripts['js'][] = 'global' . DS . $fileName;
            }
        }
    }

    // getters (oop)

    public function getDefaultTemplateName(): string
    {
        return $this->defaultTemplateName;
    }

    public function getDefaultViewsPath(): string
    {
        return $this->defaultViewsPath;
    }

    public function getMenus(): array
    {
        return $this->menus;
    }

    public function getDefaultMenus(): array
    {
        return $this->defaultMenus;
    }

    public function getMenu(string $menuPosition): array
    {
        return $this->getMenus()[$menuPosition];
    }

    public function getDefaultMenu(string $menuPosition): string
    {
        return $this->getDefaultMenus()[$menuPosition];
    }

    // getters (static)

    public static function getLogo(): void
    {
        // Check file extension
        $parts =  explode('.', SITE_LOGO);
        $extension = end($parts);
        // Tags
        if($extension === 'svg') {
            $before = '<svg class="logo-header bi me-2" width="'.SITE_LOGO_WIDTH.'" height="'.SITE_LOGO_HEIGHT.'" role="img">';
            ob_start(); include IMG_PATH . DS . 'logo' . DS . SITE_LOGO; $logo = ob_get_clean();
            $after = '</svg>';
            echo $before . $logo . $after;
        } else {
            echo '<img alt="logo of '. SITE_NAME.'" width="auto" height="'.SITE_LOGO_HEIGHT.'px" src="img/logo/'.SITE_LOGO.'">';
        }
    }

    
    /**
     * Scripts run on every pages (footer)
     * 
     * @param $location Location where to execute the scripts. Allowed slots 'header', 'footer'
     */
    public function executeScripts(string $location): void
    {
        $arrayName = strtolower($location).'Scripts';

        foreach($this->$arrayName as $language => $files) {
            switch($language) {
                case 'css':
                    foreach($files as $fileName) {
                        echo '<style>';
                        include CSS_PATH . DS . $fileName . '.css';
                        echo '</style>';
                    }
                    break;
                case 'js':
                    foreach($files as $fileName) {
                        echo '<script>';
                        include JS_PATH . DS . $fileName . '.js';
                        echo '</script>';
                    }
                    break;
                default: 
                    throw new Exception('Variable $language for method '.__METHOD__.' is not valid. Please check allowed values in the method info.');
                    break;
            }
        }
        
    }

    /**
     * Add a script to array property: $this->headerScripts and $this->footerScripts
     * All script are executed in bulk using $this->executeScripts()
     * 
     * @param  string $language  Lnaguage of the script. Allowed: 'js', 'css'
     * @param  string $file  Name of the file (without extension). Include subfolder. Eg. 'folder/fileName'
     * @param  bool $defer  Defer script execution: true = load at the end of the file / false = load in header
     */
    public function loadScript(string $language, string $fileName, bool $defer = false): void
    {
        if($defer) {
            $this->footerScripts[$language][] = 'components' . DS . $fileName;
        } else {
            $this->headerScripts[$language][] = 'components' .DS . $fileName;
        }
    }

}