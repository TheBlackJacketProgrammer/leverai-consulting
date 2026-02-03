<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LEVER AI Consulting</title>

    <link rel="apple-touch-icon" href="<?php echo base_url('assets/img/logo-mini.png'); ?>" type="image/x-icon" />
    <link rel="shortcut icon" href="<?php echo base_url('assets/img/logo-mini.png'); ?>" type="image/x-icon" />

    <link rel="stylesheet" href="<?php echo base_url('assets/css/main.css?v=' . time()); ?>">
    <link rel="stylesheet" href="<?php echo base_url('assets/css/tailwind.css?v=' . time()); ?>">
    <link rel="stylesheet" href="<?php echo base_url('assets/devtools/toastr/toastr.min.css'); ?>">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Montserrat:wght@600&display=swap" rel="stylesheet">
</head>
<body class="home font-inter text-white antialiased selection:bg-cyan-500 selection:text-white" ng-app="leverai-dev" ng-controller="ng-variables">
    <?php $this->load->view('components/header', array('status' => 'home', 'active_page' => 'faqs')); ?>

    <main>
        

        <section class="py-24 px-6 md:px-16 relative z-10">
            <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_center,rgba(0,194,255,0.10)_0%,rgba(0,0,0,0)_62%)] -z-10 pointer-events-none"></div>

            <div class="max-w-5xl mx-auto" data-faq>
                <div class="text-center mb-14">
                    <h2 class="text-3xl md:text-5xl font-semibold text-white tracking-tight" style="font-family: 'Montserrat', sans-serif;">Frequently Asked Questions</h2>
                </div>

                <div class="border-t border-cyan-500/25">
                    <div class="border-b border-cyan-500/25" data-faq-item>
                        <button type="button" class="w-full flex items-center justify-between gap-6 py-7 text-left" data-faq-trigger aria-expanded="false">
                            <span class="text-lg md:text-xl font-semibold text-white tracking-tight">What if I don't use all my advisory hours?</span>
                            <span class="shrink-0 text-white" data-faq-icon>
                                <svg class="h-6 w-6 transition-transform duration-300 ease-out" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M12 5v14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                    <path d="M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                </svg>
                            </span>
                        </button>
                        <div class="overflow-hidden max-h-0 transition-[max-height] duration-300 ease-out" data-faq-panel>
                            <div class="pb-7 pr-10 text-gray-300/90 text-sm md:text-base font-light leading-relaxed">
                                Your hours roll over indefinitely while your subscription is active. Many clients save hours for major decisions, strategic planning sessions, or crisis situations. There's no "use it or lose it" pressure.
                            </div>
                        </div>
                    </div>

                    <div class="border-b border-cyan-500/25" data-faq-item>
                        <button type="button" class="w-full flex items-center justify-between gap-6 py-7 text-left" data-faq-trigger aria-expanded="false">
                            <span class="text-lg md:text-xl font-semibold text-white tracking-tight">How quickly can I get advice when I need it?</span>
                            <span class="shrink-0 text-white" data-faq-icon>
                                <svg class="h-6 w-6 transition-transform duration-300 ease-out" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M12 5v14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                    <path d="M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                </svg>
                            </span>
                        </button>
                        <div class="overflow-hidden max-h-0 transition-[max-height] duration-300 ease-out" data-faq-panel>
                            <div class="pb-7 pr-10 text-gray-300/90 text-sm md:text-base font-light leading-relaxed">
                                We respond to text messages within 2 hours during business days. Voice and video calls can typically be scheduled within 24-48 hours. For urgent decisions, we offer same-day consultation.
                            </div>
                        </div>
                    </div>

                    <div class="border-b border-cyan-500/25" data-faq-item>
                        <button type="button" class="w-full flex items-center justify-between gap-6 py-7 text-left" data-faq-trigger aria-expanded="false">
                            <span class="text-lg md:text-xl font-semibold text-white tracking-tight">What exactly are Process Definition Requirements (PDRs)?</span>
                            <span class="shrink-0 text-white" data-faq-icon>
                                <svg class="h-6 w-6 transition-transform duration-300 ease-out" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M12 5v14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                    <path d="M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                </svg>
                            </span>
                        </button>
                        <div class="overflow-hidden max-h-0 transition-[max-height] duration-300 ease-out" data-faq-panel>
                            <div class="pb-7 pr-10 text-gray-300/90 text-sm md:text-base font-light leading-relaxed">
                                PDRs are engineering-grade specifications that translate your business goals into buildable requirements. Think of them as blueprints that any developer (ours or yours) can execute without guesswork.
                            </div>
                        </div>
                    </div>

                    <div class="border-b border-cyan-500/25" data-faq-item>
                        <button type="button" class="w-full flex items-center justify-between gap-6 py-7 text-left" data-faq-trigger aria-expanded="false">
                            <span class="text-lg md:text-xl font-semibold text-white tracking-tight">Can you help me evaluate vendors and technical hires?</span>
                            <span class="shrink-0 text-white" data-faq-icon>
                                <svg class="h-6 w-6 transition-transform duration-300 ease-out" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M12 5v14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                    <path d="M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                </svg>
                            </span>
                        </button>
                        <div class="overflow-hidden max-h-0 transition-[max-height] duration-300 ease-out" data-faq-panel>
                            <div class="pb-7 pr-10 text-gray-300/90 text-sm md:text-base font-light leading-relaxed">
                                Yes. We audit proposals, interview candidates alongside you, and validate technical claims. Many clients use advisory hours specifically for hiring decisions to avoid expensive mistakes.
                            </div>
                        </div>
                    </div>

                    <div class="border-b border-cyan-500/25" data-faq-item>
                        <button type="button" class="w-full flex items-center justify-between gap-6 py-7 text-left" data-faq-trigger aria-expanded="false">
                            <span class="text-lg md:text-xl font-semibold text-white tracking-tight">What if our needs change or we want to cancel?</span>
                            <span class="shrink-0 text-white" data-faq-icon>
                                <svg class="h-6 w-6 transition-transform duration-300 ease-out" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M12 5v14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                    <path d="M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                </svg>
                            </span>
                        </button>
                        <div class="overflow-hidden max-h-0 transition-[max-height] duration-300 ease-out" data-faq-panel>
                            <div class="pb-7 pr-10 text-gray-300/90 text-sm md:text-base font-light leading-relaxed">
                                You can upgrade, downgrade, or cancel anytime with no penalties. Your banked hours freeze for 12 months, giving you time to reactivate and reclaim them. Your data remains accessible for 12 months post-cancellation.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="py-24 px-6 md:px-16 relative z-10">
            <div class="max-w-6xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-8">
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

    <?php $this->load->view('components/modals/modal_developers_login'); ?>

    <script src="<?php echo base_url('assets/devtools/jquery/jquery-3.7.1.min.js'); ?>"></script>
    <script src="<?php echo base_url('assets/devtools/toastr/toastr.min.js'); ?>"></script>
    <script src="<?php echo base_url('assets/devtools/angularjs/angular.min.js'); ?>"></script>
    <script src="<?php echo base_url('assets/dist/bundle.min.js'); ?>"></script>

    <script>
        (function () {
            function initFaqAccordion() {
                var root = document.querySelector('[data-faq]');
                if (!root) return;

                var items = Array.prototype.slice.call(root.querySelectorAll('[data-faq-item]'));
                if (!items.length) return;

                function closeItem(item) {
                    var trigger = item.querySelector('[data-faq-trigger]');
                    var panel = item.querySelector('[data-faq-panel]');
                    var icon = item.querySelector('[data-faq-icon] svg');

                    if (trigger) trigger.setAttribute('aria-expanded', 'false');
                    if (panel) panel.style.maxHeight = '0px';
                    if (icon) icon.classList.remove('rotate-45');
                }

                function openItem(item) {
                    var trigger = item.querySelector('[data-faq-trigger]');
                    var panel = item.querySelector('[data-faq-panel]');
                    var icon = item.querySelector('[data-faq-icon] svg');

                    if (trigger) trigger.setAttribute('aria-expanded', 'true');
                    if (panel) panel.style.maxHeight = panel.scrollHeight + 'px';
                    if (icon) icon.classList.add('rotate-45');
                }

                items.forEach(function (item) {
                    var trigger = item.querySelector('[data-faq-trigger]');
                    var panel = item.querySelector('[data-faq-panel]');
                    if (!trigger || !panel) return;

                    trigger.addEventListener('click', function () {
                        var isOpen = trigger.getAttribute('aria-expanded') === 'true';
                        items.forEach(closeItem);
                        if (!isOpen) openItem(item);
                    });
                });

                var resizeTimer = null;
                window.addEventListener('resize', function () {
                    if (resizeTimer) window.clearTimeout(resizeTimer);
                    resizeTimer = window.setTimeout(function () {
                        items.forEach(function (item) {
                            var trigger = item.querySelector('[data-faq-trigger]');
                            var panel = item.querySelector('[data-faq-panel]');
                            if (!trigger || !panel) return;
                            if (trigger.getAttribute('aria-expanded') === 'true') {
                                panel.style.maxHeight = panel.scrollHeight + 'px';
                            }
                        });
                    }, 100);
                });
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initFaqAccordion);
            } else {
                initFaqAccordion();
            }
        })();
    </script>
    
</body>
</html>
