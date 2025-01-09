<footer class="main">
    <section class="section-padding-60">
        <div class="container">
            <div class="row">
                {!! dynamic_sidebar('footer_sidebar') !!}
            </div>
        </div>
    </section>
    <div class="container pb-20 wow fadeIn animated">
        <div class="row">
            <div class="col-12 mb-20">
                <div class="footer-bottom"></div>
            </div>
            <div class="col-lg-6">
                <p class="float-md-left font-sm mb-0">{{ theme_option('copyright') }} {{ __('All rights reserved.') }}
                </p>
            </div>
            {{-- <div class="col-lg-6">
                <p class="text-lg-end text-start font-sm mb-0">
                    Made with <i class="fa fa-heart" style="color:red"></i> Wisdom
                </p>
            </div> --}}
        </div>
    </div>
</footer>

<!-- Quick view -->
<div class="modal fade custom-modal" id="quick-view-modal" tabindex="-1" aria-labelledby="quick-view-modal-label"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            <div class="modal-body">
                <div class="half-circle-spinner loading-spinner">
                    <div class="circle circle-1"></div>
                    <div class="circle circle-2"></div>
                </div>
                <div class="quick-view-content"></div>
            </div>
        </div>
    </div>
</div>

@if (is_plugin_active('ecommerce'))
    <script>
        window.currencies = {!! json_encode(get_currencies_json()) !!};
    </script>
@endif

{!! Theme::footer() !!}

<script>
    window.trans = {
        "Views": "{{ __('Views') }}",
        "Read more": "{{ __('Read more') }}",
        "days": "{{ __('days') }}",
        "hours": "{{ __('hours') }}",
        "mins": "{{ __('mins') }}",
        "sec": "{{ __('sec') }}",
        "No reviews!": "{{ __('No reviews!') }}"
    };
</script>

{!! Theme::place('footer') !!}

@if (session()->has('success_msg') ||
        session()->has('error_msg') ||
        (isset($errors) && $errors->count() > 0) ||
        isset($error_msg))
    <script type="text/javascript">
        window.onload = function() {
            @if (session()->has('success_msg'))
                window.showAlert('alert-success', '{{ session('success_msg') }}');
            @endif

            @if (session()->has('error_msg'))
                window.showAlert('alert-danger', '{{ session('error_msg') }}');
            @endif

            @if (isset($error_msg))
                window.showAlert('alert-danger', '{{ $error_msg }}');
            @endif

            @if (isset($errors))
                @foreach ($errors->all() as $error)
                    window.showAlert('alert-danger', '{!! BaseHelper::clean($error) !!}');
                @endforeach
            @endif
        };
    </script>
