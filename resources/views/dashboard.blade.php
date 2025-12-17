<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ScheMatch</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Raleway">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: "Raleway", Arial, sans-serif;
            background-color: #e1a3b9b5; 
            color: #333;
        }
        
        .header {
            background-color: #951313ff;
            border-bottom: 1px solid #951313ff;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .logo {
            font-size: 1.5rem;
            letter-spacing: 3px;
            color: #f6f6f6ff;
            text-decoration: none;
        }
        
        .nav {
            display: flex;
            gap: 2rem;
            align-items: center;
        }
        
        .nav a, .nav button {
            text-decoration: none;
            color: #e1d0d0ff;
            font-size: 0.95rem;
            transition: color 0.3s;
            background: none;
            border: none;
            cursor: pointer;
            font-family: "Raleway", Arial, sans-serif;
        }
        
        .nav a:hover, .nav button:hover {
            color: #dfc603ff;
        }
        
        .nav a.active {
            color: #e8b210ff;
            font-weight: 600;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 3rem 2rem;
        }
        
        .welcome-section {
            text-align: center;
            padding: 4rem 2rem;
        }
        
        .welcome-section h1 {
            font-size: 2.5rem;
            letter-spacing: 4px;
            margin-bottom: 1rem;
            color: #333;
        }
        
        .welcome-section p {
            font-size: 1.1rem;
            color: #666;
            margin-bottom: 2rem;
        }
        
        .cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-top: 3rem;
        }
        
        .card {
            background: white;
            padding: 2rem;
            border: 1px solid #e5e5e5;
            transition: box-shadow 0.3s;
        }
        
        .card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        .card h3 {
            font-size: 1.3rem;
            margin-bottom: 1rem;
            letter-spacing: 2px;
        }
        
        .card p {
            color: #666;
            line-height: 1.6;
            margin-bottom: 1.5rem;
        }
        
        .btn {
            display: inline-block;
            padding: 0.75rem 2rem;
            background-color: #000;
            color: white;
            text-decoration: none;
            font-size: 0.9rem;
            letter-spacing: 1px;
            transition: background-color 0.3s;
            border: none;
            cursor: pointer;
        }
        
        .btn:hover {
            background-color: #333;
        }
    </style>
</head>
<body>
    <header class="header">
        <a href="/" class="logo">ScheMatch</a>
        <nav class="nav">
            <a href="/dashboard" class="active">Home</a>
            <a href="/availabilities">Availability</a>  
            <!--<a href="/friends">Friends</a>-->
            <a href="/courses">Courses</a>
            <form method="POST" action="/logout" style="display: inline;">
                <button type="submit">Logout</button>
            </form>
        </nav>
    </header>

    <div class="container">
        <div class="welcome-section">
            <h1>Welcome! Want to start matching?</h1>
            <p>Follow this user guide to start your experience.</p>
            <br><br><br><br>

            <h2>User Guide</h2>
        </div>
            <h3>Matching Availability</h3>
            <p>1. From the navigation bar, click on the Availabiliy.<br>
            2. Click and drag the time slots you are available, from the dates your group discussed.<br>
            3. To match availability with users, click the button "Public Users" found on top of the page and select users you want to compare your availability with.<br>
            4. Manage visibility with the buttons Public, Private, and Delete.<br>
            </p>
            <br><br>

            <h3>Matching Class Details</h3>
            <p>1. From the navigation bar, click on the Courses.<br>
            2. Add class details to start matching with other users.<br>
            3. Users with similar details would appear on the right.<br>
            4. If you find a match, you can click on their username to message them.<br>
            5. Messages are on the bottom right corner of the page.

            </p>
        </div>
            
        </div>
    </div>
</body>
</html>