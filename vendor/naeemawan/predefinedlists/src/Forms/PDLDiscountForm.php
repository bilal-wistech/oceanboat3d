<?php

namespace NaeemAwan\PredefinedLists\Forms;

use Assets;
use Carbon\Carbon;
use Botble\Base\Forms\FormAbstract;
use NaeemAwan\PredefinedLists\Models\BoatDiscount;
use NaeemAwan\PredefinedLists\Http\Requests\PDLDiscountRequest;
use NaeemAwan\PredefinedLists\Repositories\Interfaces\PredefinedListInterface;

class PDLDiscountForm extends FormAbstract
{
    public function buildForm(): void
    {
        $predefinedListRepository = app(PredefinedListInterface::class);
        $boats = $predefinedListRepository->getByWhereIn('parent_id', [0])->pluck('ltitle', 'id');
        $boatsArray = $boats->toArray();

        $this
            ->setupModel(new BoatDiscount())
            ->setValidatorClass(PDLDiscountRequest::class)
            ->withCustomFields()
            ->add('code', 'text', [
                'label' => 'Code',
                'label_attr' => ['class' => 'control-label required'],
                'attr' => [
                    'placeholder' => 'Enter the code',
                    'data-counter' => 120,
                    'id' => 'discount-code-input',
                ],
                'wrapper' => [
                    'class' => 'form-group col-md-9',
                ],
            ])
            ->add('generate_code', 'html', [
                'html' => '
                <div class="form-group col-md-3">
                    <label class="control-label">&nbsp;</label>
                    <button type="button" id="generate-code-btn" class="btn btn-info">Generate Random Code</button>
                </div>
            ',
            ])
            ->add('list_id', 'select', [
                'label' => 'Boat',
                'label_attr' => ['class' => 'control-label required'],
                'choices' => $boatsArray,
                'attr' => [
                    'class' => 'form-control select-full',
                    'multiple' => 'multiple',
                    'id' => 'boat-select',
                ],
            ])
            ->add('accessory_id', 'select', [
                'label' => 'Accessory',
                'label_attr' => ['class' => 'control-label required'],
                'choices' => [], // Empty initially, will be populated by JS
                'attr' => [
                    'class' => 'form-control select-full accessory_discount',
                    'id' => 'accessory-select',
                ],
            ])
            ->add('discount_type', 'customSelect', [
                'label' => 'Select Type of Discount',
                'label_attr' => ['class' => 'control-label required'],
                'attr' => [
                    'class' => 'form-control select-full',
                ],
                'choices' => getDiscountTypeArr(),
            ])
            ->add('discount', 'text', [
                'label' => 'Discount Amount/Percentage',
                'label_attr' => ['class' => 'control-label required'],
                'attr' => [
                    'placeholder' => 'Enter Discount Amount/Percentage',
                    'data-counter' => 10,
                ],
            ])
            ->add('valid_from', 'datePicker', [
                'label' => __('Valid From'),
                'label_attr' => ['class' => 'control-label required'],
                'attr' => [
                    'class' => 'form-control',
                    'data-date-format' => 'yyyy/mm/dd',
                ],
                'default_value' => Carbon::now()->format('Y/m/d'),
            ])
            ->add('valid_to', 'datePicker', [
                'label' => __('Valid To'),
                'label_attr' => ['class' => 'control-label required'],
                'attr' => [
                    'class' => 'form-control',
                    'data-date-format' => 'yyyy/mm/dd',
                ],
                'default_value' => Carbon::now()->format('Y/m/d'),
            ])
            ->add('never_expires', 'onOff', [
                'label' => 'Never Expires',
                'label_attr' => ['class' => 'control-label'],
                'default_value' => 0,
            ]);

        // Adding the script for the form
        Assets::addScriptsDirectly('vendor/core/core/base/js/predefined-lists-discount-form.js');
    }
}
