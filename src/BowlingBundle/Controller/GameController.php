<?php

namespace BowlingBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
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
     */
    public function playAction()
    {
        $gameService = $this->get('service.game');
        $players = $gameService->getPlayers();
        $activePlayer = $gameService->getActivePlayer();

        return $this->render('BowlingBundle:Game:main.ui.html.twig', [
            'players' => $players,
            'activePlayer' => $activePlayer
        ]);
    }
}
