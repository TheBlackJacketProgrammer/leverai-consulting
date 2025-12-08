<div id="modal_create_request" class="modal hidden modal_create_request">
    <!-- Modal Dialog Box -->
    <div class="modal-dialog-box">
        <!-- Header -->
        <div class="modal-header">
            <h4 class="text-white">New Request</h4>
            <button id="closeModal" ng-click="closeModal('modal_create_request')" class="primary">
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
            <div class="remaining-hours container">
                <h5 class="text-white">Remaining Hours</h5>
                <h5 class="primary" ng-bind="hours_remaining"></h5><span class="text-white">hours</span>
            </div>
            <div class="grid grid-cols-1 lg:grid-cols-12 items-center gap-2 mt-0 lg:mt-4 w-full">
                <div class="col-span-2">
                    <label for="title" class="text-white ">Request Title</label>
                </div>
                <div class="col-span-10 flex">
                    <input type="text" id="title" class="input-text" placeholder="Enter Request Title" ng-model="request.title">
                </div>
            </div>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-2 w-full">
                <div class="grid grid-cols-1 lg:grid-cols-12 items-center justify-center gap-2">
                    <div class="col-span-4">
                        <label for="title" class="text-white">Priority</label>
                    </div>
                    <div class="col-span-8 flex">
                        <select id="request-type" class="input-text" ng-model="request.request_priority">
                            <option value="0" selected>Select Priority</option>
                            <option value="Low">Low</option>
                            <option value="Medium">Medium</option>
                            <option value="High">High</option>
                            <option value="Urgent">Urgent</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-1 lg:grid-cols-12 items-center justify-center gap-2">
                    <div class="col-span-5">
                        <label for="title" class="text-white">Dedicated Hours</label>
                    </div>
                    <div class="col-span-7 flex">
                        <input type="number" id="dedicated-hours" class="input-text" placeholder="Enter Dedicated Hours" ng-model="request.dedicate_hours">
                    </div>
                </div>
            </div>
            <div class="flex flex-col items-start justify-start gap-2 w-full">
                <label for="title" class="text-white">Details</label>
                <textarea id="details" class="input-text" placeholder="Enter Details" rows="7" ng-model="request.details"></textarea>
            </div>
            <div class="flex flex-row items-end justify-between gap-4 w-full mt-0 lg:mt-4">
                <div>
                    <label for="title" class="text-white">Attach File</label>
                    <input type="file" id="file" class="hidden">
                    <button class="btn-secondary" ng-click="btnAttachFile()">Upload File</button>
                </div>
                <button class="btn-primary" ng-click="btnCreateRequest()">Submit</button>
            </div>
        </div>
    </div>
</div>