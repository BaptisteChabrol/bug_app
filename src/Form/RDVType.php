<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfonycasts\DynamicForms\DynamicFormBuilder;

class RDVType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder = new DynamicFormBuilder($builder);

        $builder
            ->add('fichierAudio', FileType::class, [
                'label' => 'Fichier audio',
                'required' => false,
                'mapped' => false,
                'help' => 'Merci de mettre un fichier de type mp3, mp4, ogg ou flac de moins de 200Mo auquel cas le fichier ne sera pas téléchargé.',
                'constraints' => [
                    new Assert\File(
                        maxSize: '200M',
                        maxSizeMessage: 'Le fichier est trop lourd. Max : 200Mo',
                        extensions: ['mp3', 'mp4', 'ogg', 'flac'],
                        extensionsMessage: "L'extension du fichier est invalide {{ extension }}, les extensions autorisées sont : {{ extensions }}",
                    ),
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Créer',
                'attr' => [
                    'class' => 'btn btn-primary action-save',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
