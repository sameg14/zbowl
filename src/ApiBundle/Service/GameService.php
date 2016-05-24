<?php

namespace ApiBundle\Service;

use ApiBundle\Entity\Game;
use ApiBundle\Entity\Player;
use Doctrine\Bundle\DoctrineBundle\Registry;
use ApiBundle\Repository\LaneRepository;
use Doctrine\ORM\EntityManager;

/**
 * Class GameService
 * @package ApiBundle\Service
 */
class GameService
{

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var LaneRepository
     */
    protected $laneRepo;


    public function __construct(Registry $registry)
    {
        $this->em = $registry->getManager();
        $this->laneRepo = $registry->getRepository('ApiBundle:Lane');
    }
}