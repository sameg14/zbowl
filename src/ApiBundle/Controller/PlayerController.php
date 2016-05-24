<?php

namespace ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class PlayerController
 * @package ApiBundle\Controller
 */
class PlayerController extends Controller
{
    /**
     * Register new players for this game
     * @param Request $request
     */
    public function registerAction(Request $request)
    {
        $players = $request->get('players');
    }
}
