<?php

/* 
 * @license The Ryuu Technology License
 * 
 * Copyright 2014 Ryuu Technology by
 * KatsuoRyuu <anders-github@drake-development.org>.
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 * Ryuu Technology shall be visible and readable to anyone using the software
 * and shall be written in one of the following ways: 竜技術, Ryuu Technology
 * or by using the company logo.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 * 
 * 
 * @link https://github.com/KatsuoRyuu/
 */

namespace KryuuOccasus;

use Iterator;
use Zend\Stdlib\AbstractOptions;

/**
 * @project Ryuu-ZF2
 * @authors spawn
 * @encoding UTF-8
 * @date Feb 25, 2016 - 1:34:54 AM
 * @package *
 * @todo *
 * @depends *
 * @note *
 */

class Options implements Iterator, OptionsInterface
{
    private $configuration = [];
    
    public function __construct($config)
    {
        $this->setConfiguration($config);
    }
    
    public function __call($func, $arg = null)
    {
        if (isset($arg[0])) {
            $default = $arg[0];
        } else {
            $default = null;
        }
        try { 
            if (preg_match('/^getArray/', $func)) {
                return $this->getArray(substr($func,8), $default);
            } else if (preg_match('/^get/', $func)) {
                return $this->get(substr($func,3), $default);
            }
        } catch (Exception\WrongVariableTypeException $e) {
            throw $e;
        }
    }
    
    public function get($key, $default = null)
    {
        if (!is_string($key) && !is_integer($key) && $key != NULL) {
            throw new Exception\WrongVariableTypeException(sprintf(
                'Please use a variable of type: %s as a key for getting the options, you used: %s',
                'string, integer',
                gettype($key)
            ));
        }
        
        if (array_key_exists($key, $this->configuration)) {
            if (is_array($this->configuration[$key])) {
                return new Options($this->configuration[$key]);
            }
            return $this->configuration[$key];
        }
        
        $regex = '/(?<!^)((?<![[:upper:]])[[:upper:]]|[[:upper:]](?![[:upper:]]))/';
        $fAlias = strtolower(preg_replace( $regex, '_$1', $key ));
        if (array_key_exists($fAlias, $this->configuration)) {
            if (is_array($this->configuration[$fAlias])) {
                return new Options($this->configuration[$fAlias]);
            }
            return $this->configuration[$fAlias];
        }
        
        if ($default == null) {
            return new Options([]);
        } else {
            return $default;
        }
    }
    
    public function getArray($key, $default = null)
    {
        if (!is_string($key) && !is_integer($key) && $key != NULL) {
            throw new Exception\WrongVariableTypeException(sprintf(
                'Please use a variable of type: %s as a key for getting the options, you used: %s',
                'string, integer',
                gettype($key)
            ));
        }
        if (array_key_exists($key, $this->configuration)) {
            return $this->configuration[$key];
        }
        $regex = '/(?<!^)((?<![[:upper:]])[[:upper:]]|[[:upper:]](?![[:upper:]]))/';
        $fAlias = strtolower(preg_replace( $regex, '_$1', $key ));
        
        if (array_key_exists($fAlias, $this->configuration)) {
            return $this->configuration[$fAlias];
        }     
        if ($default == null) {
            return new Options([]);
        } else {
            return $default;
        }
    }

    public function rewind()
    {
        reset($this->configuration);
    }
  
    public function current()
    {
        current($this->configuration);
        return $this->get($this->key());
    }
  
    public function key() 
    {
        $var = key($this->configuration);
        return $var;
    }
  
    public function next() 
    {
        next($this->configuration);
        return $this->get($this->key());
    }
  
    public function valid()
    {
        $key = key($this->configuration);
        $var = ($key !== NULL && $key !== FALSE);
        return $var;
    }
    
    private function setConfiguration($config)
    {
        $this->configuration = $config;
    }
}