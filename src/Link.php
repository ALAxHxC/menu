<?php
/**
 * Contains the Menu Link class.
 *
 * @author      Lavary
 * @author      Attila Fulop
 * @license     MIT
 * @since       2017-06-16
 *
 */

namespace Konekt\Menu;

class Link
{

    /** @var array  Path Information */
    protected $path = [];

    /** @var  string */
    protected $activeClass;

    /** @var string Explicit href for the link */
    protected $href;

    /** @var array  Link attributes */
    protected $attributes = [];

    /** @var bool   Flag for active state */
    public $isActive = false;

    /**
     * Class constructor
     *
     * @param  array $path
     * @param string $activeClass
     */
    public function __construct($path = [], $activeClass = 'active')
    {
        $this->path        = $path;
        $this->activeClass = $activeClass;
    }

    /**
     * Make the anchor active
     *
     * @return static
     */
    public function activate()
    {
        $this->attributes['class'] = Utils::addHtmlClass(array_get($this->attributes, 'class', ''), $this->activeClass);
        $this->isActive            = true;

        return $this;
    }

    /**
     * Set Anchor's href property
     *
     * @param $href
     *
     * @return static
     */
    public function href($href)
    {
        $this->href = $href;

        return $this;
    }

    /**
     * Return the URL for the link
     *
     * @return string|null
     */
    public function url()
    {
        if (!is_null($this->href)) {
            return $this->href;
        } elseif (isset($this->path['url'])) {
            return $this->getUrl();
        } elseif (isset($this->path['route'])) {
            return $this->getRoute();
        } elseif (isset($this->path['action'])) {
            return $this->getControllerAction();
        }

        return null;
    }

    /**
     * Add attributes to the link
     *
     * @param  mixed
     *
     * @return string|Link|array
     */
    public function attr()
    {
        $args = func_get_args();

        if (isset($args[0]) && is_array($args[0])) {
            $this->attributes = array_merge($this->attributes, $args[0]);

            return $this;
        } elseif (isset($args[0]) && isset($args[1])) {
            $this->attributes[$args[0]] = $args[1];

            return $this;
        } elseif (isset($args[0])) {
            return isset($this->attributes[$args[0]]) ? $this->attributes[$args[0]] : null;
        }

        return $this->attributes;
    }

    /**
     * Check for a method of the same name if the attribute doesn't exist.
     *
     * @param  string $property
     *
     * @return mixed
     */
    public function __get($property)
    {
        return $this->attr($property);
    }

    public function __set($property, $value)
    {
        return $this->attr($property, $value);
    }

    /**
     * Get the action for "url" option.
     *
     * @return string
     */
    protected function getUrl()
    {
        $url = $this->path['url'];

        $uri    = is_array($url) ? $url[0] : $url;
        $params = is_array($url) ? array_slice($url, 1) : null;

        if (Utils::isAbsoluteUrl($uri)) {
            return $uri;
        }

        return url($uri, $params);
    }

    /**
     * Get the url for a "route" option.
     *
     * @return string
     */
    protected function getRoute()
    {
        $route = $this->path['route'];
        if (is_array($route)) {
            return route($route[0], array_slice($route, 1));
        }

        return route($route);
    }

    /**
     * Get the url for an "action" option
     *
     * @return string
     */
    protected function getControllerAction()
    {
        $action = $this->path['action'];
        if (is_array($action)) {
            return action($action[0], array_slice($action, 1));
        }

        return action($action);
    }



}