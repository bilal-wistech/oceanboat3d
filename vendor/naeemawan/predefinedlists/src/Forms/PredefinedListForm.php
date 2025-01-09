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
            $this->add('descp', 'textarea', [
                'label' => 'Enter details',
                'label_attr' => ['class' => 'control-label required'],
                'attr' => [
                    'data-counter' => 255,
                ],
            ]);
            if(currentPDLLevel($parent,1)!=2 && currentPDLLevel($parent,1)!=3){
                $this->add('image[]', 'mediaImages', [
                    'label' => 'Slider Images',
                    'label_attr' => ['class' => 'control-label'],
                    'values' => $this->getModel()->image!=null ? $this->getModel()->image: [],
                ]);
            }
            $this->add('parent_id', 'hidden', [
                'default_value' => $parent,
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
                ])
                ->add('standard_options', 'editor', [
                    'label' => 'Standard Options',
                    'label_attr' => ['class' => 'control-label'],
                    'attr' => [
                        'rows' => 4,
                        'placeholder' => trans('core/base::forms.description_placeholder'),
                        'data-counter' => 1000,
                    ],
                    'value' => $this->getModel()->detail!=null ? $this->getModel()->detail->standard_options: '',
                ]);
            }
            if($parent!=0 && currentPDLLevel($parent,1)!=1 && currentPDLLevel($parent,1)!=4 && currentPDLLevel($parent,1)!=3){
                // $this->add('multi_select', 'onOff', [
                //     'label' => 'Multi Select?',
                //     'label_attr' => ['class' => 'control-label'],
                //     'default_value' => false,
                // ]);
                $this->add('multi_select', 'customSelect', [
                    'label' => 'Option selection',
                    'label_attr' => ['class' => 'control-label'],
                    'attr' => [
                        'class' => 'form-control select-full',
                    ],
                    'choices' => ['0'=>'None','2'=>'Single Select','1'=>'Multi Select','3'=>'Info Only'],
                ]);
            }
            $this->add('status', 'customSelect', [
                'label' => trans('core/base::tables.status'),
                'label_attr' => ['class' => 'control-label required'],
                'attr' => [
                    'class' => 'form-control select-full',
                ],
                'choices' => getStatusArr(),
            ]);
            if($parent!=0 && currentPDLLevel($parent,1)!=1 && currentPDLLevel($parent,1)!=4 && currentPDLLevel($parent,1)!=3){
                $this->add('display_order','number',[
                    'label' => 'Display Order',
                    'label_attr' => ['class' => 'control-label required'],
                    'default_value' => 0,
                ]);
            }
            if($parent==0){
                $this->add('sort_order','number',[
                    'label' => 'Sort Order',
                    'label_attr' => ['class' => 'control-label required'],
                    'default_value' => 0,
                ]);
                $this->add('category_id', 'customSelect', [
                    'label' => 'Select product category',
                    'label_attr' => ['class' => 'control-label required'],
                    'attr' => [
                        'class' => 'form-control select-full',
                    ],
                    'choices' => getcategories(),
                ]);
            }
            if($parent!=0 && currentPDLLevel($parent,1)!=2 && currentPDLLevel($parent,1)!=3){
                $this->add('side_layout', 'customSelect', [
                    'label' => 'Side Layout',
                    'label_attr' => ['class' => 'control-label required'],
                    'attr' => [
                        'class' => 'form-control select-full',
                    ],
                    'choices' => ['radio'=>'Radio Select','toggle'=>'Toggle Select'],
                ]);
            }
            if(currentPDLLevel($parent,1)!=2 && currentPDLLevel($parent,1)!=3){
                $this->add('price', 'number', [
                    'label' => 'Price',
                    'label_attr' => ['class' => 'control-label required'],
                    'default_value' => 0,
                ]);
            }
            if(currentPDLLevel($parent,1)!=4 && currentPDLLevel($parent,1)!=1){
                $this->add('sort_order', 'number', [
                    'label' => 'Sort Order',
                    'label_attr' => ['class' => 'control-label required'],
                    'default_value' => 0,
                ]);
            }
            if($parent!=0 && currentPDLLevel($parent,1)!=2 && currentPDLLevel($parent,1)!=3){
                $this->add('preview_enabled', 'onOff', [
                    'label' => 'Preview Enabled?',
                    'label_attr' => ['class' => 'control-label'],
                    'default_value' => false,
                ]);
            }
            
            if($parent==0){
                $this->add('preview_enabled', 'hidden', [
                    'default_value' => 1,
                ]);
            }
            $this->setBreakFieldPoint('status');
            if(currentPDLLevel($parent,1)!=2 && currentPDLLevel($parent,1)!=3){
                $this->add('main_image', 'mediaImage', [
                    'label' => 'Main Image',
                    'label_attr' => ['class' => 'control-label'],
                    'values' => $this->getModel()->main_image!=null ? $this->getModel()->main_image: [],
                ]);
            }
            
            
            
    }
}
