<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Event Command Invoker
 *
 * The event invoker will translate the command name to a onCommandName format and let the event publisher publish
 * to any registered event listeners.
 *
 * The 'immutable' config option defines if the context is clone before being passed to the event publisher or
 * it passed by reference instead. By default the context is cloned and changes to the event will not impact the
 * command context.
 *
 * @author  Johan Janssens <http://github.com/johanjanssens>
 * @package Koowa\Library\Command
 */
class KCommandInvokerEvent extends KCommandInvokerAbstract implements KCommandInvokerInterface
{
    /**
     * The command priority
     *
     * @var KEventPublisherInterface
     */
    private $__event_publisher;

    /**
     * Is the event immutable
     *
     * @var boolean
     */
    protected $_immutable;

    /**
     * Object constructor
     *
     * @param KObjectConfig $config Configuration options
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        if (is_null($config->event_publisher)) {
            throw new InvalidArgumentException('event_publisher [KEventPublisherInterface] config option is required');
        }

        //Set the event dispatcher
        $this->__event_publisher = $config->event_publisher;

        //Set the immutable state of the invoker
        $this->_immutable = $config->immutable;

    }

    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   KObjectConfig $config  An optional ObjectConfig object with configuration options
     * @return  void
     */
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'priority'        => self::PRIORITY_LOWEST,
            'event_publisher' => 'event.publisher',
            'immutable'       => true,
        ));

        parent::_initialize($config);
    }

    /**
     * Get the event publisher
     *
     * @throws UnexpectedValueException
     * @return  KEventPublisherInterface
     */
    public function getEventPublisher()
    {
        if(!$this->__event_publisher instanceof KEventPublisherInterface)
        {
            $this->__event_publisher = $this->getObject($this->__event_publisher);

            if(!$this->__event_publisher instanceof KEventPublisherInterface)
            {
                throw new UnexpectedValueException(
                    'EventPublisher: '.get_class($this->__event_publisher).' does not implement KEventPublisherInterface'
                );
            }
        }

        return $this->__event_publisher;
    }

    /**
     * Set the event publisher
     *
     * @param   KEventPublisherInterface  $publisher An event publisher object
     * @return  Object  The mixer object
     */
    public function setEventPublisher(KEventPublisherInterface $publisher)
    {
        $this->__event_publisher = $publisher;
        return $this;
    }

    /**
     * Command handler
     *
     * This functions returns void to prevent is from breaking the chain.
     *
     * @param   KCommandInterface $command The command
     * @return  void
     */
    public function executeCommand(KCommandInterface $command, $condition = null)
    {
        $type    = '';
        $package = '';
        $subject = '';

        if ($command->getSubject())
        {
            $identifier = $command->getSubject()->getIdentifier()->toArray();
            $package    = $identifier['package'];

            if ($identifier['path'])
            {
                $type    = array_shift($identifier['path']);
                $subject = $identifier['name'];
            }
            else $type = $identifier['name'];
        }

        $parts  = explode('.', $command->getName());
        $when   = array_shift($parts);               // Before or After
        $name   = KStringInflector::implode($parts); // Read Dispatch Select etc.

        // Create Specific and Generic event names
        $event_specific = 'on'.ucfirst($when).ucfirst($package).ucfirst($subject).ucfirst($type).$name;
        $event_generic  = 'on'.ucfirst($when).ucfirst($type).$name;

        // Clone the context
        if($this->_immutable) {
            $event = clone($command);
        } else {
            $event = $command;
        }

        // Create event object to check for propagation
        $event = $this->getEventPublisher()->publishEvent($event_specific, $command->getAttributes(), $command->getSubject());

        // Ensure event can be propagated and event name is different
        if ($event->canPropagate() && $event_specific != $event_generic)
        {
            $event->setName($event_generic);
            $this->getEventPublisher()->publishEvent($event);
        }
    }

    /*
     * Is the command context immutable
     *
     * @return bool
     */
    public function isImmutable()
    {
        return $this->_immutable;
    }
}