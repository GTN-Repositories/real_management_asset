<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
<div class="row gap-5">
    <div class="col-12 col-md-2 text-center">
        <img src="{{ asset('images/notif_email.png') }}" width="150" height="110" style="border-radius: 10px;"
            alt="">
    </div>
    <div class="col-12 col-md-8 d-flex flex-column gap-2">
        <h3 class="mb-3 fw-bold">{{ $notification->title }}</h3>
        <p class="m-0">
            {!! $notification->body !!}
        </p>
        <span class="m-0 fw-bold">{{ $notification->created_at->diffForHumans() }}</span>
    </div>
</div>
<hr>
