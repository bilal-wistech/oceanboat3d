@extends(BaseHelper::getAdminMasterLayoutTemplate())
@section('content')
    <div class="d-none" id="dashboard-alerts">
        <verify-license-component verify-url="{{ route('settings.license.verify') }}"
            setting-url="{{ route('settings.options') }}"></verify-license-component>
        @if (config('core.base.general.enable_system_updater') && Auth::user()->isSuperUser())
            <check-update-component check-update-url="{{ route('system.check-update') }}"
                setting-url="{{ route('system.updater') }}"></check-update-component>
        @endif
    </div>
    {!! apply_filters(DASHBOARD_FILTER_ADMIN_NOTIFICATIONS, null) !!}
    <div class="row">
        {!! apply_filters(DASHBOARD_FILTER_TOP_BLOCKS, null) !!}
    </div>
    <div class="row">
        @foreach ($statWidgets as $widget)
            {!! $widget !!}
        @endforeach
    </div>

    <div class="col-12">
        <div id="main-view">
            <h6>Boat Views</h6>
            <ul class="list-group boat-list">
                <?php foreach ($result as $index => $boat): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center boat-item"
                    onclick="showDetails('boat<?php echo $index; ?>')">
                    <span><?php echo htmlspecialchars($boat['boat_title']); ?></span>
                    <span class="badge bg-primary rounded-pill"><?php echo number_format($boat['count']); ?> views</span>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <?php foreach ($result as $index => $boat): ?>
        <div id="boat<?php echo $index; ?>" class="boat-details">
            <h6><?php echo htmlspecialchars($boat['boat_title']); ?> Accessories</h6>
            <?php
            $accessoriesByCategory = [];
            foreach ($boat['accessories'] as $accessory) {
                $categoryTitle = $accessory['category']['title'];
                $subCategoryTitle = $accessory['sub_category']['title'];
                if (!isset($accessoriesByCategory[$categoryTitle])) {
                    $accessoriesByCategory[$categoryTitle] = [];
                }
                if (!isset($accessoriesByCategory[$categoryTitle][$subCategoryTitle])) {
                    $accessoriesByCategory[$categoryTitle][$subCategoryTitle] = [];
                }
                $accessoriesByCategory[$categoryTitle][$subCategoryTitle][] = $accessory;
            }
            ?>
            <?php foreach ($accessoriesByCategory as $categoryTitle => $subCategories): ?>
            <div onclick="toggleSuboptions(event, this)">
                <div class="boat-option">
                    <div><?php echo htmlspecialchars($categoryTitle); ?></div>
                    <i class="opt fas fa-chevron-down float-end"></i>
                </div>
                <div class="suboptions">
                    <?php foreach ($subCategories as $subCategoryTitle => $accessories): ?>
                    <div onclick="toggleSuboptions(event, this)" class="sub-category">
                        <div class="boat-option">
                            <div><?php echo htmlspecialchars($subCategoryTitle); ?></div>
                            <i class="opt fas fa-chevron-down float-end"></i>
                        </div>
                        <div class="suboptions">
                            <?php foreach ($accessories as $accessory): ?>
                            <div class="boat-sub-item justify-content-between align-items-center">
                                <div><?php echo htmlspecialchars($accessory['accessory']['title']); ?></div>
                                <span class="badge bg-primary rounded-pill"><?php echo number_format($accessory['accessory']['count']); ?> views</span>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endforeach; ?>
            <button class="back-button" onclick="showMainView()">Back</button>
        </div>
        <?php endforeach; ?>
    </div>
    </div>
    <div id="list_widgets" class="row">
        @foreach ($userWidgets as $widget)
            {!! $widget !!}
        @endforeach
    </div>

    @if (count($widgets) > 0)
        <a href="#" class="manage-widget">
            <i class="fa fa-plus"></i>
            {{ trans('core/dashboard::dashboard.manage_widgets') }}
        </a>
        @include('core/dashboard::partials.modals', compact('widgets'))
    @endif
@stop

<style>
    .boat-list {
        list-style-type: none;
        padding: 0;
        background: white;
        border: 1px solid #ccc;
    }

    .boat-item {
        display: flex;
        justify-content: space-between;
        padding: 10px;
        border-bottom: 1px solid #ccc;
        cursor: pointer;
    }

    .boat-item:hover {
        background-color: #f0f0f0;
    }

    .boat-details {
        display: none;
    }

    .boat-option {
        padding: 10px;
        background: white;

        justify-content: space-between;
        border: 1px solid #ccc;
    }

    .back-button {
        margin-top: 20px;
        color: white;
        background: black;
        margin-bottom: 20px;
        padding: 5px 10px;
        cursor: pointer;
        border: none;
    }

    .suboptions {
        border-left: 1px solid #ccc;
        border-right: 1px solid #ccc;
        display: none;
        background: #f8f7f7;
    }

    .boat-sub-item {
        display: flex;
        padding: 10px 30px 10px 30px;
        border-bottom: 1px solid #ccc;
        cursor: pointer;
    }

    .opt {
        position: relative;
        top: -15px
    }
</style>
<script>
    function showDetails(boatId) {
        document.getElementById('main-view').style.display = 'none';
        document.getElementById(boatId).style.display = 'block';
    }

    function showMainView() {
        document.getElementById('main-view').style.display = 'block';
        var boatDetails = document.getElementsByClassName('boat-details');
        for (var i = 0; i < boatDetails.length; i++) {
            boatDetails[i].style.display = 'none';
        }
    }

    function toggleSuboptions(event, element) {
        event.stopPropagation();

        var suboptions = element.querySelector('.suboptions');
        var chevronIcon = element.querySelector('.fa-chevron-down, .fa-chevron-up');

        if (suboptions.style.display === 'block') {
            suboptions.style.display = 'none';
            chevronIcon.classList.remove('fa-chevron-up');
            chevronIcon.classList.add('fa-chevron-down');
        } else {
            suboptions.style.display = 'block';
            chevronIcon.classList.remove('fa-chevron-down');
            chevronIcon.classList.add('fa-chevron-up');
        }
    }
</script>
