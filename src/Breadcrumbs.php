<?php

/**
 * This file is part of the Phalcon Breadcrumbs.
 *
 * (c) Serghei Iakovlev <serghei@phalcon.io>
 *
 * For the full copyright and license information, please view the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Phalcon;

use Phalcon\Breadcrumbs\Exception\InvalidArgumentException;
use Phalcon\Breadcrumbs\Exception\OutOfBoundsException;
use Phalcon\Breadcrumbs\Exception\UnderflowException;
use Phalcon\Di\Injectable;
use Phalcon\Events\ManagerInterface as EventsManagerInterface;
use Phalcon\Logger\AdapterInterface as LegacyLoggerInterface;
use Phalcon\Translate\Adapter\AdapterInterface as TranslateInterface;
use Phalcon\Translate\AdapterInterface as LegacyTranslateInterface;
use Psr\Log\LoggerInterface as PsrLoggerInterface;

/**
 * \Phalcon\Breadcrumbs
 *
 * Handles the breadcrumbs for the application.
 *
 * @package Phalcon
 */
class Breadcrumbs extends Injectable
{
    /**
     * Keeps all the breadcrumbs
     * @var array
     */
    protected $elements = [];

    /**
     * Internal logger
     * @var null|LegacyLoggerInterface|PsrLoggerInterface
     */
    protected $logger;

    /**
     * Events Manager
     * @var null|EventsManagerInterface
     */
    protected $eventsManager;

    /**
     * Crumb separator
     * @var string
     */
    protected $separator = ' / ';

    /**
     * Array holding output template
     * @var array
     */
    protected $template = [
        'linked'     => '<li><a href="{{link}}">{{icon}}{{label}}</a></li>',
        'not-linked' => '<li class="active">{{icon}}{{label}}</li>',
        'icon'       => '<i class="fa fa-dashboard"></i>'
    ];

    /**
     * Internal translate adapter
     * @var null|TranslateInterface|LegacyTranslateInterface
     */
    protected $translate;

    /**
     * Use implicit flush?
     * @var bool
     */
    protected $implicitFlush = true;

    /**
     * Last element not link
     * @var bool
     */
    protected $lastNotLinked = false;

    /**
     * Count null link
     * @var integer
     */
    protected $countNull = 0;

    /**
     * Breadcrumbs constructor.
     */
    public function __construct()
    {
        if ($this->getDI()->has('logger')) {
            $logger = $this->getDI()->getShared('logger');
            if ($logger instanceof LegacyLoggerInterface || $logger instanceof PsrLoggerInterface) {
                $this->logger = $logger;
            }
        }

        if ($this->getDI()->has('translate')) {
            $translate = $this->getDI()->getShared('translate');
            if ($translate instanceof TranslateInterface || $translate instanceof LegacyTranslateInterface) {
                $this->translate = $translate;
            }
        }

        if ($this->getDI()->has('eventsManager')) {
            $eventsManager = $this->getDI()->getShared('eventsManager');
            if ($eventsManager instanceof EventsManagerInterface) {
                $this->eventsManager = $eventsManager;
            }
        }
    }

    /**
     * Gets internal translate adapter.
     *
     * @return null|TranslateInterface|LegacyTranslateInterface
     */
    public function getTranslateAdapter()
    {
        return $this->translate;
    }

    /**
     * Sets internal translate adapter.
     *
     * @param LegacyTranslateInterface|TranslateInterface $translate Translate adapter
     * @return $this
     */
    public function setTranslateAdapter($translate)
    {
        if (is_object($translate)) {
            if ($translate instanceof TranslateInterface || $translate instanceof LegacyTranslateInterface) {
                $this->translate = $translate;
            }

            return $this;
        }

        throw new InvalidArgumentException(
            sprintf(
                'Invalid translate instance. Expected %s or %s, got %s',
                LegacyTranslateInterface::class,
                TranslateInterface::class,
                is_object($translate) ? get_class($translate) : gettype($translate)
            )
        );
    }

