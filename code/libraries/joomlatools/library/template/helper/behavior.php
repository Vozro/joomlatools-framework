<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Behavior Template Helper
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Template\Helper
 */
class KTemplateHelperBehavior extends KTemplateHelperAbstract
{
    /**
     * Array which holds a list of loaded Javascript libraries
     *
     * @type array
     */
    protected static $_loaded = array();

    /**
     * Marks the resource as loaded
     *
     * @param      $key
     * @param bool $value
     */
    public static function setLoaded($key, $value = true)
    {
        static::$_loaded[$key] = $value;
    }

    /**
     * Checks if the resource is loaded
     *
     * @param $key
     * @return bool
     */
    public static function isLoaded($key)
    {
        return !empty(static::$_loaded[$key]);
    }

    /**
     * Loads koowa essentials
     *
     * @param array|KObjectConfig $config
     * @return string
     */
    public function koowa($config = array())
    {
        $config = new KObjectConfigJson($config);
        $config->append(array(
            'debug' => false
        ));

        $html = $this->jquery();

        if (!static::isLoaded('koowa'))
        {
            $html .= $this->jquery();
            $html .= '<ktml:script src="assets://js/'.($config->debug ? 'build/' : 'min/').'koowa.js" />';

            static::setLoaded('koowa');
        }

        return $html;
    }

    /**
     * Loads Modernizr
     *
     * @param array|KObjectConfig $config
     * @return string
     */
    public function modernizr($config = array())
    {
        $config = new KObjectConfigJson($config);
        $config->append(array(
            'debug' => false
        ));

        $html = '';

        if (!static::isLoaded('modernizr'))
        {
            $html = '<ktml:script src="assets://js/'.($config->debug ? 'build/' : 'min/').'modernizr.js" />';

            static::setLoaded('modernizr');
        }

        return $html;
    }

    /**
     * Loads jQuery under a global variable called kQuery.
     *
     * If debug config property is set, an uncompressed version will be included.
     *
     * You can do window.jQuery = window.$ = window.kQuery; to use the default names
     *
     * @param array|KObjectConfig $config
     * @return string
     */
    public function jquery($config = array())
    {
        $config = new KObjectConfigJson($config);
        $config->append(array(
            'debug' => false
        ));

        $html = '';

        if (!static::isLoaded('jquery'))
        {
            $html .= '<ktml:script src="assets://js/'.($config->debug ? 'build/' : 'min/').'jquery.js" />';

            static::setLoaded('jquery');
        }

        return $html;
    }

    /**
     * Add Bootstrap JS and CSS a modal box
     *
     * @param array|KObjectConfig $config
     * @return string   The html output
     */
    public function bootstrap($config = array())
    {
        $config = new KObjectConfigJson($config);
        $config->append(array(
            'debug' => false,
            'css'   => true,
            'javascript' => false
        ));

        $html = '';

        if ($config->javascript && !static::isLoaded('bootstrap-javascript'))
        {
            $html .= $this->jquery($config);
            $html .= '<ktml:script src="assets://js/'.($config->debug ? '' : 'min/').'bootstrap.js" />';

            static::setLoaded('bootstrap-javascript');
        }

        if ($config->css && !static::isLoaded('bootstrap-css'))
        {
            $html .= '<ktml:style src="assets://css/bootstrap.css" />';

            static::setLoaded('bootstrap-css');
        }

        return $html;
    }

    /**
     * Render a modal box
     *
     * @param array|KObjectConfig $config
     * @return string   The html output
     */
    public function modal($config = array())
    {
        $config = new KObjectConfigJson($config);
        $config->append(array(
            'debug'    => false,
            'selector' => '.koowa-modal',
            'data'     => 'koowa-modal',
            'options_callback' => null,
            'options'  => array('type' => 'image')
        ));

        $html = '';

        if(!static::isLoaded('modal'))
        {
            $html .= $this->jquery();
            $html .= '<ktml:script src="assets://js/'.($config->debug ? 'build/' : 'min/').'jquery.magnific-popup.js" />';

            static::setLoaded('modal');
        }

        $options   = (string)$config->options;
        $signature = md5('modal-'.$config->selector.$config->options_callback.$options);

        if (!static::isLoaded($signature))
        {
            if ($config->options_callback) {
                $options = $config->options_callback.'('.$options.')';
            }

            $html .= "<script>
            kQuery(function($){
                $('$config->selector').each(function(idx, el) {
                    var el = $(el);
                    var data = el.data('$config->data');
                    var options = ".$options.";
                    if (data) {
                        $.extend(true, options, data);
                    }
                    el.magnificPopup(options);
                });
            });
            </script>";

            static::setLoaded($signature);
        }

        return $html;
    }

