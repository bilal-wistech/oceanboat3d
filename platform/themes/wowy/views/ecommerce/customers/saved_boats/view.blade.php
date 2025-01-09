@php use NaeemAwan\PredefinedLists\Models\PredefinedList; @endphp
@extends(Theme::getThemeNamespace() . '::views.ecommerce.customers.master')
@section('content')
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">{{ __('Saved Boat details') }}</h5>
        </div>
        <div class="card-body">
            <div class="customer-order-detail">
                <div class="row">
                    <div class="col-auto me-auto">
                        <div class="order-slogan">
                            @php
                                $logo = theme_option('logo_in_the_checkout_page') ?: theme_option('logo');
                            @endphp
                            @if ($logo)
                                <img width="100" src="{{ RvMedia::getImageUrl($logo) }}"
                                    alt="{{ theme_option('site_title') }}">
                                <br />
                            @endif
                            {{ setting('contact_address') }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="order-meta">
                            <span class="d-inline-block">{{ __('Time') }}:</span>
                            <strong class="order-detail-value">{{ $boat->created_at->format('h:m d/m/Y') }}</strong>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 border-top pt-2">
                        <h4>{{ __('Boat information') }}</h4>
                        <div>
                            <div>
                                <span class="d-inline-block">{{ __('Boat Name') }}:</span>
                                <strong class="order-detail-value">{{ $boat->boat->ltitle }}</strong>
                            </div>
                            <div>
                                <span class="d-inline-block">{{ __('Boat Price') }}:</span>
                                <strong class="order-detail-value"> {{ format_price($boat->boat->price) }} </strong>
                            </div>
                            @php
                                $discount = 0;
                                $discount_type = '';
                                $discount_on_boat = false;
                                foreach ($boat->boat->boat_discounts as $boat_discount) {
                                    $discount = $boat_discount->discount;
                                    $discount_type = $boat_discount->discount_type;
                                    if (
                                        $boat_discount->code == 'BOAT' ||
                                        empty($boat_discount->code) ||
                                        empty($boat_discount->accessory_id)
                                    ) {
                                        $discount_on_boat = true;
                                    }
                                }
                                // Calculate the final price after applying the discount
                                $original_price = $boat->boat->price;

                                if ($discount_type == 'percentage') {
                                    $discounted_price = $original_price - $original_price * ($discount / 100);
                                } elseif ($discount_type == 'amount') {
                                    $discounted_price = $original_price - $discount;
                                } else {
                                    $discounted_price = $original_price; // No discount
                                }

                                $formatted_price = format_price($discounted_price);
                            @endphp
                            @if ($discount && $discount_on_boat)
                                <div>
                                    <span class="d-inline-block">{{ __('Discount Boat Price') }}:</span>
                                    <strong class="order-detail-value"> {{ $formatted_price }} </strong>
                                </div>
                            @endif
                        </div>

                        <h4 class="mt-3 mb-1">{{ __('Final Model') }}</h4>
                        @php
                            $modelPath = $boat->boat->file;
                        @endphp
                        <div class="row">
                            <div id="loader">
                                <div class="spinner"></div>
                                <p>Loading Model...</p>
                            </div>
                            {{-- 3d model div starts --}}
                            <div id="3d-model" style="width: 100%; height: 500px; overflow:hidden"></div>
                            {{-- 3d model div ends --}}

                            <h4 class="mt-3 mt-50">{{ __('Options Selected') }}</h4>
                            <div class="card-body summary-card justify-content-center d-flex flex-row flex-wrap">
                                @foreach ($boat->details as $key => $value)
                                    <div class="card m-1">
                                        <div class="card-body text-center">
                                            <p>
                                                <b>{{ $value->slug->ltitle }}:</b>
                                                @if ($value->slug->parent)
                                                    <span>
                                                        @if ($value->color)
                                                            <div class="color-title-container">
                                                                <span>{{ $value->ltitle }}</span>
                                                                <div class="color-rounded"
                                                                    style="background-color: {{ $value->color }};"></div>
                                                            </div>
                                                        @else
                                                            {{ $value->ltitle }}
                                                        @endif
                                                        @if ($value->is_standard_option == 1)
                                                            <small>(Standard Option)</small>
                                                        @endif
                                                    </span>
                                                @endif
                                            <p>
                                                @php
                                                    $discounted_price =
                                                        $value->enquiry_option->price - $value->discount_amount;
                                                @endphp
                                                <b>Price</b> :
                                                {{ format_price($value->enquiry_option->price) }}
                                                @if ($value->has_discount == 1)
                                                    <br>
                                                    <b>Discounted Price</b> :
                                                    {{ format_price($discounted_price) }} - <small>
                                                        (coupon: {{ $value->discount_code }})
                                                    </small>
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <h4 class="mt-3 mb-1">{{ __('Total') }}</h4>
                        <div>
                            <div>
                                <span class="d-inline-block">{{ __('Total Price Included Vat') }}:</span>
                                <strong class="order-detail-value">{{ format_price($boat->vat_total) }}</strong>
                            </div>
                            @if ($boat->is_finished)
                                <hr />
                                <div>
                                    <span class="d-inline-block">{{ __('Paid') }}:</span>
                                    <strong class="order-detail-value">{{ format_price($boat->paid_amount) }}</strong>
                                </div>
                                <div>
                                    <span class="d-inline-block">{{ __('Remaining') }}:</span>
                                    <strong
                                        class="order-detail-value">{{ format_price($boat->vat_total - $boat->paid_amount) }}</strong>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/three/examples/js/controls/OrbitControls.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/three/examples/js/loaders/GLTFLoader.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/three/examples/js/loaders/DRACOLoader.js"></script>
     <script>
    document.addEventListener('DOMContentLoaded', function() {
        const baseModelPath = '{{ asset('storage/' . $modelPath) }}';
        const accessoryModelPaths = [
            @foreach ($boat->details as $value)
                '{{ asset('storage/' . $value->file) }}',
            @endforeach
        ];
        const loadingIndicator = document.getElementById('loader');
        loadingIndicator.style.display = 'block';
        const container = document.getElementById('3d-model');
        const raycaster = new THREE.Raycaster();
        const mouse = new THREE.Vector2();
        const scene = new THREE.Scene();
        const camera = new THREE.PerspectiveCamera(75, container.clientWidth / container.clientHeight, 0.1, 1000);
        camera.position.set(0, 0, 6);
        camera.lookAt(scene.position);

        const renderer = new THREE.WebGLRenderer({ antialias: true });
        renderer.physicallyCorrectLights = true;
        renderer.outputEncoding = THREE.sRGBEncoding;
        renderer.toneMapping = THREE.ACESFilmicToneMapping;
        renderer.toneMappingExposure = 1.6;
        renderer.setSize(container.clientWidth, container.clientHeight);
        renderer.setClearColor(0x182955);
        renderer.shadowMap.enabled = true;
        renderer.shadowMap.type = THREE.PCFSoftShadowMap;
        container.appendChild(renderer.domElement);
        renderer.render(scene, camera);

        const directionalLight = new THREE.DirectionalLight(0xffffff, 1);
        directionalLight.position.set(5, 5, 5).normalize();
        directionalLight.castShadow = true;
        scene.add(directionalLight);

        const directionalLight2 = new THREE.DirectionalLight(0xffffff, 0.5);
        directionalLight2.position.set(-5, -5, -5).normalize();
        scene.add(directionalLight2);

        const ambientLight = new THREE.AmbientLight(0xffffff, 0.8);
        scene.add(ambientLight);

        const hemiLight = new THREE.HemisphereLight(0xffffff, 0x444444, 0.5);
        scene.add(hemiLight);

        const dracoLoader = new THREE.DRACOLoader();
        dracoLoader.setDecoderPath("https://www.gstatic.com/draco/versioned/decoders/1.4.1/");

        const loader = new THREE.GLTFLoader();
        loader.setDRACOLoader(dracoLoader);

        let baseModel, additionalModels = [];
        let originalMaterials = {};
        let originalColors = {};
        let modelsToLoad = accessoryModelPaths.length + 1;
        let loadedModels = 0;
        container.addEventListener('mousemove', onMouseMove, false);
        container.addEventListener('dblclick', onDoubleClick, false);

        let originalFOV;
        let isZoomedIn = false;
        let controls; // Declare controls in the global scope

        function onDoubleClick(event) {
            const rect = container.getBoundingClientRect();
            mouse.x = ((event.clientX - rect.left) / container.clientWidth) * 2 - 1;
            mouse.y = -((event.clientY - rect.top) / container.clientHeight) * 2 + 1;

            raycaster.setFromCamera(mouse, camera);

            const intersects = raycaster.intersectObjects(scene.children, true);
            if (intersects.length > 0) {
                const intersectedObject = intersects[0].object;
                if (!isZoomedIn) {
                    originalFOV = camera.fov;
                    camera.fov = originalFOV * 0.75;
                    camera.updateProjectionMatrix();
                    controls.target.copy(intersectedObject.position);

                    controls.update();
                    isZoomedIn = true;
                } else {
                    camera.fov = originalFOV;
                    camera.updateProjectionMatrix();
                    controls.target.set(0, 0, 0);

                    controls.update();
                    isZoomedIn = false;
                }
            }
        }

        function onMouseMove(event) {
            const rect = container.getBoundingClientRect();
            mouse.x = ((event.clientX - rect.left) / container.clientWidth) * 2 - 1;
            mouse.y = -((event.clientY - rect.top) / container.clientHeight) * 2 + 1;
        }

        function onAllModelsLoaded() {
            applyColors();
            loadingIndicator.style.display = 'none';

            controls = new THREE.OrbitControls(camera, renderer.domElement);
            controls.rotateSpeed = 0.4;
            controls.enablePan = false;
            controls.enableDamping = true;
            controls.dampingFactor = 0.1;
            controls.minDistance = 5;
            controls.maxDistance = 10;
            controls.enabled = true;

            function centerModel() {
                if (baseModel) {
                    const box = new THREE.Box3().setFromObject(baseModel);
                    const center = box.getCenter(new THREE.Vector3());
                    baseModel.position.sub(center);
                    scene.position.add(center);
                }
            }

            function animate() {
                requestAnimationFrame(animate);
                raycaster.setFromCamera(mouse, camera);

                const intersects = raycaster.intersectObjects(scene.children, true);
                if (intersects.length > 0) {
                    container.style.cursor = 'pointer';
                } else {
                    container.style.cursor = 'default';
                }

                controls.update();
                centerModel();
                renderer.render(scene, camera);
            }

            animate();
        }

        function incrementLoadedModels() {
            loadedModels++;
            if (loadedModels === modelsToLoad) {
                onAllModelsLoaded();
            }
        }

        function applyColors() {
            const boatDetails = @json($boat->details);
            const colorMap = {};
            const otherOptions = [];

            boatDetails.forEach(detail => {
                if (detail.subcat_slug.endsWith('-color')) {
                    const firstWord = detail.subcat_slug.split('-')[0];
                    colorMap[firstWord] = detail.color;
                } else {
                    otherOptions.push(detail);
                }
            });

            Object.keys(colorMap).forEach(firstWord => {
                let modelFound = false;
                otherOptions.forEach(option => {
                    if (option.subcat_slug.startsWith(firstWord)) {
                        additionalModels.forEach((model, index) => {
                            if (model.userData.path.includes(option.file)) {
                                model.traverse(child => {
                                    if (child.isMesh) {
                                        child.material.color.set(colorMap[firstWord]);
                                    }
                                });
                                modelFound = true;
                            }
                        });
                    }
                });

                if (!modelFound) {
                    baseModel.traverse(child => {
                        if (child.name) {
                            const childName = child.name.trim().toLowerCase();
                            if (childName.includes(firstWord)) {
                                child.traverse(child => {
                                    if (child.isMesh && child.material) {
                                        child.material.color.set(colorMap[firstWord]);
                                        modelFound = true;
                                    }
                                });
                            }
                        }
                    });
                }
            });
        }

        function loadModel(path, targetSize, callback) {
            loader.load(path, function(gltf) {
                const model = gltf.scene;
                model.userData.path = path;
                model.traverse(child => {
                    if (child.isMesh) {
                        child.castShadow = true;
                        child.receiveShadow = true;

                        originalMaterials[child.name] = child.material.clone();
                        originalColors[child.name] = child.material.color.clone();

                        const newMaterial = new THREE.MeshStandardMaterial({
                            color: child.material.color,
                            metalness: 0.5,
                            roughness: 0.3
                        });
                        child.material = newMaterial;
                    }
                });

                const bbox = new THREE.Box3().setFromObject(model);
                const size = new THREE.Vector3();
                bbox.getSize(size);
                const maxDimension = Math.max(size.x, size.y, size.z);
                const scaleFactor = targetSize / maxDimension;
                model.scale.set(scaleFactor, scaleFactor, scaleFactor);
                bbox.setFromObject(model);
                const center = new THREE.Vector3();
                bbox.getCenter(center);

                model.position.x -= center.x;
                model.position.y -= center.y;
                model.position.z -= center.z;

                if (baseModel) {
                    model.position.copy(baseModel.position);
                    model.scale.copy(baseModel.scale);
                }

                callback(model);
                incrementLoadedModels();
            }, undefined, function(error) {
                console.error('Error loading model:', path, error);
                incrementLoadedModels();
            });
        }

        function calculateTargetSize() {
            return window.innerWidth < 768 ? 5 : 8;
        }

        loadModel(baseModelPath, calculateTargetSize(), function(model) {
            baseModel = model;
            scene.add(baseModel);

            accessoryModelPaths.forEach(function(path) {
                loadModel(path, 4, function(accessoryModel) {
                    additionalModels.push(accessoryModel);
                    scene.add(accessoryModel);
                });
            });
        });
    });
</script>





@endsection
