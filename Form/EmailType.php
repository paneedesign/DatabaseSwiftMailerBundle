<?php

declare(strict_types=1);

namespace PaneeDesign\DatabaseSwiftMailerBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EmailType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('status')
            ->add('retries')
            ->add('errorMessage');
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => 'PaneeDesign\DatabaseSwiftMailerBundle\Entity\Email',
        ]);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'ped_bundle_databaseswiftmailerbundle_email';
    }
}
