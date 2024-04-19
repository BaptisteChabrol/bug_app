<?php

namespace App\Form;

use App\Form\AgendaRDV\SuiviType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\UX\LiveComponent\Form\Type\LiveCollectionType;
use Symfonycasts\DynamicForms\DynamicFormBuilder;
use Vich\UploaderBundle\Form\Type\VichFileType;

class RDVType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder = new DynamicFormBuilder($builder);

        $builder
            ->add('titre', TextType::class, [
                'required' => true,
                'constraints' => [new Assert\NotBlank()],
            ])
            ->add('agence', ChoiceType::class, [
                'required' => true,
                'placeholder' => '- Veuillez sélectionner une agence -',
                'choices' => [
                    'Bordeaux' => 'Bordeaux',
                    'Toulouse' => 'Toulouse',
                    'Nantes' => 'Nantes',
                    'Lyon' => 'Lyon',
                ],
                'attr' => [
                    'data-ea-widget' => 'ea-autocomplete',
                ],
                'constraints' => [new Assert\NotBlank()],
            ])
            ->add('adresse', TextType::class, [
                'label' => 'Adresse du RDV',
                'required' => true,
                'help' => 'Au format : n°voie rue CP ville',
                'constraints' => [new Assert\NotBlank()],
            ])
            ->add('telephone', TelType::class, [
                'label' => 'Téléphone',
                'required' => true,
                'help' => 'Formats acceptés : </br> 
                <ul>
                    <li>+336 01 02 03 04</li>
                    <li>+33601020304</li>
                </ul>
                ',
                'help_html' => true,
                'constraints' => [
                    new Regex(
                        '/^\+33\s?\d{1}\s?\d{2}\s?\d{2}\s?\d{2}\s?\d{2}$/',
                        "Le numéro de téléphone n'est pas valide."
                    ),
                    new Assert\NotBlank(),
                ],
            ])
//            ->add('description', CKEditorType::class, [
//                'required' => false,
//                'config_name' => 'agenda_rdv_config',
//                'attr' => [
//                    'data-live-ignore' => '',
//                ],
//            ])
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
            ->add('solocal_content', ChoiceType::class, [
                'label' => 'Solocal : content ?',
                'required' => false,
                'choices' => [
                    'Oui' => 'Oui',
                    'Non' => 'Non',
                ],
                'attr' => [
                    'data-ea-widget' => 'ea-autocomplete',
                ],
            ])
            ->add('montantSolocal', MoneyType::class, [
                'required' => false,
            ])
            ->add('dateSolocal', DateType::class, [
                'label' => "Date d'engagement solocal",
                'required' => false,
                'widget' => 'single_text',
            ])
            ->add('siteInternet', ChoiceType::class, [
                'label' => 'Site internet ?',
                'required' => false,
                'choices' => [
                    'Oui' => 'Oui',
                    'Non' => 'Non',
                ],
                'attr' => [
                    'data-ea-widget' => 'ea-autocomplete',
                ],
            ])
            ->add('nomAgence', TextType::class, [
                'label' => "Nom de l'agence de comm",
                'required' => false,
            ])
            ->add('agenceContent', ChoiceType::class, [
                'label' => 'Agence : content ?',
                'required' => false,
                'choices' => [
                    'Oui' => 'Oui',
                    'Non' => 'Non',
                ],
                'attr' => [
                    'data-ea-widget' => 'ea-autocomplete',
                ],
            ])
            ->add('montantAgence', MoneyType::class, [
                'label' => "Montant de l'agence",
                'required' => false,
            ])
            ->add('dateAgence', DateType::class, [
                'label' => "Date d'engagement agence",
                'required' => false,
                'widget' => 'single_text',
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Créer',
                'attr' => [
                    'class' => 'btn btn-primary action-save',
                ],
            ]);
//            ->add('suivis', LiveCollectionType::class, [
//                'entry_type' => SuiviType::class,
//                'entry_options' => false,
//                'label' => false,
//                'allow_add' => true,
//                'allow_delete' => true,
//                'by_reference' => false,
//            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
