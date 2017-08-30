<?php

namespace TangoMan\TestBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use TangoMan\ListManagerBundle\Model\SearchForm;
use TangoMan\ListManagerBundle\Model\SearchInput;
use TangoMan\ListManagerBundle\Model\SearchOption;
use TangoMan\ListManagerBundle\Model\Fields;
use TangoMan\ListManagerBundle\Model\OrderField;
use Symfony\Component\HttpFoundation\Response;
use TangoMan\MenuBundle\Model\Item;
use TangoMan\MenuBundle\Model\Menu;

/**
 * @Route("/test")
 */
class DefaultController extends Controller
{
    /**
     * @Route("/app-request")
     */
    public function indexAction()
    {
        return $this->render('@TangoManTest/Default/index.html.twig');
    }

    /**
     * @Route("/search-form-builder")
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
     * @Route("/order-builder")
     */
    public function orderBuilderAction()
    {
        $fields = new Fields();
        $fields->setRoute('app_admin_user_index');

        $field = new OrderField();
        $field->setName('username')
            ->setLabel('Utilisateur');
        $fields->addField($field);

        $field = new OrderField();
        $field->setName('email')
            ->setLabel('Email');
        $fields->addField($field);

        $field = new OrderField();
        $field->setName('c-posts')
            ->setLabel('Articles');
        $fields->addField($field);

        $field = new OrderField();
        $field->setName('c-posts')
            ->setLabel('Médias');
        $fields->addField($field);

        $field = new OrderField();
        $field->setName('c-comments')
            ->setLabel('Commentaires');
        $fields->addField($field);

        $field = new OrderField();
        $field->setName('created')
            ->setLabel('Création');
        $fields->addField($field);

        $field = new OrderField();
        $field->setName('c-password')
            ->setLabel('Actif');
        $fields->addField($field);

        $fields = json_encode($fields);

        return new Response($fields);
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
        $item->setLabel('Sites')
            ->setRoute('app_admin_site_index')
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
}
