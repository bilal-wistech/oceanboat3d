<?php

namespace NaeemAwan\PredefinedLists\Forms;

use Assets;
use Botble\Base\Forms\FormAbstract;
use Botble\Contact\Enums\ContactStatusEnum;
use Botble\Contact\Http\Requests\EditContactRequest;
use NaeemAwan\PredefinedLists\Models\BoatEnquiry;

class BoatEnquiryForm extends FormAbstract
{
    public function buildForm(): void
    {
        Assets::addScriptsDirectly('vendor/core/plugins/contact/js/contact.js')
            ->addStylesDirectly('vendor/core/plugins/contact/css/contact.css');

        $this
            ->setupModel(new BoatEnquiry())
            ->setValidatorClass(EditContactRequest::class)
            ->withCustomFields()
            ->add('status', 'customSelect', [
                'label' => trans('core/base::tables.status'),
                'label_attr' => ['class' => 'control-label required'],
                'choices' => ContactStatusEnum::labels(),
            ])
            ->setBreakFieldPoint('status')
            ->addMetaBoxes([
                'information' => [
                    'title' => 'Custom Boat Enquiry',
                    'content' => view('pdlists::customizeboat.enquiry', ['boat_enquiry' => $this->getModel()])->render(),
                    'attributes' => [
                        'style' => 'margin-top: 0',
                    ],
                ]
            ]);
    }
}
