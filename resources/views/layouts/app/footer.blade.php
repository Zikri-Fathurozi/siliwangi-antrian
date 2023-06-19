<footer class="footer cstm_footer_app">
	<div class="container">
	  <div class="row align-items-center flex-row-reverse">
			<div class="col-auto ml-lg-auto">
				<div class="row align-items-center">
					<ul class="list-inline list-inline-dots mb-0">
						<li class="list-inline-item"><small>Version {{ config('app.version') }}</small></li>
					</ul>
				</div>
			</div>
			<div class="col-12 col-lg-auto mt-3 mt-lg-0 text-center">
				Copyright © {{ date('Y') }} <a href="">Sistem Antrian {{ config('antrian.header.name') }}</a>
			</div>
	  </div>
	</div>
</footer>
