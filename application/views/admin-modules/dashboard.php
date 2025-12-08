<div class="admin-module-dashboard">
    <section class="full-bleed">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4"> 
            <div class="col-span-1">
                <div class="dashboard-card">
                    <div class="flex flex-col items-start justify-start w-full">
                        <h5>Total Revenue</h5>
                        <h3 class="primary">${{formatCurrency(getTotalRevenue())}}</h3>
                        <div class="divider my-2"></div>
                        <div class="flex flex-row items-center justify-between w-full" ng-if="billingTotals[0]">
                            <p>{{billingTotals[0].month_name}}</p>
                            <p>${{formatCurrency(billingTotals[0].total_amount)}}</p>
                        </div>
                        <div class="flex flex-row items-center justify-between w-full" ng-if="billingTotals[1]">
                            <p>{{billingTotals[1].month_name}}</p>
                            <p>${{formatCurrency(billingTotals[1].total_amount)}}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-span-1">
                <div class="dashboard-card">
                    <div class="flex flex-col items-start justify-start w-full">
                        <h5>Active Subscriptions</h5>
                        <h3 class="primary">{{getTotalActiveSubscriptions()}}</h3>
                        <div class="divider my-2"></div>
                        <div class="flex flex-row items-center justify-between w-full">
                            <p>Basic</p>
                            <p>{{getPlanCount('Basic')}}</p>
                        </div>
                        <div class="flex flex-row items-center justify-between w-full">
                            <p>Standard</p>
                            <p>{{getPlanCount('Standard')}}</p>
                        </div>
                        <div class="flex flex-row items-center justify-between w-full">
                            <p>Pro</p>
                            <p>{{getPlanCount('Pro')}}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-span-1">
                <div class="dashboard-card">
                    <div class="flex flex-col items-start justify-start w-full">
                        <h5>Total Requests</h5>
                        <h3 class="primary">{{getTotalTicketRequests()}}</h3>
                        <div class="divider my-2"></div>
                        <div class="flex flex-row items-center justify-between w-full">
                            <p>Pending</p>
                            <p>{{getTicketCountByStatus('Pending')}}</p>
                        </div>
                        <div class="flex flex-row items-center justify-between w-full">
                            <p>Ongoing</p>
                            <p>{{getTicketCountByStatus('Ongoing')}}</p>
                        </div>
                        <div class="flex flex-row items-center justify-between w-full">
                            <p>Implemented</p>
                            <p>{{getTicketCountByStatus('Implemented')}}</p>
                        </div>
                        <div class="flex flex-row items-center justify-between w-full">
                            <p>Rejected</p>
                            <p>{{getTicketCountByStatus('Rejected')}}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>