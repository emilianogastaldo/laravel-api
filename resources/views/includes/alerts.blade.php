{{-- Alert per i messaggi --}}
@session('message')
<div class="alert alert-{{session('type', 'info')}} alert-dismissible fade show" role="alert">
    {{$value}}
    <button type="button" id="btnClose" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endsession


{{-- Alert per gli errori --}}
@if ($errors->any())    
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <ul class="m-0">
        @foreach ($errors->all() as $error )
            <li>{{$error}}</li>
        @endforeach
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<script>
    // Faccio sparire dopo 3 secondi il toast
    const closeBtn = document.getElementById('btnClose');
    if(closeBtn) setTimeout(() => { closeBtn.click() }, 5000);
</script>