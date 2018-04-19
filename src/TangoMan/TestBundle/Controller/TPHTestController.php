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
 * @Route("/test-tph")
 */
class TPHTestController extends Controller
{

    /**
     * @return int
     */
    private function recursionTest()
    {
        $sum = 0;
        if (count($this->numbers) !== 0) {
            $sum = array_pop($this->numbers);
            $sum += $this->recursionTest();
        }

        return $sum;
    }
}
