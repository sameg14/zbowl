<?php

namespace ApiBundle\Service;

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
}