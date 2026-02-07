 <header>
     <div class="container">
         <div class="row">
             <div class="col-lg-12">
                 <nav class="navbar navbar-expand-lg">
                     <div class="container-fluid p-0">
                         <a class="navbar-brand" href="{{ route('client.index.get') }}">
                             <img src="{{ url('admin-assets/dpm-logos/4.png') }}" class="img-fluid logo-mobile"
                                 alt="Logo">
                         </a>

                         <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                             data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                             aria-expanded="false" aria-label="Toggle navigation">
                             <span class="navbar-toggler-icon"></span>
                         </button>
                         <div class="collapse navbar-collapse" id="navbarSupportedContent">
                             <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-center custom-menu">
                                 <link rel="stylesheet"
                                     href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
                                     integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg=="
                                     crossorigin="anonymous" referrerpolicy="no-referrer" />
                                 <li class="nav-item" data-step='2'>
                                     <a class="nav-link" aria-current="page"
                                         href="{{ route('client.raised-tickets.get') }}">
                                         <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25"
                                             fill="#6c0e0e" class="bi bi-chat-left" viewBox="0 0 16 16">
                                             <path
                                                 d="M14 1a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1H4.414A2 2 0 0 0 3 11.586l-2 2V2a1 1 0 0 1 1-1zM2 0a2 2 0 0 0-2 2v12.793a.5.5 0 0 0 .854.353l2.853-2.853A1 1 0 0 1 4.414 12H14a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2z" />
                                         </svg>
                                         <span>
                                             Tickets
                                         </span>
                                     </a>
                                 </li>
                                 <li class="nav-item" data-step='1'>
                                     <a class="nav-link" href="{{ route('client.brief.get') }}">
                                         <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25"
                                             fill="#6c0e0e" class="bi bi-card-text" viewBox="0 0 16 16">
                                             <path
                                                 d="M14.5 3a.5.5 0 0 1 .5.5v9a.5.5 0 0 1-.5.5h-13a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5zm-13-1A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h13a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 14.5 2z" />
                                             <path
                                                 d="M3 5.5a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5M3 8a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9A.5.5 0 0 1 3 8m0 2.5a.5.5 0 0 1 .5-.5h6a.5.5 0 0 1 0 1h-6a.5.5 0 0 1-.5-.5" />
                                         </svg>
                                         <span>
                                             Brief
                                         </span>
                                     </a>
                                 </li>
                                 <li class="nav-item">
                                     <a class="nav-link" href="{{ route('client.invoice.get') }}">
                                         <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25"
                                             fill="#6c0e0e" class="bi bi-credit-card" viewBox="0 0 16 16">
                                             <path
                                                 d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2zm2-1a1 1 0 0 0-1 1v1h14V4a1 1 0 0 0-1-1zm13 4H1v5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1z" />
                                             <path
                                                 d="M2 10a1 1 0 0 1 1-1h1a1 1 0 0 1 1 1v1a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1z" />
                                         </svg>
                                         <span>
                                             Invoices
                                         </span>
                                     </a>
                                 </li>
                             </ul>
                             @php
                                 $user = auth()->guard('client')->user(); // Get authenticated user
                                 $profile = null;
                                 // Check if the user is logged in
                                 if ($user) {
                                     $profile = App\Models\ProfileDetail::where('user_id', $user->id)
                                         ->where('user_type', get_class($user))
                                         ->first();
                                 }
                             @endphp

                             <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-center">
                                 <div class="profile-detail">
                                     @if (!$profile)
                                         <img id="profile_image" src="https://designcrm.net/images/avatar.png"
                                             class="img-fluid" alt="Profile Image">
                                     @else
                                         <img id="profile_image"
                                             src="{{ url('uploads/profiles/' . $profile->profile) }}" class="img-fluid"
                                             alt="Profile Image">
                                     @endif
                                 </div>
                                 <li class="nav-item dropdown profile-drop-down">
                                     <a class="nav-link dropdown-toggle" href="setup-profile.php" role="button"
                                         data-bs-toggle="dropdown" aria-expanded="false">
                                         {{ $user ? $user->name : 'Guest' }}
                                     </a>
                                     <ul class="dropdown-menu">
                                         <hr class="dropdown-divider">
                                         @if ($user)
                                             <li><a class="dropdown-item" href="{{ route('client.logout') }}">Logout</a>
                                             </li>
                                         @else
                                             <li><a class="dropdown-item"
                                                     href="{{ route('client.login.get') }}">Login</a></li>
                                         @endif
                                     </ul>
                                 </li>
                             </ul>
                             <form class="d-flex" role="search">
                                 <div class="parent-search-bar">
                                     <input class="form-control me-2" type="search" placeholder="Search"
                                         aria-label="Search">
                                     <button>
                                         <img src="https://designcrm.net/images/search-bar.png" class="img-fluid"
                                             alt="">
                                     </button>
                                 </div>
                             </form>
                             {{-- <button class="bell-icon" type="submit">
                                 <img src="https://designcrm.net/images/bell-icon.png" class="img-fluid" alt="">
                             </button> --}}
                         </div>
                     </div>
                 </nav>
             </div>
         </div>
     </div>
 </header>