    /**
     * Renders an overlay
     *
     * @param array|KObjectConfig $config
     * @return string
     */
    public function overlay($config = array())
    {
        $config = new KObjectConfigJson($config);
        $config->append(array(
            'url'       => '',
            'options_callback' => null,
            'options'   => array(),
            'attribs'   => array(),
        ));

        $translator = $this->getObject('translator');

        $html = '';
        // Load the necessary files if they haven't yet been loaded
        if (!static::isLoaded('overlay'))
        {
            $html .= $this->koowa();
            $html .= '<ktml:script src="assets://js/koowa.overlay.js" />';

            $html .= '
            <style>
            .-koowa-overlay-status {
                float: right;
                background-color:#FFFFDD;
                padding: 5px;
            }
            </style>
            ';

            static::setLoaded('overlay');
        }

        $url = $this->getObject('lib:http.url', array('url' => $config->url));

        if(!isset($url->query['format'])) {
            $url->query['format'] = 'overlay';
        }

        $attribs = $this->buildAttributes($config->attribs);

        $id = 'overlay'.mt_rand();
        if($url->fragment)
        {
            //Allows multiple identical ids, legacy should be considered replaced with #$url->fragment instead
            $config->append(array(
                'options' => array(
                    'selector' => '[id='.$url->fragment.']'
                )
            ));
        }

        $config->options->url = (string) $url;

        $options   = (string)$config->options;

        if ($config->options_callback) {
            $options = $config->options_callback.'('.$options.')';
        }

        $html .= sprintf("<script>kQuery(function(){ new Koowa.Overlay('#%s', %s);});</script>", $id, $options);

        $html .= '<div class="-koowa-overlay" id="'.$id.'" '.$attribs.'><div class="-koowa-overlay-status">'.$translator->translate('Loading...').'</div></div>';
        return $html;
    }

    /**
     * Loads the Forms.Validator class and connects it to Koowa.Controller.Form
     *
     * @param array|KObjectConfig $config
     * @return string   The html output
     */
    public function validator($config = array())
    {
        $config = new KObjectConfigJson($config);
        $config->append(array(
            'debug' => false,
            'selector' => '.k-js-form-controller',
            'options_callback' => null,
            'options'  => array(
                'ignoreTitle' => true,
                'onsubmit'    => false // We run the validation ourselves
            )
        ));

        $html = '';

        if(!static::isLoaded('validator'))
        {
            $html .= $this->koowa();
            $html .= '<ktml:script src="assets://js/'.($config->debug ? 'build/' : 'min/').'jquery.validate.js" />';

            static::setLoaded('validator');
        }

        $options   = (string) $config->options;
        $signature = md5('validator-'.$config->selector.$config->options_callback.$options);

        if (!static::isLoaded($signature))
        {
            if ($config->options_callback) {
                $options = $config->options_callback.'('.$options.')';
            }

            $html .= "<script>
            kQuery(function($){
                $('$config->selector').on('koowa:validate', function(event){
                    if(!$(this).valid()) {
                        event.preventDefault();
                    }
                }).validate($options);
            });
            </script>";

            static::setLoaded($signature);
        }

        return $html;
    }

    /**
     * Loads the select2 behavior and attaches it to a specified element
     *
     * @see    http://ivaynberg.github.io/select2/select-2.1.html
     *
     * @param  array|KObjectConfig $config
     * @return string   The html output
     */
    public function select2($config = array())
    {
        $config = new KObjectConfigJson($config);
        $config->append(array(
            'cleanup' => false,
            'debug'   => false,
            'element' => '.select2-listbox',
            'options_callback' => null, // wraps the call to select2 options in JavaScript, can be used to add JS code
            'options' => array(
                'theme'   => 'bootstrap',
                'width' => 'resolve'
            )
        ));

        $html = '';

        if (!static::isLoaded('select2'))
        {
            $html .= $this->jquery();
            $html .= '<ktml:script src="assets://js/'.($config->debug ? 'build/' : 'min/').'koowa.select2.js" />';

            static::setLoaded('select2');
        }

        $options   = $config->options;
        $signature = md5('select2-'.$config->element.$config->options_callback.$options);

        if($config->element && !static::isLoaded($signature))
        {
            $options = (string) $options;

            if ($config->options_callback) {
                $options = $config->options_callback.'('.$options.')';
            }

            $html .= '<script>
            kQuery(function($){
                $("'.$config->element.'").select2('.$options.');
            });</script>';

            static::setLoaded($signature);
        }

        return $html;
    }

