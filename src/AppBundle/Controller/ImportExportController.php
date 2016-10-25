<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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
     * Exports user list in csv format.
     *
     * @Route("/export-csv", name="export")
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
                implode(",", $user->getRoles()),
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
        $file = $_FILES['import']['tmp_name'];
        // Security checks
        if ($_FILES['import']['error'] > 0) {
            $erreur = "Erreur lors du transfert";
            $this->get('session')->getFlashBag()->add('error',
                "Une erreur s'est produite lors du transfert. <br />Veuillez réessayer.");
            return $this->redirectToRoute( $this->getUri() );
        }
        // Extension check
        $validExtensions = ['csv'];
        $uploadExtension = strtolower(substr(strrchr($_FILES['import']['name'], '.'), 1));
        if ( !in_array($uploadExtension, $validExtensions) ) {
            $this->get('session')->getFlashBag()->add('error', "Ce format du fichier n'est pas supporté.");
            return $this->redirectToRoute( $this->getUri() );
        };
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
                // When not found persist new user
                if(!$user) {
                    $user = new User();
                    $user->setUsername($line->get('username'));
                    $user->setEmail($line->get('email'));
                    $user->setPassword($line->get('password'));
                    $user->setAvatar($line->get('avatar'));
                    $user->setRoles(explode(",", $line->get('roles')));
                    $user->setDateCreated(date_create_from_format('Y/m/d H:i:s', $line->get('date_created')));
                    $em->save($user);
                }
            }
        }

        return $this->redirectToRoute( $this->getUri() );
    }
}
