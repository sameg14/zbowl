<?php

namespace ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Ball
 *
 * @ORM\Table(name="ball")
 * @ORM\Entity(repositoryClass="ApiBundle\Repository\BallRepository")
 */
class Ball
{
    const FIRST = 1;

    const SECOND = 2;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="frame_id", type="integer")
     */
    private $frameId;

    /**
     * @var int
     *
     * @ORM\Column(name="ball_number", type="integer")
     */
    private $ballNumber;

    /**
     * @var int
     *
     * @ORM\Column(name="pins", type="integer")
     */
    private $pins;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set frameId
     *
     * @param integer $frameId
     *
     * @return Ball
     */
    public function setFrameId($frameId)
    {
        $this->frameId = $frameId;

        return $this;
    }

    /**
     * Get frameId
     *
     * @return int
     */
    public function getFrameId()
    {
        return $this->frameId;
    }

    /**
     * Set ballNumber
     *
     * @param integer $ballNumber
     *
     * @return Ball
     */
    public function setBallNumber($ballNumber)
    {
        $this->ballNumber = $ballNumber;

        return $this;
    }

    /**
     * Get ballNumber
     *
     * @return int
     */
    public function getBallNumber()
    {
        return $this->ballNumber;
    }

    /**
     * @return int
     */
    public function getPins()
    {
        return $this->pins;
    }

    /**
     * @param int $pins
     */
    public function setPins($pins)
    {
        $this->pins = $pins;
    }
}

