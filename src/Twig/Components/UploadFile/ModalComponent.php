<?php

declare(strict_types=1);

namespace App\Twig\Components\UploadFile;

use App\Entity\Cms\Media;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\File;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
class ModalComponent extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;
    use ComponentToolsTrait;

    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    #[LiveProp]
    public bool $isOpen = false;

    #[LiveProp]
    public bool $isUploading = false;

    #[LiveProp]
    public ?string $successMessage = null;

    #[LiveProp]
    public ?string $errorMessage = null;

    protected function instantiateForm(): FormInterface
    {
        return $this->createFormBuilder()
            ->add('file', FileType::class, [
                'label' => 'Select file',
                'constraints' => [
                    new File([
                        'maxSize' => '10M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'application/pdf',
                            'application/msword',
                            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid file (JPEG, PNG, PDF, DOC, DOCX).',
                    ]),
                ],
                'required' => true,
            ])
            ->add('type', ChoiceType::class, [
                'label' => 'Select type',
                'choices' => [
                    'Image' => 'image',
                    'Document' => 'document',
                ],
                'required' => true,
            ])
            ->getForm();
    }

    #[LiveAction]
    public function upload(Request $request): void
    {
        $files = $request->files->all();
        if (!isset($files['form']['file'])) {
            $this->errorMessage = 'Please select a file.';

            return;
        }

        $this->formValues['file'] = $request->files->all()['form']['file'];

        $this->submitForm();

        if (!$this->getForm()->isValid()) {
            $this->errorMessage = 'Please select a valid file.';

            return;
        }

        $this->isUploading = true;

        try {
            /** @var ?UploadedFile $uploadedFile */
            $uploadedFile = $this->getForm()->get('file')->getData();

            if (null === $uploadedFile) {
                $this->errorMessage = 'No file was uploaded.';
                $this->isUploading = false;

                return;
            }

            $media = Media::create($this->formValues['type']);
            $media->setFile($uploadedFile);

            $this->entityManager->persist($media);
            $this->entityManager->flush();

            $this->successMessage = 'File uploaded successfully!';
            $this->errorMessage = null;

            // Reset form after successful upload
            $this->resetForm();

            $this->dispatchBrowserEvent('upload:success', [
                'filename' => $media->getFilePath(),
                'originalName' => $media->getOriginalName(),
                'mediaId' => $media->getId(),
            ]);
        } catch (\Exception $e) {
            $this->isUploading = false;
            $this->errorMessage = 'Upload failed: '.$e->getMessage();
        }
    }

    #[LiveAction]
    public function close(): void
    {
        $this->isOpen = false;
        $this->successMessage = null;
        $this->errorMessage = null;
        $this->resetForm();
    }
}
