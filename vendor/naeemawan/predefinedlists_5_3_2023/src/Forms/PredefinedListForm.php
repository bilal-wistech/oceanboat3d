<?php

namespace NaeemAwan\PredefinedLists\Forms;

use AdsManager;
use NaeemAwan\PredefinedLists\Http\Requests\PredefinedListRequest;
use NaeemAwan\PredefinedLists\Models\PredefinedList;
use Botble\Base\Forms\FormAbstract;

class PredefinedListForm extends FormAbstract
{
    public function buildForm(): void
    {
        $parent = $this->getFormOption('parent')!=null ? $this->getFormOption('parent') : 0;
        $this
            ->setupModel(new PredefinedList())
            ->setValidatorClass(PredefinedListRequest::class)
            ->withCustomFields()
            ->add('ltitle', 'text', [
                'label' => 'Title',
                'label_attr' => ['class' => 'control-label required'],
                'attr' => [
                    'placeholder' => 'Enter Title',
                    'data-counter' => 120,
                ],
            ]);
            if($parent==0){
            $this->add('category_id', 'customSelect', [
                'label' => 'Select product category',
                'label_attr' => ['class' => 'control-label required'],
                'attr' => [
                    'class' => 'form-control select-full',
                ],
                'choices' => getcategories(),
            ]);
            }
            $this->add('descp', 'textarea', [
                'label' => 'Enter details',
                'label_attr' => ['class' => 'control-label required'],
                'attr' => [
                    'data-counter' => 255,
                ],
            ])
            ->add('parent_id', 'hidden', [
                'default_value' => $parent,
            ])
            ->add('type', 'hidden', [
                'default_value' => $parent==0 ? 'parent' : 'child',
            ]);
            
            if($parent==0){
            $this->add('details', 'editor', [
                'label' => 'Specifications',
                'label_attr' => ['class' => 'control-label'],
                'attr' => [
                    'rows' => 4,
                    'placeholder' => trans('core/base::forms.description_placeholder'),
                    'data-counter' => 1000,
                ],
                'value' => $this->getModel()->detail!=null ? $this->getModel()->detail->details: '',
            ])
            ->add('url', 'url', [
                'label' => 'Video Url',
                'label_attr' => ['class' => 'control-label'],
                'value' => $this->getModel()->detail!=null ? $this->getModel()->detail->url: '',
            ])
            ->add('images[]', 'mediaImages', [
                'label' => 'Gallery Images',
                'label_attr' => ['class' => 'control-label'],
                'values' => $this->getModel()->detail!=null ? $this->getModel()->detail->images: [],
            ]);
            }
            $this->add('status', 'customSelect', [
                'label' => trans('core/base::tables.status'),
                'label_attr' => ['class' => 'control-label required'],
                'attr' => [
                    'class' => 'form-control select-full',
                ],
                'choices' => getStatusArr(),
            ])
            ->add('price', 'number', [
                'label' => 'Price',
                'label_attr' => ['class' => 'control-label'],
                'default_value' => 0,
            ])
            ->setBreakFieldPoint('status');
            if($parent==0){
            $this->add('main_image', 'mediaImage', [
                'label' => 'Boat Image',
                'label_attr' => ['class' => 'control-label'],
                'values' => $this->getModel()->main_image!=null ? $this->getModel()->main_image: [],
            ]);
            }
            $this->add('image[]', 'mediaImages', [
                'label' => 'Images',
                'label_attr' => ['class' => 'control-label'],
                'values' => $this->getModel()->image!=null ? $this->getModel()->image: [],
            ]);
            
            
    }
}
