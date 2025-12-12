<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1"/>
<title>Home - Raisan Live | Rplex</title>
<link rel="icon" href="https://assets.apk.live/com.roarzone.tvapps--3-icon.png"/>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" />
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" />
<style>
    body { font-family: "Montserrat", sans-serif; background-color: white; }
    .card { color: #fff; border-radius:15px; background-color: #f2e9e9; text-align:center; border:1px solid #333; transition:0.3s; cursor: pointer; overflow: hidden; }
    .card:hover { background-color: #ed1111; border-color: #050503; transform: translateY(-5px); box-shadow: 0 5px 15px rgba(229, 9, 20, 0.4); }
    .card a { text-decoration:none; color:#fff; }
    .tvimage { object-fit: contain; background: #000; padding: 10px; width: 100%; height: 100px; }
    .boldbtn { font-weight:bold; }
    .prvselect { user-select:none; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; padding: 10px; font-size: 14px; }
    .toast-body { font-weight:bold; color:#f52f0c; }
    .spinner-border { width: 3rem; height: 3rem; color: #e6230e; }
</style>
</head>
<body>

<nav class="navbar bg-body-dark">
  <div class="container-fluid justify-content-center">
    <a class="navbar-brand mt-4 mb-2" href="index.php">
      <img class="navbar-brand-logo" src="https://assets.apk.live/com.roarzone.tvapps--3-icon.png" alt="Logo" style="width:120px;height:auto;">
    </a>
  </div>
</nav>

<div class="container mt-2 mb-4">
  <div class="input-group">
    <input type="text" class="form-control" placeholder="Search Channels..." id="inpSearchTV" autocomplete="off" style="background:#222;border:none;color:white;padding:15px;"/>
    <button class="btn btn-danger" type="button" id="btnInitTVSearch"><i class="fa-solid fa-magnifying-glass"></i></button>
  </div>
</div>

<div class="container">
  <div align="center" class="mt-4" id="tvsGrid">
    <div style="margin-top:100px;">
      <div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div>
      <p class="text-white mt-3">Loading Channels...</p>
    </div>
  </div>
</div>

<div class="toast-container position-fixed bottom-0 start-50 translate-middle-x p-3"></div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

<script>
$(document).ready(function(){
    loadTVlist();
});

$("#btnInitTVSearch").on("click", function() {
    searchTVlist();
});

$("#inpSearchTV").on('keydown', function(e) {
    if(e.key==='Enter' || e.keyCode===13){ 
        e.preventDefault(); 
        searchTVlist(); 
    }
});

function toaster(text){
    $(".toast-container").html(`<div class="toast" role="alert" aria-live="assertive" aria-atomic="true"><div class="toast-body">`+text+`</div></div>`);
    $(".toast").toast("show");
}

function loadTVlist(){
    $.ajax({
        url: "api.php", // আপনার আগের তৈরি করা api.php ফাইলটি থাকতে হবে
        type: "GET",
        data: { route: "getChannels" },
        dataType: "json",
        success: function(data){
            if(data.status=="success"){
                renderChannelGrid(data.data.list);
            } else {
                toaster("Error: "+data.message);
                $("#tvsGrid").html('<div class="text-white" style="margin:100px;">'+data.message+'</div>');
            }
        },
        error: function(xhr){
            $("#tvsGrid").html('<div class="text-white" style="margin:100px;">Failed to connect to API.</div>');
        }
    });
}

function renderChannelGrid(channels){
    if(channels.length==0){
        $("#tvsGrid").html('<div class="text-white" style="margin:100px;">No Channels Available</div>');
        return;
    }
    let lmtl='<div class="row mt-3">';
    $.each(channels,function(k,v){
        // স্ট্রিম লিংকটি এনকোড করা হচ্ছে যাতে URL এ ভেঙে না যায়
        let streamEncoded = btoa(v.stream_url); 
        let titleEncoded = encodeURIComponent(v.title);
        let logoEncoded = encodeURIComponent(v.logo);

        lmtl+='<div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-4" onclick="playlivetv(\''+streamEncoded+'\', \''+titleEncoded+'\', \''+logoEncoded+'\')" title="Watch '+v.title+'">';
        lmtl+='<div class="card"><div class="card-body p-2">';
        lmtl+='<img class="tvimage" src="'+v.logo+'" onerror="this.onerror=null;this.src=\'https://via.placeholder.com/100x100?text=TV\';" alt="Logo"/>';
        lmtl+='<div class="mt-2 prvselect"><b>'+v.title+'</b></div>';
        lmtl+='</div></div></div>';
    });
    lmtl+='</div>';
    $("#tvsGrid").html(lmtl);
}

// নতুন প্লেয়ার ফাংশন - সরাসরি play.php তে নিয়ে যাবে
function playlivetv(stream, title, logo){
    window.location = "play.php?stream=" + stream + "&title=" + title + "&logo=" + logo;
}

function searchTVlist(){
    let query=$("#inpSearchTV").val().trim().toLowerCase();
    if(query.length<1){ loadTVlist(); return; }
    
    toaster("Searching...");
    
    $.ajax({
        url:"api.php",
        type:"POST",
        data:{action:"searchChannels",query:query},
        dataType: "json",
        success:function(data){
            if(data.status=="success"){
                renderChannelGrid(data.data.list);
            } else {
                toaster("No Results");
                $("#tvsGrid").html('<div class="text-white" style="margin:100px;">No channels found.</div>');
            }
        },
        error:function(){
            toaster("Search Failed");
        }
    });
}
</script>
</body>
<a style="display: inline-block; font-size: 16px; font-weight: 500; text-align: center; border-radius: 20px; padding: 25px 30px; background: #15e2ed; text-decoration: none; color: #e8150e;" href="https://t.me/rrplex" target="_blank"> Join Us Telegram </a>
</html>