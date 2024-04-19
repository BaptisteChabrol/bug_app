<?php

namespace App\Twig\Components;

use App\Form\RDVType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\Component\Validator\Constraints as Assert;

#[AsLiveComponent]
class RDVForm extends AbstractController
{
    use ComponentWithFormTrait;

    use DefaultActionTrait;

    #[LiveProp]
    public ?string $fichierAudioName = null;

    #[LiveProp]
    public ?string $fichierAudioUploadError = null;

    public function __construct(
        private readonly ValidatorInterface    $validator,
        private readonly ParameterBagInterface $parameterBag,
    )
    {
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(RDVType::class);
    }

    public function hasValidationErrors(): bool
    {
        return $this->getForm()->isSubmitted() && !$this->getForm()->isValid();
    }

    #[LiveAction]
    public function uploadFile(Request $request): ?string
    {
        $file = $request->files->get('rdv')['fichierAudio'];
        $file && $this->validateFile($file);

        if ($file instanceof UploadedFile) {
            [$this->fichierAudioName] = $this->processFileUpload($file);
        }

        return $this->fichierAudioName;
    }

    private function validateFile(UploadedFile $file): void
    {
        $errors = $this->validator->validate($file, [
            new Assert\File(
                maxSize: '200M',
                maxSizeMessage: 'Le fichier est trop lourd. Max : 200Mo',
                extensions: ['mp3', 'mp4', 'ogg', 'flac'],
                extensionsMessage: 'L\'extension du fichier est invalide {{ extension }}, les extensions autorisées sont : {{ extensions }}',
            ),
        ]);

        if (0 === \count($errors)) {
            return;
        }

        $this->fichierAudioUploadError = $errors->get(0)->getMessage();
        throw new UnprocessableEntityHttpException('Validation failed');
    }

    private function processFileUpload(UploadedFile $file): array
    {
        $fileName = $file->getClientOriginalName();
        $path = $this->parameterBag->get('kernel.project_dir') . '/public/temp/';

        return [$file->getClientOriginalName(), $file->getSize()];
    }

    #[LiveAction]
    public function save()
    {
        $this->submitForm();
    }
}