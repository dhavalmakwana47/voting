<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Method Not Allowed</title>
    <style>
        /* Resetting some default margins */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background-color: #f0f0f5;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            overflow: hidden;
        }

        .container {
            text-align: center;
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            animation: fadeIn 1.2s ease-in-out;
        }

        h1 {
            font-size: 3rem;
            color: #ff4d4d;
            margin-bottom: 10px;
        }

        p {
            font-size: 1.2rem;
            color: #555;
            margin-bottom: 20px;
        }

        a {
            display: inline-block;
            padding: 12px 25px;
            font-size: 1rem;
            color: white;
            background-color: #3498db;
            border-radius: 5px;
            text-decoration: none;
            transition: background-color 0.3s, transform 0.2s;
        }

        a:hover {
            background-color: #2980b9;
            transform: scale(1.05);
        }

        @keyframes fadeIn {
            0% {
                opacity: 0;
                transform: translateY(20px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 600px) {
            h1 {
                font-size: 2.5rem;
            }
            p {
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>404 - Page Not Found</h1>
        <p>The page is not available. Please go back to the previous page.</p>
        <a href="{{ url('/') }}">Return to Home</a>
    </div>
</body>
</html>
