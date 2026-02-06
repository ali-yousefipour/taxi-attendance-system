@extends('layouts.app')

@section('content')
<div class="container">
    <h3>جستجوی راننده</h3>
    <input type="text" id="driver-search" class="form-control" placeholder="نام یا کد ملی راننده">

    <div id="driver-results" class="mt-2"></div>

    {{-- فرم ثبت راننده جدید --}}
    <div id="new-driver-form" class="mt-3" style="display:none;">
        <h5>ثبت راننده جدید</h5>
        <input type="text" id="new-name" placeholder="نام راننده" class="form-control mb-2">
        <input type="text" id="new-nid" placeholder="کد ملی راننده" class="form-control mb-2">
        <select id="new-line" class="form-control mb-2">
            @foreach(Auth::user()->lines as $line)
                <option value="{{ $line->id }}">{{ $line->name }}</option>
            @endforeach
        </select>
        <button id="create-request" class="btn btn-primary">ارسال درخواست</button>
        <div id="request-msg" class="mt-2"></div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function(){
    let searchInput = document.getElementById('driver-search');
    let resultsDiv = document.getElementById('driver-results');
    let newDriverForm = document.getElementById('new-driver-form');
    let requestMsg = document.getElementById('request-msg');

    searchInput.addEventListener('input', function(){
        let q = this.value;
        if(q.length > 1){
            fetch(`/drivers/search?q=${q}`)
                .then(res => res.json())
                .then(data => {
                    resultsDiv.innerHTML = '';
                    if(data.length>0){
                        newDriverForm.style.display='none';
                        data.forEach(d => {
                            let div = document.createElement('div');
                            div.textContent = d.name + ' - ' + d.national_id;
                            div.classList.add('driver-item','p-1','border','mb-1');
                            div.addEventListener('click', function(){
                                alert('راننده انتخاب شد: ' + d.name);
                            });
                            resultsDiv.appendChild(div);
                        });
                    } else {
                        // راننده پیدا نشد → فرم راننده جدید
                        newDriverForm.style.display='block';
                        document.getElementById('new-nid').value = q;
                    }
                });
        } else {
            resultsDiv.innerHTML='';
            newDriverForm.style.display='none';
        }
    });

    document.getElementById('create-request').addEventListener('click', function(){
        fetch('/drivers/create-request',{
            method:'POST',
            headers:{
                'Content-Type':'application/json',
                'X-CSRF-TOKEN':'{{ csrf_token() }}'
            },
            body: JSON.stringify({
                name: document.getElementById('new-name').value,
                national_id: document.getElementById('new-nid').value,
                line_id: document.getElementById('new-line').value
            })
        }).then(res=>res.json())
          .then(data=>{
            requestMsg.textContent = data.message;
            newDriverForm.style.display='none';
          });
    });
});
</script>
@endsection
