<?php

namespace BowlingBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
        $activePlayer = $gameService->getActivePlayer();
        if ($activePlayer->getId() != $playerId) {
            throw new \Exception('UI PlayerId does not match active player, something is amiss');
        }

        $frameNumber = $gameService->getFrameNumber();

        $frameService = $this->get('service.frame');
        $playerFrame = $frameService->getFrameForPlayer($frameNumber, $playerId);
        $ballNumber = $frameService->getBallNumber($playerFrame->getId());

        // If we are on the first ball, then the player's strength will count

        if($ballNumber == 1){
            $droppedPins = rand($activePlayer->getStrength(), 10);
        }else{
            // Otherwise figure out what they threw the last time, and rand 0-n from the rest of the pins
            $firstFramePins = $frameService->getPins($playerFrame->getId(), $ballNumber);
            $droppedPins = rand(0, $firstFramePins);
        }

        $frameService->throwBall($playerFrame->getId(), $ballNumber, $droppedPins);

        // Check to see if the player threw a strike, create another ball, with no pins dropped
        if ($droppedPins == 10 && $ballNumber == 1) {
            $frameService->throwBall($playerFrame->getId(), $ballNumber, 0);
            $gameService->getNextPlayer();
        }

        // If this is the second ball, the player has completed their turn
        if ($ballNumber == 2) {
            $gameService->getNextPlayer();
        }

        // This is the last player, and their last ball, which means a new frame begins
        if ($activePlayer->getId() == $gameService->getLastPlayerId() && $ballNumber == 2) {
            $frameInc = $frameService->incrementFrame();
        }

        return $this->redirectToRoute('game_page');
    }

    /**
     * End the game
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function logoutAction()
    {
        $session = $this->get('session');
        $session->clear();
        $session->save();

        return $this->redirectToRoute('bowling_homepage');
    }
}
