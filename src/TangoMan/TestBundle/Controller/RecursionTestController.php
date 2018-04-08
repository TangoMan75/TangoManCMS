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
 * @Route("/test-recursion")
 */
class RecursionTestController extends Controller
{

    /**
     * @var array
     */
    private $numbers;

    /**
     * RecursionTestController constructor.
     */
    public function __construct()
    {
        $this->numbers = [1, 2, 3, 4];
    }

    /**
     * @Route("/")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function defaultAction()
    {
        $result = [
            'array_sum' => array_sum($this->numbers),
            'for'       => $this->forTest(),
            'foreach'   => $this->forEachTest(),
            'while'     => $this->whileTest(),
            'recursion' => $this->recursionTest(),
        ];

        return new Response(json_encode($result));
    }

    /**
     * @return int
     */
    private function forTest()
    {
        $sum = 0;
        for ($i = 0; $i < count($this->numbers); $i++) {
            $sum += $this->numbers[$i];
        }

        return $sum;
    }

    /**
     * @return int
     */
    private function forEachTest()
    {
        $sum = 0;
        foreach ($this->numbers as $value) {
            $sum += $value;
        }

        return $sum;
    }

    /**
     * @return int
     */
    private function whileTest()
    {
        $sum = 0;
        $i   = 0;
        while ($i < count($this->numbers)) {
            $sum += $this->numbers[$i];
            $i++;
        }

        return $sum;
    }

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
