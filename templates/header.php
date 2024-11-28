<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Uchū Docs</title>
        <link rel="icon" href="/img/32x32.png" type="image/x-icon">
        <style>
            :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --background-light: #f4f7f6;
            --text-color: #333;
            --code-background: #f8f9fa;
            --border-color: #e0e4e7;
            --highlight-color: #ecf0f1;
        }
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            scroll-behavior: smooth;
        }
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
            line-height: 1.6;
            color: var(--text-color);
            background-color: var(--background-light);
            display: flex;
            min-height: 100vh;
            max-width: 1400px;
            margin: 0 auto;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.05);
        }
        /* Sidebar */
        .sidebar {
            width: 250px;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .sidebar h2 {
            color: var(--primary-color);
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--secondary-color);
        }
        .sidebar nav ul {
            list-style-type: none;
        }
        .sidebar nav ul li {
            margin-bottom: 10px;
        }
        .sidebar nav ul li a {
            text-decoration: none;
            color: var(--primary-color);
            transition: color 0.3s ease;
            display: block;
            padding: 8px 15px;
            border-radius: 5px;
        }
        .sidebar nav ul li a:hover,
        .sidebar nav ul li a.active {
            background-color: var(--secondary-color);
            color: white;
        }
        /* Content */
        .content {
            flex-grow: 1;
            padding: 20px;
        }
        .content h1 {
            color: var(--primary-color);
            border-bottom: 2px solid var(--secondary-color);
            padding-bottom: 15px;
            margin-bottom: 30px;
        }
        .content h2,
        .content h3,
        .content h4 {
            color: var(--primary-color);
            margin-top: 30px;
            margin-bottom: 15px;
        }
        /* Code block styles */
        pre {
            background-color: #f4f4f4;
            padding: 15px;
            border-radius: 5px;
            overflow-x: auto;
        }
        code {
            background-color: #f4f4f4;
            padding: 2px 4px;
            border-radius: 3px;
        }
        pre code {
            background-color: transparent;
            padding: 0;
        }
        /* Search Container */
        .search-container {
            margin-bottom: 30px;
            position: relative;
        }
        #search-input {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s ease;
        }
        #search-input:focus {
            outline: none;
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.2);
        }
        #search-results {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background-color: white;
            border: 1px solid var(--border-color);
            border-top: none;
            border-radius: 0 0 8px 8px;
            max-height: 300px;
            overflow-y: auto;
            z-index: 10;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        #search-results .result-item {
            padding: 10px 15px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        #search-results .result-item:hover {
            background-color: var(--highlight-color);
        }
        #search-results .result-item .result-title {
            font-weight: bold;
            color: var(--primary-color);
        }
        #search-results .result-item .result-snippet {
            color: #666;
            font-size: 0.9em;
            margin-top: 5px;
        }
        /* Responsive Styles */
        @media (max-width: 1024px) {
            body {
                flex-direction: column;
            }
            .sidebar {
                width: 100%;
                height: auto;
                border-right: none;
                border-bottom: 1px solid var(--border-color);
            }
            .content {
                padding: 20px;
            }
        }
        /* Accessibility and print styles */
        @media print {
            body {
                max-width: none;
            }
                .sidebar {
                    display: none;
                }
        }
        /* Scroll bar styles for webkit browsers */
        ::-webkit-scrollbar {
            width: 10px;
        }
        ::-webkit-scrollbar-track {
            background: var(--background-light);
        }
        ::-webkit-scrollbar-thumb {
            background-color: var(--secondary-color);
            border-radius: 5px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background-color: var(--primary-color);
        }
        </style>
    </head>
<body>
