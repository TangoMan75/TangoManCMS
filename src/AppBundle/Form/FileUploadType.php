<?php

/**
 * This file is part of the TangoManCMS package.
 *
 * Copyright (c) 2018 Matthias Morin <matthias.morin@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;

class FileUploadType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'file',
                FileType::class,
                [
                    'label'       => false,
                    'constraints' => [
                        new NotBlank(),
                        new File(
                            [
                                //                        'maxSize' => '1024k',
                                //                        'maxSizeMessage' => "Le fichier que vous tentez d'importer est trop volumineux",
                            ]
                        ),
                    ],
                ]
            );
    }

    public function getName()
    {
        return 'fileupload';
    }
}
