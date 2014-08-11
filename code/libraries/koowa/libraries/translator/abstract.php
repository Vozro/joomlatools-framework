<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright	Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Abstract Translator
 *
 * @author  Arunas Mazeika <https://github.com/arunasmazeika>
 * @author  Ercan Ozkaya <https://github.com/ercanozkaya>
 * @package Koowa\Library\Translator
 */
abstract class KTranslatorAbstract extends KObject implements KTranslatorInterface
{
    /**
     * Locale
     *
     * @var string
     */
    protected $_locale;

    /**
     * Locale Fallback
     *
     * @var string
     */
    protected $_locale_fallback;

    /**
     * The translator catalogue.
     *
     * @var KTranslatorCatalogueInterface
     */
    protected $_catalogue;

    /**
     * List of file paths that have been loaded.
     *
     * @var array
     */
    protected $_loaded;

    /**
     * Constructor.
     *
     * @param KObjectConfig $config Configuration options
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->setLocale($config->locale);

        $this->_loaded          = array();
        $this->_catalogue       = $config->catalogue;
        $this->_locale_fallback = $config->locale_fallback;
    }

    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   KObjectConfig $config Configuration options.
     * @return  void
     */
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'locale'          => 'en-GB',
            'locale_fallback' => 'en-GB',
            'cache_enabled'   => false,
        ))->append(array(
            'catalogue' =>  $config->cache_enabled ? 'cache' : 'default'
        ));

        parent::_initialize($config);
    }

    /**
     * Translates a string and handles parameter replacements
     *
     * Parameters are wrapped in curly braces. So {foo} would be replaced with bar given that $parameters['foo'] = 'bar'
     *
     * @param string $string String to translate
     * @param array  $parameters An array of parameters
     * @return string Translated string
     */
    public function translate($string, array $parameters = array())
    {
        $translation = '';

        if(!empty($string))
        {
            $catalogue   = $this->getCatalogue();
            $translation = $catalogue->has($string) ? $catalogue->get($string) : $string;

            if (count($parameters)) {
                $translation = $this->_replaceParameters($translation, $parameters);
            }
        }

        return $translation;
    }

    /**
     * Translates a string based on the number parameter passed
     *
     * @param array   $strings Strings to choose from
     * @param integer $number The number of items
     * @param array   $parameters An array of parameters
     * @throws \InvalidArgumentException
     * @return string Translated string
     */
    public function choose(array $strings, $number, array $parameters = array())
    {
        if (count($strings) < 2) {
            throw new InvalidArgumentException('Choose method requires at least 2 strings to choose from');
        }

        $choice = KTranslatorInflector::getPluralPosition($number, $this->getLocale());

        if ($choice !== 0)
        {
            while ($choice > 0)
            {
                $candidate = $strings[1] . ($choice === 1 ? '' : ' ' . $choice);

                if ($this->getCatalogue()->has($candidate))
                {
                    $string = $candidate;
                    break;
                }

                $choice--;
            }
        }
        else  $string = $strings[0];

        return $this->translate(isset($string) ? $string : $strings[1], $parameters);
    }

    /**
     * Loads translations from a file.
     *
     * @param string $file     The path to the file containing translations.
     * @param bool   $override Tells if previous loaded translations should be overridden
     * @return bool True if translations were loaded, false otherwise
     */
    public function load($file, $override = false)
    {
        $result = false;

        if (!$this->isLoaded($file))
        {
            try
            {
                $translations = $this->getObject('object.config.factory')->fromFile($file)->toArray();

                if(is_array($translations))
                {
                    if($result = $this->getCatalogue()->add($translations, $override))
                    {
                        //Mark the file as loaded to prevent re-loading
                        $this->_loaded[$file] = true;
                        return true;
                    }
                }
            }
            catch (Exception $e) {}
        }
        //Translation file has already been loaded
        else $result = true;

        return $result;
    }

    /**
     * Translations finder.
     *
     * Looks for translation files on the provided path.
     *
     * @param string $path      The path to look for translations.
     * @param string $extension The file extension to look for.
     * @return string|false The translation filename. False in no translations file is found.
     */
    public function find($path, $extension = 'ini')
    {
        $locale          = $this->getLocale();
        $locale_fallback = $this->getLocaleFallback();

        $locales = array($locale);

        if ($locale_fallback && ($locale !== $locale_fallback)) {
            $locales[] = $locale_fallback;
        }

        $file = null;

        foreach ($locales as $locale)
        {
            $candidate = $path . $locale . '.'.$extension;

            if (file_exists($candidate))
            {
                $file = $candidate;
                break;
            }
        }

        return $file;
    }

    /**
     * Sets the locale
     *
     * @param string $locale
     * @return KTranslatorAbstract
     */
    public function setLocale($locale)
    {
        $this->_locale = $locale;

        //Set locale information for date and time formatting
        setlocale(LC_TIME, $locale);

        //Sets the default runtime locale
        if (version_compare(phpversion(), '5.3.0', '>=')) {
            locale_set_default($locale);
        }

        return $this;
    }

    /**
     * Gets the locale
     *
     * @return string|null
     */
    public function getLocale()
    {
        return $this->_locale;
    }

    /**
     * Set the fallback locale
     *
     * @param string $locale The fallback locale
     * @return KTranslatorAbstract
     */
    public function setLocaleFallback($locale)
    {
        $this->_fallback_locale = $locale;
        return $this;
    }

    /**
     * Set the fallback locale
     *
     * @return string
     */
    public function getLocaleFallback()
    {
        return $this->_locale_fallback;
    }

    /**
     * Get a catalogue
     *
     * @throws	UnexpectedValueException	If the catalogue doesn't implement the TranslatorCatalogueInterface
     * @return KTranslatorCatalogueInterface The translator catalogue.
     */
    public function getCatalogue()
    {
        if (!$this->_catalogue instanceof KTranslatorCatalogueInterface)
        {
            if(!($this->_catalogue instanceof KObjectIdentifier)) {
                $this->setCatalogue($this->_catalogue);
            }

            $this->_catalogue = $this->getObject($this->_catalogue);
        }

        if(!$this->_catalogue instanceof KTranslatorCatalogueInterface)
        {
            throw new UnexpectedValueException(
                'Catalogue: '.get_class($this->_catalogue).' does not implement TranslatorCatalogueInterface'
            );
        }

        return $this->_catalogue;
    }

    /**
     * Set a catalogue
     *
     * @param	mixed	$catalogue An object that implements KObjectInterface, KObjectIdentifier object
     * 					           or valid identifier string
     * @return KTranslatorAbstract
     */
    public function setCatalogue($catalogue)
    {
        if(!($catalogue instanceof KModelInterface))
        {
            if(is_string($catalogue) && strpos($catalogue, '.') === false )
            {
                $identifier			= $this->getIdentifier()->toArray();
                $identifier['path']	= array('translator', 'catalogue');
                $identifier['name'] = $catalogue;

                $identifier = $this->getIdentifier($identifier);
            }
            else $identifier = $this->getIdentifier($catalogue);

            $catalogue = $identifier;
        }

        $this->_catalogue = $catalogue;

        return $this;
    }

    /**
     * Checks if the translator can translate a string
     *
     * @param $string String to check
     * @return bool
     */
    public function isTranslatable($string)
    {
        return $this->getCatalogue()->has($string);
    }

    /**
     * Tells if translations from a given file are already loaded.
     *
     * @param mixed $file The file to check
     * @return bool True if loaded, false otherwise.
     */
    public function isLoaded($file)
    {
        return isset($this->_loaded[$file]);
    }

    /**
     * Handles parameter replacements
     *
     * @param string $string String
     * @param array  $parameters An array of parameters
     * @return string String after replacing the parameters
     */
    protected function _replaceParameters($string, array $parameters = array())
    {
        $keys       = array_map(array($this, '_replaceParam'), array_keys($parameters));
        $parameters = array_combine($keys, $parameters);

        return strtr($string, $parameters);
    }

    /**
     * Adds curly braces around a param to make strtr work in replaceParameters method
     *
     * @param string $name The parameter name
     * @return string
     */
    protected function _replaceParam($name)
    {
        return '{'.$name.'}';
    }
}