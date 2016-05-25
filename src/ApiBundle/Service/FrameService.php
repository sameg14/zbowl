<?php

namespace ApiBundle\Service;

use ApiBundle\Entity\Frame;
use ApiBundle\Entity\Game;
use ApiBundle\Entity\Ball;
use ApiBundle\Repository\FrameRepository;
use ApiBundle\Repository\BallRepository;
use ApiBundle\Repository\GameRepository;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Session\Session;
use Doctrine\ORM\EntityManager;

class FrameService
{
    /**
     * @var Session
     */
    protected $session;

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var GameRepository
     */
    protected $gameRepository;

    /**
     * @var FrameRepository
     */
    protected $frameRepository;

    /**
     * @var BallRepository
     */
    protected $ballRepository;

    /**
     * FrameService constructor.
     * @param Registry $registry
     * @param Session $session
     */
    public function __construct(Registry $registry, Session $session)
    {
        $this->frameRepository = $registry->getRepository('ApiBundle:Frame');
        $this->ballRepository = $registry->getRepository('ApiBundle:Ball');
        $this->gameRepository = $registry->getRepository('ApiBundle:Game');
        $this->session = $session;
        $this->em = $registry->getManager();
    }

    /**
     * Get frame object for a player for the provided frame number, create one if needed
     * @param int $frameNumber Which frame are we on[1-10]
     * @param int $playerId PK for this player
     * @return Frame
     */
    public function getFrameForPlayer($frameNumber, $playerId)
    {
        $frame = $this->frameRepository->findOneBy(
            ['playerId' => $playerId, 'frameNumber' => $frameNumber, 'isActive' => 1]
        );
        if (empty($frame)) {
            $frame = new Frame();
            $frame->setGameId($this->getGameId());
            $frame->setFrameNumber($frameNumber);
            $frame->setIsActive(true);
            $frame->setPlayerId($playerId);
            $this->em->persist($frame);
            $this->em->flush();
        }

        return $frame;
    }

    /**
     * Get the numeric ball number
     * @param int $frameId PK for this particular frame
     * @return int
     */
    public function getBallNumber($frameId)
    {
        $ball = $this->ballRepository->findBy(['frameId' => $frameId]);
        if (empty($ball)) {
            return 1;
        } else {
            return 2;
        }
    }

    /**
     * Throw a ball
     * @param int $frameId Which frame are we on
     * @param int $ballNumber Which ball, [1-2]
     * @param int $pins Number of pins dropped
     * @return void
     */
    public function throwBall($frameId, $ballNumber, $pins)
    {
        if ($ballNumber > 2) {
            throw new Exception('You cannot throw more than two balls at a time');
        }

        $ball = new Ball();
        $ball->setFrameId($frameId);
        $ball->setBallNumber($ballNumber);
        $ball->setPins($pins);
        $this->em->persist($ball);
        $this->em->flush();
    }

    /**
     * Increment the current frame and return the incremented value
     * @return int
     */
    public function incrementFrame()
    {
        /** @var Game $game */
        $game = $this->gameRepository->findOneBy(['id' => $this->getGameId(), 'isActive' => true]);
        if (empty($game)) {
            throw new Exception('Cannot find game with Id: ' . $this->getGameId());
        }
        $currentFrame = $game->getFrame();
        $currentFrame = $currentFrame + 1;
        $game->setFrame($currentFrame);

        $this->em->persist($game);
        $this->em->flush($game);

        return $currentFrame;
    }

    /**
     * Get the number of pins per frame
     * @param int $frameId The frame where the ball was thrown
     * @param int $ballNumber The ball to get pins for
     * @return int
     */
    public function getPins($frameId, $ballNumber)
    {
        $ball = $this->ballRepository->findOneBy(['frameId' => $frameId, 'ballNumber' => $ballNumber]);
        if (empty($ball)) {
            return 0;
        } else {
            return $ball->getPins();
        }
    }

    /**
     * Get an active game identifier
     * @return int
     */
    public function getGameId()
    {
        return $this->session->get('gameId');
    }
}