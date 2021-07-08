<?php

namespace CashBerry\UnifiedBusClientV2\Request;

use RuntimeException;

/**
 * Class AbstractRequestPayload
 */
abstract class AbstractRequestPayload
{
    /**
     * @param string $name
     * @param string $value
     *
     * @return string
     */
    protected function buildProperty(string $name, string $value): string
    {
        return "<{$name}>" . (isset($value) ? htmlspecialchars($value, ENT_QUOTES | ENT_XML1) : '') . "</{$name}>\n";
    }

    /**
     * @return string
     */
    public function toXml(): string
    {
        $properties = get_object_vars($this);
        $result = '';
        foreach ($properties as $p => $v) {
            if (is_array($this->$p)) {
                /**
                 * @var self $i
                 */
                foreach ($this->$p as $i) {
                    $result .= $i->toXml();
                }
                continue;
            }

            $result .= (method_exists($this->$p, __FUNCTION__))
                ? $this->$p->toXml()
                : $this->buildProperty($p, $v);
        }

        return $result;
    }

    /**
     * @return array
     */
    public function toQuery(): array
    {
        $properties = get_object_vars($this);
        $result = [];
        /**
         * @var $v self
         */
        foreach ($properties as $p => $v) {
            $result[$p] = is_object($v) ? $v->toXml() : $v;
        }

        return $result;
    }

    /**
     * @return array
     */
    public function fromObject(): array
    {
        $properties = get_object_vars($this);
        $result = [];

        /**
         * @var self $p
         */
        foreach ($properties as $n => $p) {
            if ($p === null) {
                continue;
            }

            if (is_scalar($p)) {
                $result[$n] = $p;
                continue;
            }

            if (is_object($p) && ($p instanceof self)) {
                $result[$n] = $p->fromObject();
                continue;
            }

            if (is_array($p)) {
                $result[$n] = $p;
                continue;
            }

            throw new RuntimeException("Property $n contains wrong value");
        }

        return $result;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return $this->fromObject();
    }

    /**
     * @return false|string
     */
    public function toJson()
    {
        $result = $this->fromObject();

        return json_encode($result);
    }

    /**
     * @return bool
     */
    public function isEmpty(): bool
    {
        $properties = get_object_vars($this);

        foreach ($properties as $n => $p) {
            if (isset($p)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return string
     */
    abstract public function getServiceName(): string;
}
