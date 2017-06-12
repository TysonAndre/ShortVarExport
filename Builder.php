<?php declare(strict_types=1);
namespace ShortVarExport;

/**
 * See README.md and COPYING for license information.
 * Builds a smaller representation of PHP values, similar to `var_export`, but shorter. The representation is also valid PHP code. Supports PHP 7.0+ (Will work in php5 if you remove scalar type hints and return types)
 * This returns arrays formatted with the php 5.4 square bracket syntax.
 * self::build($value)` returns arrays formatted with the php 5.4 square bracket syntax.
 * @author Tyson Andre<tysonandre775@hotmail.com>
 * @version 0.0.1
 */
class Builder {
    const MULTI_LINE = 1;

    private $_parts = [];

    private function __construct() { }

    public static final function build($data, int $flags = 0) : string {
        $builder = new Builder();
        if ($flags & self::MULTI_LINE) {
            $builder->_buildMultiLine($data);
        } else {
            $builder->_build($data);
        }
        return $builder->asString();
    }

    /**
     * Builds a multi-line representation of $data.
     * The top level has one element per line.
     *
     * Single element arrays and 0 element arrays are rendered as a single line.
     *
     * May be multi-line if the data contains strings with newlines.
     * @return void
     */
    private function _buildMultiLine($data) {
        if (is_scalar($data) || $data === null) {
            $this->_parts[] = var_export($data, true);
        } else if (is_array($data)) {
            $this->_parts[] = "[";
            $hasMultipleParts = count($data) > 1;
            if ($hasMultipleParts) {
                $this->_parts[] = "\n";
                $separator = ",\n";
            } else {
                $separator = "";
            }
            $expectedIncrementingKey = 0;
            foreach ($data as $key => $value) {
                if ($key === $expectedIncrementingKey) {
                    ++$expectedIncrementingKey;
                } else {
                    $expectedIncrementingKey = null;
                    $this->_parts[] = var_export($key, true);
                    $this->_parts[] = "=>";
                }
                $this->_build($value);
                $this->_parts[] = $separator;
            }
            $this->_parts[] = "]";
        } else if (is_object($data)) {
            $this->_handleObject($data, $key);
        } else {
            // impossible
            throw new \RuntimeException("Unexpected type " . gettype($data));
        }
    }

    /**
     * Builds a single-line representation of $data. May be multi-line if the data contains strings with newlines.
     * @param mixed $data
     * @param int|string $key
     * @return void
     */
    private final function _build($data, string $key = null) {
        if (is_scalar($data) || $data === null) {
            $this->_parts[] = var_export($data, true);
        } else if (is_array($data)) {
            $isFirst = true;
            $this->_parts[] = '[';
            $expectedIncrementingKey = 0;
            foreach ($data as $key => $value) {
                if ($isFirst) {
                    $isFirst = false;
                } else {
                    $this->_parts[] = ',';
                }
                if ($key === $expectedIncrementingKey) {
                    ++$expectedIncrementingKey;
                } else {
                    $expectedIncrementingKey = null;
                    $this->_parts[] = var_export($key, true);
                    $this->_parts[] = "=>";
                }
                $this->_build($value);
            }
            $this->_parts[] = ']';
        } else if (is_object($data)) {
            $this->_handleObject($data, $key);
        } else if (is_resource($data)) {
            $this->_handleResource($data, $key);
        } else {
            // impossible
            throw new \RuntimeException("Unexpected type " . gettype($data));
        }
    }


    /**
     * Appends the given string to the serialized representation.
     */
    protected function _appendString(string $data) {
        $this->_parts[] = $data;
    }

    /**
     * Can be overridden in subclasses, which could call _appendString if they have a representation for the data.
     *
     * @param object $object
     * @param int|string|null $key
     * @return void
     */
    protected function _handleObject($object, $key) {
        $className = get_class($object);
        if ($key !== null) {
            $message = "Saw unexpected object of class $className for key " . var_export($key, true);
        } else {
            $message = "Saw unexpected object of class $className";
        }
        throw new \InvalidArgumentException($message);
    }

    /**
     * Can be overridden in subclasses, which could call _appendString if they have a representation for the data.
     *
     * @param resource $resource
     * @param int|string|null $key
     * @return void
     */
    protected function _handleResource($resource, $key) {
        if ($key !== null) {
            $message = "Saw unexpected resource for key " . var_export($key, true);
        } else {
            $message = "Saw unexpected resource";
        }
        throw new \InvalidArgumentException($message);
    }

    public function asString() {
        return implode('', $this->_parts);
    }
}
