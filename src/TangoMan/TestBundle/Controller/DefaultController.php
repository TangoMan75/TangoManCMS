<?php

namespace TangoMan\TestBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use TangoMan\ListManagerBundle\Model\SearchForm;
use TangoMan\ListManagerBundle\Model\SearchInput;
use TangoMan\ListManagerBundle\Model\SearchOption;
use TangoMan\ListManagerBundle\Model\Th;
use TangoMan\ListManagerBundle\Model\Thead;
use Symfony\Component\HttpFoundation\Response;
use TangoMan\MenuBundle\Model\Item;
use TangoMan\MenuBundle\Model\Menu;

/**
 * @Route("/test")
 */
class DefaultController extends Controller
{
    /**
     * @Route("/")
     */
    public function indexAction()
    {
        return $this->render('@TangoManTest/Default/index.html.twig');
    }

    /**
     * @Route("/app-request")
     */
    public function appRequestAction()
    {
        return $this->render('@TangoManTest/app-request.html.twig');
    }

    /**
     * @Route("/navbar")
     */
    public function navbarAction()
    {
        return $this->render('@TangoManTest/navbar.html.twig');
    }

    /**
     * @Route("/search-builder")
     */
    public function searchFormBuilderAction()
    {
        $form = new SearchForm();

        // Number
        $input = new SearchInput();
        $input->setLabel('Id')
            ->setName('e-id')
            ->setType('number');
        $form->addInput($input);

        // Text
        $input = new SearchInput();
        $input->setLabel('Utilisateur')
            ->setName('user-username');
        $form->addInput($input);

        // Text
        $input = new SearchInput();
        $input->setLabel('Email')
            ->setName('user-email');
        $form->addInput($input);

        // Select
        $input = new SearchInput();
        $input->setLabel('Status')
            ->setName('n-password');
        $option = new SearchOption();
        $option->setName('Tous');
        $input->addOption($option);
        $option = new SearchOption();
        $option->setName('Actif')
            ->setValue('true');
        $input->addOption($option);
        $option = new SearchOption();
        $option->setName('Inactif')
            ->setValue('false');
        $input->addOption($option);
        $form->addInput($input);

        // Select
        $input = new SearchInput();
        $input->setLabel('Role')
            ->setName('roles-type');
        $option = new SearchOption();
        $option->setName('Tous');
        $input->addOption($option);
        $option = new SearchOption();
        $option->setName('Super Admin')
            ->setValue('ROLE_SUPER_ADMIN');
        $input->addOption($option);
        $option = new SearchOption();
        $option->setName('Admin')
            ->setValue('ROLE_ADMIN');
        $input->addOption($option);
        $option = new SearchOption();
        $option->setName('Super Utilisateur')
            ->setValue('ROLE_SUPER_USER');
        $input->addOption($option);
        $option = new SearchOption();
        $option->setName('Utilisateur')
            ->setValue('ROLE_USER');
        $input->addOption($option);
        $form->addInput($input);

        // Submit
        $input = new SearchInput();
        $input->setLabel('Filtrer')
            ->setType('submit')
            ->setIcon('glyphicon glyphicon-search');
        $form->addInput($input);

        $form = json_encode($form);

        return new Response($form);
    }

    /**
     * @Route("/isgranted/{role}")
     */
    public function roleServiceAction($role)
    {
        $permission = $this->get('security.authorization_checker')->isGranted($role);
        return new Response(json_encode($permission));
    }

    /**
     * @Route("/thead-builder")
     */
    public function theadBuilderAction()
    {
        $thead = new Thead();
        $thead->setClass('thead');

        $item = new Th();
        $item->setName('username')
            ->setLabel('Utilisateur');
        $thead->addTh($item);

        $item = new Th();
        $item->setName('email')
            ->setLabel('Email');
        $thead->addTh($item);

        $item = new Th();
        $item->setName('c-posts')
            ->setLabel('Articles');
        $thead->addTh($item);

        $item = new Th();
        $item->setName('c-posts')
            ->setLabel('Médias');
        $thead->addTh($item);

        $item = new Th();
        $item->setName('c-comments')
            ->setLabel('Commentaires');
        $thead->addTh($item);

        $item = new Th();
        $item->setName('created')
            ->setLabel('Création');
        $thead->addTh($item);

        $item = new Th();
        $item->setName('c-password')
            ->setLabel('Actif');
        $thead->addTh($item);

        $item = new Th();
        $item->setName('actions')
            ->setLabel('Actions')
            ->setRoles(['ROLE_USER', 'ROLE_ADMIN', 'ROLE_MANAGER']);
        $thead->addTh($item);

        $thead = json_encode($thead);

        return new Response($thead);
    }

