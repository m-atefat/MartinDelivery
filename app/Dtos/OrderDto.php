<?php

namespace App\Dtos;

use App\Models\Business;

class OrderDto
{
    private int $businessId;

    private string $orderSourceName;

    private string $orderSourceAddress;

    private string $orderSourcePhone;

    private float $orderSourceLat;

    private float $orderSourceLong;

    private string $orderDestinationName;

    private string $orderDestinationAddress;

    private string $orderDestinationPhone;

    private float $orderDestinationLat;

    private float $orderDestinationLong;

    /**
     * @return string
     */
    public function getOrderSourceName(): string
    {
        return $this->orderSourceName;
    }

    /**
     * @return int
     */
    public function getBusinessId(): int
    {
        return $this->businessId;
    }

    /**
     * @param int $businessId
     */
    public function setBusinessId(int $businessId)
    {
        $this->businessId = $businessId;
        return $this;
    }

    /**
     * @param string $orderSourceName
     */
    public function setOrderSourceName(string $orderSourceName)
    {
        $this->orderSourceName = $orderSourceName;
        return $this;
    }

    /**
     * @return string
     */
    public function getOrderSourceAddress(): string
    {
        return $this->orderSourceAddress;
    }

    /**
     * @param string $orderSourceAddress
     */
    public function setOrderSourceAddress(string $orderSourceAddress)
    {
        $this->orderSourceAddress = $orderSourceAddress;
        return $this;
    }

    /**
     * @return string
     */
    public function getOrderSourcePhone(): string
    {
        return $this->orderSourcePhone;
    }

    /**
     * @param string $orderSourcePhone
     */
    public function setOrderSourcePhone(string $orderSourcePhone)
    {
        $this->orderSourcePhone = $orderSourcePhone;
        return $this;
    }

    /**
     * @return float
     */
    public function getOrderSourceLat(): float
    {
        return $this->orderSourceLat;
    }

    /**
     * @param float $orderSourceLat
     */
    public function setOrderSourceLat(float $orderSourceLat)
    {
        $this->orderSourceLat = $orderSourceLat;
        return $this;
    }

    /**
     * @return float
     */
    public function getOrderSourceLong(): float
    {
        return $this->orderSourceLong;
    }

    /**
     * @param float $orderSourceLong
     */
    public function setOrderSourceLong(float $orderSourceLong)
    {
        $this->orderSourceLong = $orderSourceLong;
        return $this;
    }

    /**
     * @return string
     */
    public function getOrderDestinationName(): string
    {
        return $this->orderDestinationName;
    }

    /**
     * @param string $orderDestinationName
     */
    public function setOrderDestinationName(string $orderDestinationName)
    {
        $this->orderDestinationName = $orderDestinationName;
        return $this;
    }

    /**
     * @return string
     */
    public function getOrderDestinationAddress(): string
    {
        return $this->orderDestinationAddress;
    }

    /**
     * @param string $orderDestinationAddress
     */
    public function setOrderDestinationAddress(string $orderDestinationAddress)
    {
        $this->orderDestinationAddress = $orderDestinationAddress;
        return $this;
    }

    /**
     * @return string
     */
    public function getOrderDestinationPhone(): string
    {
        return $this->orderDestinationPhone;
    }

    /**
     * @param string $orderDestinationPhone
     */
    public function setOrderDestinationPhone(string $orderDestinationPhone)
    {
        $this->orderDestinationPhone = $orderDestinationPhone;
        return $this;
    }

    /**
     * @return float
     */
    public function getOrderDestinationLat(): float
    {
        return $this->orderDestinationLat;
    }

    /**
     * @param float $orderDestinationLat
     */
    public function setOrderDestinationLat(float $orderDestinationLat)
    {
        $this->orderDestinationLat = $orderDestinationLat;
        return $this;
    }

    /**
     * @return float
     */
    public function getOrderDestinationLong(): float
    {
        return $this->orderDestinationLong;
    }

    /**
     * @param float $orderDestinationLong
     */
    public function setOrderDestinationLong(float $orderDestinationLong)
    {
        $this->orderDestinationLong = $orderDestinationLong;
        return $this;
    }


}
