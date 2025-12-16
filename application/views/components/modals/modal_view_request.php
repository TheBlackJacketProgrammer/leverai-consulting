<div id="modal_view_request" class="modal hidden modal_view_request">
    <!-- Modal Dialog Box -->
    <div class="modal-dialog-box">
        <!-- Header -->
        <div class="modal-header">
            <h4 class="text-white">Ticket Details</h4>
            <button id="closeModal" ng-click="closeModal('modal_view_request')" class="btn-close primary">
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
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 w-full" <?php echo ($this->session->userdata("role") == "admin") ? "" : "style='display: none;'"?>>
                <div class="ticket-details container col-span-2">
                    <div class="text-sm space-y-1">
                        <p>
                            <span class="font-medium text-white">Ticket #</span>
                            <span class="primary font-semibold hover:underline ml-1">{{ ticketDetails.ticket_id }}</span>
                        </p>
                        <p>
                            <span class="font-medium text-white">Customer Name</span>
                            <span class="primary font-semibold hover:underline ml-1">{{ ticketDetails.name }}</span>
                        </p>
                        <p>
                            <span class="font-medium text-white">Request</span>
                            <span class="primary font-semibold hover:underline ml-1">{{ ticketDetails.title }}</span>
                        </p>
                    </div>
                </div>
                <div class="ticket-details container col-span-1">
                    <div class="text-sm space-y-2 w-full">
                        <p>
                            <span class="font-medium text-white">Dedicated Hrs</span>
                            <span class="primary font-semibold ml-2">{{ ticketDetails.dedicate_hours }} hrs</span>
                        </p>
                        <div>
                            <label for="status" class="font-medium text-white block mb-1">Status</label>
                            <select id="status" ng-model="ticketDetails.status" ng-change="updateStatus(ticketDetails)">
                                <option value="Submitted" ng-selected="ticketDetails.status == 'Submitted'">Submitted</option>
                                <option value="Under Review" ng-selected="ticketDetails.status == 'Under Review'">Under Review</option>
                                <option value="Awaiting Client" ng-selected="ticketDetails.status == 'Awaiting Client'">Awaiting Client</option>
                                <option value="Scheduled" ng-selected="ticketDetails.status == 'Scheduled'">Scheduled</option>
                                <option value="In Progress" ng-selected="ticketDetails.status == 'In Progress'">In Progress</option>
                                <option value="Internal QA" ng-selected="ticketDetails.status == 'Internal QA'">Internal QA</option>
                                <option value="Delivered" ng-selected="ticketDetails.status == 'Delivered'">Delivered</option>
                                <option value="Revision Requested" ng-selected="ticketDetails.status == 'Revision Requested'">Revision Requested</option>
                                <option value="Ongoing for Revision" ng-selected="ticketDetails.status == 'Ongoing for Revision'">Ongoing for Revision</option>
                                <option value="Closed" ng-selected="ticketDetails.status == 'Closed'">Closed</option>
                                <option value="On Hold" ng-selected="ticketDetails.status == 'On Hold'">On Hold</option>
                                <option value="Blocked" ng-selected="ticketDetails.status == 'Blocked'">Blocked</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 w-full" <?php echo ($this->session->userdata("role") == "customer") ? "" : "style='display: none;'"?>>
                <div class="ticket-details container col-span-2" ng-if="ticketDetails.status != 'Rejected'">
                    <div class="text-sm space-y-1">
                        <p>
                            <span class="font-medium text-white">Ticket #</span>
                            <span class="primary font-semibold hover:underline ml-1">{{ ticketDetails.ticket_id }}</span>
                        </p>
                        <p>
                            <span class="font-medium text-white">Request</span>
                            <span class="primary font-semibold hover:underline ml-1">{{ ticketDetails.title }}</span>
                        </p>
                        <p>
                            <span class="font-medium text-white">Status</span>
                            <span class="primary font-semibold ml-2">{{ ticketDetails.status }}</span>
                        </p>
                    </div>
                </div>
                <div class="ticket-details container col-span-3" ng-if="ticketDetails.status == 'Rejected'">
                    <div class="text-sm space-y-1">
                        <p>
                            <span class="font-medium text-white">Ticket #</span>
                            <span class="primary font-semibold hover:underline ml-1">{{ ticketDetails.ticket_id }}</span>
                        </p>
                        <p>
                            <span class="font-medium text-white">Request</span>
                            <span class="primary font-semibold hover:underline ml-1">{{ ticketDetails.title }}</span>
                        </p>
                        <p>
                            <span class="font-medium text-white">Status</span>
                            <span class="primary font-semibold ml-2">{{ ticketDetails.status }}</span>
                        </p>
                    </div>
                </div>
                <div class="ticket-details container col-span-1" ng-if="ticketDetails.status != 'Rejected'">
                    <div class="text-sm space-y-2 w-full">
                        <p ng-if="ticketDetails.status != 'Ongoing'">
                            <span class="font-medium text-white">Remaining Hrs</span>
                            <span class="primary font-semibold ml-2">{{ hours_remaining }} hrs</span>
                        </p>
                        <div class="flex flex-row items-end justify-start gap-2 w-full">
                            <div class=" w-full" ng-if="ticketDetails.status == 'Ongoing'">
                                <p>
                                    <span class="font-medium text-white">Dedicated Hrs</span>
                                    <span class="primary font-semibold ml-2">{{ ticketDetails.dedicate_hours }} hrs</span>
                                </p>
                            </div>
                            <div class=" w-1/2" ng-if="ticketDetails.status != 'Ongoing'">
                                <p class="font-medium text-white my-2" ng-if="ticketDetails.status != 'Ongoing'">Dedicated Hrs</p>
                                <button class="btn-hour" ng-click="btnDedicateHours('add')" ng-if="ticketDetails.status != 'Ongoing'">+</button>
                                <span class="primary font-semibold mx-2" ng-if="ticketDetails.status != 'Ongoing'">{{ ticketDetails.dedicate_hours }} hrs</span>
                                <button class="btn-hour" ng-click="btnDedicateHours('subtract')" ng-if="ticketDetails.status != 'Ongoing'">-</button>    
                            </div>
                            <div class="flex flex-row items-end justify-center w-1/2">
                                <button class="btn-update" ng-click="btnUpdateDedicateHours()" ng-if="ticketDetails.status != 'Ongoing'">Update</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 w-full mt-4">
                <div class="col-span-1">
                    <div class="flex flex-col items-center justify-center roadmap mb-4 pb-4">
                        <h5 class="text-white mb-3 mt-4">Roadmap</h5>
                        <div class="status-box" ng-class="{'active': ticketDetails.status == 'Submitted'}">
                            <p class="text-white status-name">SUBMITTED</p>
                            <p class="status-subname">(Pre-Work)</p>
                        </div>
                        <span class="arrow-down"></span>
                        <div class="status-box" ng-class="{'active': ticketDetails.status == 'Under Review'}">
                            <p class="text-white status-name">UNDER REVIEW</p>
                            <p>(Pre-Work)</p>
                        </div>
                        <span class="arrow-down"></span>
                        <div class="status-box" ng-class="{'active': ticketDetails.status == 'Awaiting Client'}">
                            <p class="text-white status-name">AWAITING CLIENT</p>
                            <p>(Paused)</p>
                        </div>
                        <span class="arrow-down"></span>
                        <div class="status-box" ng-class="{'active': ticketDetails.status == 'Scheduled'}">
                            <p class="text-white status-name">SCHEDULED</p>
                            <p>(Edge Case)</p>
                        </div>
                        <span class="arrow-down"></span>
                        <div class="status-box" ng-class="{'active': ticketDetails.status == 'In Progress'}">
                            <p class="text-white status-name">IN PROGRESS</p>
                            <p>(Active)</p>
                        </div>
                        <span class="arrow-down"></span>
                        <div class="status-box" ng-class="{'active': ticketDetails.status == 'Blocked'}" ng-if="ticketDetails.status == 'Blocked'">
                            <p class="text-white status-name">BLOCKED</p>
                            <p>(Active)</p>
                        </div>
                        <div class="status-box" ng-class="{'active': ticketDetails.status == 'Internal QA'}" ng-if="ticketDetails.status == 'Internal QA'">
                            <p class="text-white status-name">INTERNAL QA</p>
                            <p>(Active)</p>
                        </div>
                        <div class="status-box" ng-class="{'active': ticketDetails.status == 'On Hold'}" ng-if="ticketDetails.status == 'On Hold'">
                            <p class="text-white status-name">ON HOLD</p>
                            <p>(Active)</p>
                        </div>
                        <span class="arrow-down"></span>
                        <div class="status-box" ng-class="{'active': ticketDetails.status == 'Delivered'}">
                            <p class="text-white status-name">DELIVERED</p>
                            <p>(Handover)</p>
                        </div>
                        <span class="arrow-down"></span>
                        <div class="status-box" ng-class="{'active': ticketDetails.status == 'Revision Requested'}">
                            <p class="text-white status-name">REVISION REQUESTED</p>
                            <p>(Edge Case)</p>
                        </div>
                        <span class="arrow-down"></span>
                        <div class="status-box" ng-class="{'active': ticketDetails.status == 'Ongoing for Revision'}">
                            <p class="text-white status-name">ONGOING FOR REVISION</p>
                            <p>(For Revision)</p>
                        </div>
                        <span class="arrow-down"></span>
                        <div class="status-box" ng-class="{'active': ticketDetails.status == 'Closed'}">
                            <p class="text-white status-name">CLOSED</p>
                            <p>(Completed)</p>
                        </div>
                    </div>
                </div>
                <div class="col-span-2">
                    <div class="flex flex-col gap-2">
                        <p class="text-sm text-white font-semibold">Request Description</p>
                        <!-- <div class="divider"></div> -->
                        <div class="ticket-description scrollable">
                            <!-- <textarea readonly class="text-8 font-sm font-light w-full resize-none" rows="4">{{ ticketDetails.details }}</textarea> -->
                            <p>{{ ticketDetails.details }}</p>
                        </div>
                    </div>
                    <div class="flex flex-col gap-2">
                        <p class="text-sm text-white font-semibold mt-4">Comments</p>
                        <!-- <div class="divider"></div> -->
                        <div class="comments-container" ng-class="{'scrollable': ticketComments.length >= 3}">
                            <div class="flex flex-row gap-2" ng-repeat="comment in ticketComments">
                                <div class="flex flex-col gap-1 items-start justify-start w-10">
                                    <div class="w-10 h-10 bg-black rounded-full flex items-center justify-center">
                                        <p class="primary text-sm font-semibold">{{ comment.usertype == 'admin' ? 'LAI' : (comment.user_name ? comment.user_name.charAt(0).toUpperCase() : 'C') }}</p>
                                    </div>
                                </div>
                                <div class="flex flex-col gap-1 w-full">
                                    <h5 class="primary font-semibold">{{ comment.usertype == 'admin' ? 'Lever A.I Dev Team' : (comment.user_name || 'Customer') }}</h5>
                                    <p class="text-sm text-8 text-white">{{ comment.message }}</p>
                                    <p class="dt-tag">{{ comment.created_at | date:'dd/MM/yyyy HH:mm' }}</p>
                                    <p class="dt-error" ng-if="comment.error">Error in sending comment</p>
                                </div>
                            </div>
                            <div ng-if="ticketComments.length === 0" class="text-center py-4">
                                <p class="text-gray-400 text-sm">No Comments Yet</p>
                            </div>
                        </div>
                        <div class="flex items-center justify-center gap-2 w-full" ng-if="ticketDetails.status == 'Rejected'">
                            <p class="text-sm text-white">Ticket has been rejected and closed.</p>
                        </div>
                        <div class="flex items-center gap-2 w-full" ng-if="ticketDetails.status != 'Rejected'">
                        
                            <!-- Input Field -->
                            <textarea ng-model="commentData.text" ng-disabled="sendingComment"
                                placeholder="Type your comment here..."
                                rows="1">
                            </textarea>

                            <!-- Send Button -->
                            <button class="btn-primary" ng-click="sendComment(ticketDetails)" ng-disabled="sendingComment">
                                <span ng-if="!sendingComment">Send</span>
                                <span ng-if="sendingComment">Sending...</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>