    /**
     * @Route("/menu-builder")
     */
    public function menuBuilderAction()
    {
        $menu = new Menu();

        $item = new Item();
        $item->setLabel('Tableau de bord')
            ->setRoute('app_admin_admin_index')
            ->setIcon('glyphicon glyphicon-dashboard');
        $menu->addItem($item);

        $item = new Item();
        $item->setDivider(true);
        $menu->addItem($item);

        $item = new Item();
        $item->setLabel('Sites')
            ->setRoute('app_admin_site_index')
            ->setActive('app_admin_site')
            ->setIcon('glyphicon glyphicon-home');
        $menu->addItem($item);

        $item = new Item();
        $item->setLabel('Pages')
            ->setRoute('app_admin_page_index')
            ->setIcon('glyphicon glyphicon-file');
        $menu->addItem($item);

        $item = new Item();
        $item->setLabel('Sections')
            ->setRoute('app_admin_section_index')
            ->setIcon('glyphicon glyphicon-list-alt');
        $menu->addItem($item);

        $item = new Item();
        $item->setLabel('Galeries')
            ->setRoute('app_admin_gallery_index')
            ->setIcon('glyphicon glyphicon-picture');
        $menu->addItem($item);

        $item = new Item();
        $item->setDivider(true);
        $menu->addItem($item);

        $item = new Item();
        $item->setLabel('Articles')
            ->setRoute('app_admin_post_index')
            ->setIcon('glyphicon glyphicon-text-color');
        $menu->addItem($item);

        $item = new Item();
        $item->setLabel('Medias')
            ->setRoute('app_admin_media_index')
            ->setIcon('glyphicon glyphicon-music');
        $menu->addItem($item);

        $item = new Item();
        $item->setLabel('Commentaires')
            ->setRoute('app_admin_comment_index')
            ->setIcon('glyphicon glyphicon-comment');
        $menu->addItem($item);

        $item = new Item();
        $item->setLabel('Étiquettes')
            ->setRoute('app_admin_tag_index')
            ->setIcon('glyphicon glyphicon-tag');
        $menu->addItem($item);

        $item = new Item();
        $item->setDivider(true);
        $menu->addItem($item);

        $item = new Item();
        $item->setLabel('Utilisateurs')
            ->setRoute('app_admin_user_index')
            ->setIcon('glyphicon glyphicon-user');
        $menu->addItem($item);

        $item = new Item();
        $item->setLabel('Rôles')
            ->setRoute('app_admin_role_index')
            ->setIcon('glyphicon glyphicon-king');
        $menu->addItem($item);

        $item = new Item();
        $item->setLabel('Privilèges')
            ->setRoute('app_admin_privilege_index')
            ->setIcon('glyphicon glyphicon-thumbs-up');
        $menu->addItem($item);

        $menu = json_encode($menu);

        return new Response($menu);
    }

    /**
     * @Route("/navbar-builder")
     */
    public function navbarBuilderAction()
    {
        $menu = new Menu();
        $subMenu = new Menu();

        $item = new Item();
        $item->setLabel('Tableau de bord')
            ->setRoute('app_admin_admin_index')
            ->setIcon('glyphicon glyphicon-dashboard')
            ->addRole('ROLE_ADMIN');
        $menu->addItem($item);

        $item = new Item();
        $item->setLabel('Sociétés')
            ->setRoute('admin_company')
            ->setActive('admin_company')
            ->setIcon('glyphicon glyphicon-home')
            ->addRole('ROLE_ADMIN');
        $menu->addItem($item);

        $item = new Item();
        $item->setLabel('Sites')
            ->setRoute('admin_local')
            ->setActive('admin_local')
            ->addRole('admin_local');
        $subMenu->addItem($item);

        $item = new Item();
        $item->setLabel('Services')
            ->setRoute('admin_service')
            ->setActive('admin_service')
            ->addRole('admin_service');
        $subMenu->addItem($item);

        $item = new Item();
        $item->setLabel('Parkings')
            ->setRoute('admin_pool')
            ->setActive('admin_pool')
            ->addRole('admin_pool');
        $subMenu->addItem($item);

        $item = new Item();
        $item->setLabel('Véhicules')
            ->setRoute('admin_vehicle')
            ->setActive('admin_vehicle')
            ->addRole('admin_vehicle');
        $subMenu->addItem($item);

        $item = new Item();
        $item->setLabel('Fournisseurs')
            ->setRoute('admin_provider')
            ->setActive('admin_provider')
            ->addRole('admin_provider');
        $subMenu->addItem($item);

        $item = new Item();
        $item->setLabel('Assurances')
            ->setRoute('admin_insurance')
            ->setActive('admin_insurance')
            ->addRole('admin_insurance');
        $subMenu->addItem($item);

        $item = new Item();
        $item->setLabel('Révisions')
            ->setRoute('admin_carService')
            ->setActive('admin_carService')
            ->addRole('admin_carService');
        $subMenu->addItem($item);

        $item = new Item();
        $item->setLabel('Utilisateurs')
            ->setRoute('admin_user_list')
            ->setActive('admin_user_list')
            ->addRole('admin_user_list');
        $subMenu->addItem($item);

        $item = new Item();
        $item->setLabel('Espace de gestion')
            ->setIcon('caret')
            ->setSubMenu($subMenu)
            ->addRole('ROLE_ADMIN')
            ->addRole('ROLE_MANAGER');
        $menu->addItem($item);

        $menu = json_encode($menu);

        return new Response($menu);
    }
}
