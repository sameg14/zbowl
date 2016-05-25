<?php

namespace ApiBundle\Service;

use \Exception;
use ApiBundle\Entity\Lane;
use ApiBundle\Entity\Game;
use ApiBundle\Entity\Frame;
use ApiBundle\Entity\Player;
use ApiBundle\Repository\GameRepository;
use ApiBundle\Repository\PlayerRepository;
use ApiBundle\Repository\LaneRepository;

use Doctrine\ORM\EntityManager;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\HttpFoundation\Session\Session;

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
     * @var Session
     */
    protected $session;

    /**
     * @var GameRepository
     */
    protected $gameRepo;

    /**
     * @var LaneRepository
     */
    protected $laneRepo;

    /**
     * @var PlayerRepository
     */
    protected $playerRepo;

    /**
     * Local in memory copy of players
     * @var Player[]
     */
    protected $players;

    /**
     * What is the identifier for the last player
     * @var int
     */
    protected $lastPlayerId;

    /**
     * Identifier of the current game being played
     * @var int
     */
    protected $gameId;

    /**
     * Is this game active?
     * @var bool
     */
    protected $isGameActive;

    /**
     * GameService constructor.
     * @param Registry $registry
     * @param Session $session
     */
    public function __construct(Registry $registry, Session $session)
    {
        $this->em = $registry->getManager();
        $this->laneRepo = $registry->getRepository('ApiBundle:Lane');
        $this->session = $session;
        $this->playerRepo = $registry->getRepository('ApiBundle:Player');
        $this->gameRepo = $registry->getRepository('ApiBundle:Game');
    }

    /**
     * Start playing this game
     * @param string $playerNames Comma separated list of players
     * @param int $laneId Which lane do they want to play on
     * @throws Exception
     * @return bool
     */
    public function startGame($playerNames, $laneId)
    {
        $gameId = $this->getGameId();
        $isGameActive = $this->isGameActive();

        // The game is already in progress
        if (!empty($gameId) && $isGameActive === true) {
            return false;
        }

        if (empty($playerNames)) {
            throw new Exception('Player names cannot be empty');
        }

        $players = explode(",", $playerNames);

        $game = new Game();
        $game->setIsActive(true);
        $game->setLaneId($laneId);
        $game->setFrame(1);
        $game->setDateStarted(new \DateTime());

        $this->em->persist($game);
        $this->em->flush();
        $gameId = $game->getId();

        if (empty($gameId)) {
            throw new \Exception('Game cannot be started');
        }

        // Mark this lane as used
        $lane = $this->laneRepo->findOneBy(['id' => $laneId]);
        $lane->setIsAvailable(false);
        $this->em->persist($lane);
        $this->em->flush();

        // Create players and add them to the game
        foreach ($players as $index => $playerName) {

            $playerName = trim($playerName);

            $player = new Player();
            $player->setGameId($gameId);
            $player->setName($playerName);
            $player->setStrength(rand(4, 10));
            $this->em->persist($player);
            $this->em->flush();

            if ($index == 0) {
                $firstPlayer = $player;
            }
        }

        // Create a new frame for the first player
        $frame = new Frame();
        $frame->setGameId($gameId);
        $frame->setPlayerId(isset($firstPlayer) ? $firstPlayer->getId() : null);
        $frame->setFrameNumber(1);
        $frame->setIsActive(1);
        $this->em->persist($frame);
        $this->em->flush();

        $this->session->set('gameId', $gameId);
        $this->session->set('isGameActive', true);

        return true;
    }

    /**
     * Get the player whose turn it is
     * @throws Exception
     * @return Player
     */
    public function getActivePlayer()
    {
        $activePlayerId = $this->session->get('activePlayerId');

        // We have no active player, this is the beginning of the game
        if (empty($activePlayerId)) {

            $allPlayers = $this->getPlayers();
            $player = array_shift($allPlayers);
            $this->session->set('activePlayerId', $player->getId());
        } else {

            $player = $this->playerRepo->findOneBy(['id' => $activePlayerId]);
        }

        if (empty($player)) {
            throw new \Exception('Cannot get active player');
        }

        return $player;
    }

    /**
     * Attempt to get the next player
     * @throws Exception
     * @return Player
     */
    public function getNextPlayer()
    {
        $activePlayer = $this->getActivePlayer();
        $allPlayers = $this->getPlayers();

        foreach ($allPlayers as $index => $player) {

            if ($player->getId() == $activePlayer->getId()) {

                // get the next player
                $nextPlayer = isset($allPlayers[$index + 1]) ? $allPlayers[$index + 1] : $allPlayers[0];

                // Mark them as active
                $this->session->set('activePlayerId', $nextPlayer->getId());

                return $nextPlayer;
            }
        }

        throw new Exception('Cannot get next player');
    }

    /**
     * Get all players that are playing this particular game
     * @throws Exception
     * @return Player[]
     */
    public function getPlayers()
    {
        if (isset($this->players)) {
            return $this->players;
        }

        $players = $this->playerRepo->findBy(
            ['gameId' => $this->getGameId()],
            ['id' => 'asc']
        );
        if (empty($players)) {
            throw new \Exception('No players found');
        }

        return $this->players = $players;
    }

    /**
     * Get the numeric identifier for the last player
     * @return int
     */
    public function getLastPlayerId()
    {
        if (!isset($this->lastPlayerId)) {
            $players = $this->getPlayers();
            $sizeofPlayers = sizeof($players) - 1;
            $lastPlayer = $players[$sizeofPlayers];
            $this->lastPlayerId = $lastPlayer->getId();
        }

        return $this->lastPlayerId;
    }

    /**
     * Get the lane this game is being planed on
     * @throws Exception
     * @return Lane
     */
    public function getLane()
    {
        $game = $this->getGame();
        $laneId = $game->getLaneId();
        if (empty($laneId)) {
            throw new Exception('No lane assigned to this game');
        }

        return $this->laneRepo->findOneBy(['id' => $laneId]);
    }

    /**
     * Get the number of the current frame we are on
     * @return int
     */
    public function getFrameNumber()
    {
        return $this->getGame()->getFrame();
    }


    /**
     * Is this game active
     * @return bool
     */
    public function isGameActive()
    {
        if (!isset($this->isGameActive)) {
            $this->isGameActive = $this->session->get('isGameActive');
        }

        return $this->isGameActive;
    }

    /**
     * Get the current game identifier of the game being played
     * @return int|mixed
     */
    public function getGameId()
    {
        if (!isset($this->gameId)) {
            $this->gameId = $this->session->get('gameId');
        }

        return $this->gameId;
    }

    /**
     * Get the active game being played
     * @return Game
     */
    public function getGame()
    {
        return $this->gameRepo->findOneBy([
            'id' => $this->getGameId(),
            'isActive' => true
        ]);
    }
}