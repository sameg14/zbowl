<?php

namespace BowlingBundle\Controller;

use ApiBundle\Entity\Ball;
use ApiBundle\Entity\Game;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class GameController
 * @package BowlingBundle\Controller
 */
class GameController extends Controller
{
    /**
     * Show game start page
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $session = $this->get('session');
        $gameId = $session->get('gameId');
        $isGameActive = $session->get('isGameActive');

        if (!empty($gameId) && $isGameActive === true) {

            return $this->redirectToRoute('game_page');
        } else {

            // Get a list of all available lanes
            $laneService = $this->get('service.lane');
            $availableLanes = $laneService->getAllAvailableLanes();

            // Show start game page
            return $this->render('BowlingBundle:Game:new.game.page.html.twig', [
                'availableLanes' => $availableLanes
            ]);
        }
    }

    /**
     * Handle the starting of a game
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Exception
     */
    public function startAction(Request $request)
    {
        $gameService = $this->get('service.game');
        $players = $request->get('player_names');
        $laneId = $request->get('lane_id');

        $gameService->startGame($players, $laneId);

        return $this->redirectToRoute('game_page');
    }

    /**
     * We are ready to play the game. This is the main UI
     * @return Response
     */
    public function playAction()
    {
        $frameService = $this->get('service.frame');
        $gameService = $this->get('service.game');
        $scoreService = $this->get('service.score');

        $players = $gameService->getPlayers();
        $activePlayer = $gameService->getActivePlayer();
        $lane = $gameService->getLane();

        $frameNumber = $gameService->getFrameNumber();
        $playerFrame = $frameService->getFrameForPlayer($frameNumber, $activePlayer->getId());
        $ballNumber = $frameService->getBallNumber($playerFrame->getId());

        $scores = $scoreService->getScores();

        return $this->render('BowlingBundle:Game:main.ui.html.twig', [
            'lane' => $lane,
            'players' => $players,
            'activePlayer' => $activePlayer,
            'frameNumber' => $gameService->getFrameNumber(),
            'ballNumber' => $ballNumber,
            'scores' => $scores
        ]);
    }

    /**
     * Throw a ball and knock over some pins
     * @param Request $request
     * @throws \Exception
     * @return Response
     */
    public function throwBallAction(Request $request)
    {
        $playerId = $request->get('player_id');

        $gameService = $this->get('service.game');
        $frameService = $this->get('service.frame');

        $activePlayer = $gameService->getActivePlayer();
        if ($activePlayer->getId() != $playerId) {
            throw new \Exception('UI PlayerId does not match active player, something is amiss');
        }

        $frameNumber = $gameService->getFrameNumber();
        $playerFrame = $frameService->getFrameForPlayer($frameNumber, $playerId);
        $ballNumber = $frameService->getBallNumber($playerFrame->getId());

        // If we are on the first ball, then the player's strength will count
        if ($ballNumber == Ball::FIRST) {

            $droppedPins = rand($activePlayer->getStrength(), Game::MAX_PINS);

        } else { // Otherwise figure out what they threw the last time, and rand 0-n from the rest of the pins

            $firstFramePins = $frameService->getPins($playerFrame->getId(), $ballNumber - 1);
            $droppedPins = rand(0, Game::MAX_PINS - $firstFramePins);
        }

        // Record the ball throw for this particular player's frame
        $frameService->throwBall($playerFrame->getId(), $ballNumber, $droppedPins);

        // Check to see if the player threw a strike, create another ball, with no pins dropped
        if ($droppedPins == Game::MAX_PINS && $ballNumber == Ball::FIRST) {
            $frameService->throwBall($playerFrame->getId(), $ballNumber, 0);
            $gameService->getNextPlayer();
        }

        // If this is the second ball, the player has completed their turn
        if ($ballNumber == Ball::SECOND) {
            $gameService->getNextPlayer();
        }

        // This is the last player, and their last ball, which is the signal for the start of a new frame
        if ($activePlayer->getId() == $gameService->getLastPlayerId() && $ballNumber == Ball::SECOND) {
            $frameService->incrementFrame();
        }

        return $this->redirectToRoute('game_page');
    }

    /**
     * End the game
     * @return RedirectResponse
     */
    public function logoutAction()
    {
        $session = $this->get('session');
        $session->clear();
        $session->save();

        return $this->redirectToRoute('bowling_homepage');
    }
}
