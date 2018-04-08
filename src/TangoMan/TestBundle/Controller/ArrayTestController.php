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
 * @Route("/test-array")
 */
class ArrayTestController extends Controller
{

    /**
     * @var array
     */
    private $data;

    /**
     * RecursionTestController constructor.
     */
    public function __construct()
    {
        // item_name, value, quantity, date
        $this->data = [
            ['item_4', 4, 28, '2018-04-01'],
            ['item_8', 5, 64, '2018-04-02'],
            ['item_2', 2, 45, '2018-04-03'],
            ['item_4', 4, 58, '2018-04-04'],
            ['item_2', 2, 17, '2018-04-05'],
            ['item_1', 1, 15, '2018-04-06'],
            ['item_5', 5, 58, '2018-04-07'],
            ['item_8', 5, 62, '2018-04-08'],
            ['item_6', 5, 59, '2018-04-09'],
            ['item_4', 4, 38, '2018-04-10'],
            ['item_7', 5, 60, '2018-04-11'],
            ['item_7', 5, 61, '2018-04-12'],
            ['item_2', 2, 27, '2018-04-13'],
            ['item_2', 2, 36, '2018-04-14'],
            ['item_8', 5, 63, '2018-04-15'],
            ['item_3', 3, 26, '2018-04-16'],
            ['item_9', 1, 1, '2018-04-17'],
            ['item_9', 1, 1, '2018-04-18'],
        ];
    }

    /**
     * @Route("/")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function defaultAction()
    {
        // Sort items by item_name and sum up quantity and value
        $results = [];
        foreach ($this->data as $item) {
            if (isset($results[$item[0]])) {
                $item[1] = $results[$item[0]]['value'] + $item[1];
                $item[2] = $results[$item[0]]['quantity'] + $item[2];
            }

            $results[$item[0]] = [
                'value'    => $item[1],
                'quantity' => $item[2],
            ];
        }

        // Computing total
        foreach ($results as $item => $details) {
            $results[$item]['total'] = $details['quantity'] * $details['value'];
        }

        ksort($results);

        return new Response(json_encode($results));
    }
}
