<?php
/**
 * Created by PhpStorm.
 * User: benm
 * Date: 13/05/2014
 * Time: 4:37 PM
 */

namespace Nejibem\WhattoeatBundle\Entity;


class Ingredient {

    private $name;
    private $quantity;
    private $unit;
    private $usedByDate;

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $quantity
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
    }

    /**
     * @return mixed
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @param mixed $unit
     */
    public function setUnit($unit)
    {
        $this->unit = $unit;
    }

    /**
     * @return mixed
     */
    public function getUnit()
    {
        return $this->unit;
    }

    /**
     * @param mixed $usedByDate
     */
    public function setUsedByDate($usedByDate)
    {
        $this->usedByDate = \DateTime::createFromFormat( 'j/n/Y', $usedByDate);
    }

    /**
     * @return mixed
     */
    public function getUsedByDate()
    {
        return $this->usedByDate;
    }

    public function isUsedByDateExpired()
    {
        return $this->usedByDate < new \DateTime('now');
    }

    public function getUsedByDateInterval()
    {
        $date = new \DateTime('now');
        return (int) $date->diff( $this->usedByDate )->format('%r%a');
    }

}