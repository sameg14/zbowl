<?php

namespace ApiBundle\Service;

use ApiBundle\Entity\Frame;
use Doctrine\ORM\EntityManager;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\HttpFoundation\Session\Session;
use ApiBundle\Repository\GameRepository;
use ApiBundle\Repository\FrameRepository;
use ApiBundle\Repository\LaneRepository;
use ApiBundle\Repository\BallRepository;

/**
 * Class ScoreService gets scores for players in realtime
 * @package ApiBundle\Service
 */
class ScoreService
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var Session
     */
    protected $session;

    /**
     * @var GameRepository
     */
    protected $gameRepo;

    /**
     * @var FrameRepository
     */
    protected $frameRepo;

    /**
     * @var LaneRepository
     */
    protected $laneRepo;

    /**
     * @var BallRepository
     */
    protected $ballRepo;

    /**
     * ScoreService constructor.
     * @param Registry $registry
     * @param Session $session
     */
    public function __construct(Registry $registry, Session $session)
    {
        $this->session = $session;
        $this->em = $registry->getManager();
        $this->gameRepo = $this->em->getRepository('ApiBundle:Game');
        $this->frameRepo = $this->em->getRepository('ApiBundle:Frame');
        $this->laneRepo = $this->em->getRepository('ApiBundle:Lane');
        $this->ballRepo = $this->em->getRepository('ApiBundle:Ball');
    }


    /**
     * @param $playerId
     * @return array
     */
    public function getScoresForPlayer($playerId)
    {
        $scores = [];

        for($i = 0; $i < 10; $i++){

            $scores[$i] = array(
                'ball1' => '',
                'ball2' => '',
                'score' => ''
            );
        }

        return $scores;
    }
}