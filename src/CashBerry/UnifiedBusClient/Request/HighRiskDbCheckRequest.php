<?php

namespace CashBerry\UnifiedBusClient\Request;

class HighRiskDbCheckRequest implements RequestPayload
{
    /**
     * @var string
     */
    private $fullName;

    /**
     * @var string
     */
    private $birthday;

    /**
     * @var string
     */
    private $ipn;

    /**
     * @var string
     */
    private $address;

    /**
     * @var string
     */
    private $doc;

    /**
     * @return string
     */
    public function getFullName(): string
    {
        return $this->fullName;
    }

    /**
     * @param string $fullName
     */
    public function setFullName(string $fullName)
    {
        $this->fullName = $fullName;
    }

    /**
     * @return string
     */
    public function getBirthday(): string
    {
        return $this->birthday;
    }

    /**
     * @param string $birthday
     */
    public function setBirthday(string $birthday)
    {
        $this->birthday = $birthday;
    }

    /**
     * @return string
     */
    public function getIpn(): string
    {
        return $this->ipn;
    }

    /**
     * @param string $ipn
     */
    public function setIpn(string $ipn)
    {
        $this->ipn = $ipn;
    }

    /**
     * @return string
     */
    public function getAddress(): string
    {
        return $this->address;
    }

    /**
     * @param string $address
     */
    public function setAddress(string $address)
    {
        $this->address = $address;
    }

    /**
     * @return string
     */
    public function getDoc(): string
    {
        return $this->doc;
    }

    /**
     * @param string $doc
     */
    public function setDoc(string $doc)
    {
        $this->doc = $doc;
    }

    /**
     * @return array
     */
    public function getBody(): array
    {
        return [
            "name" => $this->fullName,
            "date" => $this->birthday,
            "ipn" => $this->ipn,
            "address" => $this->address,
            "doc" => $this->doc
        ];
    }
}
