<?php

namespace ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Frame
 *
 * @ORM\Table(name="frame")
 * @ORM\Entity(repositoryClass="ApiBundle\Repository\FrameRepository")
 */
class Frame
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
     * @ORM\Column(name="game_id", type="integer")
     */
    private $gameId;

    /**
     * @var int
     *
     * @ORM\Column(name="player_id", type="integer")
     */
    private $playerId;

    /**
     * @var int
     *
     * @ORM\Column(name="frame_number", type="integer")
     */
    private $frameNumber;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive;

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
     * Set gameId
     *
     * @param integer $gameId
     *
     * @return Frame
     */
    public function setGameId($gameId)
    {
        $this->gameId = $gameId;

        return $this;
    }

    /**
     * Get gameId
     *
     * @return int
     */
    public function getGameId()
    {
        return $this->gameId;
    }

    /**
     * Set playerId
     *
     * @param integer $playerId
     *
     * @return Frame
     */
    public function setPlayerId($playerId)
    {
        $this->playerId = $playerId;

        return $this;
    }

    /**
     * Get playerId
     *
     * @return int
     */
    public function getPlayerId()
    {
        return $this->playerId;
    }

    /**
     * Set frameNumber
     *
     * @param integer $frameNumber
     *
     * @return Frame
     */
    public function setFrameNumber($frameNumber)
    {
        $this->frameNumber = $frameNumber;

        return $this;
    }

    /**
     * Get frameNumber
     *
     * @return int
     */
    public function getFrameNumber()
    {
        return $this->frameNumber;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     *
     * @return Frame
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
}

