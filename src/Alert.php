<?php

namespace App;

class Alert
{

    private $name;
    private $class;
    private $content;

    /**
     * Constructor
     *
     * @param  string $content Text of the alert
     * @param  string $class Bootstrap alerts colors: 'primary', 'danger', 'info', etc. | Default: 'primary'
     * @param  mixed $name Allow to hide double alerts: if two alerts occures on a page with the same name, only the first will appear. If you want an alert not to compound with another, then you should choose a unique name for it.     * @return void
     */
    public function __construct(string $content, string $class = 'primary', $name = null)
    {
        $this->content = $content;
        $this->class = $class;
        $this->name = $name;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getClass(): ?string
    {
        return $this->class;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    /**
     * Find an alert inside the Alerts array based on the Name.
     *
     * @param  string $alertName Alert name to search for
     * @param  Alert[] $alerts Array of alerts to search into
     * @return bool True if the name is found. False otherwise.
     */
    public static function findByName(string $alertName, array $alerts): bool
    {
        $alertsNames = [];
        foreach ($alerts as $alert) {
            $alertsNames[] = $alert->getName();
        }
        if (in_array($alertName, $alertsNames)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     *Find an alert inside the Alerts array based on the Class.
     *
     * @param  string $alertClass Alert class to search for
     * @param  Alert[] $alerts Array of alerts to search into
     * @return bool True if the name is found. False otherwise.
     */
    public static function findByClass(string $alertClass, array $alerts): bool
    {
        $alertsClasses = [];
        foreach ($alerts as $alert) {
            $alertsClasses[] = $alert->getClass();
        }
        if (in_array($alertClass, $alertsClasses)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Return a HTML string of the current Alert instance
     *
     * @return string
     */
    public function show(): ?string
    {
        return '<div class="alert alert-' . $this->class . '">' . $this->content . '</div>';
    }

    /**
     * Return an HTML list of ALL alerts 
     * (or a simple HTML string if only one alert: better use $this->show() in this case)
     *
     * @param  Alert|Alert[] $alerts One instance of Alert or an array containing several instances of Alert
     * @return string HTML output 
     */
    public static function showAll($alerts): ?string
    {
        if (empty($alerts)) {
            return null;
        }
        // If only 1 alert (string or in array)
        if (is_string($alerts) || (is_array($alerts) && count($alerts) === 1)) {
            $alert = $alerts;
            if (is_array($alerts)) $alert = $alerts[0];
            return '<div class="alert alert-' . $alert->getClass() . '">' . $alert->getContent() . '</div>';
        }
        // If is array & array contains more than 1 alert
        elseif (is_array($alerts) && count($alerts) > 1) {
            $uniqueAlerts = self::removeDoubles($alerts); // remove alerts doubles
            $alertsByClass = self::orderByClass($uniqueAlerts);
            $alertsHtml = '';
            foreach ($alertsByClass as $class => $alerts) {
                $lines = '';
                if (count($alerts) === 1) {
                    $alertsHtml .= '<div class="alert alert-' . $class . '">' . $alerts[0]->getContent() . '</div>';
                } else {
                    foreach ($alerts as $alert) {
                        $lines .= '<li>' . $alert->getContent() . '</li>';
                    }
                    $alertsHtml .= '<div class="alert alert-' . $class . '">' . $lines . '</div>';
                }
            }
            return $alertsHtml;
        }
    }

    private static function removeDoubles($alerts): array
    {
        $uniqueAlerts = [];
        $alertsByClassAndName = self::orderByClassAndName($alerts);
        foreach ($alertsByClassAndName as $class => $alertsByName) {
            foreach ($alertsByName as $name => $alerts) {
                $uniqueAlerts[] = $alerts[0]; // We only keep one alerts to avoid
            }
        }
        return $uniqueAlerts;
    }

    private static function orderByClassAndName($alerts): array
    {

        $alertsbyClassAndName = [];
        $alertsbyClassTemp = self::orderByClass($alerts); // alerts ordered by class but not by name yet inside each class
        foreach ($alertsbyClassTemp as $class => $alerts) {
            $i = 1;
            foreach ($alerts as $alert) {
                if (empty($alert->getName())) { // auto attribute a name if the $name parameter is not defined
                    $alert->setName('no-name-' . $i);
                }
                $i++;
                $alertsbyClassAndName[$class][$alert->getName()][] = $alert;
            }
        }
        return $alertsbyClassAndName;
    }

    private static function orderByClass($alerts): array
    {
        $alertsByClass = [];
        foreach ($alerts as $alert) {
            $alertsByClass[$alert->getClass()][] = $alert;
        }
        return $alertsByClass;
    }
}
