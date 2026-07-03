@extends('layouts.app')

@section('title')
    @yield('code') - @yield('title_text')
@endsection

@section('content')
<div class="error-page-wrapper">
    <!-- Animated background particles/shapes -->
    <div class="bg-shape shape-1"></div>
    <div class="bg-shape shape-2"></div>
    <div class="bg-shape shape-3"></div>

    <div class="error-container">
        <!-- Floating Icon Container -->
        <div class="error-icon-box">
            <i class="@yield('icon')"></i>
        </div>

        <!-- Big Error Code -->
        <div class="error-code">@yield('code')</div>

        <!-- Error Heading -->
        <h1 class="error-heading">@yield('heading')</h1>

        <!-- Error Message -->
        <p class="error-message">@yield('message')</p>

        <!-- Actions -->
        <div class="error-actions">
            @yield('actions')
        </div>
    </div>
</div>

<style>
    /* Full Screen Centering and Background Gradient */
    .error-page-wrapper {
        min-height: 100vh;
        width: 100vw;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        overflow: hidden;
        background: radial-gradient(circle at 50% 50%, rgba(var(--color-primary-rgb), 0.08) 0%, rgba(var(--color-secondary-rgb), 0.04) 70%, var(--color-light) 100%);
        padding: 24px;
        box-sizing: border-box;
    }

    /* Floating Background Shapes */
    .bg-shape {
        position: absolute;
        border-radius: 50%;
        filter: blur(60px);
        z-index: 1;
        opacity: 0.5;
        animation: floatAnimation 12s infinite ease-in-out;
    }

    .shape-1 {
        width: 300px;
        height: 300px;
        background-color: rgba(var(--color-primary-rgb), 0.15);
        top: -100px;
        left: -100px;
    }

    .shape-2 {
        width: 400px;
        height: 400px;
        background-color: rgba(var(--color-secondary-rgb), 0.1);
        bottom: -150px;
        right: -100px;
        animation-delay: -4s;
    }

    .shape-3 {
        width: 250px;
        height: 250px;
        background-color: rgba(22, 163, 74, 0.08); /* Success green-600 */
        top: 40%;
        right: 15%;
        animation-delay: -8s;
    }

    /* Glassmorphism Card Container */
    .error-container {
        position: relative;
        z-index: 10;
        background: rgba(255, 255, 255, 0.75);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 1px solid rgba(255, 255, 255, 0.5);
        border-radius: var(--border-radius-lg);
        padding: 48px 32px;
        max-width: 540px;
        width: 100%;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.08);
        text-align: center;
        display: flex;
        flex-direction: column;
        align-items: center;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .error-container:hover {
        transform: translateY(-4px);
        box-shadow: 0 30px 60px -10px rgba(0, 0, 0, 0.12), var(--shadow-glow);
    }

    /* Floating Animated Icon Box */
    .error-icon-box {
        width: 90px;
        height: 90px;
        background: linear-gradient(135deg, rgb(var(--color-primary-rgb)), var(--color-secondary));
        border-radius: var(--border-radius-md);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.5rem;
        color: var(--color-white);
        box-shadow: 0 10px 25px rgba(var(--color-primary-rgb), 0.3);
        margin-bottom: 24px;
        animation: boxFloat 4s infinite ease-in-out;
    }

    .error-icon-box i {
        line-height: 1;
    }

    /* Animated Big Error Code */
    .error-code {
        font-family: var(--font-heading);
        font-size: 5.5rem;
        font-weight: 900;
        line-height: 1;
        background: linear-gradient(to right, rgb(var(--color-primary-rgb)), var(--color-secondary));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        margin-bottom: 12px;
        letter-spacing: -2px;
        position: relative;
    }

    /* Headings & Text */
    .error-heading {
        font-family: var(--font-heading);
        font-size: 1.6rem;
        font-weight: 700;
        color: var(--color-dark);
        margin-bottom: 16px;
        line-height: 1.3;
    }

    .error-message {
        font-family: var(--font-body);
        font-size: 1rem;
        color: var(--color-gray-600);
        margin-bottom: 32px;
        line-height: 1.6;
        max-width: 420px;
    }

    /* Action Buttons layout */
    .error-actions {
        display: flex;
        flex-direction: row;
        align-items: center;
        justify-content: center;
        gap: 16px;
        width: 100%;
        flex-wrap: wrap;
    }

    .error-actions .btn {
        flex: 1;
        min-width: 160px;
        max-width: 200px;
        justify-content: center;
        box-sizing: border-box;
    }

    /* Animations definition */
    @keyframes floatAnimation {
        0%, 100% {
            transform: translate(0, 0) scale(1);
        }
        33% {
            transform: translate(30px, -50px) scale(1.1);
        }
        66% {
            transform: translate(-20px, 20px) scale(0.95);
        }
    }

    @keyframes boxFloat {
        0%, 100% {
            transform: translateY(0);
        }
        50% {
            transform: translateY(-8px);
        }
    }

    /* Responsiveness adjustments */
    @media (max-width: 640px) {
        .error-container {
            padding: 32px 20px;
        }
        .error-code {
            font-size: 4.5rem;
        }
        .error-heading {
            font-size: 1.35rem;
        }
        .error-actions {
            flex-direction: column;
            gap: 12px;
        }
        .error-actions .btn {
            max-width: 100%;
            width: 100%;
        }
    }
</style>
@endsection
