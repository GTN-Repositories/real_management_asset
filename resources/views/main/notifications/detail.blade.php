<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
<div class="d-flex p-2 gap-5">
    <img src="{{ asset('images/notif_email.png') }}" width="150" height="110" style="border-radius: 10px;"
        alt="">
    <div class=" d-flex flex-column gap-2">
        <h3 class="m-0 fw-bold">{{ $notification->title }}</h3>
        <div class="d-flex justify-content-between">
            <h5 class="m-0 fw-bold">{{ $notification->title }}</h5>
            <span class="m-0 fw-bold">{{ $notification->created_at->diffForHumans() }}</span>
        </div>
        <p class="m-0">
            {!! $notification->body !!}
        </p>
        </div>
    </div>
    <hr>