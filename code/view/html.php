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
 * Html View
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Kodekit\Library\View
 */
class ViewHtml extends ViewTemplate
{
    /**
     * Initializes the config for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   ObjectConfig $config Configuration options
     * @return  void
     */
    protected function _initialize(ObjectConfig $config)
    {
        $config->append(array(
            'mimetype'          => 'text/html',
            'template_filters'  => array('form', 'include'),
        ));

        parent::_initialize($config);
    }

    /**
     * Force the route to be not fully qualified and escaped
     *
     * @param string|array  $route  The query string used to create the route
     * @param boolean       $fqr    If TRUE create a fully qualified route. Default FALSE.
     * @param boolean       $escape If TRUE escapes the route for xml compliance. Default TRUE.
     * @return  DispatcherRouterRoute The route
     */
    public function getRoute($route = '', $fqr = false, $escape = true)
    {
        return parent::getRoute($route, $fqr, $escape);
    }
}