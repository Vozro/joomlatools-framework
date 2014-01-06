<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Event Subscriber
 *
 * An EventSubscriber knows himself what events he is interested in. If an EventSubscriber is added to an
 * EventDispatcherInterface, the dispatcher invokes {@link getListeners} and registers the subscriber
 * as a listener for all returned events.
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Event
 */
abstract class KEventSubscriberAbstract extends KObject implements KEventSubscriberInterface
{
    /**
     * List of event listeners
     *
     * @var array
     */
    private $__listeners;

    /**
     * The event priority
     *
     * @var int
     */
    protected $_priority;

    /**
     * Constructor.
     *
     * @param KObjectConfig $config 	An optional ObjectConfig object with configuration options.
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->_priority = $config->priority;
    }

    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param 	KObjectConfig $config 	An optional ObjectConfig object with configuration options.
     * @return 	void
     */
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'priority' => KEvent::PRIORITY_NORMAL
        ));

        parent::_initialize($config);
    }

    /**
     * Get the priority of the handler
     *
     * @return	integer The event priority
     */
    public function getPriority()
    {
        return $this->_priority;
    }

    /**
     * Get a list of subscribed events
     *
     * Event listeners always start with 'on' and need to be public methods.
     *
     * @return array An array of public methods
     */
    public function getListeners()
    {
        if(!$this->__listeners)
        {
            $listeners = array();

            //Get all the public methods
            $reflection = new \ReflectionClass($this);
            foreach ($reflection->getMethods(\ReflectionMethod::IS_PUBLIC) as $method)
            {
                if(substr($method->name, 0, 2) == 'on')
                {
                    $listeners[$method->name] = array(
                        'listener' => array($this, $method->name),
                        'priority' => $this->getPriority()
                    );
                }
            }

            $this->__listeners = $listeners;
        }

        return $this->__listeners;
    }
}