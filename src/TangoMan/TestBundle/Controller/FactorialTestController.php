<?php

/**
 * This file is part of the TangoManCMS package.
 *
 * Copyright (c) 2018 Matthias Morin <matthias.morin@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace TangoMan\TestBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class RecursionTestController
 *
 * @author Matthias Morin <matthias.morin@gmail.com>
 * @Route("/test-factorial")
 */
class FactorialTestController extends Controller
{

    /**
     * @Route("/")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function defaultAction()
    {
        $result = [
            'iterative' => $this->iterativeFactorial(10),
            'recursive' => $this->recursiveFactorial(10),
        ];

        return new Response(json_encode($result));
    }

    /**
     * @param $n
     *
     * @return mixed
     */
    private function iterativeFactorial($n)
    {
        $result = 1;
        for ($i = $n; $i >= 1; $i--) {
            $result = $result * $i;
        }

        return $result;
    }

    /**
     * @param $n
     *
     * @return int
     */
    private function recursiveFactorial($n)
    {
        if ($n === 0) {
            return 1;
        } else {
            return $n * $this->recursiveFactorial($n - 1);
        }
    }
}
