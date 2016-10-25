<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class UserController
 *
 * @package AppBundle\Controller
 * @Route("/admin")
 */
class ImportExportController extends Controller
{
    /**
     * Exports user list in json format.
     *
     * @Route("/export-json", name="user_export_json")
     * @Method("GET")
     */
    public function exportJsonAction()
    {
        $users = $this->get('em')->repository('AppBundle:User')->findBy([], ['username' => 'ASC']);

        $response = serialize($users);

        return new JsonResponse($response, 200, [
            'Content-Type' => 'application/force-download',
            'Content-Disposition' => 'attachment; filename="export_users.json"'
        ]);
    }

    /**
     * Exports user list in csv format.
     *
     * @Route("/export-csv", name="user_export_csv")
     * @Method("GET")
     */
    public function exportCSVAction()
    {
        $users = $this->get('em')->repository('AppBundle:User')->findBy([], ['username' => 'ASC']);
        $delimiter = ';';
        $handle = fopen('php://memory', 'r+');

        fputcsv($handle, [
            'User Id',
            'Utilisateur',
            'Email',
            'Date de Création'
        ], $delimiter);

        foreach ($users as $user) {
            fputcsv($handle, [
                $user->getId(),
                $user->getusername(),
                $user->getEmail(),
                $user->getDateCreated()->format('d/m/Y H:i:s')
            ], $delimiter);
        }

        rewind($handle);
        $response = stream_get_contents($handle);
        fclose($handle);

        return new Response($response, 200, [
            'Content-Type' => 'application/force-download',
            'Content-Disposition' => 'attachment; filename="export_users.csv"'
        ]);
    }
}