    /**
     * Loads the autocomplete behavior and attaches it to a specified element
     *
     * @param  array|KObjectConfig $config
     * @return string   The html output
     */
    public function autocomplete($config = array())
    {
        $config = new KObjectConfigJson($config);
        $config->append(array(
            'element'  => null,
            'options_callback' => null, // wraps the call to select2 options in JavaScript, can be used to add JS code
            'options'  => array(
                'minimumInputLength' => 2,
                'validate'      => false, //Toggle if the forms validation helper is loaded
                'queryVarName'  => 'search',
                'width'         => 'resolve',
                'model'		    => $config->model,
                'placeholder'   => $config->prompt,
                'allowClear'    => $config->deselect,
                'value'         => $config->value,
                'text'          => $config->text,
                'selected'      => $config->selected,
                'url'           => $config->url,
                'multiple'      => false
            )
        ))->append(array(
            'options' => array(
                'label' => $config->text
            )
        ));

        $html ='';

        if (!$config->options->url instanceof KHttpUrl) {
            $config->options->url = $this->getObject('lib:http.url', array('url' => $config->options->url));
        }

        $config->options->url->setQuery(array('fields' => $config->value.','.$config->text), true);

        $config->options->url = (string)$config->options->url;

        $options   = $config->options;
        $signature = md5('autocomplete-'.$config->element.$config->options_callback.$options);

        if($config->element && !static::isLoaded($signature))
        {
            $options = (string) $options;

            if ($config->options_callback) {
                $options = $config->options_callback.'('.$options.')';
            }

            $html .= $this->select2(array('element' => false));
            $html .= '<script>
            kQuery(function($){
                $("'.$config->element.'").koowaSelect2('.$options.');
            });</script>';

            static::setLoaded($signature);
        }

        return $html;
    }

    /**
     * Loads the Koowa customized jQtree behavior and renders a sidebar-nav list useful in split views
     *
     * @see    http://mbraak.github.io/jqTree/
     *
     * @note   If no 'element' option is passed, then only assets will be loaded.
     *
     * @param  array|KObjectConfig $config
     * @throws InvalidArgumentException
     * @return string    The html output
     */
    public function tree($config = array())
    {
        $config = new KObjectConfigJson($config);
        $config->append(array(
            'debug'   => false,
            'element' => '',
            'selected'  => '',
            'list'    => array()
        ))->append(array(
            'options_callback' => null,
            'options' => array(
                'selected' => $config->selected
            )
        ));

        $html = '';

        /**
         * Loading the assets, if not already loaded
         */
        if (!static::isLoaded('tree'))
        {
            $html .= $this->koowa();
            $html .= '<ktml:script src="assets://js/'.($config->debug ? 'build/' : 'min/').'koowa.tree.js" />';

            static::setLoaded('tree');
        }

        /**
         * Parse and validate list data, if any. And load the inline js that initiates the tree on specified html element
         */
        $signature = md5('tree-'.$config->element);
        if($config->element && !static::isLoaded($signature))
        {
            /**
             * If there's a list set, but no 'data' in the js options, parse it
             */
            if(isset($config->list) && !isset($config->options->data))
            {
                $data = array();
                foreach($config->list as $item)
                {
                    $parts = explode('/', $item->path);
                    array_pop($parts); // remove current id
                    $data[] = array(
                        'label'  => $item->title,
                        'id'     => (int)$item->id,
                        'level'  => (int)$item->level,
                        'path'   => $item->path,
                        'parent' => (int)array_pop($parts)
                    );
                }
                $config->options->append(array('data' => $data));
            }
            /**
             * Validate that the data is the right format
             */
            elseif(isset($config->options->data, $config->options->data[0]))
            {
                $data     = $config->options->data[0];
                $required = array('label', 'id', 'level', 'path', 'parent');
                foreach($required as $key)
                {
                    if(!isset($data[$key])) {
                        throw new InvalidArgumentException('Data must contain required param: '.$key);
                    }
                }
            }

            $options = (string) $config->options;

            if ($config->options_callback) {
                $options = $config->options_callback.'('.$options.')';
            }

            $html .= '<script>
            kQuery(function($){
                new Koowa.Tree('.json_encode($config->element).', '.$options.');
            });</script>';

            static::setLoaded($signature);
        }

        return $html;
    }

