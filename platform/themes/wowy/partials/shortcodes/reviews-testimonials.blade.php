@php
$reviews=\Botble\Ecommerce\Models\Review::where('show_on_page',1)->get();
@endphp
<section class="mt-50 pb-50">
    <div class="mt-50">
        <ul class="testimonial-wrapper row d-flex justify-content-center">
            <div class="row">
                @foreach($reviews as $review)
                <li class="col-lg-6 col-12">
                    <div class="testimonial-content">

                        <div class="spacing">
                            <div class="quote">
                                <i class='fas fa-quote-left fa-flip-vertical fa-3x'></i>
                            </div>
                            <p class="text2 text-left">{{$review->comment}}</p>
                        </div>
                        <div class="user-info d-flex">
                            <!-- <img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxzZWFyY2h8MXx8dXNlciUyMHByb2ZpbGV8ZW58MHx8MHx8&w=1000&q=80"
                                class="user-image"> -->
                            <img src="{{ RvMedia::getImageUrl($review->user->avatar, 'thumb') }}" alt="{{ $review->user->name }}" class="user-image">
                            <div class="detail-info">
                                <p class="name">{{$review->user->name}}</p>
                                <p class="occupation">Our Customer</p>
                            </div>
                        </div>

                    </div>
                </li>
                @endforeach
                
            </div>

        </ul>

    </div>


</section>