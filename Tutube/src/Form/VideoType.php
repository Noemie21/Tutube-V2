<?php

namespace App\Form;

use App\Entity\Video;
use \Datetime;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VideoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('description')
            /*->add('publicationDate', null, [
                'required'   => false,
            ])
            ->add('publicationDate', DateTimeType::class, array(
                'format' => \IntlDateFormatter::SHORT,
                'input' => 'datetime',
                'widget' => 'single_text',
                'data' => new \DateTime("now")))*/
            ->add('link')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Video::class,
        ]);
    }
}
