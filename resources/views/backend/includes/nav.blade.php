<ul class="nav nav-tabs nav-tabs-fillup">
    <li class="">
        {{-- <a href="{{ route('client.index') }}" class="">
            Clients
        </a> --}}
    </li>
    <li class="">
        <a href="{{ route('payment.setup.index') }}" class="{{ request()->route()->getName() =='payment.setup.index'?'active':'' }}">
            Payment Setups
        </a>
    </li>
    <li class="">
        <a href="{{ route('subscription.index') }}" class="{{ request()->route()->getName() =='subscription.index'?'active':'' }}">
            Subscription
        </a>
    </li>
    <li class="">
        <a href="{{ route('payment.entry.index') }}" class="{{ request()->route()->getName() =='payment.entry.index'?'active':'' }}">
            Payment Records
        </a>
    </li>
    {{-- <li class="">
        <a href="{{ route('payment.expired')}}" class="">
            Expired Payments
        </a>
    </li> --}}
    <li class="">
        <a href="{{ route('payment.detail.index') }}" class="{{ request()->route()->getName() =='payment.detail.index'?'active':'' }}">
            Payment History
        </a>
    </li>
    <li class="">
        <a href="{{ route('email.index') }}" class="{{ request()->route()->getName() =='email.index'?'active':'' }}">
            Email Notifications
        </a>
    </li>
</ul>