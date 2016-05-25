<?php

namespace ApiBundle\Service;

use \Exception;
use ApiBundle\Entity\Frame;
use ApiBundle\Repository\FrameRepository;
use ApiBundle\Repository\BallRepository;
use ApiBundle\Repository\GameRepository;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\HttpFoundation\Session\Session;

class FrameService
{
    /**
     * @var Session
     */
    protected $session;

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
    }

    /**
     * The active frame being played in this game
     * @throws Exception
     * @return Frame
     */
    public function getActiveFrame()
    {
        $gameId = $this->session->get('gameId');
        $game = $this->gameRepository->findOneBy(['id' => $gameId, 'isActive' => true]);
        if (empty($game)) {
            throw new Exception('There is no active game being played');
        }
        $frameId = $game->getFrameId();
        if(empty($frameId)){
            throw new Exception('Game is active but does not have any active frame being played');
        }

        return $this->frameRepository->findOneBy(['id' => $frameId]);
    }
}