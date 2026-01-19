<div id="modal_topUp" class="modal hidden modal_topUp">
    <!-- Modal Dialog Box -->
    <div class="modal-dialog-box">
        <!-- Header -->
        <div class="modal-header">
            <h4 class="text-white">Top Up</h4>
            <button id="closeModal" ng-click="closeModal('modal_topUp')" class="primary">
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
                <h3 class="primary"><?php echo $this->session->userdata('hours_remaining'); ?> hours</h3>
            </div>
            <div class="top-up-options container">
                <h6>Indicate Top Up Value</h6>
                <div class="top-up-choice">
                    <p>+ 1 hours - $ 100.00</p>
                    <div class="top-up-choice-value">
                        <button class="btn-value" ng-click="btnTopUpHours('add')"><span>+</span></button>
                        <label class="input-value">{{ topUpHours }}</label>
                        <button class="btn-value" ng-click="btnTopUpHours('subtract')"><span>-</span></button>
                    </div>
                </div>
                <div class="divider mt-6"></div>
                <div class="top-up-total">
                    <label class="text-white">Total </label>
                    <span class="primary"><b>$ {{ topUpTotal }}</b></span>
                </div>
            </div>
            <button class="btn-primary" ng-click="btnTopUpProceed()">Proceed to Payment</button>
        </div>
    </div>
</div>