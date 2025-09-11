<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }} - モダンSNS</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <style>
            .gradient-bg {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            }
            .card-hover {
                transition: all 0.3s ease;
            }
            .card-hover:hover {
                transform: translateY(-2px);
                box-shadow: 0 8px 25px rgba(0,0,0,0.1);
            }
            .btn-primary {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: #ffffff;
            }
            .btn-primary:hover {
                background: linear-gradient(135deg, #5a67d8 0%, #6b46c1 100%);
                color: #ffffff;
            }
            
            /* ページネーション スタイル */
            .pagination-container nav {
                display: flex;
                justify-content: center;
            }
            
            .pagination-container .flex {
                background: white;
                border-radius: 1rem;
                padding: 0.5rem;
                box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
                border: 1px solid #e5e7eb;
            }
            
            .pagination-container a,
            .pagination-container span {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                min-width: 2.5rem;
                height: 2.5rem;
                padding: 0 0.75rem;
                margin: 0 0.125rem;
                border-radius: 0.5rem;
                text-decoration: none;
                font-weight: 500;
                transition: all 0.2s ease;
            }
            
            .pagination-container a {
                color: #6b7280;
                background: transparent;
            }
            
            .pagination-container a:hover {
                color: #ffffff;
                background: linear-gradient(135deg, #8b5cf6, #a855f7);
                transform: scale(1.05);
            }
            
            .pagination-container span[aria-current="page"] {
                color: #ffffff;
                background: linear-gradient(135deg, #8b5cf6, #a855f7);
                box-shadow: 0 4px 6px -1px rgba(139, 92, 246, 0.3);
            }
            
            .pagination-container .disabled span {
                color: #d1d5db;
                cursor: not-allowed;
            }

            /* ヘッダー内のテキストを白で統一（ボタンやリンクも含む） */
            #page-header,
            #page-header * {
                color: #ffffff !important;
            }
        </style>
    </head>
    <body class="font-sans antialiased bg-gradient-to-br from-gray-50 to-gray-100">
        <div class="min-h-screen">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header id="page-header" class="gradient-bg bg-gradient-to-r from-purple-500 to-blue-500 shadow-lg fixed left-0 right-0 w-full z-50">
                    <div class="max-w-7xl mx-auto py-12 px-6 sm:px-8 lg:px-12">
                        <div class="text-white">
                            {{ $header }}
                        </div>
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main id="page-main" class="pt-32 sm:pt-44 pb-8">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    {{ $slot }}
                </div>
            </main>

            <script>
                // Adjust header position and main padding-top to avoid overlap with fixed nav/header
                function adjustMainOffset(){
                    const nav = document.querySelector('nav');
                    const header = document.getElementById('page-header');
                    const main = document.getElementById('page-main');

                    const navHeight = nav ? nav.getBoundingClientRect().height : 0;

                    // place header directly under nav and ensure nav stays above header
                    if(header){
                        header.style.position = 'fixed';
                        header.style.left = '0';
                        header.style.right = '0';
                        header.style.top = navHeight + 'px';
                        // header should be below nav so it doesn't block nav interactions
                        header.style.zIndex = '1000';
                    }
                    if(nav){
                        // keep nav on top so dropdowns/triggers remain clickable
                        nav.style.zIndex = '9999';
                    }

                    // measure header after positioning
                    const headerHeight = header ? header.getBoundingClientRect().height : 0;

                    const offset = navHeight + headerHeight;
                    if(main){ main.style.paddingTop = offset + 'px'; }
                }

                // Run early (DOMContentLoaded) and on load/resize to cover different loading orders
                document.addEventListener('DOMContentLoaded', adjustMainOffset);
                window.addEventListener('load', adjustMainOffset);
                window.addEventListener('resize', adjustMainOffset);
            </script>
        </div>
    </body>
</html>
