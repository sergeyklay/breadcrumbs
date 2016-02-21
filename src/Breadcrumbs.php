<?php

/*
 +------------------------------------------------------------------------+
 | Phalcon Breadcrumbs                                                    |
 +------------------------------------------------------------------------+
 | Copyright (c) 2016 Phalcon Framework Team                              |
 +------------------------------------------------------------------------+
 | This source file is subject to the New BSD License that is bundled     |
 | with this package in the file docs/LICENSE.txt.                        |
 |                                                                        |
 | If you did not receive a copy of the license and are unable to         |
 | obtain it through the world-wide-web, please send an email             |
 | to serghei@phalconphp.com so I can send you a copy immediately.        |
 +------------------------------------------------------------------------+
 | Authors: Serghei Iakovlev <serghei@phalconphp.com>                     |
 +------------------------------------------------------------------------+
*/

namespace Phalcon;

use Phalcon\Mvc\User\Component;
use Phalcon\Events\ManagerInterface;

/**
 * \Phalcon\Breadcrumbs
 *
 * Handles the breadcrumbs for the application.
 *
 * @package Phalcon
 */
class Breadcrumbs extends Component
{
    /**
     * Keeps all the breadcrumbs
     * @var array
     */
    protected $elements = [];

    /**
     * Internal logger
     * @var null|Logger\AdapterInterface
     */
    protected $logger;

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
     * @var null|Translate\AdapterInterface
     */
    protected $translate;

    /**
     * Breadcrumbs constructor.
     */
    public function __construct()
    {
        if ($this->getDI()->has('logger')) {
            $logger = $this->getDI()->getShared('logger');
            if ($logger instanceof Logger\AdapterInterface) {
                $this->logger = $logger;
            }
        }

        if ($this->getDI()->has('translate')) {
            $translate = $this->getDI()->getShared('translate');
            if ($translate instanceof Translate\AdapterInterface) {
                $this->translate = $translate;
            }
        }

        if ($this->getDI()->has('eventsManager')) {
            $manager = $this->getDI()->getShared('eventsManager');
            if ($manager instanceof ManagerInterface) {
                $this->setEventsManager($this->getDI()->getShared('eventsManager'));
            }
        }
    }

    /**
     * Gets internal translate adapter
     *
     * @return null|Translate\AdapterInterface
     */
    public function getTranslateAdapter()
    {
        return $this->translate;
    }

    /**
     * Sets internal translate adapter
     *
     * @param Translate\AdapterInterface $translate Translate adapter
     * @return $this
     */
    public function setTranslateAdapter(Translate\AdapterInterface $translate)
    {
        $this->translate = $translate;

        return $this;
    }

    /**
     * Gets internal logger
     *
     * @return null|Logger\AdapterInterface
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * Sets internal logger
     *
     * @param Logger\AdapterInterface $logger
     */
    public function setLogger(Logger\AdapterInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Gets crumb separator
     *
     * @return string
     */
    public function getSeparator()
    {
        return $this->separator;
    }

    /**
     * Sets crumb separator
     *
     * @param string $separator Separator
     * @return $this
     */
    public function setSeparator($separator)
    {
        try {
            if (!is_string($separator)) {
                $type = gettype($separator);
                throw new \InvalidArgumentException(
                    "Expected value of separator to be string, {$type} given."
                );
            }

            $this->separator = $separator;
        } catch (\Exception $e) {
            $this->log($e->getMessage());
        }

        return $this;
    }

    /**
     * Adds a new crumb
     *
     * @param string $link The link that will be used
     * @param string $label Text displayed in the breadcrumb trail
     * @param bool $linked If false no link will be returned when rendering
     * @return $this
     */
    public function add($link, $label, $linked = true)
    {
        try {
            $id = md5(json_encode([$link, $label, $linked]));

            if (!is_string($link)) {
                $type = gettype($link);
                throw new \InvalidArgumentException(
                    "Expected value of second argument to be string, {$type} given."
                );
            }

            if (!is_string($label)) {
                $type = gettype($label);
                throw new \InvalidArgumentException(
                    "Expected value of third argument to be string, {$type} given."
                );
            }

            $this->elements[$id] = [
                'link' => $link,
                'label' => $label,
                'linked' => (bool) $linked
            ];
        } catch (\Exception $e) {
            $this->log($e->getMessage());
        }

        return $this;
    }

    /**
     * Render breadcrumb output based on previously set template
     */
    public function render()
    {
        if (empty($this->elements)) {
            return;
        }

        $output = '';
        $i = 0;
        foreach ($this->elements as $key => $crumb) {
            $label = $crumb['label'];
            if ($this->translate) {
                $label = $this->translate->query($label);
            }

            if ($crumb['linked']) {
                $output .= str_replace(
                    ['{{link}}', '{{label}}'],
                    [$crumb['link'], $label],
                    $this->template['linked']
                );
            } else {
                $output .= str_replace('{{label}}', $label, $this->template['not-linked']);
            }

            if (1 == $i) {
                $output = str_replace('{{icon}}', $this->template['icon'], $output);
            }

            $this->remove($key);
            $output .= (!empty($this->elements) ? $this->separator : '');
        }

        echo $output;
    }

    /**
     * Removes crumb by id
     *
     * @param string $id Crumb ID
     * @return $this
     */
    public function remove($id)
    {
        try {
            if (!is_scalar($id)) {
                $type = gettype($id);
                throw new \InvalidArgumentException(
                    "Expected value of first argument to be scalar type, {$type} given."
                );
            }

            if (!empty($this->elements) && array_key_exists($id, $this->elements)) {
                unset($this->elements[$id]);
            }
        } catch (\Exception $e) {
            $this->log($e->getMessage());
        }

        return $this;
    }

    protected function log($message)
    {
        if ($this->logger) {
            $this->logger->error($message);
        } else {
            error_log($message);
        }
    }
}
