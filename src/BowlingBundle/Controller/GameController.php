<?php

namespace BowlingBundle\Controller;

use ApiBundle\Entity\Game;
use ApiBundle\Entity\Player;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

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
        $session = $this->get('session');
        $gameId = $session->get('gameId');
        $isGameActive = $session->get('isGameActive');

        if (empty($gameId) && empty($isGameActive)) {

            $em = $this->getDoctrine()->getManager();

            $playerNames = $request->get('player_names');
            if (empty($playerNames)) {
                throw new \Exception('Player names cannot be empty');
            }

            $players = explode(",", $playerNames);

            $laneId = $request->get('lane_id');

            $game = new Game();
            $game->setIsActive(true);
            $game->setLaneId($laneId);
            $game->setDateStarted(new \DateTime());

            $em->persist($game);
            $em->flush();
            $gameId = $game->getId();

            if (empty($gameId)) {
                throw new \Exception('Game cannot be started');
            }

            // Mark this lane as used
            $laneRepo = $this->getDoctrine()->getManager()->getRepository('ApiBundle:Lane');
            $lane = $laneRepo->findOneBy(['id' => $laneId]);
            $lane->setIsAvailable(false);
            $em->persist($lane);
            $em->flush();

            // Create players and add them to the game
            foreach ($players as $playerName) {
                $playerName = trim($playerName);
                $player = new Player();
                $player->setGameId($gameId);
                $player->setName($playerName);
                $player->setDateCreated(new \DateTime());
                $em->persist($player);
                $em->flush();
            }

            $session->set('gameId', $gameId);
            $session->set('isGameActive', true);
        }

        return $this->redirectToRoute('game_page');
    }


    /**
     * We are ready to play the game
     */
    public function playAction()
    {
        return $this->render('BowlingBundle:Game:main.ui.html.twig');
    }
}
