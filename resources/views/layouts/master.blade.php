<!DOCTYPE html>
<html lang="en">
	<head>
	    <meta charset="utf-8">
	    <meta http-equiv="X-UA-Compatible" content="IE=edge">
	    <!-- Tell the browser to be responsive to screen width -->
	    <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta http-equiv="cache-control" content="max-age=0" />
        <link rel="manifest" href="{{url('/manifest.json')}}">
	    <meta name="author" content="">
	    <title>Polibet</title>
        <meta name="csrf-token" content="{{ csrf_token() }}" />
        <meta property="og:site_name" content="Polibet">
        <meta property="og:title" content="First Peer to Peer Betting Platform" />
        <meta property="og:description" content="Polibet allows its users to create their own bets, set their options and conditions and share" />
        <meta property="og:image" itemprop="image" content="/images/favi/pb.png">
        <meta property="og:type" content="website" />
    	<link rel="shortcut icon" href="/images/favi/pb.png">
        <link href="https://fonts.googleapis.com/css?family=Material+Icons" rel="stylesheet">
        <link href="/css/loaderone.css" type="text/css" rel="stylesheet">
	    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
	    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
	    <!--[if lt IE 9]>
		    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
		    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
        <![endif]-->

           {{-- checks for service worker support.if you have the push manager package then use this line
        if ('serviceWorker' in navigator && 'PushManager' in window) instead of
        if ('serviceWorker' in navigator ) --}}
        <script>
            if ('serviceWorker' in navigator ) {
              window.addEventListener('load', function() {
                  navigator.serviceWorker.register('/service-worker.js').then(function(registration) {
                      // Registration was successful
                      console.log('ServiceWorker registration successful with scope: ', registration.scope);
                  }, function(err) {
                      // registration failed :(
                      console.log('ServiceWorker registration failed: ', err);
                  });
              });
          }
          </script>
	</head>
	<body>
	    <div id="root">
	        <router-view>
                <h1 style="color:green; position: fixed;
                top: 35%;
                left: 50%;
                transform: translate(-35%, -50%); font-style:italic; font-weight:bolder; font:larger;">Polibet</h1>
                <div class="spinner">
                        <div class="blob top"></div>
                        <div class="blob bottom"></div>
                        <div class="blob left"></div>
                        <div class="blob move-blob"></div>
                      </div>
            </router-view>
	    </div>
	    <script src="/js/app.js"></script>
	</body>
</html>
