<?php

declare(strict_types=1);

namespace PaneeDesign\DatabaseSwiftMailerBundle\Form;

use PaneeDesign\DatabaseSwiftMailerBundle\Entity\Email;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EmailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('status')
            ->add('retries')
            ->add('errorMessage');
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Email::class,
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
