<?php

namespace BowlingBundle\Controller;

use ApiBundle\Entity\Game;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

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

            // Show start game page
            return $this->render('BowlingBundle:Game:new.game.page.html.twig');
        }
    }


    /**
     * Start a new game
     */
    public function startAction()
    {
        $session = $this->get('session');
        $gameId = $session->get('gameId');
        $isGameActive = $session->get('isGameActive');

        if (empty($gameId) && empty($isGameActive)) {

            $game = new Game();
            $game->getIsActive(true);


            $gameRepo = $this->getDoctrine()->getManager()->getRepository('ApiBundle:Game');

        }
    }
}
