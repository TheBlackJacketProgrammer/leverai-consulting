<!-- Navigation Bar (Tailwind) -->
<?php if (isset($status) && $status == 'home'): ?>
    <!-- Landing Page Header -->
    <header class="header-landing">
        <div class="logo">
            <a href="<?php echo base_url(); ?>" class="flex flex-col w-fit group no-underline hover:no-underline active:no-underline focus:no-underline outline-none">
                <div class="self-start text-lg md:text-xl font-bold tracking-[0.2em] text-cyan-400 uppercase leading-none group-hover:text-cyan-300 transition-colors whitespace-nowrap">LEVER <span class="font-normal">AI</span></div>
                <div class="self-end text-[0.5rem] md:text-[0.6rem] tracking-[0.3em] text-white uppercase text-right group-hover:text-gray-200 transition-colors">DEVELOPMENT</div>
            </a>
        </div>
        <nav>
            <a href="<?php echo base_url('login'); ?>" class="login-link text-xs md:text-sm">Login</a>
            <button class="btn-cyan text-[0.65rem] md:text-sm font-bold px-3 py-1.5 md:px-6 md:py-2.5 rounded-md shadow-[0_0_20px_rgba(0,194,255,0.3)] hover:shadow-[0_0_30px_rgba(0,194,255,0.5)] transition-all duration-300 whitespace-nowrap" ng-click="openModal('modal_developerlogin')">Start Building</button>
        </nav>
    </header>
<?php else: ?>
    <!-- Standard App Header -->
    <header class="header-main" ng-controller="ng-header" style="padding: 20px 0;">
      <div class="flex flex-row items-center justify-between">
        <div class="company-logo">
            <a href="<?php echo base_url(); ?>" class="flex flex-col w-fit group no-underline hover:no-underline active:no-underline focus:no-underline outline-none">
                <div class="self-start text-lg md:text-xl font-bold tracking-[0.2em] text-cyan-400 uppercase leading-none group-hover:text-cyan-300 transition-colors whitespace-nowrap">LEVER <span class="font-normal">AI</span></div>
                <div class="self-end text-[0.5rem] md:text-[0.6rem] tracking-[0.3em] text-white uppercase text-right group-hover:text-gray-200 transition-colors">DEVELOPMENT</div>
            </a>
        </div>
        <nav>
          <div class="flex flex-row items-center justify-end gap-4">
            <div class="relative notification-dropdown" <?php echo (isset($status) && $status == 'dashboard') ? '' : 'style="display: none;"'; ?>>
              <button class="relative btn-notification" ng-click="toggleNotificationPanel($event)">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <g clip-path="url(#clip0_17_4765)">
                    <path d="M12 22C13.1 22 14 21.1 14 20H10C10 21.1 10.9 22 12 22ZM18 16V11C18 7.93 16.37 5.36 13.5 4.68V4C13.5 3.17 12.83 2.5 12 2.5C11.17 2.5 10.5 3.17 10.5 4V4.68C7.64 5.36 6 7.92 6 11V16L4 18V19H20V18L18 16ZM16 17H8V11C8 8.52 9.51 6.5 12 6.5C14.49 6.5 16 8.52 16 11V17Z" fill="#02C5FE"/>
                  </g>
                  <defs>
                    <clipPath id="clip0_17_4765">
                      <rect width="24" height="24" fill="white"/>
                    </clipPath>
                  </defs>
                </svg>
                <span class="notification-badge">{{ notificationCount }}</span>
              </button>
              <div class="notification-panel" ng-if="notificationPanelOpen" ng-click="$event.stopPropagation()">
                <div class="notification-header">
                  <h3>Notifications</h3>
                  <button class="mark-all-read" ng-click="markAllAsRead($event)">Mark all as read</button>
                </div>
                <div class="notification-list">
                  <div class="notification-item" ng-class="{'unread': !notification.is_read || notification.is_read === 'f'}" ng-repeat="notification in notifications" ng-click="markNotificationAsRead(notification.id, $event); openRequestModal('modal_view_request', notification.ticket_id)">
                    <div class="notification-icon">
                      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z" fill="#02C5FE"/>
                      </svg>
                    </div>
                    <div class="notification-content">
                      <p class="notification-title">{{ notification.message }}</p>
                      <p class="notification-time">{{ notification.formatted_date || notification.created_at }}</p>
                    </div>
                  </div>
                </div>
                <div class="notification-footer">
                  <a href="#" class="view-all" ng-click="loadPreviousNotifications($event)" ng-if="hasMoreNotifications">Show previous notifications</a>
                </div>
              </div>
            </div>
            <a class="btn-transparent-bg" href="<?php echo base_url('login'); ?>" <?php echo (isset($status) && ($status == 'login' || $status == 'dashboard')) ? 'style="display: none;"' : ''; ?>>
              <img class="mr-2" src="<?php echo base_url('assets/img/icons/login.svg'); ?>" alt="Login Icon">
              <span>Login</span>
            </a>
            <!-- <a class="btn-primary" href="<?php echo base_url('subscribe'); ?>">Subscribe</a> -->
            <button class="btn-primary" ng-click="openModal('modal_developerlogin')" <?php echo (isset($status) && ($status == 'subscribe' || $status == 'dashboard')) ? 'style="display: none;"' : ''; ?>>Subscribe</button>

            <a class="btn-transparent-bg" href="<?php echo base_url('logout'); ?>" <?php echo (isset($status) && $status == 'dashboard') ? '' : 'style="display: none;"'; ?>>
              <img class="mr-2" src="<?php echo base_url('assets/img/icons/login.svg'); ?>" alt="Login Icon">
              <span>Logout</span>
            </a>
          </div>
        </nav>
      </div>
    </header>
<?php endif; ?>









