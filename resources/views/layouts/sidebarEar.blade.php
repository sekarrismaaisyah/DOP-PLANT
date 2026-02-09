<aside class="sidebar-wrapper">
    <style>
      .sidebar-wrapper .sidebar-header .logo-icon { width:100%; display:flex; justify-content:center; align-items:center; }
      .sidebar-wrapper .sidebar-header .logo-img { height:70px; width:auto; }
      .sidebar-wrapper .sidebar-header .logo-img.logo-small { display:none; height:40px; width:auto; }
      @media screen and (min-width:1199px){
        body.toggled:not(.sidebar-hovered) .sidebar-wrapper .sidebar-header .logo-img.logo-large { display:none; }
        body.toggled:not(.sidebar-hovered) .sidebar-wrapper .sidebar-header .logo-img.logo-small { display:block; }
      }
      @media screen and (max-width:1199px){
        .toggled .sidebar-wrapper .sidebar-header .logo-img.logo-large { display:none; }
        .toggled .sidebar-wrapper .sidebar-header .logo-img.logo-small { display:block; }
      }
      
      /* Custom: Item sidebar yang tidak aktif tidak berwarna biru */
      .sidebar-wrapper .sidebar-nav .metismenu a {
        color: #5f5f5f !important;
        background-color: transparent !important;
      }
      
      .sidebar-wrapper .sidebar-nav .metismenu a:hover {
        color: #5f5f5f !important;
        background-color: rgba(0, 0, 0, 0.05) !important;
      }
      
      .sidebar-wrapper .sidebar-nav .metismenu a:focus,
      .sidebar-wrapper .sidebar-nav .metismenu a:active {
        color: #5f5f5f !important;
        background-color: rgba(0, 0, 0, 0.05) !important;
      }
      
      /* Item aktif tetap biru */
      .sidebar-wrapper .sidebar-nav .metismenu .mm-active > a {
        color: #008cff !important;
        background-color: rgba(0, 140, 255, 0.05) !important;
      }
      
      /* Submenu yang tidak aktif */
      .sidebar-wrapper .sidebar-nav .metismenu ul a {
        color: #5f5f5f !important;
        background-color: transparent !important;
      }
      
      .sidebar-wrapper .sidebar-nav .metismenu ul a:hover {
        color: #5f5f5f !important;
        background-color: rgba(0, 0, 0, 0.05) !important;
      }
      
      .sidebar-wrapper .sidebar-nav .metismenu ul .mm-active > a {
        color: #008cff !important;
        background-color: rgba(0, 140, 255, 0.05) !important;
      }
    </style>
    <div class="sidebar-header">
      <div class="logo-icon">
        <img src="{{ URL::asset('build/images/logo-removebg.png') }}" class="logo-img logo-large" alt="">
        <img src="{{ URL::asset('build/images/logo-berau.png') }}" class="logo-img logo-small" alt="">
      </div>
      <!-- <div class="logo-name flex-grow-1">
        <h5 class="mb-0">Berau Coal</h5>
      </div> -->
      <div class="sidebar-close">
        <span class="material-icons-outlined">close</span>
      </div>
    </div>
    <div class="sidebar-nav" data-simplebar="true">
      
        <!--navigation-->
        <ul class="metismenu" id="sidenav">
          
          <li>
            <a href="{{ route('dms.dashboard-static') }}">
              <div class="parent-icon"><i class="material-icons-outlined">dashboard</i>
              </div>
              <div class="menu-title">Dashboard</div>
            </a>
          </li>


          <li class="menu-label">Deteksi Fatigue</li>
          

          <li>
            <a class="" href="{{ route('dms.index') }}">
              <div class="parent-icon"><i class="material-icons-outlined">camera</i>
              </div>
              <div class="menu-title">Kalibrasi Operator</div>
            </a>
          </li>

          
          <li>
            <a class="" href="{{ route('dms.detection') }}">
              <div class="parent-icon"><i class="material-icons-outlined">gps_fixed</i>
              </div>
              <div class="menu-title">Deteksi Operator</div>
            </a>
          </li>

           <li>
            <a class="" href="{{ route('daily-operation-plan.index') }}">
              <div class="parent-icon"><i class="material-icons-outlined">picture_as_pdf</i>
              </div>
              <div class="menu-title">Report</div>
            </a>
          </li>

         
         
         </ul>
        <!--end navigation-->
    </div>
    <div class="sidebar-bottom gap-4">
        <div class="dark-mode">
          <a href="javascript:;" class="footer-icon dark-mode-icon">
            <i class="material-icons-outlined">dark_mode</i>  
          </a>
        </div>
        <div class="dropdown dropup-center dropup dropdown-laungauge">
          <a class="dropdown-toggle dropdown-toggle-nocaret footer-icon" href="avascript:;" data-bs-toggle="dropdown"><img src="{{ URL::asset('build/images/county/02.png') }}" width="22" alt="">
          </a>
          <ul class="dropdown-menu dropdown-menu-end">
            <li><a class="dropdown-item d-flex align-items-center py-2" href="javascript:;"><img src="{{ URL::asset('build/images/county/01.png') }}" width="20" alt=""><span class="ms-2">English</span></a>
            </li>
            <li><a class="dropdown-item d-flex align-items-center py-2" href="javascript:;"><img src="{{ URL::asset('build/images/county/02.png') }}" width="20" alt=""><span class="ms-2">Catalan</span></a>
            </li>
            <li><a class="dropdown-item d-flex align-items-center py-2" href="javascript:;"><img src="{{ URL::asset('build/images/county/03.png') }}" width="20" alt=""><span class="ms-2">French</span></a>
            </li>
            <li><a class="dropdown-item d-flex align-items-center py-2" href="javascript:;"><img src="{{ URL::asset('build/images/county/04.png') }}" width="20" alt=""><span class="ms-2">Belize</span></a>
            </li>
            <li><a class="dropdown-item d-flex align-items-center py-2" href="javascript:;"><img src="{{ URL::asset('build/images/county/05.png') }}" width="20" alt=""><span class="ms-2">Colombia</span></a>
            </li>
            <li><a class="dropdown-item d-flex align-items-center py-2" href="javascript:;"><img src="{{ URL::asset('build/images/county/06.png') }}" width="20" alt=""><span class="ms-2">Spanish</span></a>
            </li>
            <li><a class="dropdown-item d-flex align-items-center py-2" href="javascript:;"><img src="{{ URL::asset('build/images/county/07.png') }}" width="20" alt=""><span class="ms-2">Georgian</span></a>
            </li>
            <li><a class="dropdown-item d-flex align-items-center py-2" href="javascript:;"><img src="{{ URL::asset('build/images/county/08.png') }}" width="20" alt=""><span class="ms-2">Hindi</span></a>
            </li>
          </ul>
        </div>
        <div class="dropdown dropup-center dropup dropdown-help">
          <a class="footer-icon  dropdown-toggle dropdown-toggle-nocaret option" href="javascript:;"
            data-bs-toggle="dropdown" aria-expanded="false">
            <span class="material-icons-outlined">
              info
            </span>
          </a>
          <div class="dropdown-menu dropdown-option dropdown-menu-end shadow">
            <div><a class="dropdown-item d-flex align-items-center gap-2 py-2" href="javascript:;"><i
                  class="material-icons-outlined fs-6">inventory_2</i>Archive All</a></div>
            <div><a class="dropdown-item d-flex align-items-center gap-2 py-2" href="javascript:;"><i
                  class="material-icons-outlined fs-6">done_all</i>Mark all as read</a></div>
            <div><a class="dropdown-item d-flex align-items-center gap-2 py-2" href="javascript:;"><i
                  class="material-icons-outlined fs-6">mic_off</i>Disable Notifications</a></div>
            <div><a class="dropdown-item d-flex align-items-center gap-2 py-2" href="javascript:;"><i
                  class="material-icons-outlined fs-6">grade</i>What's new ?</a></div>
            <div>
              <hr class="dropdown-divider">
            </div>
            <div><a class="dropdown-item d-flex align-items-center gap-2 py-2" href="javascript:;"><i
                  class="material-icons-outlined fs-6">leaderboard</i>Reports</a></div>
          </div>
        </div>

    </div>
    <script>
      // Set sidebar default tertutup (collapsed)
      document.addEventListener('DOMContentLoaded', function() {
        if (!document.body.classList.contains('toggled')) {
          document.body.classList.add('toggled');
          
          // Setup hover event untuk sidebar-wrapper (seperti di main.js)
          if (window.innerWidth >= 1199) {
            var sidebarWrapper = document.querySelector('.sidebar-wrapper');
            if (sidebarWrapper) {
              sidebarWrapper.addEventListener('mouseenter', function() {
                document.body.classList.add('sidebar-hovered');
              });
              sidebarWrapper.addEventListener('mouseleave', function() {
                document.body.classList.remove('sidebar-hovered');
              });
            }
          }
        }
      });
    </script>
</aside>