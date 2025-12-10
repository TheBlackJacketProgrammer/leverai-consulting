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
                    Stop guessing.<br>
                    <span class="text-gradient-cyan">Start strategizing.</span>
                </h1>
                <p class="text-gray-400 text-lg md:text-xl mb-12 max-w-2xl mx-auto leading-relaxed font-light">
                    You know Al can grow your business and automation can slash costs, but you
don't know where to start. We uncover the opportunities that deliver real ROI and
design the technical plans to bring them to life.
                </p>
                <button class="btn-cyan text-base font-bold px-10 py-4 rounded-md shadow-[0_0_20px_rgba(0,194,255,0.3)] hover:shadow-[0_0_40px_rgba(0,194,255,0.6)] transition-all duration-300 transform hover:-translate-y-1" ng-click="openModal('modal_developerlogin')">Get started</button>
            </div>
        </section>

        <!-- Pricing Section -->
        <section class="py-24 px-6 md:px-16 relative z-10">
             <!-- Decorative background for pricing -->
             <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-4xl h-full bg-cyan-900/5 blur-[100px] -z-10 pointer-events-none"></div>

            <div class="max-w-7xl mx-auto text-center">
                <h2 class="text-3xl md:text-5xl font-bold mb-6">Flexible Strategic Expertise</h2>
                <p class="text-gray-400 text-lg mb-16 max-w-3xl mx-auto font-light">
                    Connect with our strategists via text, voice, or video. <br>
Subscribe to a monthly plan to ensure you always have expert advice on
standby.
                </p>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 items-stretch">
                    <!-- Plan 1 -->
                    <div class="glass-panel p-8 rounded-2xl border border-white/5 bg-gray-900/40 flex flex-col hover:border-cyan-500/30 transition-all duration-300">
                        <h3 class="text-xl font-bold mb-2 text-white">1 Hour / Month</h3>
                        <div class="text-3xl font-bold mb-6">$100 <span class="text-sm font-normal text-gray-500">/ mo</span></div>
                        <p class="text-gray-400 text-sm mb-8 min-h-[80px] flex-grow">
                            Ideal for a monthly strategy
check-in, quick idea validation,
or maintaining a direct line to
an expert for ad-hoc questions.
                        </p>
                        <button class="btn-cyan w-full py-3 rounded-lg font-bold shadow-lg hover:shadow-cyan-500/40 transition-all duration-300" ng-click="openModal('modal_developerlogin')">Select Plan</button>
                    </div>

                    <!-- Plan 2 (Popular) -->
                    <div class="glass-panel p-8 rounded-2xl border border-cyan-500 bg-gray-900/80 relative transform md:-translate-y-6 flex flex-col shadow-[0_0_40px_rgba(0,194,255,0.15)]">
                        <div class="absolute -top-4 left-1/2 transform -translate-x-1/2 bg-gradient-to-r from-cyan-400 to-blue-500 text-black text-xs font-bold px-6 py-1.5 rounded-full shadow-lg tracking-wide uppercase">Most Popular</div>
                        <h3 class="text-xl font-bold mb-2 text-white mt-2">10 Hours / Month</h3>
                        <div class="text-3xl font-bold mb-6 text-cyan-400">$900 <span class="text-sm font-normal text-gray-500 text-white">/ mo</span></div>
                        <p class="text-gray-300 text-sm mb-8 min-h-[80px] flex-grow">
                            Ideal for active project
oversight, deep-dive workflow
analysis, vendor selection, or
guiding your team through
implementation.
                        </p>
                        <button class="btn-cyan w-full py-3 rounded-lg font-bold shadow-lg hover:shadow-cyan-500/40 transition-all duration-300" ng-click="openModal('modal_developerlogin')">Select Plan</button>
                    </div>

                    <!-- Plan 3 -->
                    <div class="glass-panel p-8 rounded-2xl border border-white/5 bg-gray-900/40 flex flex-col hover:border-cyan-500/30 transition-all duration-300">
                        <h3 class="text-xl font-bold mb-2 text-white">100 Hours / Month</h3>
                        <div class="text-3xl font-bold mb-6">$8,000 <span class="text-sm font-normal text-gray-500">/ mo</span></div>
                        <p class="text-gray-400 text-sm mb-8 min-h-[80px] flex-grow">
                            Ideal for acting as your
fractional leadership team,
managing long-term strategy,
and providing high-level
decision support.
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
                <div class="glass-panel bg-gray-900/60 rounded-2xl p-12 border border-white/5 relative overflow-hidden group hover:border-cyan-500/30 transition-all duration-300 flex flex-col">
                    <div class="relative z-10 flex flex-col flex-grow">
                        <div class="inline-block mb-8 w-fit">
                            <div class="text-3xl font-bold tracking-[0.2em] text-cyan-400 uppercase leading-none whitespace-nowrap">LEVER <span class="font-normal">AI</span></div>
                            <div class="text-base tracking-[0.3em] text-white uppercase text-right mt-2">TOOLS</div>
                        </div>
                        
                        <h3 class="text-2xl font-bold text-cyan-400 mb-4 transition-colors">Need immediate access?</h3>
                        <p class="text-gray-400 text-sm mb-10 leading-relaxed max-w-md">
                            Skip the build time. Gain immediate access to our growing library of pre-configured custom and open-source tools, hosted and ready for you to use.
                        </p>
                        
                        <button class="btn-cyan w-fit px-8 py-3 rounded-md text-sm font-bold text-gray-900 shadow-[0_0_20px_rgba(0,194,255,0.2)] hover:shadow-[0_0_30px_rgba(0,194,255,0.4)] transition-all duration-300 mt-auto" onclick="window.open('https://leverai.tools', '_blank')">Visit Library</button>
                    </div>
                </div>

                <!-- CTA 2 -->
                <div class="glass-panel bg-gray-900/60 rounded-2xl p-12 border border-white/5 relative overflow-hidden group hover:border-cyan-500/30 transition-all duration-300 flex flex-col">
                    <div class="relative z-10 flex flex-col flex-grow">
                        <div class="inline-block mb-8 w-fit">
                            <div class="text-3xl font-bold tracking-[0.2em] text-cyan-400 uppercase leading-none whitespace-nowrap">LEVER <span class="font-normal">AI</span></div>
                            <div class="text-base tracking-[0.3em] text-white uppercase text-right mt-2">DEVELOPMENT</div>
                        </div>
                        
                        <h3 class="text-2xl font-bold text-cyan-400 mb-4 transition-colors">Don't see what you need?</h3>
                        <p class="text-gray-400 text-sm mb-10 leading-relaxed max-w-md">
                            We engineer specialized Al and automation
solutions from scratch, adding them directly to
your library to solve unique challenges.
                        </p>
                        
                        <button class="btn-cyan w-fit px-8 py-3 rounded-md text-sm font-bold text-gray-900 shadow-[0_0_20px_rgba(0,194,255,0.2)] hover:shadow-[0_0_30px_rgba(0,194,255,0.4)] transition-all duration-300 mt-auto" onclick="window.open('https://leverai.dev', '_blank')">Start Building</button>
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