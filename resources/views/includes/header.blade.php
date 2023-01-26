<div class="header">
    <div class="inline p-l-30">
        <h4><a href="{{ route('dash.index') }}"><img src="{{ asset('assets/img/logo.png') }}" width="150" alt=""></a></h4>
    </div>
    <div class="inline">
        <div class="dropdown profile-menu">
            <button class="profile-dropdown-toggle" type="button" data-toggle="dropdown">
                <span class="thumbnail-wrapper d32 circular inline">
                    <img src="{{ asset('assets/img/default.jpg') }}" width="32" height="32">
                </span>
            </button>
            <div class="dropdown-menu dropdown-menu-right profile-dropdown" role="menu">
                <a href="javascript:;" class="dropdown-item"><span>Signed in as <br/><b>{{ Auth::user()->name }}</b></span></a>
                <div class="dropdown-divider"></div>
                <a href="{{ route('profile') }}" class="dropdown-item">Edit Profile</a>
                <a href="{{ route('password.change') }}" class="dropdown-item">Change Password</a>
                <div class="dropdown-divider"></div>
                <a href="{{ route('secret.logout') }}" class="dropdown-item" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">@csrf</form>
            </div>
        </div>
    </div>
    <div class="inline">
        <div class="btn-menu">
            <span class="menu-top"></span>
            <span class="menu-middle"></span>
            <span class="menu-bottom"></span>
        </div>
    </div>
</div>