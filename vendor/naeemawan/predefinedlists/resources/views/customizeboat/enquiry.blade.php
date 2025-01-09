@if ($boat_enquiry)
    <p>User Email: <i>{{ $boat_enquiry->customer->email }}</i></p>
    <p>User Name: <b><i>{{ $boat_enquiry->customer->name }}</i></b></p>
    <p>User Phone Number: <b><i>{{ $boat_enquiry->customer->phone }}</i></b></p>
    <p>Submitted At: <i>{{ $boat_enquiry->created_at }}</i></p>
    <p>Boat Name: <b><i>{{ $boat_enquiry->boat->ltitle }}</i></b></p>
    <p>Boat Customization Details: <b><i>{{ $boat_enquiry->message ?? '' }}</i></b></p>
    <div class="row" id="summary-end">
        <!-- start -->
        <!-- the selected options will be shown here -->
        <div class="col-12 m-auto">
            <div class="card card-custom">
                <div class="card-body summary-card justify-content-center d-flex flex-row flex-wrap">
                    @foreach ($boat_enquiry->details as $key => $value)
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
                                        $discounted_price = $value->enquiry_option->price - $value->discount_amount;
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
        </div>
        <!-- Final image -->
        <div class="row">
            @php
                $modelPath = $boat_enquiry->boat->file;
            @endphp
            {{-- 3d model div starts --}}
            <div id="3d-model" style="width: 100%; height: 500px; overflow:hidden"></div>
            {{-- 3d model div ends --}}
        </div>
    </div>
    <!-- end -->
    @php
        $discount = 0;
        $discount_type = '';
        $discount_on_boat = false;
        foreach ($boat_enquiry->boat->boat_discounts as $boat_discount) {
            $discount = $boat_discount->discount;
            $discount_type = $boat_discount->discount_type;
            if ($boat_discount->code == 'BOAT' || empty($boat_discount->code) || empty($boat_discount->accessory_id)) {
                $discount_on_boat = true;
            }
        }
        // Calculate the final price after applying the discount
        $original_price = $boat_enquiry->boat->price;

        if ($discount_type == 'percentage') {
            $discounted_price = $original_price - $original_price * ($discount / 100);
        } elseif ($discount_type == 'amount') {
            $discounted_price = $original_price - $discount;
        } else {
            $discounted_price = $original_price; // No discount
        }
        $formatted_price = format_price($discounted_price);
    @endphp
    <div class="card-footer">
        <div class="row m-2">
            <div class="col text-end">
                <hr />
                <p><b>Boat Price</b>: <span class="sub-total">{{ format_price($boat_enquiry->boat->price) }}</span>
                </p>
                @if ($discount && $discount_on_boat)
                    <p><b>Discount Boat Price</b>: <span class="sub-total">{{ $formatted_price }}</span>
                    </p>
                @endif
                <p><b>Total Price</b>: <span class="sub-total">{{ format_price($boat_enquiry->total_price) }}</span>
                </p>
                <p><b>Total Price with 5% Vat</b>: <span
                        class="sub-total">{{ format_price($boat_enquiry->vat_total) }}</span></p>
                <p><b>Paid</b>: <span class="boat-price">{{ format_price($boat_enquiry->paid_amount) }}</span></p>
                <p><b>Remaining Price</b>: <span
                        class="boat-price">{{ format_price($boat_enquiry->vat_total - $boat_enquiry->paid_amount) }}</span>
                </p>
            </div>
        </div>
    </div>
    </div>
    </div>
