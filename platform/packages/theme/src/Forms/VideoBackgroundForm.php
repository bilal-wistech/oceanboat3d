<?php

namespace Botble\Theme\Forms;

use AdsManager;
use Botble\Base\Forms\FormAbstract;
use Botble\Theme\Http\Requests\VideoBackgroundRequest;
use Botble\Theme\Models\VideoBackground;

class VideoBackgroundForm extends FormAbstract
{
    public function buildForm(): void
    {
        $this
            ->setupModel(new VideoBackground())
            ->setValidatorClass(VideoBackgroundRequest::class)
            ->withCustomFields()
            ->add('title', 'text', [
                'label' => 'Title',
                'label_attr' => ['class' => 'control-label required'],
                'attr' => [
                    'placeholder' => 'Enter Title',
                    'data-counter' => 120,
                ],
            ])
            ->add('action', 'text', [
                'label' => 'Action Url',
                'label_attr' => ['class' => 'control-label'],
                'attr' => [
                    'placeholder' => 'Enter action url',
                    'data-counter' => 120,
                ],
            ])
            ->add('action_title', 'text', [
                'label' => 'Button Text',
                'label_attr' => ['class' => 'control-label required'],
                'attr' => [
                    'placeholder' => 'Enter button text',
                    'data-counter' => 120,
                ],
            ])
            ->add('description', 'textarea', [
                'label' => 'Enter description',
                'label_attr' => ['class' => 'control-label'],
                'attr' => [
                    'data-counter' => 255,
                ],
            ])
            ->add('image', 'mediaFile', [
                'label' => 'Image',
                'label_attr' => ['class' => 'control-label required'],
                'values' => $this->getModel()->image!=null ? $this->getModel()->image: [],
            ])
            ->add('status', 'customSelect', [
                'label' => trans('core/base::tables.status'),
                'label_attr' => ['class' => 'control-label required'],
                'attr' => [
                    'class' => 'form-control select-full',
                ],
                'choices' => getStatusArr(),
            ])
            ->setBreakFieldPoint('image');
            
            
    }
}
