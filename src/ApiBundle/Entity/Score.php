<?php

namespace ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Score
 *
 * @ORM\Table(name="score")
 * @ORM\Entity(repositoryClass="ApiBundle\Repository\ScoreRepository")
 */
class Score
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
     * @ORM\Column(name="player_id", type="integer")
     */
    private $playerId;

    /**
     * @var int
     *
     * @ORM\Column(name="game_id", type="integer")
     */
    private $gameId;

    /**
     * @var int
     *
     * @ORM\Column(name="frame", type="integer")
     */
    private $frame;

    /**
     * Is this a regular score or a strike [regular, strike]
     * @var string
     *
     * @ORM\Column(name="score_type", type="string", length=255)
     */
    private $scoreType;

    /**
     * @var int
     *
     * @ORM\Column(name="score_value", type="integer")
     */
    private $scoreValue;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_created", type="datetime")
     */
    private $dateCreated;


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
     * Set playerId
     *
     * @param integer $playerId
     *
     * @return Score
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
     * Set gameId
     *
     * @param integer $gameId
     *
     * @return Score
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
     * Set frame
     *
     * @param integer $frame
     *
     * @return Score
     */
    public function setFrame($frame)
    {
        $this->frame = $frame;

        return $this;
    }

    /**
     * Get frame
     *
     * @return int
     */
    public function getFrame()
    {
        return $this->frame;
    }

    /**
     * Set scoreType
     *
     * @param string $scoreType
     *
     * @return Score
     */
    public function setScoreType($scoreType)
    {
        $this->scoreType = $scoreType;

        return $this;
    }

    /**
     * Get scoreType
     *
     * @return string
     */
    public function getScoreType()
    {
        return $this->scoreType;
    }

    /**
     * Set scoreValue
     *
     * @param integer $scoreValue
     *
     * @return Score
     */
    public function setScoreValue($scoreValue)
    {
        $this->scoreValue = $scoreValue;

        return $this;
    }

    /**
     * Get scoreValue
     *
     * @return int
     */
    public function getScoreValue()
    {
        return $this->scoreValue;
    }

    /**
     * Set dateCreated
     *
     * @param \DateTime $dateCreated
     *
     * @return Score
     */
    public function setDateCreated($dateCreated)
    {
        $this->dateCreated = $dateCreated;

        return $this;
    }

    /**
     * Get dateCreated
     *
     * @return \DateTime
     */
    public function getDateCreated()
    {
        return $this->dateCreated;
    }
}

