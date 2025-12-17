<!-- Navigation Bar (Tailwind) -->
<header class="header-main" ng-controller="ng-header-admin">
  <div class="flex flex-row items-center justify-between">
    <div class="company-logo">
      <a href="<?php echo base_url(); ?>" class="flex flex-col w-fit group no-underline hover:no-underline active:no-underline focus:no-underline outline-none">
          <div class="self-start text-lg md:text-xl font-bold tracking-[0.2em] text-cyan-400 uppercase leading-none group-hover:text-cyan-300 transition-colors whitespace-nowrap">LEVER <span class="font-normal">A.I.</span></div>
          <div class="self-end text-[0.5rem] md:text-[0.6rem] tracking-[0.3em] text-white uppercase text-right group-hover:text-gray-200 transition-colors">CONSULTING</div>
      </a>
    </div>
    <nav>
      <div class="flex flex-row items-center justify-end lg:gap-4 gap-2">
        <div class="relative notification-dropdown">
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
              <div class="flex items-center gap-2">
                <button class="mark-all-read" ng-click="markAllAsRead($event)">Mark all as read</button>
                <button class="close-notification-mobile md:hidden" ng-click="toggleNotificationPanel($event)" aria-label="Close notifications">
                  <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M18 6L6 18M6 6L18 18" stroke="#02C5FE" stroke-width="2" stroke-linecap="round"/>
                  </svg>
                </button>
              </div>
            </div>
            <div class="notification-list">
              <div class="notification-item" ng-class="{'unread': !notification.is_read || notification.is_read === 'f'}" ng-repeat="notification in notifications" ng-click="handleNotificationClick(notification, $event)">
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
            <div class="notification-footer" ng-if="notifications && notifications.length > 0">
              <a href="#" class="view-all" ng-click="loadPreviousNotifications($event)" ng-show="hasMoreNotifications">Show previous notifications</a>
            </div>
          </div>
        </div>
        <div class="relative notification-dropdown">
          <button class="relative btn-notification" ng-click="openUserProfileModal('modal_user_profile')">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <g clip-path="url(#clip0_17_4755)">
                <path d="M12 6C13.1 6 14 6.9 14 8C14 9.1 13.1 10 12 10C10.9 10 10 9.1 10 8C10 6.9 10.9 6 12 6ZM12 16C14.7 16 17.8 17.29 18 18H6C6.23 17.28 9.31 16 12 16ZM12 4C9.79 4 8 5.79 8 8C8 10.21 9.79 12 12 12C14.21 12 16 10.21 16 8C16 5.79 14.21 4 12 4ZM12 14C9.33 14 4 15.34 4 18V20H20V18C20 15.34 14.67 14 12 14Z" fill="#02C5FE"/>
              </g>
              <defs>
                <clipPath id="clip0_17_4755">
                  <rect width="24" height="24" fill="white"/>
                </clipPath>
              </defs>
            </svg>
          </button>
        </div>
        
        <a class="btn-transparent-bg" href="<?php echo base_url('logout'); ?>">
          <img class="lg:mr-2 mr-0" src="<?php echo base_url('assets/img/icons/login.svg'); ?>" alt="Login Icon">
          <span class="hidden md:block">Logout</span>
        </a>
      </div>
    </nav>
  </div>
</header>


