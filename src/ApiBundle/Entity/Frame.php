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
     * @var Ball[]
     */
    private $balls = [];

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

    /**
     * @return Ball[]
     */
    public function getBalls()
    {
        return $this->balls;
    }

    /**
     * @param Ball[] $balls
     */
    public function setBalls($balls)
    {
        $this->balls = $balls;
    }

    /**
     * @param Ball $ball
     */
    public function addBall(Ball $ball)
    {
        $this->balls[] = $ball;
    }

    /**
     * Get the first balls display score
     * @return mixed
     */
    public function getFirstBallDisplayScore()
    {
        if (empty($this->balls)) {
            return null;
        }
        if ($this->isStrike()) {
            return 'X';
        }
        if (isset($this->balls[0])) {
            return $this->balls[0]->getPins();
        }
    }

    /**
     * Get the first balls display score
     * @return mixed
     */
    public function getSecondBallDisplayScore()
    {
        if (empty($this->balls)) {
            return null;
        }
        if ($this->isStrike()) {
            return null;
        }
        if($this->isSpare()){
            return '/';
        }
        if (isset($this->balls[1])) {
            return $this->balls[1]->getPins();
        }
    }

    /**
     * Get the total number of pins for this frame i.e. for both balls
     * @return int
     */
    public function getPins()
    {
        if (!isset($this->balls) || empty($this->balls)) {
            return 0;
        } else {
            $totalPins = 0;
            foreach ($this->getBalls() as $ball) {
                $totalPins += $ball->getPins();
            }

            return $totalPins;
        }
    }

    /**
     * A frame is a strike if the first ball knocked ten pins over
     * @return bool
     */
    public function isStrike()
    {
        if (isset($this->balls) && !empty($this->balls)) {

            $ball = isset($this->balls[0]) ? $this->balls[0] : null;
            if (!empty($ball)) {

                return $ball->getPins() == Game::MAX_PINS ? true : false;
            }
        }

        return false;
    }

    /**
     * A frame is a spare if the total number of pins in both balls is ten
     * @return bool
     */
    public function isSpare()
    {
        return !$this->isStrike() && $this->getPins() == Game::MAX_PINS;
    }
}