    /**
     * Render a tooltip
     *
     * @param array|KObjectConfig $config
     * @return string   *The html output
     */
    public function tooltip($config = array())
    {
        $config = new KObjectConfigJson($config);
        $config->append(array(
            'selector' => '.koowa-tooltip',
            'data'     => 'koowa-tooltip',
            'options_callback' => null,
            'options'  => array()
        ));

        $html = '';

        // Load Bootstrap with JS plugins.
        if(!static::isLoaded('tooltip'))
        {
            $html .= $this->bootstrap(array('css' => false, 'javascript' => true));

            static::setLoaded('tooltip');
        }

        $options = (string) $config->options;

        if ($config->options_callback) {
            $options = $config->options_callback.'('.$options.')';
        }

        $signature = md5('tooltip-'.$config->selector.$options);

        if(!static::isLoaded($signature))
        {
            $html .= "<script>
                kQuery(function($) {
                    $('$config->selector').each(function(idx, el) {
                        var el = $(el);
                        var data = el.data('$config->data');
                        var options = ".$options.";
                        if (data) {
                            $.extend(true, options, data);
                        }
                        el.tooltip(options);
                        });
                });
            </script>";

            static::setLoaded($signature);
        }

        return $html;
    }


    /**
     * Loads the calendar behavior and attaches it to a specified element
     *
     * @param array|KObjectConfig $config
     * @return string   The html output
     */
    public function calendar($config = array())
    {
        $config = new KObjectConfigJson($config);
        $config->append(array(
            'debug'   => false,
            'offset'  => 'UTC',
            'user_offset'    => $this->getObject('user')->getParameter('timezone'),
            'server_offset'  => date_default_timezone_get(),
            'offset_seconds' => 0,
            'value'	  => gmdate("M d Y H:i:s"),
            'name'    => '',
            'format'  => '%Y-%m-%d %H:%M:%S',
            'first_week_day' => 0,
            'attribs'        => array(
                'size'        => 25,
                'maxlength'   => 19,
                'placeholder' => ''
            )
        ))->append(array(
            'id'      => 'datepicker-'.$config->name,
            'options_callback' => null,
            'options' => array(
                'todayBtn' => 'linked',
                'todayHighlight' => true,
                'language' => 'en-GB',
                'autoclose' => true, //Same as singleClick in previous js plugin,
                'keyboardNavigation' => false, //To allow editing timestamps,
                //'orientation' => 'auto left', //popover arrow set to point at the datepicker icon,
                //'parentEl' => false //this feature breaks if a parent el is position: relative;
            )
        ));

        if ($config->offset)
        {
            if (strtoupper($config->offset) === 'SERVER_UTC') {
                $config->offset = $config->server_offset;
            }
            else if (strtoupper($config->offset) === 'USER_UTC') {
                $config->offset = $config->user_offset ?: $config->server_offset;
            }

            $timezone               = new DateTimeZone($config->offset);
            $config->offset_seconds = $timezone->getOffset(new DateTime());
        }

        if ($config->value && $config->value != '0000-00-00 00:00:00' && $config->value != '0000-00-00')
        {
            if (strtoupper($config->value) == 'NOW') {
                $config->value = strftime($config->format);
            }

            $date = new DateTime($config->value, new DateTimeZone('UTC'));

            $config->value = strftime($config->format, ((int)$date->format('U')) + $config->offset_seconds);
        } else {
            $config->value = '';
        }

        $attribs = $this->buildAttributes($config->attribs);
        $value   = $this->getTemplate()->escape($config->value);

        if ($config->attribs->readonly === 'readonly' || $config->attribs->disabled === 'disabled')
        {
            $html  = '<div>';
            $html .= '<input type="text" name="'.$config->name.'" id="'.$config->id.'" value="'.$value.'" '.$attribs.' />';
            $html .= '</div>';
        }
        else
        {
            $html = $this->_loadCalendarScripts($config);

            // Only display the triggers once for each control.
            if (!static::isLoaded('calendar-triggers'.$config->id))
            {
                $options = (string) $config->options;

                if ($config->options_callback) {
                    $options = $config->options_callback.'('.$options.')';
                }

                $html .= "<script>
                    kQuery(function($){
                        $('#".$config->id."').datepicker(".$options.");
                    });
                </script>";

                if ($config->offset_seconds)
                {
                    $html .= "<script>
                        kQuery(function($){
                            $('.k-js-form-controller').on('koowa:submit', function() {
                                var element = kQuery('#".$config->id."'),
                                    picker  = element.data('datepicker'),
                                    offset  = $config->offset_seconds;

                                if (picker && element.children('input').val()) {
                                    picker.setDate(new Date(picker.getDate().getTime() + (-1*offset*1000)));
                                }
                            });
                        });
                    </script>";
                }

                static::setLoaded('calendar-triggers'.$config->id);
            }

            $format = str_replace(
                array('%Y', '%y', '%m', '%d', '%H', '%M', '%S'),
                array('yyyy', 'yy', 'mm', 'dd', 'hh', 'ii', 'ss'),
                $config->format
            );

            $html .= '<div class="k-input-group k-js-datepicker     date     " data-date-format="'.$format.'" id="'.$config->id.'">';
            $html .= '<input class="k-form-control" type="text" name="'.$config->name.'" value="'.$value.'"  '.$attribs.' />';
            $html .= '<span class="k-input-group__button input-group-btn">';
            $html .= '<button type="button" class="k-button k-button--default     btn     ">';
            $html .= '<span class="k-icon-calendar" aria-hidden="true"></span>';
            $html .= '<span class="k-visually-hidden">calendar</span>';
            $html .= '</button>';
            $html .= '</span>';
            $html .= '</div>';
        }

        return $html;
    }

