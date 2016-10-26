<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\File;

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
        $delimiter = ";";
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
     * @Method("POST")
     */
    public function importAction(Request $request)
    {
        $form = $this->createFormBuilder()
            ->setAction('import')
            ->add('Fichier', FileType::class, [
//                'constraints' => [
//                    new File([
//                        'maxSize' => '10',
//                        'maxSizeMessage' => "Le fichier que vous tentez d'importer est trop volumineux",
//                        'mimeTypes' => 'application/vnd.ms-excel',
//                        'mimeTypesMessage' => "Vous ne pouvez importer que des fichiers de type CSV"
//                    ]),
//                ]
            ])
            ->getForm();

        if ($request->isMethod('POST')) {
//        if ( $form->isSubmitted() && $form->isValid() ) {

            $form->handleRequest($request);
            $file = $request->files->get('form')['Fichier'];

            // Upload success check
            if (!$file->isValid()) {
                $this->get('session')->getFlashBag()->add('error',
                    "Une erreur s'est produite lors du transfert. <br />Veuillez réessayer.");
                return $this->redirectToRoute('user_index');
            }

            // Security checks
            $validExtensions = ['csv', 'tsv'];
            $clientExtension = $file->getClientOriginalExtension();

            if ($file->getClientMimeType() !== 'application/vnd.ms-excel' &&
                !in_array($clientExtension, $validExtensions)) {

                $this->get('session')->getFlashBag()->add('error', "Ce format du fichier n'est pas supporté.");
                return $this->redirectToRoute('user_index');
            } else {
                // Get CSV reader service
                $reader = $this->get('services.csv_reader');
                $counter = 0;
                $dupes = 0;
                // File check
                if (is_file($file)) {
                    // Init reader service
                    $reader->init($file, 0, ";");
                    // Load user entity
                    $em = $this->get('em');
                    $users = $em->repository('AppBundle:User');
                    while (false !== ($line = $reader->readLine())) {
                        // Check if user with same email already exist
                        $user = $users->findOneByEmail($line->get('email'));
                        // When not found persist new user
                        if (!$user) {
                            $counter++;
                            $user = new User();
                            $user->setUsername($line->get('username'));
                            $user->setEmail($line->get('email'));
                            $user->setPassword($line->get('password'));
                            $user->setAvatar($line->get('avatar'));
                            $user->setRoles(explode(",", $line->get('roles')));
                            $user->setDateCreated(date_create_from_format('Y/m/d H:i:s', $line->get('date_created')));
                            $em->save($user);
                        } else {
                            $dupes++;
                        }
                    }
                }

                if ($counter > 1) {
                    $success = "$counter comptes utilisateur ont été importés.";
                } else {
                    $success = "Aucun compte utilisateur n'a été importé.";
                }

                $this->get('session')->getFlashBag()->add('success', $success);
            }
        }

//        return $this->redirect($request->getUri());
        return $this->redirectToRoute('user_index');
    }
}
