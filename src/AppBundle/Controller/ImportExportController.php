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
        $delimiter = "\t";
        $handle = fopen('php://memory', 'r+');

        fputcsv($handle, [
            'user_id',
            'username',
            'email',
            'password',
            'avatar',
            'roles',
            'date_created'
        ], $delimiter);

        foreach ($users as $user) {
            fputcsv($handle, [
                $user->getId(),
                $user->getUsername(),
                $user->getEmail(),
                $user->getPassword(),
                $user->getAvatar(),
                json_encode($user->getRoles()),
                $user->getDateCreated()->format('Y/m/d H:i:s')
            ], $delimiter);
        }

        rewind($handle);
        $response = stream_get_contents($handle);
        fclose($handle);

        return new Response($response, 200, [
            'Content-Type' => 'application/force-download',
            'Content-Disposition' => 'attachment; filename="users.csv"'
        ]);
    }

    /**
     * @Route("/import", name="import")
     */
    public function importAction()
    {
        // Get file
        $file = $this->getParameter('kernel.root_dir').'/../web/users.csv';
        // Get CSV reader service
        $reader = $this->get('services.csv_reader');
        // File check
        if (is_file($file)) {
            // Init reader service
            $reader->init($file, 0, "\t");
            // Load user entity
            $em = $this->get('em');
            $users = $em->repository('AppBundle:User');
            while (false !== ($line = $reader->readLine())) {
                // Check if user with same email already exist
                $user = $users->findOneByEmail($line->get('email'));
                // When not found persists new user
                if(!$user) {
                    $user = new User();
                    $user->setUsername($line->get('username'));
                    $user->setEmail($line->get('email'));
                    $user->setPassword($line->get('password'));
                    $user->setAvatar($line->get('avatar'));
//                    $user->setRoles($line->get('roles'));
//                    $user->setDateCreated( date_create_from_format('Y/m/d H:i:s', $line->get('date_created')) );
                    $em->save($user);
                }
            }
        }

        return $this->redirectToRoute('user_index');
    }
}