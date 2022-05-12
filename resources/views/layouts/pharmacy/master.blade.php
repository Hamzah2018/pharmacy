<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>صيدلية اون لاين</title>

  <link href="{{ asset('css/dashboard.css') }}" rel="stylesheet" />

  @livewireStyles()

</head>

<body>
  <div class="dashboard" x-data="{ isSidebarOpen: window.innerWidth >= 786 ? true : false }">
    {{-- sidebar --}}
    @include('layouts.pharmacy.sidebar')

    <div class="dashboard-content">
      {{-- navbar --}}
      @include('layouts.shared.navbar')

      @yield('content')
    </div>
  </div>

  @livewireScripts()

  {{-- load alpinejs before any html element #fix modal light show/hide issues --}}
  @yield('alpine-script')
  <script src="{{ asset('js/alpine.min.js') }}"></script>

  <script src="{{ asset('js/jquery.min.js') }}"></script>

  <script src="https://js.pusher.com/7.0/pusher.min.js"></script>

{{--  <script src="{{ asset('js/pusher/pharamcy.js') }}"></script>--}}

<script>

  // Enable pusher logging - don't include this in production
  // Pusher.logToConsole = true;
  var pusher = new Pusher('da19676fc51825fbbeba', {
    cluster: 'mt1',
    encrypted: true
  });

  /**
   * @returns {HTMLElement}
   */
  let el = $el => document.querySelector($el);



  var dropdownNotifications = $('.dropdown-notifications-js');

  var notificationsToggle = dropdownNotifications.find('a[data-toggle]');
  var notificationsCountElem = notificationsToggle.find('span[data-count]');
  var notificationsCount = parseInt(notificationsCountElem.data('count'));

  if (notificationsCount <= 0) dropdownNotifications.hide();


  // Subscribe to the channel we specified in our Laravel Event
  var channel = pusher.subscribe('new-order-notification');

  // Bind a function to a Event (the full Laravel class)
  channel.bind(`Illuminate\\Notifications\\Events\\BroadcastNotificationCreated`, function (data) {

    if ({{Auth::id()}} === data.receiver)
    {
      let avatar = data.sender.avatar === null ? 'default_user.png' : data.sender.avatar;
      el('.js-dropdown-menu').innerHTML +=
        `<li class="t-item">
        <a href="`+ data.link +`">
          <header class="t-header">
            <img src="/uploads/user/`+ avatar+`" alt="user avatar" class="t-avatar" width="40">
            <h4 class="t-name">`+data.sender.name+`</h4>
          </header>
          <p class="t-desc">`+ data.message +`</p>
        </a>
    </li>`;

      notificationsCount += 1;
      notificationsCountElem.attr('data-count', notificationsCount);
      dropdownNotifications.find('.notif-count').text(notificationsCount);
      dropdownNotifications.show();
      notificationsCount -= 1;
    }

  });


</script>

</body>

</html>