    /**
     * Gets internal logger.
     *
     * @return null|LegacyLoggerInterface|PsrLoggerInterface
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * Sets internal logger.
     *
     * @param LegacyLoggerInterface|PsrLoggerInterface $logger
     * @return $this
     */
    public function setLogger($logger)
    {
        if (is_object($logger)) {
            if ($logger instanceof LegacyLoggerInterface || $logger instanceof PsrLoggerInterface) {
                $this->logger = $logger;
            }

            return $this;
        }

        throw new InvalidArgumentException(
            sprintf(
                'Invalid logger instance. Expected %s or %s, got %s',
                LegacyLoggerInterface::class,
                PsrLoggerInterface::class,
                is_object($logger) ? get_class($logger) : gettype($logger)
            )
        );
    }

    /**
     * Gets crumb separator.
     *
     * @return string
     */
    public function getSeparator()
    {
        return $this->separator;
    }

    /**
     * Sets crumb separator.
     *
     * <code>
     * // Set crumb separator
     * $breadcrumbs->setSeparator(' &raquo; ');
     *
     * // Remove crumb separator
     * $breadcrumbs->setSeparator('');
     * </code>
     *
     * @param string $separator Separator
     * @return $this
     */
    public function setSeparator($separator)
    {
        try {
            if (!is_string($separator)) {
                $type = gettype($separator);
                throw new InvalidArgumentException(
                    "Expected value of the separator to be string type, {$type} given."
                );
            }

            $this->separator = $separator;
        } catch (\Exception $e) {
            $this->log($e);
        }

        return $this;
    }

    /**
     * Sets whether the output must be implicitly flushed to the output or returned as string.
     *
     * <code>
     * // Enable implicit flush
     * $breadcrumbs->setImplicitFlush(true);
     *
     * // Disable implicit flush
     * $breadcrumbs->setImplicitFlush(true);
     * </code>
     *
     * @param bool $implicitFlush Implicit flush mode
     * @return $this
     */
    public function setImplicitFlush($implicitFlush)
    {
        $this->implicitFlush = (bool) $implicitFlush;

        return $this;
    }

    /**
     * Sets rendering template.
     *
     * Events:
     * * breadcrumbs:beforeSetTemplate
     * * breadcrumbs:afterSetTemplate
     *
     * <code>
     * $this->breadcrumbs->setTemplate(
     *     '<li><a href="{{link}}">{{icon}}{{label}}</a></li>', // linked
     *     '<li class="active">{{icon}}{{label}}</li>',         // not linked
     *     '<i class="fa fa-dashboard"></i>'                    // first icon
     * );
     * </code>
     *
     * @param string $linked    Linked template
     * @param string $notLinked Not-linked template
     * @param string $icon      Icon template
     * @return $this
     */
    public function setTemplate($linked, $notLinked, $icon)
    {
        if ($this->eventsManager) {
            $this->eventsManager->fire('breadcrumbs:beforeSetTemplate', $this, [$linked, $notLinked, $icon]);
        }

        try {
            if (!is_string($linked)) {
                $type = gettype($linked);
                throw new InvalidArgumentException("Expected value of the first argument to be string, {$type} given.");
            }

            if (!is_string($notLinked)) {
                $type = gettype($notLinked);
                throw new InvalidArgumentException(
                    "Expected value of the second argument to be string, {$type} given."
                );
            }

            if (!is_string($icon)) {
                $type = gettype($notLinked);
                throw new InvalidArgumentException("Expected value of the third argument to be string, {$type} given.");
            }

            $this->template = [
                'linked'     => $linked,
                'not-linked' => $notLinked,
                'icon'       => $icon
            ];

            if ($this->eventsManager) {
                $this->eventsManager->fire('breadcrumbs:afterSetTemplate', $this, [$linked, $notLinked, $icon]);
            }
        } catch (\Exception $e) {
            $this->log($e);
        }

        return $this;
    }