@endif
<script>
    // boats page thing for the boat category.....
    $(document).on('click', '.boat-category', function(e) {
        e.preventDefault();
        const $this = $(e.currentTarget);
        const href = $this.attr('data-value');
        $(".list-content-loading").show();
        $.ajax({
            url: href,
            success: function(data) {
                $(".list-content-loading").show();
                $('.products-listing').empty();
                $('.products-listing').append(data['data']);
            }
        });
        // window.location.href = href;
    });

    var url = "<?php echo url('/'); ?>";

    $(document).ready(function() {

        $('body').on('click', '.card-btn', function() {
            val = $(this).data('value');
            curval = $(this).data('curval');
            $('.card-' + val).removeClass('d-none');
            $('.card-' + curval).addClass('d-none');

            if ($(this).hasClass('prv')) {
                $('.cat-' + curval).removeClass('selected');
                $('.card-' + val).removeClass('d-none');
                $('.card-' + curval).addClass('d-none');
            }
            $('.cat-' + val).addClass('selected');
        });
        $('.dropdown').hover(function() {
            $(this).find('.dropdown-menu').addClass('show');
            $(this).find('.dropdown-toggle').attr('aria-expanded', 'true');
        }, function() {
            $(this).find('.dropdown-menu').removeClass('show');
            $(this).find('.dropdown-toggle').attr('aria-expanded', 'false');
        });
        $('.dropdown-menu').mouseleave(function() {
            $(this).removeClass('show');
            $(this).prev('.dropdown-toggle').attr('aria-expanded', 'false');
        });

        $('#summary-end').hide();
        var price = 0;
        var currency = "<?php echo get_application_currency()['symbol']; ?>";
        var boat_price = parseFloat($('input[name="boat_price"]').val());
        var total_price = boat_price;
        var vat_price = 0;
        var vat_total = 0;

        // Initialize prices for standard options
        initializeStandardOptions();

        function initializeStandardOptions() {
            $('input.cat-item-check[data-waschecked="true"]').each(function() {
                var value = $(this).val();
                var typename = $(this).attr('data-typename');
                var type = $(this).attr('data-type');
                var optionPrice = parseFloat($(this).data('price'));

                price += optionPrice;
                addToSummary($(this), value, typename, type, optionPrice);
            });
            updateTotalPrice();
        }

        function addToSummary(element, value, typename, type, optionPrice) {
            var html = `<div id="${value}" class="shadow card m-2 ${type}">
                        <div class="head-cat card-header">
                            <p class="text-center"><b>${typename}</b></p>
                        </div>
                        <div class="card-body text-center">
                            <div class="spacing">
                                <p>${element.next('label').text()}</p>
                                <p><b>Price</b> : <span class="price">${Math.trunc(optionPrice).toLocaleString('en-US')}${currency}</span></p>
                            </div>
                        </div>
                    </div>`;

            $('#summary-end .summary-card').append(html);
        }

        function updateTotalPrice() {
            var currentPrice = boat_price + price; // This is the current price

            // Get discount information from hidden fields
            var discountValue = $('#discount_value').val();
            var discountType = $('#discount_type').val();
            var priceHtml = '';

            if (discountValue && discountType) {
                var originalPrice = currentPrice;
                if (discountType === 'percentage') {
                    var discountPercentage = parseFloat(discountValue);
                    originalPrice = currentPrice / (1 - discountPercentage / 100);
                    priceHtml =
                        `<s>${originalPrice.toLocaleString('en-US', {maximumFractionDigits: 2})}${currency}</s> ${currentPrice.toLocaleString('en-US', {maximumFractionDigits: 2})}${currency}<br><small>(${discountValue}% off)</small>`;
                } else if (discountType === 'fixed') {
                    var discountAmount = parseFloat(discountValue);
                    originalPrice = currentPrice + discountAmount;
                    priceHtml =
                        `<s>${originalPrice.toLocaleString('en-US', {maximumFractionDigits: 2})}${currency}</s> ${currentPrice.toLocaleString('en-US', {maximumFractionDigits: 2})}${currency}<br><small>(${discountAmount.toLocaleString('en-US', {maximumFractionDigits: 2})}${currency} off)</small>`;
                }
            } else {
                // No discount applied, just show the current price
                priceHtml = `${currentPrice.toLocaleString('en-US', {maximumFractionDigits: 2})}${currency}`;
            }

            $('.sub-total').html(priceHtml);

            vat_price = (currentPrice * 5) / 100;
            $('.vat-price').text(vat_price.toLocaleString('en-US', {
                maximumFractionDigits: 2
            }) + currency);
            vat_total = currentPrice + vat_price;
            $('.vat-total').text(vat_total.toLocaleString('en-US', {
                maximumFractionDigits: 2
            }) + currency);
            $('input[name="total_price"]').val(currentPrice);
        }

        // Event handler for radio buttons
        $('body').on('click', 'input[type="radio"]', function() {
            var src = url + '/storage/' + 'transparent-pic-150x150.png';
            var value = $(this).val();
            var parent = $(this).data('parent');
            var typename = $(this).attr('data-typename');
            var type = $(this).attr('data-type');

            if ($(this).prop('checked') && $(this).attr('data-waschecked') == 'true') {
                handleUncheckedRadio($(this), type, parent);
            } else {
                handleCheckedRadio($(this), value, typename, type, parent);
            }
        });

        var selectedOptions = {};

        function handleCheckedRadio(element, value, typename, type, parent) {
            if (selectedOptions[type]) {
                var prevElement = $(`input[value="${selectedOptions[type]}"]`);
                handleUnchecked(prevElement, type, prevElement.val());
            } else {
                var standardOption = $(`input[name="option[${type}]"][data-waschecked="true"]`);
                if (standardOption.length && standardOption.val() !== value) {
                    handleUnchecked(standardOption, type, standardOption.val());
                }
            }

            if (element.attr('data-waschecked') == 'true') {
                var optionPrice = parseFloat(element.data('price'));
                addToSummary(element, value, typename, type, optionPrice);
                price += optionPrice;
                updateTotalPrice();
            } else {
                $.ajax({
                    type: "Post",
                    url: url + "/customize-type-content",
                    data: {
                        value: value
                    },
                    success: function(data) {
                        addToSummary(element, value, typename, type, parseFloat(data.price));
                        price += parseFloat(data.price);
                        updateTotalPrice();
                    }
                });
            }

            $('input[name="option[' + type + ']"]').attr('data-waschecked', 'false');
            element.attr('data-waschecked', 'true');
            element.prop('checked', true);
            selectedOptions[type] = value;
        }

        function handleUncheckedRadio(element, type, parent) {
            var prevDiv = $('#summary-end .summary-card #' + element.val());
            if (prevDiv.length) {
                var prevPrice = prevDiv.find('.price').text().replace(",", "");
                price -= parseFloat(prevPrice);
                prevDiv.remove();
                updateTotalPrice();
            }
            $('input[name="option[' + type + ']"]').attr('data-waschecked', 'false');
            element.attr('data-waschecked', 'false');
            element.prop('checked', false);
            delete selectedOptions[type];
        }
        // Event handler for checkboxes
        $('body').on('click', 'input[type="checkbox"]', function() {
            var value = $(this).val();
            var typename = $(this).attr('data-typename');
            var type = $(this).attr('data-type');

            if ($(this).is(':checked')) {
                handleChecked($(this), value, typename, type, value);
            } else {
                handleUnchecked($(this), type, value);
            }
        });

        function handleChecked(element, value, typename, type, parent) {
            if (element.attr('data-waschecked') == 'true') {
                // Option was already checked, use data-price
                var optionPrice = parseFloat(element.data('price'));
                addToSummary(element, value, typename, type, optionPrice);
                price += optionPrice;
                updateTotalPrice();
            } else {
                // Make AJAX call for newly selected option
                $.ajax({
                    type: "Post",
                    url: url + "/customize-type-content",
                    data: {
                        value: value
                    },
                    success: function(data) {
                        updateImages(data, parent);
                        addToSummary(element, value, typename, type, parseFloat(data.price));
                        price += parseFloat(data.price);
                        updateTotalPrice();
                    }
                });
            }
            $('input[name="option[' + type + ']"]').attr('data-waschecked', 'false');
            element.attr('data-waschecked', 'true');
            element.prop('checked', true);
        }

        function handleUnchecked(element, type, parent) {
            resetImages(parent);
            var prevDiv = $('#summary-end .summary-card #' + element.val());
            if (prevDiv.length) {
                var prevPrice = prevDiv.find('.price').text().replace(",", "");
                price -= parseFloat(prevPrice);
                prevDiv.remove();
                updateTotalPrice();
            }
            $('input[name="option[' + type + ']"]').attr('data-waschecked', 'false');
            element.attr('data-waschecked', 'false');
            element.prop('checked', false);
        }


        function updateImages(data, parent) {
            if (data['preview_enabled']) {
                $('.cat-' + parent + '-img1').find('img').attr('src', url + '/storage/' + data['image'][1]);
                $('.cat-' + parent + '-img2').find('img').attr('src', url + '/storage/' + data['image'][2]);
                $('.cat-' + parent + '-img3').find('img').attr('src', url + '/storage/' + data['image'][3]);
                if (data['image'].hasOwnProperty(4)) {
                    $('.cat-' + parent + '-img4').find('img').attr('src', url + '/storage/' + data['image'][4]);
                }
            }
        }

        function resetImages(parent) {
            var src = url + '/storage/' + 'transparent-pic-150x150.png';
            $('.cat-' + parent + '-img1').find('img').attr('src', src);
            $('.cat-' + parent + '-img2').find('img').attr('src', src);
            $('.cat-' + parent + '-img3').find('img').attr('src', src);
            if ($('.cat-' + parent + '-img4').length) {
                $('.cat-' + parent + '-img4').find('img').attr('src', src);
            }
        }
        const viewSummaryButton = document.getElementById('view-summary');
            if (viewSummaryButton) {
                viewSummaryButton.addEventListener('click', function() {
                    const summaryEnd = document.getElementById('summary-end');
                    if (summaryEnd) {
                        summaryEnd.style.display = 'block'; 
                        summaryEnd.scrollIntoView({ behavior: 'smooth' }); 
                    }
                });
            }

        // $('body').on('click', '.view-summary', function() {
        //     $('#summary-end').show();
        //     $('html, body').animate({
        //         scrollTop: $('#summary-end').offset().top
        //     }, 1000);
        // });

        $('body').on('click', '.submit-btn', function() {
            $("input[name='redirect_url_pay']").val(1);
            $('#exampleModal').modal('show');
        });

        $('body').on('click', '#submit-boot', function() {
            // Add the "button-loading" class to the button
            $(this).addClass("button-loading");
            $('#submit-form').submit();
            setTimeout(function() {
                $('#submit-boot').removeClass("button-loading");
            }, 3000);
        });

    });
    //     document.querySelectorAll('.applyPromo').forEach(button => {
    //     button.addEventListener('click', function() {
    //         const input = this.previousElementSibling;
    //         const spinner = this.nextElementSibling;
    //         const discountPercentage = parseFloat(this.closest('.promo').querySelector('.discount h4').textContent);
    //         const originalPriceElement = this.closest('.promo').querySelector('.original-price');
    //         const discountedPriceElement = this.closest('.promo').querySelector('.discounted-price');
    //         input.disabled = true;
    //         spinner.classList.remove('d-none');
    //         setTimeout(() => {
    //             spinner.classList.add('d-none');
    //             const originalPrice = parseFloat(originalPriceElement.textContent.replace('AED', ''));
    //             const discountedPrice = originalPrice - (originalPrice * (discountPercentage / 100));
    //             discountedPriceElement.textContent = `${discountedPrice.toFixed(2)} AED`;
    //         }, 2000);
    //     });
    // });
</script>


<div id="scrollUp"><i class="fal fa-long-arrow-up"></i></div>
</body>

</html>
