        <meta charset="UTF-8">
        <title>busvi</title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        <meta name="_token" content="{!! csrf_token() !!}"/>

        <link rel="icon" type="image/png" href="{{ asset('img/favicon.png') }}">

        <link href='https://fonts.googleapis.com/css?family=Raleway:400,300,500,600,700,800,900' rel='stylesheet' type='text/css'>
        <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,800,700,600,300' rel='stylesheet' type='text/css'>
        <link href='https://fonts.googleapis.com/css?family=Varela+Round' rel='stylesheet' type='text/css'>

        <link rel="stylesheet" href="{{ asset('wyzi/fonts/montserrat/font-style.css') }}">
        <link rel="stylesheet" href="{{ asset('wyzi/css/bootstrap.min.css') }}">
        <link rel="stylesheet" href="{{ asset('wyzi/css/font-awesome.min.css') }}">        
        <link rel="stylesheet" href="{{ asset('wyzi/css/meanmenu.min.css') }}">
        <link rel="stylesheet" href="{{ asset('wyzi/css/owl.carousel.css') }}">
        <link rel="stylesheet" href="{{ asset('wyzi/css/magnific-popup.css') }}">
        <link rel="stylesheet" href="{{ asset('wyzi/css/default.css') }}">
        <link rel="stylesheet" href="{{ asset('wyzi/style.css') }}">        
        <link rel="stylesheet" href="{{ asset('wyzi/css/responsive.css') }}">
        <link rel="stylesheet" href="{{ asset('bower_components/bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/sweetalert2/sweetalert2.min.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/jquery.fancybox.min.css') }}" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.9.0/fullcalendar.min.css"/>

        <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
        <link rel="stylesheet" href="{{ asset('css/busvi.css') }}?v=0.2">

        <script src="https://use.fontawesome.com/e4e04b2a26.js"></script>
        <script src='https://www.google.com/recaptcha/api.js'></script>

        <link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/cookieconsent2/3.1.0/cookieconsent.min.css" />

        <!-- Facebook Pixel Code -->
        <script>
                !function(f,b,e,v,n,t,s)
                {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
                n.callMethod.apply(n,arguments):n.queue.push(arguments)};
                if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
                n.queue=[];t=b.createElement(e);t.async=!0;
                t.src=v;s=b.getElementsByTagName(e)[0];
                s.parentNode.insertBefore(t,s)}(window,document,'script',
                'https://connect.facebook.net/en_US/fbevents.js');
                fbq('init', '333036917661682'); 
                fbq('track', 'PageView');
        </script>
        <noscript>
                <img height="1" width="1" src="https://www.facebook.com/tr?id=333036917661682&ev=PageView&noscript=1"/>
        </noscript>
        <!-- End Facebook Pixel Code -->