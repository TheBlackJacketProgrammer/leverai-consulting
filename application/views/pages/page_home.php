<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lever A.I. Development - Automate Your Business</title>

    <!-- Favicon -->
    <link rel="apple-touch-icon" href="<?php echo base_url('assets/img/logo-mini.png'); ?>" type="image/x-icon" />
    <link rel="shortcut icon" href="<?php echo base_url('assets/img/logo-mini.png'); ?>" type="image/x-icon" />

    <!-- CSS -->
    <link rel="stylesheet" href="<?php echo base_url('assets/css/main.css?v=' . time()); ?>">
    <link rel="stylesheet" href="<?php echo base_url('assets/css/tailwind.css?v=' . time()); ?>">
    <link rel="stylesheet" href="<?php echo base_url('assets/devtools/toastr/toastr.min.css'); ?>">

    <!-- Fonts (Google Fonts - Inter) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="home font-inter text-white antialiased selection:bg-cyan-500 selection:text-white" ng-app="leverai-dev" ng-controller="ng-variables">
    <!-- Header -->
    <?php $this->load->view('components/header', array('status' => 'home')); ?>


    <main>
        <!-- Hero Section -->
        <section class="hero-section min-h-screen flex flex-col justify-center items-center text-center px-4 pt-32 pb-20 relative overflow-hidden">
            <!-- Background glow/effects handled in SCSS -->
            
            <div class="max-w-5xl mx-auto z-10 relative">
                <!-- Decorative blur behind text -->
                <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[800px] h-[400px] bg-cyan-500/10 rounded-full blur-[120px] -z-10 pointer-events-none"></div>

                <h1 class="text-5xl md:text-7xl font-bold mb-8 leading-[1.1] tracking-tight">
                    Automate Your Business with<br>
                    <span class="text-gradient-cyan">Custom AI & Open-Source Tech</span>
                </h1>
                <p class="text-gray-400 text-lg md:text-xl mb-12 max-w-2xl mx-auto leading-relaxed font-light">
                    Your custom AI & automation stack, built and hosted on leverai.tools.<br>
                    We handle the tech so you get enterprise power for less.
                </p>
                <button class="btn-cyan text-base font-bold px-10 py-4 rounded-md shadow-[0_0_20px_rgba(0,194,255,0.3)] hover:shadow-[0_0_40px_rgba(0,194,255,0.6)] transition-all duration-300 transform hover:-translate-y-1" ng-click="openModal('modal_developerlogin')">Start Building</button>
            </div>
        </section>

        <!-- Features Section -->
        <section class="py-24 px-6 md:px-16 relative z-10">
            <div class="max-w-7xl mx-auto">
                <!-- Section Header -->
                <div class="text-center mb-20">
                     <h2 class="text-3xl md:text-5xl font-bold mb-6">Build Your Perfect AI &<br>Automation Stack</h2>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Card 1 -->
                    <div class="group relative rounded-2xl p-[1px] bg-gradient-to-br from-cyan-400 to-blue-600 h-full">
                        <div class="relative h-full rounded-2xl bg-gradient-to-b from-gray-900 to-black p-10 overflow-hidden">
                            <div class="icon mb-6 text-cyan-400 group-hover:text-cyan-300 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.38a2 2 0 0 0-.73-2.73l-.15-.1a2 2 0 0 1-1-1.72v-.51a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                            </div>
                            <h3 class="text-2xl font-bold mb-4 text-white group-hover:text-cyan-400 transition-colors">Custom tool development</h3>
                            <p class="text-gray-400 text-base leading-relaxed">
                                We engineer specialized AI and automation solutions from scratch, adding them directly to our library to solve your unique challenges.
                            </p>
                        </div>
                    </div>

                    <!-- Card 2 -->
                    <div class="group relative rounded-2xl p-[1px] bg-gradient-to-br from-cyan-400 to-blue-600 h-full">
                        <div class="relative h-full rounded-2xl bg-gradient-to-b from-gray-900 to-black p-10 overflow-hidden">
                            <div class="icon mb-6 text-cyan-400 group-hover:text-cyan-300 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20a8 8 0 1 0 0-16 8 8 0 0 0 0 16Z"/><path d="M12 14a2 2 0 1 0 0-4 2 2 0 0 0 0 4Z"/><path d="M12 2v2"/><path d="M12 22v-2"/><path d="m17 20.66-1-1.73"/><path d="M11 10.27 7 3.34"/><path d="m20.66 17-1.73-1"/><path d="m3.34 7 1.73 1"/><path d="M14 12h8"/><path d="M2 12h2"/><path d="m20.66 7-1.73 1"/><path d="m3.34 17 1.73-1"/><path d="m17 3.34-1 1.73"/><path d="m11 13.73-4 6.93"/></svg>
                            </div>
                            <h3 class="text-2xl font-bold mb-4 text-white group-hover:text-cyan-400 transition-colors">Library Customization</h3>
                            <p class="text-gray-400 text-base leading-relaxed">
                                We modify any tool in our existing library to fit your workflow. We re-engineer the code to align perfectly with your precise operational needs.
                            </p>
                        </div>
                    </div>

                    <!-- Card 3 -->
                    <div class="group relative rounded-2xl p-[1px] bg-gradient-to-br from-cyan-400 to-blue-600 h-full">
                        <div class="relative h-full rounded-2xl bg-gradient-to-b from-gray-900 to-black p-10 overflow-hidden">
                            <div class="icon mb-6 text-cyan-400 group-hover:text-cyan-300 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>
                            </div>
                            <h3 class="text-2xl font-bold mb-4 text-white group-hover:text-cyan-400 transition-colors">Open-Source Integration</h3>
                            <p class="text-gray-400 text-base leading-relaxed">
                                We integrate the industry's best open-source tools into our library. We configure and deploy them so they run seamlessly alongside your custom stack.
                            </p>
                        </div>
                    </div>

                    <!-- Card 4 -->
                    <div class="group relative rounded-2xl p-[1px] bg-gradient-to-br from-cyan-400 to-blue-600 h-full">
                        <div class="relative h-full rounded-2xl bg-gradient-to-b from-gray-900 to-black p-10 overflow-hidden">
                            <div class="icon mb-6 text-cyan-400 group-hover:text-cyan-300 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                            </div>
                            <h3 class="text-2xl font-bold mb-4 text-white group-hover:text-cyan-400 transition-colors">Stable. Secure. Supported.</h3>
                            <p class="text-gray-400 text-base leading-relaxed">
                                Run on enterprise-grade infrastructure with near-cost hosting. We handle security, maintenance, and support so you can scale without worry.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Pricing Section -->
        <section class="py-24 px-6 md:px-16 relative z-10">
             <!-- Decorative background for pricing -->
             <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-4xl h-full bg-cyan-900/5 blur-[100px] -z-10 pointer-events-none"></div>

            <div class="max-w-7xl mx-auto text-center">
                <h2 class="text-3xl md:text-5xl font-bold mb-6">Ready to build your library?</h2>
                <p class="text-gray-400 text-lg mb-16 max-w-3xl mx-auto font-light">
                    Get your custom tools developed, hosted, and supported on leverai.tools today.<br>Choose the monthly plan that fits your scaling needs.
                </p>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 items-stretch">
                    <!-- Plan 1 -->
                    <div class="glass-panel p-8 rounded-2xl border border-white/5 bg-gray-900/40 flex flex-col hover:border-cyan-500/30 transition-all duration-300">
                        <h3 class="text-xl font-bold mb-2 text-white">1 Hour / Month</h3>
                        <div class="text-3xl font-bold mb-6">$50 <span class="text-sm font-normal text-gray-500">/ mo</span></div>
                        <p class="text-gray-400 text-sm mb-8 min-h-[80px] flex-grow">
                            <span class="font-bold text-white block mb-2">Perfect for maintenance.</span>
                            Ideal for quick bug fixes, small script modifications, or ensuring your existing tools run smoothly.
                        </p>
                        <button class="btn-cyan w-full py-3 rounded-lg font-bold shadow-lg hover:shadow-cyan-500/40 transition-all duration-300" ng-click="openModal('modal_developerlogin')">Select Plan</button>
                    </div>

                    <!-- Plan 2 (Popular) -->
                    <div class="glass-panel p-8 rounded-2xl border border-cyan-500 bg-gray-900/80 relative transform md:-translate-y-6 flex flex-col shadow-[0_0_40px_rgba(0,194,255,0.15)]">
                        <div class="absolute -top-4 left-1/2 transform -translate-x-1/2 bg-gradient-to-r from-cyan-400 to-blue-500 text-black text-xs font-bold px-6 py-1.5 rounded-full shadow-lg tracking-wide uppercase">Most Popular</div>
                        <h3 class="text-xl font-bold mb-2 text-white mt-2">10 Hours / Month</h3>
                        <div class="text-3xl font-bold mb-6 text-cyan-400">$450 <span class="text-sm font-normal text-gray-500 text-white">/ mo</span></div>
                        <p class="text-gray-300 text-sm mb-8 min-h-[80px] flex-grow">
                            <span class="font-bold text-white block mb-2">Perfect for active development.</span>
                            Ideal for building new custom tools, modifying open-source apps, or optimizing complex workflows.
                        </p>
                        <button class="btn-cyan w-full py-3 rounded-lg font-bold shadow-lg hover:shadow-cyan-500/40 transition-all duration-300" ng-click="openModal('modal_developerlogin')">Select Plan</button>
                    </div>

                    <!-- Plan 3 -->
                    <div class="glass-panel p-8 rounded-2xl border border-white/5 bg-gray-900/40 flex flex-col hover:border-cyan-500/30 transition-all duration-300">
                        <h3 class="text-xl font-bold mb-2 text-white">100 Hours / Month</h3>
                        <div class="text-3xl font-bold mb-6">$4,000 <span class="text-sm font-normal text-gray-500">/ mo</span></div>
                        <p class="text-gray-400 text-sm mb-8 min-h-[80px] flex-grow">
                            <span class="font-bold text-white block mb-2">Perfect for scaling.</span>
                            Ideal for full-stack application development, continuous library expansion, and acting as your fractional engineering department.
                        </p>
                        <button class="btn-cyan w-full py-3 rounded-lg font-bold shadow-lg hover:shadow-cyan-500/40 transition-all duration-300" ng-click="openModal('modal_developerlogin')">Select Plan</button>
                    </div>
                </div>

                <p class="text-cyan-500/80 text-xs mt-10 tracking-wide">
                    UNUSED HOURS ROLL OVER INDEFINITELY WHILE YOUR SUBSCRIPTION IS ACTIVE. TOP UP YOUR BALANCE AT ANY TIME.
                </p>
            </div>
        </section>

        <!-- CTA / Footer Cards -->
        <section class="py-24 px-6 md:px-16 relative z-10">
            <div class="max-w-6xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- CTA 1 -->
                <div class="glass-panel bg-gray-900/60 rounded-2xl p-12 border border-white/5 relative overflow-hidden group hover:border-cyan-500/30 transition-all duration-300">
                    <div class="relative z-10">
                        <div class="inline-block mb-8">
                            <div class="text-3xl font-bold tracking-[0.2em] text-cyan-400 uppercase leading-none whitespace-nowrap">LEVER <span class="font-normal">AI</span></div>
                            <div class="text-base tracking-[0.3em] text-white uppercase text-right mt-2">TOOLS</div>
                        </div>
                        
                        <h3 class="text-2xl font-bold text-cyan-400 mb-4 transition-colors">Ready to start?</h3>
                        <p class="text-gray-400 text-sm mb-10 leading-relaxed max-w-md">
                            Skip the build time. Gain immediate access to our growing library of pre-configured custom and open-source tools, hosted and ready for you to use.
                        </p>
                        
                        <button class="btn-cyan w-fit px-8 py-3 rounded-md text-sm font-bold text-gray-900 shadow-[0_0_20px_rgba(0,194,255,0.2)] hover:shadow-[0_0_30px_rgba(0,194,255,0.4)] transition-all duration-300" ng-click="openModal('modal_developerlogin')">Browse Library</button>
                    </div>
                </div>

                <!-- CTA 2 -->
                <div class="glass-panel bg-gray-900/60 rounded-2xl p-12 border border-white/5 relative overflow-hidden group hover:border-cyan-500/30 transition-all duration-300">
                    <div class="relative z-10">
                        <div class="inline-block mb-8">
                            <div class="text-3xl font-bold tracking-[0.2em] text-cyan-400 uppercase leading-none whitespace-nowrap">LEVER <span class="font-normal">AI</span></div>
                            <div class="text-base tracking-[0.3em] text-white uppercase text-right mt-2">CONSULTING</div>
                        </div>
                        
                        <h3 class="text-2xl font-bold text-cyan-400 mb-4 transition-colors">Need a strategy?</h3>
                        <p class="text-gray-400 text-sm mb-10 leading-relaxed max-w-md">
                            Not sure where to start? We analyze your workflows and recommend the best custom and open-source tech to maximize your efficiency.
                        </p>
                        
                        <button class="btn-cyan w-fit px-8 py-3 rounded-md text-sm font-bold text-gray-900 shadow-[0_0_20px_rgba(0,194,255,0.2)] hover:shadow-[0_0_30px_rgba(0,194,255,0.4)] transition-all duration-300" ng-click="openModal('modal_developerlogin')">Get Advice</button>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer class="w-full text-center pb-10 pt-6 border-t border-white/5 mt-12">
        <p class="text-white text-sm tracking-wide mx-auto">&copy; 2025 LEVER LLC. All rights reserved Privacy Policy.</p>
    </footer>

    <!-- Modals -->
    <?php $this->load->view('components/modals/modal_developers_login'); ?>

    <!-- jQuery -->
    <script src="<?php echo base_url('assets/devtools/jquery/jquery-3.7.1.min.js'); ?>"></script>

    <!-- Toastr JS -->
    <script src="<?php echo base_url('assets/devtools/toastr/toastr.min.js'); ?>"></script>

    <!-- AngularJS -->
    <script src="<?php echo base_url('assets/devtools/angularjs/angular.min.js'); ?>"></script>

    <!-- Angular JS Scripts Bundle -->
    <script src="<?php echo base_url('assets/dist/bundle.min.js'); ?>"></script>
    
</body>
</html>