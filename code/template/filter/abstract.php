<?php
/**
 * Kodekit - http://timble.net/kodekit
 *
 * @copyright   Copyright (C) 2007 - 2016 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     MPL v2.0 <https://www.mozilla.org/en-US/MPL/2.0>
 * @link        https://github.com/timble/kodekit for the canonical source repository
 */

namespace Kodekit\Library;

/**
 * Abstract Template Filter
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Kodekit\Library\Template\Filter
 */
abstract class TemplateFilterAbstract extends Object implements TemplateFilterInterface
{
    /**
     * The filter priority
     *
     * @var integer
     */
    protected $_priority;

    /**
     * Template object
     *
     * @var TemplateInterface
     */
    private $__template;

    /**
     * Constructor.
     *
     * @param ObjectConfig $config An optional ObjectConfig object with configuration options
     */
    public function __construct(ObjectConfig $config)
    {
        parent::__construct($config);

        $this->__template = $config->template;
        $this->_priority = $config->priority;
    }

    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param  ObjectConfig $config An optional ObjectConfig object with configuration options
     */
    protected function _initialize(ObjectConfig $config)
    {
        $config->append(array(
            'template' => 'default',
            'priority' => self::PRIORITY_NORMAL
        ));

        parent::_initialize($config);
    }

    /**
     * Get the priority of a behavior
     *
     * @return  integer The command priority
     */
    public function getPriority()
    {
        return $this->_priority;
    }

    /**
     * Gets the template object
     *
     * @return  TemplateInterface	The template object
     */
    public function getTemplate()
    {
        if(!$this->__template instanceof TemplateInterface)
        {
            if(empty($this->__template) || (is_string($this->__template) && strpos($this->__template, '.') === false) )
            {
                $identifier         = $this->getIdentifier()->toArray();
                $identifier['path'] = array('template');
                $identifier['name'] = $this->__template;
            }
            else $identifier = $this->getIdentifier($this->__template);

            $this->__template = $this->getObject($identifier);

            if(!$this->__template instanceof TemplateInterface)
            {
                throw new \UnexpectedValueException(
                    'Template: '.get_class($this->__template).' does not implement TemplateInterface'
                );
            }
        }

        return $this->__template;
    }

    /**
     * Method to extract name/value pairs out of a string with xml style attributes
     *
     * @param   string  $string String containing xml style attributes
     * @return  array   name/value pairs for the attributes
     */
    public function parseAttributes($string)
    {
        $result = array();

        if (!empty($string))
        {
            $attr = array();

            //Find name/value attributes
            if(preg_match_all('/(?<name>[\w:-]+)[\s]?=[\s]?"(?<value>[^"]*)"/i', $string, $attr))
            {
                for($i = 0; $i < count($attr['name']); $i++) {
                    $result[$attr['name'][$i]] = trim($attr['value'][$i]);
                }
            }
        }

        return $result;
    }

    /**
     * Method to build a string with xml style attributes from  an array of key/value pairs
     *
     * @param   mixed   $array The array of Key/Value pairs for the attributes
     * @return  string  String containing xml style attributes
     */
    public function buildAttributes($array)
    {
        $output = array();

        if ($array instanceof ObjectConfig) {
            $array = ObjectConfig::unbox($array);
        }

        if (is_array($array))
        {
            foreach ($array as $key => $item)
            {
                if(is_array($item))
                {
                    if(empty($item)) {
                        continue;
                    }

                    $item = implode(' ', $item);
                }

                if (is_bool($item))
                {
                    if ($item === false) continue;
                    $item = $key;
                }

                $output[] = $key . '="' . str_replace('"', '&quot;', $item) . '"';
            }
        }

        return implode(' ', $output);
    }
}