    /**
     * Sets last elements not link.
     * @param bool $linked
     */
    public function setLastNotLinked($linked)
    {
        $this->lastNotLinked = $linked;
    }

    /**
     * Adds a new crumb.
     *
     * Events:
     * * breadcrumbs:beforeAdd
     * * breadcrumbs:afterAdd
     *
     * <code>
     * // Adding a crumb with a link
     * $breadcrumbs->add('Home', '/');
     *
     * // Adding a crumb without a link (normally the last one)
     * $breadcrumbs->add('User', null, ['linked' => false]);
     * </code>
     *
     * @param string $link The link that will be used
     * @param string $label Text displayed in the breadcrumb trail
     * @param array  $data The crumb data [Optional]
     * @return $this
     */
    public function add($label, $link = null, array $data = [])
    {
        if ($this->eventsManager) {
            $this->eventsManager->fire('breadcrumbs:beforeAdd', $this, [$label, $link, $data]);
        }

        try {
            if (!is_string($link) && $link !== null) {
                $type = gettype($link);
                throw new InvalidArgumentException(
                    "Expected value of the second argument to be either string or null type, {$type} given."
                );
            }

            if (!is_string($label)) {
                $type = gettype($label);
                throw new InvalidArgumentException(
                    "Expected value of the third argument to be string type, {$type} given."
                );
            }

            $linked = true;
            if (isset($data['linked'])) {
                $linked = (bool) $data['linked'];
            }

            $id = $link;
            if ($id === null) {
                $id = ':null'.$this->countNull.':';
                $this->countNull++;
            }

            $this->elements[$id] = [
                'label'  => $label,
                'link'   => (string) $link,
                'linked' => $linked,
            ];
        } catch (\Exception $e) {
            $this->log($e);
        }

        if ($this->eventsManager) {
            $this->eventsManager->fire('breadcrumbs:afterAdd', $this, [$label, $link, $data]);
        }

        return $this;
    }

    /**
     * Renders and outputs breadcrumbs based on previously set template.
     *
     * Events:
     * * breadcrumbs:beforeOutput
     * * breadcrumbs:afterOutput
     * * breadcrumbs:beforeTranslate
     * * breadcrumbs:afterTranslate
     *
     * <code>
     * // Php Engine
     * $this->breadcrumbs->output();
     *
     * // Volt Engine
     * {{ breadcrumbs.output() }};
     * </code>
     *
     * @return void|string
     */
    public function output()
    {
        if ($this->eventsManager) {
            $this->eventsManager->fire('breadcrumbs:beforeOutput', $this);
        }

        if (empty($this->elements)) {
            if (true === $this->implicitFlush) {
                echo '';
                if ($this->eventsManager) {
                    $this->eventsManager->fire('breadcrumbs:afterOutput', $this);
                }
            } else {
                return '';
            }
        }

        // We create the message with implicit flush or other
        $content = '';

        $i = 0;
        foreach ($this->elements as $key => $crumb) {
            $i++;
            $label = $crumb['label'];
            if ($this->translate) {
                if ($this->eventsManager) {
                    $this->eventsManager->fire('breadcrumbs:beforeTranslate', $this);
                }

                $label = $this->translate->query($label);

                if ($this->eventsManager) {
                    $this->eventsManager->fire('breadcrumbs:afterTranslate', $this);
                }
            }

            if (true === $this->lastNotLinked && end($this->elements) === $crumb) {
                $crumb['linked'] = null;
            }

            if ($crumb['linked']) {
                $htmlCrumb = str_replace(
                    ['{{link}}', '{{label}}'],
                    [$crumb['link'], $label],
                    $this->template['linked']
                );
            } else {
                $htmlCrumb = str_replace('{{label}}', $label, $this->template['not-linked']);
            }

            if (1 === $i) {
                $htmlCrumb = str_replace('{{icon}}', $this->template['icon'], $htmlCrumb);
            } else {
                $htmlCrumb = str_replace('{{icon}}', '', $htmlCrumb);
            }

            $this->remove($key);
            $htmlCrumb .= (!empty($this->elements) ? $this->separator : '');

            if (true === $this->implicitFlush) {
                echo $htmlCrumb;
            } else {
                $content .= $htmlCrumb;
            }
        }

        // We return the breadcrumbs as string if the implicitFlush is turned off
        if (false === $this->implicitFlush) {
            return $content;
        }

        if ($this->eventsManager) {
            $this->eventsManager->fire('breadcrumbs:afterOutput', $this);
        }
    }

