<div id="modal_user_profile" class="modal hidden" ng-controller="ng-user-profile" ng-init="init()">
    <!-- Modal Dialog Box -->
    <div class="modal-dialog-box">
        <!-- Header -->
        <div class="modal-header">
            <h4 class="text-white">Update User Profile</h4>
            <button id="closeModal" ng-click="closeModal('modal_user_profile')" class="primary">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <g clip-path="url(#clip0_17_4653)">
                        <path d="M19 6.41L17.59 5L12 10.59L6.41 5L5 6.41L10.59 12L5 17.59L6.41 19L12 13.41L17.59 19L19 17.59L13.41 12L19 6.41Z" fill="#0289B3"/>
                    </g>
                    <defs>
                        <clipPath id="clip0_17_4653">
                            <rect width="24" height="24" fill="white"/>
                        </clipPath>
                    </defs>
                </svg>
            </button>
        </div>
        <div class="divider"></div>
        <!-- Body-->
        <div class="modal-body">
            <div class="grid grid-cols-1 gap-4 mt-4">
                <div class="user-profile-form">
                    <!-- Fullname -->
                    <div class="input-wrapper mb-2">
                        <svg class="input-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <g clip-path="url(#clip0_17_4755)">
                                <path d="M12 6C13.1 6 14 6.9 14 8C14 9.1 13.1 10 12 10C10.9 10 10 9.1 10 8C10 6.9 10.9 6 12 6ZM12 16C14.7 16 17.8 17.29 18 18H6C6.23 17.28 9.31 16 12 16ZM12 4C9.79 4 8 5.79 8 8C8 10.21 9.79 12 12 12C14.21 12 16 10.21 16 8C16 5.79 14.21 4 12 4ZM12 14C9.33 14 4 15.34 4 18V20H20V18C20 15.34 14.67 14 12 14Z" fill="#FFFFFF"/>
                            </g>
                            <defs>
                                <clipPath id="clip0_17_4755">
                                    <rect width="24" height="24" fill="white"/>
                                </clipPath>
                            </defs>
                        </svg>
                        <input type="text" placeholder="Fullname*" ng-model="credentials.fullname" ng-keypress="$event.keyCode === 13 && updateUserProfile()" required>
                    </div>
                    <!-- New Password -->
                    <div class="input-wrapper mb-2">
                        <svg class="input-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <g clip-path="url(#clip0_6_16196)">
                                <path d="M18 8H17V6C17 3.24 14.76 1 12 1C9.24 1 7 3.24 7 6V8H6C4.9 8 4 8.9 4 10V20C4 21.1 4.9 22 6 22H18C19.1 22 20 21.1 20 20V10C20 8.9 19.1 8 18 8ZM9 6C9 4.34 10.34 3 12 3C13.66 3 15 4.34 15 6V8H9V6ZM18 20H6V10H18V20ZM12 17C13.1 17 14 16.1 14 15C14 13.9 13.1 13 12 13C10.9 13 10 13.9 10 15C10 16.1 10.9 17 12 17Z" fill="white"/>
                            </g>
                            <defs>
                                <clipPath id="clip0_6_16196">
                                    <rect width="24" height="24" fill="white"/>
                                </clipPath>
                            </defs>
                        </svg>
                        <input type="password" placeholder="Set New Password*" ng-model="credentials.new_password" ng-keypress="$event.keyCode === 13 && updateUserProfile()" required>
                    </div>
                    <!-- Confirm New Password -->
                    <div class="input-wrapper mb-2">
                        <svg class="input-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <g clip-path="url(#clip0_6_16196)">
                                <path d="M18 8H17V6C17 3.24 14.76 1 12 1C9.24 1 7 3.24 7 6V8H6C4.9 8 4 8.9 4 10V20C4 21.1 4.9 22 6 22H18C19.1 22 20 21.1 20 20V10C20 8.9 19.1 8 18 8ZM9 6C9 4.34 10.34 3 12 3C13.66 3 15 4.34 15 6V8H9V6ZM18 20H6V10H18V20ZM12 17C13.1 17 14 16.1 14 15C14 13.9 13.1 13 12 13C10.9 13 10 13.9 10 15C10 16.1 10.9 17 12 17Z" fill="white"/>
                            </g>
                            <defs>
                                <clipPath id="clip0_6_16196">
                                <rect width="24" height="24" fill="white"/>
                                </clipPath>
                            </defs>
                        </svg>
                        <input type="password" placeholder="Confirm Password*" ng-model="credentials.confirm_password" ng-keypress="$event.keyCode === 13 && updateUserProfile()" required>
                    </div>
                    <div class="submit-area mt-6">
                        <input type="button" value="Update Profile" class="btn-primary w-full" ng-click="updateUserProfile()">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>