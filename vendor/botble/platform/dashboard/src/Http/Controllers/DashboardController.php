<?php

namespace Botble\Dashboard\Http\Controllers;


use Assets;
use Exception;
use Illuminate\Http\Request;
use NaeemAwan\PredefinedLists\Models\BoatView;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Botble\ACL\Repositories\Interfaces\UserInterface;
use Botble\Dashboard\Repositories\Interfaces\DashboardWidgetInterface;
use NaeemAwan\PredefinedLists\Repositories\Interfaces\BoatViewInterface;
use Botble\Dashboard\Repositories\Interfaces\DashboardWidgetSettingInterface;
use NaeemAwan\PredefinedLists\Repositories\Interfaces\PredefinedListInterface;

class DashboardController extends BaseController
{
    protected DashboardWidgetSettingInterface $widgetSettingRepository;

    protected DashboardWidgetInterface $widgetRepository;

    protected UserInterface $userRepository;

    protected BoatViewInterface $boatViewRepository;
    protected PredefinedListInterface $predefinedListRepository;
    public function __construct(
        DashboardWidgetSettingInterface $widgetSettingRepository,
        DashboardWidgetInterface $widgetRepository,
        UserInterface $userRepository,
        BoatViewInterface $boatViewRepository,
        PredefinedListInterface $predefinedListRepository
    ) {
        $this->widgetSettingRepository = $widgetSettingRepository;
        $this->widgetRepository = $widgetRepository;
        $this->userRepository = $userRepository;
        $this->boatViewRepository = $boatViewRepository;
        $this->predefinedListRepository = $predefinedListRepository;
    }

    public function getDashboard(Request $request)
    {
        page_title()->setTitle(trans('core/dashboard::dashboard.title'));

        Assets::addScripts(['blockui', 'sortable', 'equal-height', 'counterup'])
            ->addScriptsDirectly('vendor/core/core/dashboard/js/dashboard.js')
            ->addStylesDirectly('vendor/core/core/dashboard/css/dashboard.css')
            ->usingVueJS();

        do_action(DASHBOARD_ACTION_REGISTER_SCRIPTS);

        $widgets = $this->widgetRepository->getModel()
            ->with([
                'settings' => function (HasMany $query) use ($request) {
                    $query->where('user_id', $request->user()->getKey())
                        ->select(['status', 'order', 'settings', 'widget_id'])
                        ->orderBy('order');
                },
            ])
            ->select(['id', 'name'])
            ->get();

        $widgetData = apply_filters(DASHBOARD_FILTER_ADMIN_LIST, [], $widgets);
        ksort($widgetData);

        $availableWidgetIds = collect($widgetData)->pluck('id')->all();

        $widgets = $widgets->reject(function ($item) use ($availableWidgetIds) {
            return !in_array($item->id, $availableWidgetIds);
        });

        $statWidgets = collect($widgetData)->where('type', '!=', 'widget')->pluck('view')->all();
        $userWidgets = collect($widgetData)->where('type', 'widget')->pluck('view')->all();
        $boatViews = BoatView::all();
        $result = [];

        foreach ($boatViews as $boatView) {
            $predefined_list = $this->predefinedListRepository->findById($boatView->entity_id);

            if ($boatView->entity_type == 'boat') {
                $result[] = [
                    'id' => $predefined_list->id,
                    'parent_id' => $predefined_list->parent_id,
                    'boat_title' => $predefined_list->ltitle,
                    'count' => $boatView->total_count,
                    'accessories' => []
                ];
            } elseif ($boatView->entity_type == 'option') {
                $lastIndex = count($result) - 1;
                $sub_category = $this->predefinedListRepository->findById($predefined_list->parent_id);
                $category = $this->predefinedListRepository->findById($sub_category->parent_id);

                if (isset($result[$lastIndex])) {
                    $result[$lastIndex]['accessories'][] = [
                        'category' => [
                            'id' => $category->id,
                            'parent_id' => $category->parent_id,
                            'title' => $category->ltitle,
                        ],
                        'sub_category' => [
                            'id' => $sub_category->id,
                            'parent_id' => $sub_category->parent_id,
                            'title' => $sub_category->ltitle,
                        ],
                        'accessory' => [
                            'id' => $predefined_list->id,
                            'parent_id' => $predefined_list->parent_id,
                            'title' => $predefined_list->ltitle,
                            'count' => $boatView->total_count,
                        ]
                    ];
                }
            }
        }
        // dd($result);
        return view('core/dashboard::list', compact('widgets', 'userWidgets', 'statWidgets', 'result'));
    }

    public function postEditWidgetSettingItem(Request $request, BaseHttpResponse $response)
    {
        try {
            $widget = $this->widgetRepository->getFirstBy([
                'name' => $request->input('name'),
            ]);

            if (!$widget) {
                return $response
                    ->setError()
                    ->setMessage(trans('core/dashboard::dashboard.widget_not_exists'));
            }

            $widgetSetting = $this->widgetSettingRepository->firstOrCreate([
                'widget_id' => $widget->id,
                'user_id' => $request->user()->getKey(),
            ]);

            $widgetSetting->settings = array_merge((array) $widgetSetting->settings, [
                $request->input('setting_name') => $request->input('setting_value'),
            ]);

            $this->widgetSettingRepository->createOrUpdate($widgetSetting);
        } catch (Exception $exception) {
            return $response
                ->setError()
                ->setMessage($exception->getMessage());
        }

        return $response;
    }

    public function postUpdateWidgetOrder(Request $request, BaseHttpResponse $response)
    {
        foreach ($request->input('items', []) as $key => $item) {
            $widget = $this->widgetRepository->firstOrCreate([
                'name' => $item,
            ]);
            $widgetSetting = $this->widgetSettingRepository->firstOrCreate([
                'widget_id' => $widget->id,
                'user_id' => $request->user()->getKey(),
            ]);
            $widgetSetting->order = $key;
            $this->widgetSettingRepository->createOrUpdate($widgetSetting);
        }

        return $response->setMessage(trans('core/dashboard::dashboard.update_position_success'));
    }

    public function getHideWidget(Request $request, BaseHttpResponse $response)
    {
        $widget = $this->widgetRepository->getFirstBy([
            'name' => $request->input('name'),
        ], ['id']);
        if (!empty($widget)) {
            $widgetSetting = $this->widgetSettingRepository->firstOrCreate([
                'widget_id' => $widget->id,
                'user_id' => $request->user()->getKey(),
            ]);

            $widgetSetting->status = 0;
            $widgetSetting->order = 99 + $widgetSetting->id;
            $this->widgetRepository->createOrUpdate($widgetSetting);
        }

        return $response->setMessage(trans('core/dashboard::dashboard.hide_success'));
    }

    public function postHideWidgets(Request $request, BaseHttpResponse $response)
    {
        $widgets = $this->widgetRepository->all();
        foreach ($widgets as $widget) {
            $widgetSetting = $this->widgetSettingRepository->firstOrCreate([
                'widget_id' => $widget->id,
                'user_id' => $request->user()->getKey(),
            ]);

            if (
                $request->has('widgets.' . $widget->name) &&
                $request->input('widgets.' . $widget->name) == 1
            ) {
                $widgetSetting->status = 1;
                $this->widgetRepository->createOrUpdate($widgetSetting);
            } else {
                $widgetSetting->status = 0;
                $widgetSetting->order = 99 + $widgetSetting->id;
                $this->widgetRepository->createOrUpdate($widgetSetting);
            }
        }

        return $response
            ->setNextUrl(route('dashboard.index'))
            ->setMessage(trans('core/dashboard::dashboard.hide_success'));
    }
}