    /**
     * Gets breadcrumbs as array
     *
     * @return array
     */
    public function toArray()
    {
        return $this->elements;
    }

    /**
     * Removes crumb by url.
     *
     * Events:
     * * breadcrumbs:beforeRemove
     * * breadcrumbs:afterRemove
     *
     * <core>
     * $this->breadcrumbs->remove('/admin/user/create');
     *
     * // remove a crumb without an url
     * $this->breadcrumbs->remove(null);
     * </code>
     *
     * @param string|null $link Crumb url
     * @return $this
     */
    public function remove($link)
    {
        if ($this->eventsManager) {
            $this->eventsManager->fire('breadcrumbs:beforeRemove', $this, [$link]);
        }

        try {
            if (empty($this->elements)) {
                throw new UnderflowException('Cannot remove crumb from an empty list.');
            }

            if (!is_string($link) && $link !== null) {
                $type = gettype($link);
                throw new InvalidArgumentException(
                    "Expected value of the first argument to be either string or null type, {$type} given."
                );
            }

            if ($link === null) {
                $link = ':null:';
            }

            if (!empty($this->elements) && array_key_exists($link, $this->elements)) {
                unset($this->elements[$link]);
            }
        } catch (\Exception $e) {
            $this->log($e);
        }

        if ($this->eventsManager) {
            $this->eventsManager->fire('breadcrumbs:afterRemove', $this, [$link]);
        }

        return $this;
    }

    /**
     * Update an existing crumb.
     *
     * Events:
     * * breadcrumbs:beforeUpdate
     * * breadcrumbs:afterUpdate
     *
     * <core>
     * $this->breadcrumbs->update('/admin/user/remove', ['label' => '<strong class="red">Remove</strong>']);
     * </code>
     *
     * @param string|null $url  Crumb URL
     * @param array       $data Crumb data
     * @return $this
     */
    public function update($url, array $data)
    {
        $id = $url;
        try {
            if (empty($this->elements)) {
                throw new UnderflowException('Cannot update on an empty breadcrumbs list.');
            }

            if (!is_string($id) && $id !== null) {
                $type = gettype($id);
                throw new InvalidArgumentException(
                    "Expected value of the second argument to be either string or null type, {$type} given."
                );
            }

            if ($url === null) {
                $id = ':null:';
            }

            if (!array_key_exists($id, $this->elements)) {
                throw new OutOfBoundsException(
                    sprintf("No such url '%s' in breadcrumbs list", $url === null ? 'null' : $id)
                );
            }

            $this->elements[$id] = array_merge($this->elements[$id], $data);
        } catch (\Exception $e) {
            $this->log($e);
        }

        return $this;
    }

    /**
     * Logs error messages.
     *
     * Events:
     * * breadcrumbs:beforeLogging
     * * breadcrumbs:afterLogging
     *
     * @param \Exception $e
     */
    protected function log(\Exception $e)
    {
        if ($this->eventsManager) {
            $this->eventsManager->fire('breadcrumbs:beforeLogging', $this, [$e]);
        }

        if ($this->logger) {
            $this->logger->error($e->getMessage());
        } else {
            error_log($e->getMessage());
        }

        if ($this->eventsManager) {
            $this->eventsManager->fire('breadcrumbs:afterLogging', $this, [$e]);
        }
    }

    /**
     * Count breadcrumbs
     *
     * @return integer
     */
    public function count()
    {
        return count($this->elements);
    }
}
