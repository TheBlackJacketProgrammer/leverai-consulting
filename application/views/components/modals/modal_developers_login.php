<div id="modal_developerlogin" class="modal hidden">
    <!-- Modal Dialog Box -->
    <div class="modal-dialog-box">
        <!-- Header -->
        <div class="modal-header">
            <h5 class="text-white">Login - Developer Mode</h5>
            <button id="closeModal" onclick="closeModal('modal_developerlogin')" class="btn-close primary">
                <b>X</b>
            </button>
        </div>
        <div class="divider"></div>
        <!-- Body-->
        <div class="modal-body">
            <!-- Message -->
            <p class="text-white text-sm mb-2">Subscribe is not yet available to the public. <br>This is for the developers only to access the subscribe page.</p>
            <!-- Username -->
            <div class="input-wrapper mb-2">
                <svg class="input-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <g clip-path="url(#clip0_6_15991)">
                        <path d="M9 12C10.93 12 12.5 10.43 12.5 8.5C12.5 6.57 10.93 5 9 5C7.07 5 5.5 6.57 5.5 8.5C5.5 10.43 7.07 12 9 12ZM9 7C9.83 7 10.5 7.67 10.5 8.5C10.5 9.33 9.83 10 9 10C8.17 10 7.5 9.33 7.5 8.5C7.5 7.67 8.17 7 9 7ZM9.05 17H4.77C5.76 16.5 7.47 16 9 16C9.11 16 9.23 16.01 9.34 16.01C9.68 15.28 10.27 14.68 10.98 14.2C10.25 14.07 9.56 14 9 14C6.66 14 2 15.17 2 17.5V19H9V17.5C9 17.33 9.02 17.16 9.05 17ZM16.5 14.5C14.66 14.5 11 15.51 11 17.5V19H22V17.5C22 15.51 18.34 14.5 16.5 14.5ZM17.71 12.68C18.47 12.25 19 11.44 19 10.5C19 9.12 17.88 8 16.5 8C15.12 8 14 9.12 14 10.5C14 11.44 14.53 12.25 15.29 12.68C15.65 12.88 16.06 13 16.5 13C16.94 13 17.35 12.88 17.71 12.68Z" fill="white"/>
                    </g>
                    <defs>
                        <clipPath id="clip0_6_15991">
                            <rect width="24" height="24" fill="white"/>
                        </clipPath>
                    </defs>
                </svg>

                <input id="username" type="text" placeholder="Username*"  required>
            </div>
            <!-- Password -->
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
                <input id="password" type="password" placeholder="Password*"  required>
            </div> 
            <!-- Login Button -->
            <div class="divider"></div>
            <button class="btn-primary" onclick="authenticateDeveloper()">Login</button>
        </div>
    </div>
</div>

<script type="text/javascript">
        
        function openModal(modalId) {
            $('#' + modalId).removeClass('hidden');
            $('#' + modalId).addClass('flex');
        }

        function closeModal(modalId) {
            $('#' + modalId).removeClass('flex');
            $('#' + modalId).addClass('hidden');
        }

        function authenticateDeveloper() {
            $("html").addClass("loading");
            $.ajax({
                url: '<?php echo base_url('authenticate_developer'); ?>',
                type: 'POST',
                dataType: 'json',
                data: {
                    username: $('#username').val(),
                    password: $('#password').val()
                },
                success: function(response) {
                    if(response.authenticated){
                        // Determine API URL based on environment
                        const hostname = window.location.hostname;
                        const currentPath = window.location.pathname;
                        const isLocal = hostname === 'localhost' || hostname === '127.0.0.1' || hostname.includes('localhost');
                        const hasCi3Template = currentPath.includes('/ci3_template/');
                        
                        // Use ci3_template path if we're local OR if the current URL contains ci3_template
                        const targetUrl = (isLocal || hasCi3Template) ? '/ci3_template/subscribe' : '/subscribe';

                        window.location.href = targetUrl;
                    }
                    else
                    {
                        $("html").removeClass("loading");
                        toastr.error('Invalid username or password');
                    }
                }
            });
        }

    </script>