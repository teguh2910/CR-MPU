<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Vendor Portal Purchasing | Cost Reduction & Price Update Management</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Tailwind CSS (via CDN for a single page) -->
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = {
                theme: {
                    extend: {
                        fontFamily: {
                            sans: ['Inter', 'ui-sans-serif', 'system-ui'],
                        },
                        colors: {
                            primary: {
                                50: '#eff6ff',
                                100: '#dbeafe',
                                200: '#bfdbfe',
                                300: '#93c5fd',
                                400: '#60a5fa',
                                500: '#3b82f6',
                                600: '#2563eb',
                                700: '#1d4ed8',
                                800: '#1e40af',
                                900: '#1e3a8a',
                                950: '#172554',
                            },
                        }
                    }
                }
            }
        </script>

        <style>
            .glass {
                background: rgba(255, 255, 255, 0.7);
                backdrop-filter: blur(10px);
                -webkit-backdrop-filter: blur(10px);
                border: 1px solid rgba(255, 255, 255, 0.3);
            }
            .hero-gradient {
                background: radial-gradient(circle at top right, #dbeafe 0%, transparent 40%),
                            radial-gradient(circle at bottom left, #eff6ff 0%, transparent 40%);
            }
        </style>
    </head>
    <body class="antialiased bg-slate-50 text-slate-900 font-sans hero-gradient min-h-screen">
        
        <!-- Navbar -->
        <nav class="fixed top-0 w-full z-50 glass">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16 items-center">
                    <div class="flex items-center space-x-2">
                        <div class="w-10 h-10 bg-primary-600 rounded-xl flex items-center justify-center shadow-lg shadow-primary-200">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                            </svg>
                        </div>
                        <span class="text-xl font-extrabold tracking-tight text-slate-900 uppercase">Vendor Portal <span class="text-primary-600">Purchasing</span></span>
                    </div>
                    
                    <div class="hidden md:flex space-x-8 text-sm font-medium">
                        <a href="#features" class="text-slate-600 hover:text-primary-600 transition">Features</a>
                        <a href="#about" class="text-slate-600 hover:text-primary-600 transition">About</a>
                        @auth
                            <a href="{{ url('/admin') }}" class="px-5 py-2.5 bg-primary-600 text-white rounded-full font-semibold shadow-lg shadow-primary-200 hover:bg-primary-700 transition transform hover:scale-105">Dashboard</a>
                        @endauth
                    </div>
                </div>
            </div>
        </nav>

        <main class="pt-32 pb-16">
            <!-- Hero Section -->
            <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <div class="inline-flex items-center px-4 py-1.5 rounded-full bg-primary-50 border border-primary-100 mb-6">
                    <span class="text-xs font-bold text-primary-700 uppercase tracking-wider">Automated Intelligence</span>
                    <span class="w-1.5 h-1.5 rounded-full bg-primary-600 ml-2 animate-pulse"></span>
                </div>
                
                <h1 class="text-5xl md:text-7xl font-black text-slate-900 mb-6 leading-tight">
                    Intelligent <span class="text-primary-600">Cost Reduction</span> <br> 
                    & Price Update Management
                </h1>
                
                <p class="max-w-2xl mx-auto text-lg text-slate-600 mb-10 leading-relaxed font-medium">
                    Optimize your material price updates, analyze quotation breakdowns, and monitor your budget vs forecast with our integrated dashboard for manufacturing excellence.
                </p>
                
                <div class="flex flex-col sm:flex-row justify-center items-center space-y-4 sm:space-y-0 sm:space-x-6">
                    <a href="{{ url('/admin') }}" class="w-full sm:w-auto px-10 py-4 bg-slate-900 text-white rounded-2xl font-bold shadow-xl hover:bg-slate-800 transition transform hover:-translate-y-1">
                        Go to Dashboard
                    </a>
                    <a href="#features" class="w-full sm:w-auto px-10 py-4 bg-white text-slate-900 rounded-2xl font-bold shadow-md hover:bg-slate-50 transition border border-slate-200">
                        Learn More
                    </a>
                </div>

                <!-- Dashboard Preview Placeholder -->
                <div class="mt-20 relative">
                    <div class="absolute -inset-4 bg-primary-400 opacity-10 blur-3xl rounded-3xl"></div>
                    <div class="relative rounded-3xl border border-slate-200 shadow-2xl overflow-hidden glass p-2">
                        <div class="bg-slate-900 rounded-2xl h-[400px] md:h-[500px] flex flex-col items-center justify-center text-slate-400">
                            <svg class="w-20 h-20 mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                            <p class="text-xl font-bold tracking-widest text-white uppercase">Vendor Portal Dashboard</p>
                            <p class="text-sm mt-2 opacity-60 uppercase">Manufacturing Resource Planning & Cost Control</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Features Section -->
            <section id="features" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-32">
                <div class="text-center mb-16">
                    <h2 class="text-3xl font-extrabold text-slate-900">Powerful Core Features</h2>
                    <p class="mt-4 text-slate-600 font-medium text-lg">Designed specifically for modern manufacturing and procurement workflows.</p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="p-8 rounded-3xl bg-white border border-slate-100 shadow-sm hover:shadow-xl transition-all group">
                        <div class="w-14 h-14 bg-blue-50 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-primary-600 transition-colors">
                            <svg class="w-7 h-7 text-primary-600 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 mb-3 tracking-tight">Price Update History</h3>
                        <p class="text-slate-600 font-medium leading-relaxed">
                            Maintain a comprehensive history of material price changes across your part numbers and periods with full audit trails.
                        </p>
                    </div>

                    <div class="p-8 rounded-3xl bg-white border border-slate-100 shadow-sm hover:shadow-xl transition-all group">
                        <div class="w-14 h-14 bg-amber-50 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-amber-500 transition-colors">
                            <svg class="w-7 h-7 text-amber-600 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 mb-3 tracking-tight">Quotation Analysis</h3>
                        <p class="text-slate-600 font-medium leading-relaxed">
                            Deep dive into quotation breakdowns with granular analysis of raw material, process, tooling, and foh costs.
                        </p>
                    </div>

                    <div class="p-8 rounded-3xl bg-white border border-slate-100 shadow-sm hover:shadow-xl transition-all group">
                        <div class="w-14 h-14 bg-emerald-50 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-emerald-500 transition-colors">
                            <svg class="w-7 h-7 text-emerald-600 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 mb-3 tracking-tight">Budget vs Forecast</h3>
                        <p class="text-slate-600 font-medium leading-relaxed">
                            Visualize budget vs forecast comparisons through dynamic charts and interactive reporting tools for better planning.
                        </p>
                    </div>
                </div>
            </section>

            <!-- About Section -->
            <section id="about" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-32">
                <div class="bg-white rounded-3xl border border-slate-100 shadow-xl overflow-hidden">
                    <div class="grid grid-cols-1 lg:grid-cols-2">
                        <div class="p-8 lg:p-16 flex flex-col justify-center">
                            <h2 class="text-3xl font-extrabold text-slate-900 mb-6">About Vendor Portal Purchasing</h2>
                            <p class="text-slate-600 font-medium text-lg leading-relaxed mb-6">
                                Vendor Portal Purchasing is a specialized enterprise solution designed to streamline the procurement and cost management lifecycle in manufacturing environments.
                            </p>
                            <p class="text-slate-600 font-medium text-lg leading-relaxed mb-8">
                                Our mission is to provide procurement teams with real-time visibility into cost structures, automated price update tracking, and advanced financial forecasting tools to drive efficiency and transparency.
                            </p>
                            <div class="flex items-center space-x-4">
                                <div class="flex flex-col">
                                    <span class="text-2xl font-bold text-primary-600">100%</span>
                                    <span class="text-sm text-slate-500 font-bold uppercase tracking-wider">Visibility</span>
                                </div>
                                <div class="w-px h-10 bg-slate-200 mx-4"></div>
                                <div class="flex flex-col">
                                    <span class="text-2xl font-bold text-primary-600">Real-time</span>
                                    <span class="text-sm text-slate-500 font-bold uppercase tracking-wider">Analytics</span>
                                </div>
                            </div>
                        </div>
                        <div class="bg-slate-50 p-8 flex items-center justify-center border-t lg:border-t-0 lg:border-l border-slate-100">
                            <div class="relative w-full aspect-square max-w-sm">
                                <div class="absolute inset-0 bg-primary-600 opacity-10 rounded-full blur-3xl animate-pulse"></div>
                                <div class="relative flex items-center justify-center h-full">
                                    <svg class="w-48 h-48 text-primary-600 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </main>

        <!-- Footer -->
        <footer class="mt-32 border-t border-slate-200 py-12 glass">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row justify-between items-center">
                <div class="mb-4 md:mb-0">
                    <div class="flex items-center space-x-2">
                        <div class="w-8 h-8 bg-primary-600 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                            </svg>
                        </div>
                        <span class="text-lg font-extrabold tracking-tight text-slate-900 uppercase">Vendor Portal <span class="text-primary-600 italic">Purchasing</span></span>
                    </div>
                </div>
                <div class="text-slate-500 text-sm font-medium">
                    &copy; {{ date('Y') }} Vendor Portal Purchasing. All rights reserved.
                </div>
            </div>
        </footer>
    </body>
</html>
