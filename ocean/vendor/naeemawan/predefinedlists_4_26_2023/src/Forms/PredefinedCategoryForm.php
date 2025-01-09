<?php

namespace NaeemAwan\PredefinedLists\Forms;

use AdsManager;
use NaeemAwan\PredefinedLists\Http\Requests\PredefinedCategoryRequest;
use NaeemAwan\PredefinedLists\Models\PredefinedCategory;
use Botble\Base\Forms\FormAbstract;

class PredefinedCategoryForm extends FormAbstract
{
    public function buildForm(): void
    {
        $this
            ->setupModel(new PredefinedCategory())
            ->setValidatorClass(PredefinedCategoryRequest::class)
            ->withCustomFields()
            ->add('name', 'text', [
                'label' => 'Title',
                'label_attr' => ['class' => 'control-label required'],
                'attr' => [
                    'placeholder' => 'Enter Title',
                    'data-counter' => 120,
                ],
            ])
            ->add('description', 'textarea', [
                'label' => 'Enter description',
                'label_attr' => ['class' => 'control-label required'],
                'attr' => [
                    'data-counter' => 255,
                ],
            ])
            ->add('status', 'customSelect', [
                'label' => trans('core/base::tables.status'),
                'label_attr' => ['class' => 'control-label required'],
                'attr' => [
                    'class' => 'form-control select-full',
                ],
                'choices' => getStatusArr(),
            ])
            ->setBreakFieldPoint('status');
            
            
    }
}
