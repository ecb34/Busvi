
        <script src="{{ asset('wyzi/js/vendor/jquery-3.1.1.min.js') }}"></script>
        <script src="{{ asset('wyzi/js/bootstrap.min.js') }}"></script>
        <script src="{{ asset('wyzi/js/jquery.meanmenu.min.js') }}"></script>
        <script src="{{ asset('wyzi/js/owl.carousel.min.js') }}"></script>        
        <script src="{{ asset('wyzi/js/jquery.scrollup.min.js') }}"></script>
        <script src="{{ asset('wyzi/js/rangeslider.min.js') }}"></script>
        <script src="{{ asset('wyzi/js/jquery.magnific-popup.min.js') }}"></script>
        <script src="{{ asset('wyzi/js/range-active.js') }}"></script>

        <script src="{{ asset('plugins/moment/moment-with-locales.min.js') }}"></script>
        <script src="{{ asset('plugins/moment/moment.min.js') }}"></script>
        <script src="{{ asset('plugins/sweetalert2/sweetalert2.min.js') }}"></script>

        <script src="{{ asset('lib/animateNumber/jquery.animateNumber.min.js') }}"></script>
        <script src="{{ asset('bower_components/bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js') }}"></script>

        <script src="{{ asset('plugins/fullcalendar_cloudflare/fullcalendar.min.js') }}"></script>
        <script src="{{ asset('plugins/fullcalendar_cloudflare/es.min.js') }}"></script>
        <script src="{{ asset('plugins/jquery.fancybox.min.js') }}"></script>        
    
        <script src="{{ asset('wyzi/js/main.js') }}"></script>

        <script>

            $('.btn-logout').on('click', function (e) {
                e.preventDefault();
                $('#logout-form').submit();
            });

            function mostrar_error(text){
                swal({
                    title: "<?=trans('app.error')?>",
                    text: text,
                    type: "error",
                    showCancelButton: false,
                    confirmButtonClass: "btn-danger",
                    confirmButtonText: "<?=trans('app.cerrar')?>",
                    closeOnConfirm: false
                });
            }

            function mostrar_success(text){
                swal({
                    title: text,
                    type: "success",
                    showCancelButton: false,
                    confirmButtonText: "<?=trans('app.cerrar')?>",
                    closeOnConfirm: false
                });
            }

      		function mostrar_info(text){
      			notie.alert(1, text, 1);
      		}

        </script>

      	<?php if($error = $errors->first()) \Session::put('error', ucfirst($error)); ?>

      	<?php if(\Session::has('error')){ ?>
      	<script>$(document).ready(function(){ mostrar_error("<?=\Session::get('error')?>"); });</script>
      	<?php \Session::forget('error'); } ?>

      	<?php if(\Session::has('success')){ ?>
      	<script>$(document).ready(function(){ mostrar_success("<?=\Session::get('success')?>"); });</script>
      	<?php \Session::forget('success'); } ?>

      	<?php if(\Session::has('info')){ ?>
      	<script>$(document).ready(function(){ mostrar_info("<?=\Session::get('info')?>"); });</script>
      	<?php \Session::forget('info'); } ?>

        <script src="//cdnjs.cloudflare.com/ajax/libs/cookieconsent2/3.1.0/cookieconsent.min.js"></script>
        <script>
            window.addEventListener("load", function(){
                window.cookieconsent.initialise({
                    "palette": {
                        "popup": { "background": "#000" },
                        "button": { "background": "#f1d600" }
                    },
                    "theme": "edgeless",
                    "content": {
                        "message": "Busvi utiliza cookies para mejorar la experiencia de los usuarios, facilitando la navegación por nuestra web. Estamos haciendo todo lo posible por facilitar el uso de dichas cookies, así como su gestión y control al utilizar nuestros servicios.",
                        "dismiss": "De acuerdo",
                        "link": "Cookies",
                        "href": "<?=\URL::to('/terminos-y-condiciones')?>"
                    }
                });
            });
        </script>

      @yield('scripts')
