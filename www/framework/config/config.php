<?php
/**
 * @file    framework/config/config.php
 *
 * depage config module
 *
 *
 * copyright (c) 2002-2009 Frank Hellenkamp [jonas@depagecms.net]
 *
 * @author    Frank Hellenkamp [jonas@depagecms.net]
 */

class config implements Iterator {
    protected $data = array();

    // {{{ constructor
    /**
     * instatiates config class
     *
     * @param   $options (array) named options for base class
     *
     * @return  null
     */
    public function __construct($values = array(), $defaults = array()) {
        $this->setConfig($values);
    }
    // }}}
    // {{{ readConfig
    /**
     * reads configuration from a file
     *
     * @param   $options (array) named options for base class
     *
     * @return  null
     */
    public function readConfig($configFile) {
        $values = include $configFile;
        $depage_base = "";

        $urls = array_keys($values);
        
        // sort that shorter urls with same beginning are tested first for a match
        sort($urls);

        if (!isset($_SERVER['HTTP_HOST'])) {
            $_SERVER['HTTP_HOST'] = "";
            $_SERVER['REQUEST_URI'] = "";
        }

        // test url against settings
        $acturl = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        foreach ($urls as $url) {
            $pattern = "/(" . str_replace(array(".", "?", "*", "/"), array("\.", "(.)", "(.*)", "\/"), $url) . ")/";
            if (preg_match($pattern, $acturl, $matches)) {
                // url fits into pattern

                if (isset($values[$url]['base']) && $values[$url]['base'] == "inherit") {
                    // don't set the base when it is set to "inherit"
                } else {
                    $depage_base = $matches[0];
                }

                $this->setConfig($values[$url]);
            }
        }

        // set protocol
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != "off") {
            $protocol = "https://";
        } else {
            $protocol = "http://";
        }

        // set base-url
        if (!isset($depage_base[0])) {
            define("DEPAGE_BASE", "");
        } else if ($depage_base[0] != "*") {
            define("DEPAGE_BASE", $protocol . $depage_base);
        } else {
            define("DEPAGE_BASE", $protocol . $_SERVER['HTTP_HOST']);
        }
    }
    // }}}
    // {{{ setConfig
    /**
     * sets configuration options as array
     *
     * @param   $options (array) named options for base class
     *
     * @return  null
     */
    public function setConfig($values) {
        if (count($values) > 0) {
            foreach ($values as $key => $value) {
                if (is_array($value)) {
                    $this->data[$key] = new self($value);
                } else {
                    $this->data[$key] = $value;
                }
            }
        }
    }
    // }}}
    // {{{ toArray
    /**
     * returns options as array
     *
     * @return  options as array
     */
    public function toArray() {
        $data = array();

        foreach ($this->data as $key => $value) {
            if (is_object($value)) {
                $data[$key] = $value->toArray();
            } else {
                $data[$key] = $value;
            }
        }

        return $data;
    }
    // }}}
    // {{{ getFromDefaults
    /**
     * returns options based on defaults as array
     *
     * @param $defaults (array) default options from class
     *
     * @return  options as array
     */
    public function getFromDefaults($defaults) {
        $data = array();

        if (count($defaults) > 0) {
            foreach ($defaults as $key => $value) {
                if (isset($this->data[$key]) && !is_null($this->data[$key])) {
                    $data[$key] = $this->data[$key];
                } else {
                    $data[$key] = $value;
                }
                if (is_array($data[$key])) {
                    $data[$key] = (object) $data[$key];
                }
            }
        }

        return (object) $data;
    }
    // }}}
    // {{{ getDefaultsFromClass
    /**
     * returns options based on defaults as array
     *
     * @param $object (object) object to get defaults from
     *
     * @return  options as object
     */
    public function getDefaultsFromClass($object) {
        $data = array();
        $defaults = array();

        $class = get_class($object);
        while ($class) {
            // go through class hierarchy for defaults and merge with parent's defaults
            $class_vars = get_class_vars($class);
            $defaults = array_merge($class_vars['defaults'], $defaults);

            $class = get_parent_class($class);
        }

        return $this->getFromDefaults($defaults);
    }
    // }}}
    
    // {{{ __get
    /**
     * gets a value from configuration
     *
     * @param   $name (string) name of option
     *
     * @return  null
     */
    public function __get($name) {
        if (array_key_exists($name, $this->data)) {
            if (is_array($this->data[$name])) {
                return "sub $name";
            } else {
                return $this->data[$name];
            }
        }
    }
    // }}}
    // {{{ __set
    /**
     * gets a value from configuration
     *
     * @param   $name (string) name of option
     *
     * @return  null
     */
    public function __set($name, $value) {
        // make readonly
    }
    // }}}
    // {{{ __isset
    /**
     * checks, if value exists
     *
     * @param   $name (string) name of option
     *
     * @return  null
     */
    public function __isset($name) {
        return isset($this->data[$name]);
    }
    // }}}
     
    // {{{ rewind()
    public function rewind() {
        reset($this->data);
    }
    // }}}
    // {{{ current()
    public function current() {
        return current($this->data);
    }
    // }}}
    // {{{ key()
    public function key() {
        return key($this->data);
    }
    // }}}
    // {{{ next()
    public function next() {
        return next($this->data);
    }
    // }}}
    // {{{ valid()
    public function valid() {
        return $this->current() !== false;
    }
    // }}}
}

/* vim:set ft=php fenc=UTF-8 sw=4 sts=4 fdm=marker et : */
