<?php

namespace ApiBundle\Service;

use ApiBundle\Repository\LaneRepository;

use Doctrine\Bundle\DoctrineBundle\Registry;

/**
 * Class LaneService is responsible for actions against a lane
 * @package ApiBundle\Service
 */
class LaneService
{
    /**
     * @var LaneRepository
     */
    protected $laneRepo;

    /**
     * LaneService constructor.
     * @param Registry $registry
     */
    public function __construct(Registry $registry)
    {
        $this->laneRepo = $registry->getRepository('ApiBundle:Lane');
    }

    /**
     * Get all available lanes
     * @return \ApiBundle\Entity\Lane[]
     */
    public function getAllAvailableLanes()
    {
        return $this->laneRepo->findBy(['isAvailable' => true]);
    }
}