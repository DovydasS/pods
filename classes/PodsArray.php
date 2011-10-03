<?php
class PodsArray implements ArrayAccess {
    private $__container = array();

    /**
     * Alternative to get_object_vars to access an object as an array with simple functionality and accepts arrays to
     * add additional functionality. Additional functionality includes validation and setting default data.
     *
     * @param mixed $container Object (or existing Array)
     * @license http://www.gnu.org/licenses/gpl-2.0.html
     * @since 2.0.0
     */
    public function __construct ($container) {
        if (is_array($container) || is_object($container))
            $this->__container = &$container;
    }

    /**
     * Set value from array usage $object['offset'] = 'value';
     *
     * @param mixed $offset Used to set index of Array or Variable name on Object
     * @param mixed $value Value to be set
     * @since 2.0.0
     */
    public function offsetSet ($offset, $value) {
        if (is_array($this->__container))
            $this->__container[$offset] = $value;
        else
            $this->__container->$offset = $value;
    }

    /**
     * Get value from array usage $object['offset'];
     *
     * @param mixed $offset Used to get value of Array or Variable on Object
     * @since 2.0.0
     */
    public function offsetGet ($offset) {
        if (is_array($this->__container)) {
            if (isset($this->__container[$offset]))
                return $this->__container[$offset];
            return null;
        }
        if (isset($this->__container->$offset))
            return $this->__container->$offset;
        return null;
    }

    /**
     * Get value from array usage $object['offset'];
     *
     * @param mixed $offset Used to get value of Array or Variable on Object
     * @since 2.0.0
     */
    public function offsetExists ($offset) {
        if (is_array($this->__container))
            return isset($this->__container[$offset]);
        return isset($this->__container->$offset);
    }

    /**
     * Get value from array usage $object['offset'];
     *
     * @param mixed $offset Used to unset index of Array or Variable on Object
     * @since 2.0.0
     */
    public function offsetUnset ($offset) {
        if (is_array($this->__container))
            unset($this->__container[$offset]);
        else
            unset($this->__container->$offset);
    }

    /**
     * Validate value on a specific type and set default (if empty)
     *
     * @param mixed $offset Used to get value of Array or Variable on Object
     * @param mixed $default Used to set default value if it doesn't exist
     * @param string $type Used to force a specific type of variable (allowed: array, object, integer, absint, boolean)
     * @param mixed $extra Used in advanced types of variables
     * @since 2.0.0
     */
    public function validate ($offset, $default = null, $type = null, $extra = null) {
        if (!$this->offsetExists($offset))
            $this->offsetSet($offset, $default);
        $value = $this->offsetGet($offset);
        if (empty($value) && null !== $default)
            $value = $default;
        if ('array' == $type || 'array_merge' == $type) {
            if (!is_array($value))
                $value = explode(',', $value);
            if ('array_merge' == $type && $value !== $default)
                $value = array_merge($default, $value);
        }
        if ('object' == $type || 'object_merge' == $type) {
            if (!is_object($value)) {
                if (!is_array($value))
                    $value = explode(',', $value);
                $value = (object) $value;
            }
            if ('object_merge' == $type && $value !== $default)
                $value = (object) array_merge((array) $default, (array) $value);
        }
        if ('integer' == $type || 'absint' == $type) {
            if (!is_numeric(trim($value)))
                $value = 0;
            else
                $value = intval($value);
            if ('absint' == $type)
                $value = abs($value);
        }
        if ('boolean' == $type)
            $value = (bool) $value;
        if ('in_array' == $type && is_array($default)) {
            if (is_array($value)) {
                foreach ($value as $k => $v) {
                    if (!in_array($v, $extra))
                        unset($value[$k]);
                }
            }
            elseif (!in_array($value, $extra))
                $value = $default;
        }
        $this->offsetSet($offset, $value);
        return $value;
    }

    /**
     *
     * @since 2.0.0
     */
    public function dump () {
        if (is_array($this->__container))
            return $this->__container;
        return get_object_vars($this->__container);
    }

    /**
     * Mapping >> offsetSet
     *
     * @since 2.0.0
     */
    public function __set ($offset, $value) {
        return $this->offsetSet($offset, $value);
    }

    /**
     * Mapping >> offsetGet
     *
     * @since 2.0.0
     */
    public function __get ($offset) {
        return $this->offsetGet($offset);
    }

    /**
     * Mapping >> offsetExists
     *
     * @since 2.0.0
     */
    public function __isset ($offset) {
        return $this->offsetExists($offset);
    }

    /**
     * Mapping >> offsetUnset
     *
     * @since 2.0.0
     */
    public function __unset ($offset) {
        $this->offsetUnset($offset);
    }
}