@endif
<style>
    .carousel-control-prev {
        top: 80%;
        left: 40%;
    }

    .carousel-control-next {
        top: 80%;
        left: 50%;
    }

    .carousel-control-next-icon {
        background-image: none;
    }

    .carousel-control-prev-icon {
        background-image: none;
    }

    .custom-boat .carousel-control-next-icon {
        width: 20px;
        height: 20px;
        border: 4px solid #182955;
        border-left: 0;
        border-bottom: 0;
        transform: rotate(45deg);
    }

    .custom-boat .carousel-control-prev-icon {
        width: 20px;
        height: 20px;
        border: 4px solid #182955;
        border-right: 0;
        border-top: 0;
        transform: rotate(45deg);
    }

    .color-title-container {
        display: flex;
    }

    .color-rounded {
        margin-left: 10px;
        border-radius: 50%;
        width: 30px;
        height: 30px;
        margin-top: -5px;
    }
</style>
<script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/three/examples/js/controls/OrbitControls.js"></script>
<script src="https://cdn.jsdelivr.net/npm/three/examples/js/loaders/GLTFLoader.js"></script>
<script src="https://cdn.jsdelivr.net/npm/three/examples/js/loaders/DRACOLoader.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const baseModelPath = '{{ asset('storage/' . $modelPath) }}';
        const accessoryModelPaths = [
            @foreach ($boat_enquiry->details as $value)
                '{{ asset('storage/' . $value->file) }}',
            @endforeach
        ];

        const container = document.getElementById('3d-model');
        const scene = new THREE.Scene();
        const camera = new THREE.PerspectiveCamera(75, container.clientWidth / container.clientHeight, 0.1,
            1000);
        camera.position.set(0, 0, 6);
        camera.lookAt(scene.position);

        const renderer = new THREE.WebGLRenderer({
            antialias: true
        });
        renderer.physicallyCorrectLights = true;
        renderer.outputEncoding = THREE.sRGBEncoding;
        renderer.toneMapping = THREE.ACESFilmicToneMapping;
        renderer.toneMappingExposure = 1.6;
        renderer.setSize(container.clientWidth, container.clientHeight);
        renderer.setClearColor(0x182955);
        renderer.shadowMap.enabled = true;
        renderer.shadowMap.type = THREE.PCFSoftShadowMap;
        container.appendChild(renderer.domElement);

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

        function onAllModelsLoaded() {
            applyColors();
        }

        function incrementLoadedModels() {
            loadedModels++;
            if (loadedModels === modelsToLoad) {
                onAllModelsLoaded();
            }
        }

        function applyColors() {
            const boatDetails = @json($boat_enquiry->details);
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

            // console.log('Color Map:', colorMap);
            // console.log('Other Options:', otherOptions);

            Object.keys(colorMap).forEach(firstWord => {
                // console.log(`Processing color map entry: ${firstWord}, Color: ${colorMap[firstWord]}`);

                let modelFound = false;
                otherOptions.forEach(option => {
                    if (option.subcat_slug.startsWith(firstWord)) {
                        console.log(`Option found for ${firstWord}: ${option.subcat_slug}`);

                        additionalModels.forEach((model, index) => {
                            if (model.userData.path.includes(option.file)) {
                                // console.log("File found in additional models");
                                model.traverse(child => {
                                    if (child.isMesh) {
                                        child.material.color.set(colorMap[
                                            firstWord]);
                                    }
                                });
                                modelFound = true;
                            }
                        });
                    }
                });

                if (!modelFound) {
                    // console.log(`No matching option found for ${firstWord} in otherOptions`);
                    // console.log(`Checking base model children for ${firstWord}`);
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
                                basePartFound = true;
                            }
                        }
                    });

                }

                if (!modelFound) {
                    console.log(`No matching parts found in base model for ${firstWord}`);
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
                            roughness: 0.5
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

            const controls = new THREE.OrbitControls(camera, renderer.domElement);
            controls.enableDamping = false;
            controls.minDistance = 5;
            controls.maxDistance = 13;
            controls.enabled = false;
            container.addEventListener('pointerenter', () => {
                controls.enabled = true;
            });
            container.addEventListener('pointerleave', () => {
                controls.enabled = false;
            });

            function animate() {
                requestAnimationFrame(animate);
                renderer.render(scene, camera);
                controls.update();
            }

            animate();
        });
    });
</script>
