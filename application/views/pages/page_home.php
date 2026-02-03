<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LEVER AI Consulting</title>

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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Montserrat:wght@600&display=swap" rel="stylesheet">
</head>
<body class="home font-inter text-white antialiased selection:bg-cyan-500 selection:text-white" ng-app="leverai-dev" ng-controller="ng-variables">
    <!-- Header -->
    <?php $this->load->view('components/header', array('status' => 'home', 'active_page' => 'home')); ?>


    <main>
        <!-- Hero Section -->
        <section class="hero-section min-h-screen flex flex-col justify-center items-center text-center px-4 pt-32 pb-20 relative overflow-hidden">
            <!-- Background glow/effects handled in SCSS -->
            
            <div class="max-w-5xl mx-auto z-10 relative">
                <!-- Decorative blur behind text -->
                <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[800px] h-[400px] bg-cyan-500/10 rounded-full blur-[120px] -z-10 pointer-events-none"></div>

                <h1 class="text-white text-4xl md:text-6xl font-semibold mb-8 leading-[1.2] tracking-normal text-center align-middle" style="font-family: 'Montserrat', sans-serif;">
                    Stop guessing<br>
                    Start strategizing
                </h1>
                <p class="text-white text-lg md:text-xl mb-12 max-w-2xl mx-auto leading-relaxed font-light" style="max-width: 53rem;">
                    You know AI can grow your business and automation can cut costs, but don’t know where to start. Get <strong>direct access</strong> to our expert strategists via text, voice, or video to avoid bad investments and build the right solutions.
                </p>
                <a href="<?php echo base_url(); ?>subscribe" class="btn-cyan text-base font-bold px-10 py-4 rounded-md shadow-[0_0_20px_rgba(0,194,255,0.3)] hover:shadow-[0_0_40px_rgba(0,194,255,0.6)] transition-all duration-300 transform hover:-translate-y-1 inline-block">Get started</a>
            </div>
        </section>

        <!-- Fractional CTO Section -->
        <section class="py-24 px-6 md:px-16 relative z-10">
            <!-- <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_center,rgba(0,194,255,0.14)_0%,rgba(0,0,0,0)_58%)] -z-10 pointer-events-none"></div> -->

            <div class="max-w-6xl mx-auto">
                <div class="text-center mb-16">
                    <h2 class="text-4xl md:text-5xl font-semibold text-white tracking-tight" style="font-family: 'Montserrat', sans-serif;">Your Fractional CTO on Standby</h2>
                    <p class="text-gray-300 mt-4 text-base md:text-lg font-light">Use your advisory hours for any strategic challenge.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="rounded-[2rem] md:rounded-none md:rounded-tl-[2rem] border-[6px] border-transparent shadow-[0_0_60px_rgba(0,194,255,0.12)] p-10 md:p-12 min-h-[260px]" style="background: linear-gradient(135deg, #0b2a33 0%, #071d23 50%, #041316 100%) padding-box, linear-gradient(90deg, #33CCFF 0%, #FFFFFF 100%) border-box;">
                        <h3 class="text-xl md:text-2xl font-semibold text-white mb-6 tracking-tight" style="font-family: 'Montserrat', sans-serif;">Identify High-ROI Opportunities</h3>
                        <p class="text-gray-200/90 text-base md:text-lg leading-relaxed font-light">
                            We analyze your operations to pinpoint exactly where AI and automation will deliver the biggest returns. Move past trendy tech to solutions that actually transform your business.
                        </p>
                    </div>

                    <div class="rounded-[2rem] md:rounded-none md:rounded-tr-[2rem] border-[6px] border-transparent shadow-[0_0_60px_rgba(0,194,255,0.12)] p-10 md:p-12 min-h-[260px]" style="background: linear-gradient(135deg, #0b2a33 0%, #071d23 50%, #041316 100%) padding-box, linear-gradient(90deg, #33CCFF 0%, #FFFFFF 100%) border-box;">
                        <h3 class="text-xl md:text-2xl font-semibold text-white mb-6 tracking-tight" style="font-family: 'Montserrat', sans-serif;">Validate Tools, Hires, &amp; Vendors</h3>
                        <p class="text-gray-200/90 text-base md:text-lg leading-relaxed font-light">
                            We help you evaluate software platforms, technical hires, and service proposals before you commit. Stop expensive experiments, invest in proven solutions.
                        </p>
                    </div>

                    <div class="rounded-[2rem] md:rounded-none md:rounded-bl-[2rem] border-[6px] border-transparent shadow-[0_0_60px_rgba(0,194,255,0.12)] p-10 md:p-12 min-h-[260px]" style="background: linear-gradient(135deg, #0b2a33 0%, #071d23 50%, #041316 100%) padding-box, linear-gradient(90deg, #33CCFF 0%, #FFFFFF 100%) border-box;">
                        <h3 class="text-xl md:text-2xl font-semibold text-white mb-6 tracking-tight" style="font-family: 'Montserrat', sans-serif;">Bridge Strategy to Execution</h3>
                        <p class="text-gray-200/90 text-base md:text-lg leading-relaxed font-light">
                            We turn your goals into engineering-grade PDRs (Process Definition Requirements) that any developer can execute. Strategy becomes action without confusion or costly rework.
                        </p>
                    </div>

                    <div class="rounded-[2rem] md:rounded-none md:rounded-br-[2rem] border-[6px] border-transparent shadow-[0_0_60px_rgba(0,194,255,0.12)] p-10 md:p-12 min-h-[260px]" style="background: linear-gradient(135deg, #0b2a33 0%, #071d23 50%, #041316 100%) padding-box, linear-gradient(90deg, #33CCFF 0%, #FFFFFF 100%) border-box;">
                        <h3 class="text-xl md:text-2xl font-semibold text-white mb-6 tracking-tight" style="font-family: 'Montserrat', sans-serif;">Accelerate Adoption</h3>
                        <p class="text-gray-200/90 text-base md:text-lg leading-relaxed font-light">
                            We bridge the gap between code and culture with proper education and support. Turn tools into actual workflow improvements instead of expensive shelfware.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Pricing Section -->
        <section class="py-24 px-6 md:px-16 relative z-10">
             <!-- Decorative background for pricing -->
             <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-4xl h-full bg-cyan-900/5 blur-[100px] -z-10 pointer-events-none"></div>

            <div class="max-w-7xl mx-auto text-center">
                <h2 class="text-3xl md:text-5xl font-bold mb-6">Leadership on Retainer</h2>
                <p class="text-gray-400 text-lg mb-16 max-w-3xl mx-auto font-light">
                    Connect with our strategists via <strong>text, voice, or video</strong>. Subscribe to a monthly plan to <br>
                    ensure you always have expert advice on standby.
                </p>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 items-stretch">
                    <!-- Plan 1 -->
                    <div class="glass-panel p-8 rounded-2xl border border-white/5 bg-gray-900/40 flex flex-col hover:border-cyan-500/30 transition-all duration-300">
                        <h3 class="text-xl font-bold mb-2 text-white">1 Hour / Month</h3>
                        <div class="text-3xl font-bold mb-6">$100 <span class="text-sm font-normal text-gray-500">/ mo</span></div>
                        <p class="text-white text-sm mb-8 min-h-[80px] flex-grow" style="font-weight: 400;">
                            <strong>Maintain Alignment.</strong><br>
                            One bad tool purchase costs more than a year of this plan. Get monthly validation on AI investments, vendor claims, and technical decisions before they become expensive mistakes.
                        </p>
                        <a href="<?php echo base_url(); ?>subscribe" class="btn-cyan w-full py-3 rounded-lg font-bold shadow-lg hover:shadow-cyan-500/40 transition-all duration-300 block text-center">Select Plan</a>
                    </div>

                    <!-- Plan 2 (Popular) -->
                    <div class="glass-panel p-8 rounded-2xl border border-cyan-500 bg-gray-900/80 relative transform md:-translate-y-6 flex flex-col shadow-[0_0_40px_rgba(0,194,255,0.15)]">
                        <div class="absolute -top-4 left-1/2 transform -translate-x-1/2 bg-gradient-to-r from-cyan-400 to-blue-500 text-black text-xs font-bold px-6 py-1.5 rounded-full shadow-lg tracking-wide uppercase">Most Popular</div>
                        <h3 class="text-xl font-bold mb-2 text-white mt-2">10 Hours / Month</h3>
                        <div class="text-3xl font-bold mb-6 text-cyan-400">$900 <span class="text-sm font-normal text-gray-500 text-white">/ mo</span></div>
                        <p class="text-white text-sm mb-8 min-h-[80px] flex-grow" style="font-weight: 400;">
                            <strong>Active Implementation</strong><br>
                            Ship automation faster with expert oversight. We give you build-ready PDRs, validate technical approaches, and keep your projects moving. Prevent expensive detours while your competitors are still scheduling discovery calls.
                        </p>
                        <a href="<?php echo base_url(); ?>subscribe" class="btn-cyan w-full py-3 rounded-lg font-bold shadow-lg hover:shadow-cyan-500/40 transition-all duration-300 block text-center">Select Plan</a>
                    </div>

                    <!-- Plan 3 -->
                    <div class="glass-panel p-8 rounded-2xl border border-white/5 bg-gray-900/40 flex flex-col hover:border-cyan-500/30 transition-all duration-300">
                        <h3 class="text-xl font-bold mb-2 text-white">100 Hours / Month</h3>
                        <div class="text-3xl font-bold mb-6">$8,000 <span class="text-sm font-normal text-gray-500">/ mo</span></div>
                        <p class="text-white text-sm mb-8 min-h-[80px] flex-grow" style="font-weight: 400;">
                            <strong>Fractional Leadership.</strong><br>
                            Get executive-level technical guidance for a fraction of a full-time CTO salary ($200K+). We manage long-term strategy, prevent technical debt, and ensure every dollar of your AI budget drives measurable ROI.
                        </p>
                        <a href="<?php echo base_url(); ?>subscribe" class="btn-cyan w-full py-3 rounded-lg font-bold shadow-lg hover:shadow-cyan-500/40 transition-all duration-300 block text-center">Select Plan</a>
                    </div>
                </div>

                <p class="text-white text-1xl mt-10 tracking-wide">
                    Not satisfied? Get a full refund with our 30 day money back guarantee!
                </p>
            </div>
        </section>

        <!-- Rollover Hours Section -->
        <section class="relative z-10">
            <div class="h-px w-full bg-gradient-to-r from-transparent via-cyan-500/40 to-transparent"></div>
            <div class="py-20 px-6 md:px-16">
                <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_center,rgba(0,194,255,0.10)_0%,rgba(0,0,0,0)_62%)] -z-10 pointer-events-none"></div>

                <div class="max-w-5xl mx-auto text-center">
                    <h2 class="text-3xl md:text-5xl font-semibold text-white tracking-tight" style="font-family: 'Montserrat', sans-serif;">Your Hours Never Expire</h2>
                    <p class="text-gray-300 mt-6 text-base md:text-lg font-light leading-relaxed">
                        Advisory hours roll over indefinitely while your subscription is active.<br class="hidden md:block">
                        Need more hours? Purchase additional hours anytime.
                    </p>
                    <p class="text-gray-300 mt-8 text-base md:text-lg font-light leading-relaxed">
                        Bank time for strategic projects, save for major initiatives, or use as needed.<br class="hidden md:block">
                        Your investment compounds month over month.
                    </p>
                </div>
            </div>
            <div class="h-px w-full bg-gradient-to-r from-transparent via-cyan-500/40 to-transparent"></div>
        </section>

        <section class="py-24 px-6 md:px-16 relative z-10">
            <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_center,rgba(0,194,255,0.12)_0%,rgba(0,0,0,0)_60%)] -z-10 pointer-events-none"></div>

            <div class="max-w-7xl mx-auto">
                <div class="text-center mb-14">
                    <h2 class="text-3xl md:text-5xl font-semibold text-white tracking-tight" style="font-family: 'Montserrat', sans-serif;">Expert Guidance, Your Terms</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="rounded-2xl border border-cyan-400/30 bg-gradient-to-b from-[#07222b] via-[#061a20] to-[#041316] p-10 min-h-[300px] shadow-[0_0_60px_rgba(0,194,255,0.10)]">
                        <h3 class="text-lg md:text-xl font-semibold text-white tracking-tight" style="font-family: 'Montserrat', sans-serif;">Immediate Text Access</h3>
                        <ul class="mt-6 space-y-3 text-gray-200/90 text-sm md:text-base font-light">
                            <li class="flex gap-3"><span class="mt-2 h-1.5 w-1.5 rounded-full bg-gray-200/80 shrink-0"></span><span>Should we build or buy this automation?</span></li>
                            <li class="flex gap-3"><span class="mt-2 h-1.5 w-1.5 rounded-full bg-gray-200/80 shrink-0"></span><span>Review our tech stack decisions?</span></li>
                            <li class="flex gap-3"><span class="mt-2 h-1.5 w-1.5 rounded-full bg-gray-200/80 shrink-0"></span><span>Does this tool integration make sense?</span></li>
                        </ul>
                        <div class="mt-10 text-cyan-400/90 text-xs md:text-sm font-medium">Response ASAP during business hours</div>
                    </div>

                    <div class="rounded-2xl border border-cyan-400/30 bg-gradient-to-b from-[#07222b] via-[#061a20] to-[#041316] p-10 min-h-[300px] shadow-[0_0_60px_rgba(0,194,255,0.10)]">
                        <h3 class="text-lg md:text-xl font-semibold text-white tracking-tight" style="font-family: 'Montserrat', sans-serif;">Strategic Voice Calls</h3>
                        <ul class="mt-6 space-y-3 text-gray-200/90 text-sm md:text-base font-light">
                            <li class="flex gap-3"><span class="mt-2 h-1.5 w-1.5 rounded-full bg-gray-200/80 shrink-0"></span><span>Map high-ROI automation opportunities</span></li>
                            <li class="flex gap-3"><span class="mt-2 h-1.5 w-1.5 rounded-full bg-gray-200/80 shrink-0"></span><span>Plan tool adoption &amp; rollouts</span></li>
                            <li class="flex gap-3"><span class="mt-2 h-1.5 w-1.5 rounded-full bg-gray-200/80 shrink-0"></span><span>Validate technical hires &amp; vendors</span></li>
                        </ul>
                        <br>
                        <div class="mt-10 text-cyan-400/90 text-xs md:text-sm font-medium">Scheduled within 36 hours</div>
                    </div>

                    <div class="rounded-2xl border border-cyan-400/30 bg-gradient-to-b from-[#07222b] via-[#061a20] to-[#041316] p-10 min-h-[300px] shadow-[0_0_60px_rgba(0,194,255,0.10)]">
                        <h3 class="text-lg md:text-xl font-semibold text-white tracking-tight" style="font-family: 'Montserrat', sans-serif;">Collaborative Video Sessions</h3>
                        <ul class="mt-6 space-y-3 text-gray-200/90 text-sm md:text-base font-light">
                            <li class="flex gap-3"><span class="mt-2 h-1.5 w-1.5 rounded-full bg-gray-200/80 shrink-0"></span><span>Review build-ready PDRs in detail</span></li>
                            <li class="flex gap-3"><span class="mt-2 h-1.5 w-1.5 rounded-full bg-gray-200/80 shrink-0"></span><span>Train teams on implementation</span></li>
                            <li class="flex gap-3"><span class="mt-2 h-1.5 w-1.5 rounded-full bg-gray-200/80 shrink-0"></span><span>Run technical architecture reviews</span></li>
                        </ul>
                        <div class="mt-10 text-cyan-400/90 text-xs md:text-sm font-medium">Scheduled within 48 hours</div>
                    </div>
                </div>

                <div class="text-center mt-16">
                    <p class="text-white text-xl md:text-2xl font-semibold">Direct access to expert validation. No gatekeepers, no waiting weeks</p>
                </div>
            </div>
        </section>

        <section class="py-24 px-6 md:px-16 relative z-10">
            <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_center,rgba(0,194,255,0.10)_0%,rgba(0,0,0,0)_62%)] -z-10 pointer-events-none"></div>

            <div class="max-w-7xl mx-auto">
                <div class="text-center mb-16">
                    <h2 class="text-3xl md:text-5xl font-semibold text-white tracking-tight" style="font-family: 'Montserrat', sans-serif;">Is This Right for You?</h2>
                    <p class="text-gray-300 mt-5 text-base md:text-lg font-light leading-relaxed">
                        This service is for leaders making technical decisions without<br class="hidden md:block">
                        dedicated technical expertise.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-12 md:gap-16">
                    <div class="max-w-sm">
                        <div class="h-[3px] w-72 bg-gradient-to-r from-cyan-400/90 via-cyan-400/50 to-transparent rounded-full [clip-path:polygon(0_0,92%_35%,100%_50%,92%_65%,0_100%)] mb-6"></div>
                        <p class="text-gray-200/90 text-base md:text-lg font-light leading-relaxed">
                            Identify where automation drives real ROI, not generic frameworks.
                        </p>
                    </div>

                    <div class="max-w-sm">
                        <div class="h-[3px] w-72 bg-gradient-to-r from-cyan-400/90 via-cyan-400/50 to-transparent rounded-full [clip-path:polygon(0_0,92%_35%,100%_50%,92%_65%,0_100%)] mb-6"></div>
                        <p class="text-gray-200/90 text-base md:text-lg font-light leading-relaxed">
                            Build-ready PDRs that turn strategy into action, not dead-end recommendations.
                        </p>
                    </div>

                    <div class="max-w-sm">
                        <div class="h-[3px] w-72 bg-gradient-to-r from-cyan-400/90 via-cyan-400/50 to-transparent rounded-full [clip-path:polygon(0_0,92%_35%,100%_50%,92%_65%,0_100%)] mb-6"></div>
                        <p class="text-gray-200/90 text-base md:text-lg font-light leading-relaxed">
                            Validate AI tools, vendors, or hires with expert guidance, not unexecutable slides.
                        </p>
                    </div>
                </div>

                <div class="mt-16 grid grid-cols-1 md:grid-cols-2 gap-12 md:gap-16 max-w-4xl mx-auto">
                    <div class="max-w-sm">
                        <div class="h-[3px] w-72 bg-gradient-to-r from-cyan-400/90 via-cyan-400/50 to-transparent rounded-full [clip-path:polygon(0_0,92%_35%,100%_50%,92%_65%,0_100%)] mb-6"></div>
                        <p class="text-gray-200/90 text-base md:text-lg font-light leading-relaxed">
                            Support your team in adopting new tools, not just buying them.
                        </p>
                    </div>

                    <div class="max-w-sm">
                        <div class="h-[3px] w-72 bg-gradient-to-r from-cyan-400/90 via-cyan-400/50 to-transparent rounded-full [clip-path:polygon(0_0,92%_35%,100%_50%,92%_65%,0_100%)] mb-6"></div>
                        <p class="text-gray-200/90 text-base md:text-lg font-light leading-relaxed">
                            Access technical guidance instantly, not weeks later.
                        </p>
                    </div>
                </div>
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
                            <div class="w-full text-base tracking-[0.3em] text-white uppercase text-center mt-2">TOOLS</div>
                        </div>
                        
                        <h3 class="text-2xl font-bold text-cyan-400 mb-4 transition-colors">Need quick wins while you strategize?</h3>
                        <p class="text-gray-400 text-sm mb-10 leading-relaxed max-w-md">
                            Test existing tools at $5/month to discover what delivers ROI in your operations. Expert strategic guidance builds on what you learn.
                        </p>
                        
                        <button class="btn-cyan w-fit px-8 py-3 rounded-md text-sm font-bold text-gray-900 shadow-[0_0_20px_rgba(0,194,255,0.2)] hover:shadow-[0_0_30px_rgba(0,194,255,0.4)] transition-all duration-300 mt-auto" onclick="window.open('https://leverai.tools', '_blank')">Try Tools Free</button>
                    </div>
                </div>

                <!-- CTA 2 -->
                <div class="glass-panel bg-gray-900/60 rounded-2xl p-12 border border-white/5 relative overflow-hidden group hover:border-cyan-500/30 transition-all duration-300 flex flex-col">
                    <div class="relative z-10 flex flex-col flex-grow">
                        <div class="inline-block mb-8 w-fit">
                            <div class="text-3xl font-bold tracking-[0.2em] text-cyan-400 uppercase leading-none whitespace-nowrap">LEVER <span class="font-normal">AI</span></div>
                            <div class="w-full text-base tracking-[0.3em] text-white uppercase text-center mt-2">DEVELOPMENT</div>
                        </div>
                        
                        <h3 class="text-2xl font-bold text-cyan-400 mb-4 transition-colors">Need a team to execute your strategy?</h3>
                        <p class="text-gray-400 text-sm mb-10 leading-relaxed max-w-md">
                            Subscribe to our development service. We'll take your roadmap, PDRs, and technical requirements, and build the exact solutions you need, hosted on our managed infrastructure.
                        </p>
                        
                        <button class="btn-cyan w-fit px-8 py-3 rounded-md text-sm font-bold text-gray-900 shadow-[0_0_20px_rgba(0,194,255,0.2)] hover:shadow-[0_0_30px_rgba(0,194,255,0.4)] transition-all duration-300 mt-auto" onclick="window.open('https://leverai.dev', '_blank')">Build Your Solution</button>
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
