<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Class Schedule - ScheMatch</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Raleway">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: "Raleway", Arial, sans-serif;
            background-color: #f5f5f5;
            color: #333;
        }

        .header {
            background-color: white;
            border-bottom: 1px solid #e5e5e5;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .logo {
            font-size: 1.5rem;
            letter-spacing: 3px;
            color: #333;
            text-decoration: none;
        }
        
        .nav {
            display: flex;
            gap: 2rem;
            align-items: center;
        }
        
        .nav a, .nav button {
            text-decoration: none;
            color: #666;
            font-size: 0.95rem;
            transition: color 0.3s;
            background: none;
            border: none;
            cursor: pointer;
            font-family: "Raleway", Arial, sans-serif;
        }
        
        .nav a:hover, .nav button:hover {
            color: #000;
        }
        
        .nav a.active {
            color: #000;
            font-weight: 600;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 3rem 2rem;
        }

        .card {
            background: white;
            padding: 2.5rem;
            border: 1px solid #e5e5e5;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.05);
        }

        h1 {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            text-align: center;
            color: #333;
        }

        form label {
            display: block;
            margin-top: 1rem;
            font-weight: 600;
        }

        form input[type="text"] {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-family: "Raleway", Arial, sans-serif;
        }

        .btn {
            display: inline-block;
            padding: 0.75rem 2rem;
            background-color: #000;
            color: white;
            text-decoration: none;
            font-size: 0.95rem;
            letter-spacing: 1px;
            transition: background-color 0.3s;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            margin-top: 1.5rem;
        }

        .btn:hover {
            background-color: #333;
        }

        .errors {
            color: red;
            margin-top: 1.5rem;
        }
    </style>
</head>
<body>
    <header class="header">
        <a href="/" class="logo">ScheMatch</a>
        <nav class="nav">
            <a href="/dashboard">Home</a>
            <a href="/availabilities">Availability</a>  
            <a href="/courses" class="active">Courses</a>
            <form method="POST" action="/logout" style="display: inline;">
                <button type="submit">Logout</button>
            </form>
        </nav>
    </header>

    <div class="container">
        <div class="card">
            <h1>Add New Class Schedule</h1>

            <form method="POST" action="{{ route('courses.store') }}">
                @csrf

                <label>School Year (2025-2026)</label>
                <input type="text" name="school_year" value="{{ old('school_year', $course->school_year) }}">

                <label>Semester (1st/2nd/MY)</label>
                <input type="text" name="semester" value="{{ old('semester', $course->semester) }}">

                <label>Class Code (5 digits)</label>
                <input type="text" name="class_code" value="{{ old('class_code', $course->class_code) }}">

                <label>Subject</label>
                <input type="text" name="subject" value="{{ old('subject', $course->subject) }}">

                <label>Section</label>
                <input type="text" name="section" value="{{ old('section', $course->section) }}">

                <label>Professor (first name, last name, Jr/II)</label>
                <input type="text" name="professor" value="{{ old('professor', $course->professor) }}">

                <button type="submit" class="btn">Save ✅</button>
            </form>

            @if ($errors->any())
                <div class="errors">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    </div>
</body>
</html>
