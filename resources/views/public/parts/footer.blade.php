
        <!-- Footer Top
        ============================================ -->
        <div class="footer-top">
            <div class="container-fluid">
                <div class="row">

                    <div class="col-md-2 col-xs-12 text-center">
                        <img src="<?= asset('img/busvi_logo_blanco.png')?>" class="logo" style="float: none; margin-bottom: 30px; width: 90%;">
                    </div>
                    <div class="sin-footer col-md-3 col-xs-12">
                        <h3>Sobre nosotros</h3>
                        <p>¿Eres profesional y tienes un negocio? ó ¿Quieres ser Usuario?</p>
                        <p>Únete a nosotros <a href="<?=\URL::to('/')?>" class="link">www.busvi.com</a></p>
                        <p>
                            <a style="display: inline-block;" target="_blank" href="https://apps.apple.com/us/app/busvi/id1442203679?l=es&ls=1"><img src="<?=asset('img/appstore-big.png')?>" class="store"></a>
                            <a style="display: inline-block;" target="_blank" href="https://play.google.com/store/apps/details?id=es.citaplus.app&gl=ES"><img src="<?=asset('img/android-big.png')?>" class="store"></a>
                        </p>
                    </div>

                    <div class="col-md-7 col-xs-12">
                        <div class="row">

                            <div class="sin-footer col-md-4 col-xs-12">
                                <h3>Información de Contacto</h3>
                                <p>
                                    <strong>E-Mail</strong> <a href="mailto:info@busvi.com" class="link">info@busvi.com</a>
                                </p>
                            </div>

                            <div class="sin-footer col-md-4 col-xs-12">
                                <h3>Términos y Condiciones</h3>
                                <p>¿Quieres conocer cuales son nuestros términos y nuestras condiciones de uso? Desde <a href="{{ url('terminos-y-condiciones') }}" class="link">aquí</a> podrás hacerlo.</p>
                            </div>

                            <div class="sin-footer col-md-4 col-xs-12">
                                <h3>Métodos de Pago</h3>
                                <img src="{{ asset('wyzi/img/payment-method.png') }}" alt="" />
                            </div>

                        </div>
                    </div>

                </div>

            </div>
        </div>