<?php

namespace ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Game
 *
 * @ORM\Table(name="game")
 * @ORM\Entity(repositoryClass="ApiBundle\Repository\GameRepository")
 */
class Game
{
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
     * @ORM\Column(name="lane_id", type="integer")
     */
    private $laneId;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_active", type="boolean", nullable=true)
     */
    private $isActive;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_started", type="datetime")
     */
    private $dateStarted;


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
     * Set laneId
     *
     * @param integer $laneId
     *
     * @return Game
     */
    public function setLaneId($laneId)
    {
        $this->laneId = $laneId;

        return $this;
    }

    /**
     * Get laneId
     *
     * @return int
     */
    public function getLaneId()
    {
        return $this->laneId;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     *
     * @return Game
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive
     *
     * @return bool
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * Set dateStarted
     *
     * @param \DateTime $dateStarted
     *
     * @return Game
     */
    public function setDateStarted($dateStarted)
    {
        $this->dateStarted = $dateStarted;

        return $this;
    }

    /**
     * Get dateStarted
     *
     * @return \DateTime
     */
    public function getDateStarted()
    {
        return $this->dateStarted;
    }
}

