
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'Europe Sales Dashboard')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        if (window.Chart) {
            Chart.defaults.color = '#b3c2bb';
            Chart.defaults.borderColor = 'rgba(145, 180, 169, 0.16)';
            Chart.defaults.font.family = 'Poppins, ui-sans-serif, system-ui, sans-serif';
        }
    </script>
    <style>
        * {
            box-sizing: border-box;
        }

        :root {
            color-scheme: dark;
            --page: #0c1116;
            --surface: rgba(21, 29, 34, 0.78);
            --surface-raised: rgba(28, 40, 43, 0.9);
            --border: rgba(145, 180, 169, 0.16);
            --text: #ecf2ef;
            --muted: #a4b4ad;
            --accent: #69d6b3;
            --accent-strong: #21a884;
            --green: #56d6a0;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            margin: 0;
            background:
                radial-gradient(ellipse at 82% 0%, rgba(23, 108, 90, 0.19), transparent 36%),
                radial-gradient(ellipse at 6% 44%, rgba(58, 73, 113, 0.13), transparent 35%),
                var(--page);
            color: var(--text);
            font-family: Poppins, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }

        .app-container {
            flex-direction: column;
            align-items: stretch;
        }

        .sidebar {
            position: sticky;
            top: 0;
            display: flex;
            flex-direction: row;
            align-items: center;
            gap: 30px;
            width: 100%;
            min-height: 76px;
            padding: 12px clamp(18px, 4vw, 56px);
            overflow: visible;
            background: rgba(12, 18, 22, 0.84);
            border-right: 0;
            border-bottom: 1px solid rgba(145, 180, 169, 0.15);
            color: var(--text);
            z-index: 20;
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
        }

        .main-content {
            min-width: 0;
            flex: 1;
        }

        .page-content {
            max-width: 1600px;
            margin: 0 auto;
        }

        .menu-link {
            display: flex;
            align-items: center;
            gap: 11px;
            padding: 11px 12px;
            margin-bottom: 5px;
            color: #acbbb5;
            text-decoration: none;
            border: 1px solid transparent;
            border-radius: 10px;
            font-size: 13px;
            transition: background-color 0.2s ease, color 0.2s ease, border-color 0.2s ease;
        }

        .menu-link:hover {
            background: rgba(57, 109, 94, 0.18);
            color: #f0fff8;
        }

        .menu-link.active {
            background: linear-gradient(110deg, rgba(34, 145, 112, 0.22), rgba(39, 73, 84, 0.28));
            border-color: rgba(91, 214, 175, 0.25);
            color: #93ebca;
        }

        .sidebar-source {
            display: flex;
            align-items: center;
            gap: 9px;
            margin-left: auto;
            padding: 0;
            border: 0;
            white-space: nowrap;
        }

        .sidebar-source p {
            margin: 0;
        }

        .sidebar-source p:first-child,
        .sidebar-source p:nth-child(3) {
            display: none;
        }

        .sidebar-source p:nth-child(2) {
            color: #c3d0ca;
            font-size: 12px;
        }

        .sidebar-source p:last-child {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: #7ce0bb;
            font-size: 11px;
        }

        .sidebar-source p:last-child::before {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #5de0ae;
            content: "";
            box-shadow: 0 0 10px rgba(93, 224, 174, 0.6);
        }

        .sidebar-brand {
            flex: 0 0 auto;
            margin: 0 !important;
        }

        .sidebar nav {
            display: flex;
            align-items: center;
            gap: 5px;
            margin-left: 8px;
        }

        .sidebar-menu-label {
            display: none;
        }

        .card {
            min-width: 0;
            background: linear-gradient(145deg, rgba(29, 41, 43, 0.82), rgba(19, 27, 32, 0.82));
            border: 1px solid rgba(145, 180, 169, 0.15);
            border-radius: 16px;
            padding: 21px;
            box-shadow: 0 16px 42px rgba(0, 0, 0, 0.2), inset 0 1px 0 rgba(230, 255, 243, 0.035);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
        }

        .page-title {
            font-size: 26px;
            font-weight: 700;
            color: #f2f7f4;
            letter-spacing: -0.035em;
        }

        .form-control {
            width: 100%;
            border: 1px solid rgba(145, 180, 169, 0.22);
            border-radius: 9px;
            padding: 10px 12px;
            background: rgba(13, 20, 23, 0.74);
            color: var(--text);
            font-size: 13px;
            outline: none;
        }

        .form-control:focus {
            border-color: #5bcba5;
            box-shadow: 0 0 0 3px rgba(50, 184, 141, 0.16);
        }

        select.form-control {
            min-width: 0;
            padding-right: 38px;
            color-scheme: dark;
            appearance: none;
            background-image:
                linear-gradient(45deg, transparent 50%, #78ddba 50%),
                linear-gradient(135deg, #78ddba 50%, transparent 50%);
            background-position:
                calc(100% - 17px) 50%,
                calc(100% - 12px) 50%;
            background-size: 5px 5px, 5px 5px;
            background-repeat: no-repeat;
            cursor: pointer;
            transition: border-color 0.2s ease, box-shadow 0.2s ease, background-color 0.2s ease;
        }

        select.form-control option {
            background: #172326;
            color: var(--text);
        }

        input[type="date"].form-control {
            min-width: 0;
            color-scheme: dark;
        }

        .kpi-card {
            overflow: hidden;
            padding: clamp(16px, 2vw, 23px);
        }

        .kpi-value {
            max-width: 100%;
            overflow-wrap: anywhere;
            font-size: clamp(1.05rem, 1.65vw, 1.55rem);
            line-height: 1.25;
            letter-spacing: -0.035em;
            font-variant-numeric: tabular-nums;
        }

        .kpi-revenue {
            border-top-color: rgba(99, 183, 255, 0.48);
        }

        .kpi-cost {
            border-top-color: rgba(240, 178, 103, 0.52);
        }

        .kpi-profit {
            border-top-color: rgba(93, 224, 174, 0.5);
        }

        .kpi-margin {
            border-top-color: rgba(183, 155, 255, 0.52);
        }

        .kpi-units {
            border-top-color: rgba(238, 137, 156, 0.5);
        }

        .table-head {
            background: rgba(44, 64, 62, 0.52);
            color: #bed0c8;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .table-cell {
            padding: 13px 14px;
            border-bottom: 1px solid rgba(145, 180, 169, 0.12);
            white-space: nowrap;
        }

        .btn-primary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            background: linear-gradient(120deg, #1d9e7b, #247c85);
            color: #ffffff;
            padding: 10px 16px;
            border-radius: 9px;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            border: none;
            cursor: pointer;
            transition: background-color 0.2s ease, transform 0.2s ease;
        }

        .btn-primary:hover {
            background: linear-gradient(120deg, #39bc94, #328f9b);
            transform: translateY(-1px);
        }

        .btn-secondary {
            display: inline-block;
            background: rgba(25, 36, 39, 0.8);
            color: #dce8e2;
            border: 1px solid rgba(145, 180, 169, 0.22);
            padding: 10px 16px;
            border-radius: 9px;
            font-size: 13px;
            text-decoration: none;
            transition: border-color 0.2s ease, background-color 0.2s ease;
        }

        .btn-secondary:hover {
            background: rgba(49, 74, 69, 0.72);
            border-color: rgba(111, 209, 171, 0.38);
        }

        .pagination {
            display: flex;
            gap: 5px;
            flex-wrap: wrap;
        }

        .pagination a,
        .pagination span {
            display: inline-block;
            padding: 7px 11px;
            border: 1px solid rgba(145, 180, 169, 0.22);
            border-radius: 6px;
            font-size: 12px;
            text-decoration: none;
            color: #dce8e2;
        }

        .pagination .active span {
            background: #218e73;
            color: white;
            border-color: #42bd95;
        }

        .main-content > header {
            background: rgba(14, 21, 24, 0.82);
            border-color: rgba(145, 180, 169, 0.15);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
        }

        .text-slate-800,
        .text-slate-700,
        .text-slate-600 {
            color: #e6efea !important;
        }

        .text-slate-500,
        .text-slate-400 {
            color: #a4b4ad !important;
        }

        .text-blue-600,
        .text-blue-700 {
            color: #78ddba !important;
        }

        .text-green-600 {
            color: var(--green) !important;
        }

        .text-red-500 {
            color: #ff8291 !important;
        }

        .bg-white,
        .bg-slate-50 {
            background-color: rgba(21, 29, 34, 0.78) !important;
        }

        .bg-slate-100 {
            background-color: rgba(44, 64, 62, 0.52) !important;
        }

        .bg-blue-100 {
            background-color: rgba(34, 99, 78, 0.4) !important;
        }

        .border-slate-200,
        .border-slate-700,
        .border-slate-500 {
            border-color: var(--border) !important;
        }

        .border-blue-500 {
            border-color: #56cba3 !important;
        }

        .hover\:bg-slate-50:hover {
            background-color: rgba(44, 78, 67, 0.26) !important;
        }

        .whatif-slider {
            width: 100%;
            height: 6px;
            margin: 11px 0 4px;
            border-radius: 999px;
            appearance: none;
            background: linear-gradient(90deg, #34484a, #39bc94);
            cursor: pointer;
        }

        .whatif-slider::-webkit-slider-thumb {
            width: 18px;
            height: 18px;
            appearance: none;
            border: 3px solid #d5fff0;
            border-radius: 50%;
            background: #38bd95;
            box-shadow: 0 0 0 4px rgba(56, 189, 149, 0.18);
        }

        .whatif-slider::-moz-range-thumb {
            width: 13px;
            height: 13px;
            border: 3px solid #d5fff0;
            border-radius: 50%;
            background: #38bd95;
            box-shadow: 0 0 0 4px rgba(56, 189, 149, 0.18);
        }

        .scenario-value {
            color: #90e7c7;
            font-variant-numeric: tabular-nums;
        }

        .level-card {
            display: block;
            min-height: 122px;
            min-width: 0;
            color: inherit;
            text-decoration: none;
            transition: transform 0.2s ease, border-color 0.2s ease, background 0.2s ease;
        }

        .level-card:hover {
            transform: translateY(-2px);
            border-color: rgba(99, 208, 170, 0.42);
            background: linear-gradient(145deg, rgba(34, 76, 65, 0.72), rgba(22, 39, 41, 0.82));
        }

        .level-card.active {
            border-color: rgba(99, 208, 170, 0.5);
            background: linear-gradient(145deg, rgba(34, 76, 65, 0.72), rgba(22, 39, 41, 0.82));
            box-shadow: 0 0 0 1px rgba(93, 224, 174, 0.1), 0 14px 36px rgba(12, 73, 57, 0.14);
        }

        .drilldown-row-link {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            color: #88e4c2;
            font-weight: 600;
            text-decoration: none;
        }

        .drilldown-row-link:hover {
            color: #d4ffed;
        }

        .drilldown-row-link span {
            transition: transform 0.2s ease;
        }

        .drilldown-row-link:hover span {
            transform: translateX(3px);
        }

        .page-content > * {
            animation: page-rise-in 0.48s both;
        }

        .page-content > *:nth-child(2) {
            animation-delay: 50ms;
        }

        .page-content > *:nth-child(3) {
            animation-delay: 100ms;
        }

        .page-content > *:nth-child(4) {
            animation-delay: 150ms;
        }

        .page-content > *:nth-child(5) {
            animation-delay: 200ms;
        }

        @keyframes page-rise-in {
            from {
                opacity: 0;
                transform: translateY(12px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (prefers-reduced-motion: reduce) {
            *,
            *::before,
            *::after {
                scroll-behavior: auto !important;
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                min-height: auto;
                height: auto;
                gap: 12px;
                padding: 12px 18px;
                overflow: visible;
                border-right: 0;
                border-bottom: 1px solid rgba(145, 180, 169, 0.15);
            }

            .sidebar-menu-label,
            .sidebar-source {
                display: none;
            }

            .sidebar nav {
                gap: 3px;
                overflow-x: auto;
                padding-bottom: 2px;
                margin-left: auto;
            }

            .menu-link {
                flex: 0 0 auto;
                padding: 9px 10px;
                margin: 0;
                white-space: nowrap;
            }

            .main-content {
                width: 100%;
            }

            .main-content > header {
                padding: 15px 18px;
            }

            .page-content {
                padding: 20px 16px;
            }

            .card {
                padding: 17px;
            }
        }

        @media (max-width: 520px) {
            .sidebar {
                align-items: flex-start;
                flex-direction: column;
                gap: 8px;
            }

            .sidebar nav {
                width: 100%;
                margin: 0;
            }

            .menu-link {
                padding: 8px 9px;
                font-size: 12px;
            }
        }
    </style>

    @stack('styles')
</head>

<body>

<div class="app-container flex min-h-screen">

    @include('partials.sidebar')

    <main class="main-content">

        @include('partials.header')

        <div class="page-content p-5 md:p-8">
            @yield('content')
        </div>

    </main>

</div>

@stack('scripts')

</body>
</html>
