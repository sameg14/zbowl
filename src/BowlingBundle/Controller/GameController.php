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
     * We are ready to play the game
     * @return Response
     */
    public function playAction()
    {
        $gameService = $this->get('service.game');
        $players = $gameService->getPlayers();
        $activePlayer = $gameService->getActivePlayer();
        $lane = $gameService->getLane();

        return $this->render('BowlingBundle:Game:main.ui.html.twig', [
            'lane' => $lane,
            'players' => $players,
            'activePlayer' => $activePlayer,
            'frameNumber' => $gameService->getFrameNumber()
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

        $droppedPins = rand($activePlayer->getStrength(), 10);

        $frameService->throwBall($playerFrame->getId(), $ballNumber, $droppedPins);

        return $this->redirectToRoute('game_page');
    }
}
