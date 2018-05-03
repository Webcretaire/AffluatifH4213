<?php

namespace Affluatif\Traits;

/**
 * Trait Autocomplete
 *
 * @package Affluatif\Traits
 */
trait Autocomplete
{
    /**
     * @var array
     */
    private $autocomplete = null;

    /**
     * @param string $attr
     */
    private function complete(string $attr)
    {
        if (!is_null($this->autocomplete) && isset($this->autocomplete[$attr])) {
            echo 'value="' . $this->autocomplete[$attr] . '"';
        }
    }
}