    /**
     * @param KObjectConfig $config
     * @return string
     */
    protected function _loadCalendarScripts(KObjectConfig $config)
    {
        $html = '';

        if (!static::isLoaded('calendar'))
        {
            if ($config->debug) {
                $html .= '<ktml:script src="assets://js/datepicker.js" />';
            }
            else $html .= '<ktml:script src="assets://js/min/koowa.datepicker.js" />';


            $locale = array(
                'days'  =>  array('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'),
                'daysShort' => array('Sun','Mon','Tue','Wed','Thu','Fri','Sat','Sun'),
                'daysMin' => array('Su','Mo','Tu','We','Th','Fr','Sa','Su'),
                'months' => array('January','February','March','April','May','June','July','August','September','October','November','December'),
                'monthsShort' => array('January_short','February_short','March_short','April_short','May_short','June_short','July_short','August_short','September_short','October_short','November_short','December_short')
            );

            $translator = $this->getObject('translator');

            foreach($locale as $key => $items){
                $locale[$key] = array_map(array($translator, 'translate'), $items);
            }
            $locale['today']     = $translator->translate('Today');
            $locale['clear']     = $translator->translate('Clear');
            $locale['weekStart'] = $config->first_week_day;

            $html .= '<script>
            (function($){
                $.fn.datepicker.dates['.json_encode($config->options->language).'] = '.json_encode($locale).';
            }(kQuery));
            </script>';

            static::setLoaded('calendar');
        }

        return $html;

    }

    /**
     * Returns an array of month names (short and long) translated to the site language
     *
     * JavaScript Date object does not have a public API to do this.
     *
     * @param array $config
     * @return string
     */
    public function local_dates($config = array())
    {
        $html   = '';
        $months = array();

        $translator = $this->getObject('translator');

        for ($i = 1; $i < 13; $i++)
        {
            $month  = strtoupper(date('F', mktime(0, 0, 0, $i, 1, 2000)));
            $long  = $translator->translate($month);
            $short = $translator->translate($month.'_SHORT');

            if (strpos($short, '_SHORT') !== false) {
                $short = $long;
            }

            $months[$i] = array('long' => $long, 'short' => $short);
        }

        if (!static::isLoaded('local_dates'))
        {
            $html = sprintf("
            <script>
            if(!Koowa) {
                var Koowa = {};
            }

            if (!Koowa.Date) {
                Koowa.Date = {};
            }

            Koowa.Date.local_month_names = %s;
            Koowa.Date.getMonthName = function(month, short) {
                month = parseInt(month, 10);

                if (month < 1 || month > 12) {
                    throw 'Month index should be between 1 and 12';
                }

                return Koowa.Date.local_month_names[month][short ? 'short' : 'long'];
            };
            </script>
            ", json_encode($months));

            static::setLoaded('local_dates');
        }

        return $html;
    }
}
