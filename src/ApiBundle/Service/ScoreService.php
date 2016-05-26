<?php

namespace ApiBundle\Service;

use ApiBundle\Entity\Frame;
use ApiBundle\Entity\Player;
use ApiBundle\Repository\GameRepository;
use ApiBundle\Repository\LaneRepository;
use ApiBundle\Repository\BallRepository;
use ApiBundle\Repository\FrameRepository;
use ApiBundle\Repository\PlayerRepository;

use Doctrine\ORM\EntityManager;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\HttpFoundation\Session\Session;

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
     * @var PlayerRepository
     */
    protected $playerRepo;

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
     * The currently played active game
     * @var int
     */
    protected $gameId;

    /**
     * @var Player[]
     */
    protected $players;

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
        $this->playerRepo = $this->em->getRepository('ApiBundle:Player');
        $this->frameRepo = $this->em->getRepository('ApiBundle:Frame');
        $this->laneRepo = $this->em->getRepository('ApiBundle:Lane');
        $this->ballRepo = $this->em->getRepository('ApiBundle:Ball');
    }

    /**
     * @return array
     */
    public function getScores()
    {
        $scores = [];
        $rawPinData = $this->getRawPinDataForEachPlayer();

        $players = $this->getPlayers();

        foreach ($players as $player) {

            /** @var Frame[] $frames */
            $frames = $rawPinData[$player->getName()];
            foreach ($frames as $frame) {

                $scores[$player->getName()][$frame->getFrameNumber()] = [
                    'score' => $this->calculateScore($frame, $frames),
                    'frame' => $frame,
                ];
            }
        }

        return $scores;
    }

    /**
     * Calculate score for the current frame with the ability to look ahead
     * This method will
     *      - return an array frames with the `calculated` score per frame
     *      - be called recursively when there is a strike
     * @param Frame $frame The current frame being considered
     * @param Frame[] $allFrames
     * @return int
     * @todo: Implement this algorithm and ensure look ahead and recursion
     */
    protected function calculateScore($frame, $allFrames)
    {
        $score = null;

        if ($frame->isStrike()) {

            $score = $frame->getPins();

            // Get the score for the next frame
            foreach ($allFrames as $idx => $allFrame) {

                if ($allFrame->getId() == $frame->getId() && isset($allFrames[$idx + 1])) {
                    $nextAvailableFrame = $allFrames[$idx + 1];
                    $score += $this->calculateScore($nextAvailableFrame, $allFrames);
                }
            }
        } elseif ($frame->isSpare()) {

            $score = $frame->getPins();
            foreach ($allFrames as $idx => $allFrame) {

                if ($allFrame->getId() == $frame->getId() && isset($allFrames[$idx + 1])) {
                    $nextAvailableFrame = $allFrames[$idx + 1];
                    $balls = $nextAvailableFrame->getBalls();
                    if (!empty($balls) && isset($balls[0])) {
                        $firstNextFrameBall = $balls[0];
                        $score += $firstNextFrameBall->getPins();
                    }
                }
            }
        } else {
            $score = $frame->getPins();
        }

        return $score;
    }

    /**
     * Get a multidimensional associative array containing data for all frames and the number of pins that were dropped
     * @return array
     */
    protected function getRawPinDataForEachPlayer()
    {
        $playerData = [];

        foreach ($this->getPlayers() as $player) {

            $frames = $this->getFramesForPlayer($player);

            foreach ($frames as &$frame) {
                $balls = $this->ballRepo->findBy(['frameId' => $frame->getId()]);
                $frame->setBalls($balls);
            }

            $playerData[$player->getName()] = $frames;
        }

        return $playerData;
    }

    /**
     * Get all the frames for this player
     * @param Player $player
     * @return \ApiBundle\Entity\Frame[]|array
     */
    protected function getFramesForPlayer(Player $player)
    {
        return $this->frameRepo->findBy(
            ['gameId' => $this->getGameId(), 'playerId' => $player->getId()]
        );
    }

    /**
     * Get all active players
     * @return \ApiBundle\Entity\Player[]|array
     */
    protected function getPlayers()
    {
        if (isset($this->players)) {
            return $this->players;
        }

        return $this->players = $this->playerRepo->findBy(['gameId' => $this->getGameId()]);
    }

    /**
     * Get the identifier of the current game
     * @return int
     */
    protected function getGameId()
    {
        if (isset($this->gameId)) {
            return $this->gameId;
        }

        return $this->gameId = $this->session->get('gameId');
    }
}